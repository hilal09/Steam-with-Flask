from flask import Flask, render_template, request, redirect, url_for, flash, session
from werkzeug.security import generate_password_hash, check_password_hash
from models import create_user, get_user_by_email

app = Flask(__name__)
app.secret_key = 'your_secret_key'

@app.route('/')
def home():
    return redirect(url_for('login'))

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        
        if not email or not password:
            flash('Please fill in the fields.', 'error')
            return redirect(url_for('login'))

        user = get_user_by_email(email)
        if user and check_password_hash(user['password'], password):
            session['user_id'] = user['id']
            session['name'] = user['name']
            return redirect(url_for('dashboard'))
        else:
            flash('Invalid email or password.', 'error')
            return redirect(url_for('login'))
    return render_template('login.html')

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        name = request.form['name']
        email = request.form['email']
        password = request.form['password']
        
        if not name or not email or not password:
            flash('Please fill in the fields.', 'error')
            return redirect(url_for('register'))
        
        if get_user_by_email(email):
            flash('Email already taken.', 'error')
            return redirect(url_for('register'))

        hashed_password = generate_password_hash(password, method='sha256')
        create_user(name, email, hashed_password)
        flash('Registration successful. You can now log in.', 'success')
        return redirect(url_for('login'))
    return render_template('register.html')

@app.route('/dashboard')
def dashboard():
    if 'user_id' not in session:
        return redirect(url_for('login'))
    return render_template('dashboard.html', name=session['name'])

@app.route('/logout')
def logout():
    session.pop('user_id', None)
    session.pop('name', None)
    return redirect(url_for('login'))

if __name__ == '__main__':
    app.run(debug=True)
