from flask import Flask, request, redirect, url_for, render_template, session
from flask_sqlalchemy import SQLAlchemy
from flask_bcrypt import Bcrypt
import re

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'  # Add a secret key for session management
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/steam'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
bcrypt = Bcrypt(app)

class UserAccounts(db.Model):
    __tablename__ = 'user_accounts'
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    password = db.Column(db.String(200), nullable=False)
    avatar = db.Column(db.String(200), nullable=True)

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')

        if not email or not password:
            return render_template('login.html', error="Please fill in the fields.")

        user = UserAccounts.query.filter_by(email=email).first()

        if user and bcrypt.check_password_hash(user.password, password):
            session['user_id'] = user.id
            return redirect(url_for('dashboard_page'))
        else:
            return render_template('login.html', error="Invalid email or password.")

    return render_template('login.html')

@app.route('/dashboard')
def dashboard_page():
    if 'user_id' not in session:
        return redirect(url_for('login'))
    return 'Dashboard Page'

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

        user_exists = UserAccounts.query.filter_by(email=email).first()
        if user_exists:
            return render_template('registration.html', error="E-mail taken.")

        hashed_password = bcrypt.generate_password_hash(password).decode('utf-8')

        new_user = UserAccounts(name=name, email=email, password=hashed_password, avatar=avatar)
        db.session.add(new_user)
        db.session.commit()

        if new_user.id:
            return redirect(url_for('dashboard_page'))
        else:
            return render_template('registration.html', error="registrationfailed")
    
    error = request.args.get('error')
    return render_template('registration.html', error=error)

def validate_email(email):
    email_regex = r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'
    return re.match(email_regex, email) is not None

if __name__ == '__main__':
    app.run(debug=True)
