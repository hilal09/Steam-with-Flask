from flask import Blueprint, render_template, request, redirect, url_for
from app import UserAccounts
from app import bcrypt
import re

registration_blueprint = Blueprint('registration', __name__)

@registration_blueprint.route('/registration', methods=['GET', 'POST'])
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
