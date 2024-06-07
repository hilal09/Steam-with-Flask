from flask import Flask, render_template, request, jsonify
from flask_mysqldb import MySQL
from flask_bcrypt import check_password_hash, generate_password_hash
from flask import session, redirect, url_for
from config import Config  



app = Flask(__name__)
app.secret_key = 'steam123'
app.config.from_object(Config)  


mysql = MySQL(app)

@app.route('/')
@app.route('/index')
def index():
    if 'user_id' in session:
        return redirect(url_for('dashboard'))
    return render_template('login.html')


@app.route('/register')
def register():
    return render_template('registration.html')

@app.route('/dashboard')
def dashboard():
    if 'user_id' in session:
        # User is authenticated, render the dashboard
        return render_template('dashboard.html')
    else:
        # User is not authenticated, redirect to login page
        return redirect(url_for('index'))


@app.route('/login', methods=['POST'])
def login():
    if 'user_id' in session:
        return redirect(url_for('dashboard'))
    
    email = request.json.get('email')
    password = request.json.get('password')

    # Retrieve user from the database using email
    cursor = mysql.connection.cursor()
    cursor.execute('SELECT * FROM user_accounts WHERE email = %s', (email,))
    user = cursor.fetchone()

    if user and check_password_hash(user[3], password):  # Assuming password hash is in the fourth column
        # Successful login
        session['user_id'] = user[0]  # Assuming user_id is the first column in your users table
        return jsonify({'message': 'Login successful'})
    else:
        # Invalid credentials
        return jsonify({'error': 'Invalid email or password'}), 401

    
@app.route('/register', methods=['POST'])
def register_user():
    data = request.get_json()
    name = data.get('name')
    email = data.get('email')
    password = data.get('password')

    if not name or not email or not password:
        return jsonify({'error': 'All fields are required'}), 400

    if not validate_email(email):
        return jsonify({'error': 'Invalid email format'}), 400

    cursor = mysql.connection.cursor()
    cursor.execute("SELECT id FROM user_accounts WHERE email = %s", (email,))
    if cursor.fetchone():
        cursor.close()
        return jsonify({'error': 'Email is already taken'}), 409

    hashed_password = generate_password_hash(password).decode('utf-8')
    cursor.execute("INSERT INTO user_accounts (name, email, password) VALUES (%s, %s, %s)",
                   (name, email, hashed_password))
    mysql.connection.commit()
    cursor.close()

    return jsonify({'success': True}), 201

def validate_email(email):
    from re import match
    return match(r'[^@]+@[^@]+\.[^@]+', email)

@app.route('/logout')
def logout():
    # Clear the session data
    session.clear()
    # Redirect to the login page
    return redirect(url_for('index'))

if __name__ == '__main__':
    app.run(debug=True)
