from flask import Blueprint, render_template, request, redirect, url_for, session
from app import UserAccounts
from app import bcrypt

login_blueprint = Blueprint('login', __name__)

@login_blueprint.route('/login', methods=['GET', 'POST'])
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
