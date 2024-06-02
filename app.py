from flask import Flask, request, redirect, url_for, render_template, session
from flask_sqlalchemy import SQLAlchemy
from flask_bcrypt import Bcrypt
from sqlalchemy import text
import re

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'  # Add a secret key for session management
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/steam'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
bcrypt = Bcrypt(app)

@app.route('/', methods=['GET', 'POST'])
def index():
    return render_template('login.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if 'user_id' not in session:
        return redirect(url_for('login'))
    elif request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')

        if not email or not password:
            return render_template('login.html', error="Please fill in the fields.")

        result = db.session.execute(
            text("SELECT * FROM user_accounts WHERE email = :email"), {'email': email}
        ).mappings().first()  # Use .mappings() to return results as dictionaries


        if result and bcrypt.check_password_hash(result['password'], password):
            session['user_id'] = result['id']
            return redirect(url_for('dashboard'))
        else:
            return render_template('login.html', error="Invalid email or password.")

    return render_template('login.html')

@app.route('/dashboard')
def dashboard():
    if 'user_id' not in session:
        return redirect(url_for('login'))
    return render_template('dashboard.html')

@app.route('/registration', methods=['GET', 'POST'])
def registration():
    if request.method == 'POST':
        name = request.form.get('name')
        email = request.form.get('email')
        password = request.form.get('password')
        avatar = request.form.get('avatar', '')  # Optional field

        if not name or not email or not password:
            return render_template('registration.html', error="Please fill in the fields.")

        if not validate_email(email):
            return render_template('registration.html', error="Invalid e-mail.")

        result = db.session.execute(
            text("SELECT * FROM user_accounts WHERE email = :email"), {'email': email}
        ).fetchone()

        if result:
            return render_template('registration.html', error="E-mail taken.")

        hashed_password = bcrypt.generate_password_hash(password).decode('utf-8')

        db.session.execute(
            text("INSERT INTO user_accounts (name, email, password, avatar) VALUES (:name, :email, :password, :avatar)"),
            {'name': name, 'email': email, 'password': hashed_password, 'avatar': avatar}
        )
        db.session.commit()

        return redirect(url_for('dashboard'))

    error = request.args.get('error')
    return render_template('registration.html', error=error)

@app.route('/logout')
def logout():
    session.pop('user_id', None)
    return redirect(url_for('login'))

def validate_email(email):
    email_regex = r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'
    return re.match(email_regex, email) is not None

if __name__ == '__main__':
    app.run(debug=True)
