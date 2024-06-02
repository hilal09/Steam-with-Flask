from flask import Flask
from config import Config
from functions.models import db, bcrypt
from functions.auth import auth_bp
from functions.main import main_bp
from functions.profile import profile_bp  # Import the profile blueprint


app = Flask(__name__)  # Create an instance of the Flask class
app.config.from_object(Config)  # Load configuration from the Config object

# Initialize the database and bcrypt with the Flask app
db.init_app(app)
bcrypt.init_app(app)

# Register blueprints for authentication and main routes
app.register_blueprint(auth_bp, url_prefix='/auth')  # Prefix all routes in auth_bp with '/auth'
app.register_blueprint(main_bp)  # No prefix for main_bp
app.register_blueprint(profile_bp)  

if __name__ == '__main__':
    with app.app_context():
        db.create_all()  # Create all database tables
    app.run(debug=True)  # Run the Flask app in debug mode
