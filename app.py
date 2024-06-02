from flask import Flask, jsonify, request, render_template, redirect, url_for, session
from flask_sqlalchemy import SQLAlchemy
from flask_bcrypt import Bcrypt

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'  # Secret key for session management
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/steam'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
bcrypt = Bcrypt(app)

# User model to represent users in the database
class UserAccounts(db.Model):
    __tablename__ = 'user_accounts'
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    password = db.Column(db.String(200), nullable=False)
    avatar = db.Column(db.String(200), nullable=True)

# Route for the home page
@app.route('/')
def index():
    if 'user_id' in session:
        return redirect(url_for('dashboard'))  # Redirect to dashboard if user is logged in
    else:
        return redirect(url_for('login'))  # Redirect to login page if not logged in

# Route for the dashboard page
@app.route('/dashboard')
def dashboard():
    if 'user_id' not in session:
        return redirect(url_for('login'))  # Redirect to login page if not logged in
    return render_template('dashboard.html')

import re

# Route for user registration
@app.route('/registration', methods=['GET', 'POST'])
def registration():
    if request.method == 'POST':
        # Check if the request contains JSON data
        if request.is_json:
            # Extract data from JSON format
            data = request.json
            name = data.get('name')
            email = data.get('email')
            password = data.get('password')
            avatar = data.get('avatar')
        else:
            # Extract data from form data
            name = request.form.get('name')
            email = request.form.get('email')
            password = request.form.get('password')
            avatar = request.form.get('avatar')

        # Check if all required fields are provided
        if not name or not email or not password:
            return render_template('registration.html', error="Please fill in the fields.")

        # Validate email format
        if not validate_email(email):
            return render_template('registration.html', error="Invalid email format.")

        # Check if the email already exists
        if UserAccounts.query.filter_by(email=email).first():
            return render_template('registration.html', error="Email already taken.")

        # Hash the password before storing it in the database
        hashed_password = bcrypt.generate_password_hash(password).decode('utf-8')

        # Create a new user instance and add it to the database
        new_user = UserAccounts(name=name, email=email, password=hashed_password, avatar=avatar)
        db.session.add(new_user)
        db.session.commit()

        return redirect(url_for('dashboard'))
    
    return render_template('registration.html')  # Render the registration page for GET requests

# Function to validate email format
def validate_email(email):
    email_regex = r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'
    return re.match(email_regex, email) is not None

# Route for the login page
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        # Handle login logic for POST requests
        # Extract email and password from the request form data
        email = request.form.get('email')
        password = request.form.get('password')

        # Check if both email and password are provided
        if not email or not password:
            return render_template('login.html', error="Please fill in the fields.")

        # Query the database for the user by email
        user = UserAccounts.query.filter_by(email=email).first()

        # Check if the user exists and the password is correct
        if user and bcrypt.check_password_hash(user.password, password):
            session['user_id'] = user.id  # Store user ID in session
            return redirect(url_for('dashboard'))  # Redirect to dashboard after successful login
        else:
            return render_template('login.html', error="Invalid email or password.")

    if 'user_id' in session:
        return redirect(url_for('dashboard'))  # Redirect to dashboard if user is already logged in

    return render_template('login.html')  # Render the login page for GET requests

# Route for user logout
@app.route('/logout')
def logout():
    session.pop('user_id', None)  # Remove 'user_id' from the session
    return redirect(url_for('login'))  # Redirect to the login page after logging out


if __name__ == '__main__':
    app.run(debug=True)
