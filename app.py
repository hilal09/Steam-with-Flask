import base64
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
        user_id = session['user_id']
        cursor = mysql.connection.cursor()
        cursor.execute('SELECT id, title, year, seasons, genre, platform, picture, rating FROM my_series WHERE user_id = %s', (user_id,))
        series = cursor.fetchall()
        cursor.close()

        series_list = []
        for row in series:
            # Convert the picture blob to base64 encoding
            if row[6] is not None:  # Assuming picture is at index 6
                picture_base64 = base64.b64encode(row[6]).decode('utf-8')
            else:
                picture_base64 = None

            series_list.append({
                'id': row[0],
                'title': row[1],
                'year': row[2],
                'seasons': row[3],
                'genre': row[4],
                'platform': row[5],
                'picture': picture_base64,  # Replace row[6] with base64 encoded string
                'rating': row[7]
            })

        return render_template('dashboard.html', series=series_list)
    else:
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


#Profile start

@app.route('/profile')
def profile():
    if 'user_id' not in session:
        return redirect(url_for('index'))

    user_id = session['user_id']
    cursor = mysql.connection.cursor()
    cursor.execute('SELECT * FROM user_accounts WHERE id = %s', (user_id,))
    user = cursor.fetchone()
    cursor.close()

    if user:
        user = {
            'id': user[0],
            'name': user[1],
            'email': user[2],
            'password': user[3],
            'avatar': user[4] if user[4] else 'default_avatar.jpg' 
        }

    return render_template('profile.html', user=user)

@app.route('/update_profile', methods=['POST'])
def update_profile():
    if 'user_id' not in session:
        return redirect(url_for('index'))

    data = request.get_json()
    user_id = data.get('userId')
    name = data.get('full_name')
    email = data.get('email')
    password = data.get('password')
    avatar = data.get('avatar', '')

    cursor = mysql.connection.cursor()

    if password:
        hashed_password = generate_password_hash(password).decode('utf-8')
        cursor.execute('''
            UPDATE user_accounts 
            SET name = %s, email = %s, password = %s, avatar = %s 
            WHERE id = %s
        ''', (name, email, hashed_password, avatar, user_id))
    else:
        cursor.execute('''
            UPDATE user_accounts 
            SET name = %s, email = %s, avatar = %s 
            WHERE id = %s
        ''', (name, email, avatar, user_id))

    mysql.connection.commit()
    cursor.close()

    return jsonify({'message': 'Profile updated successfully'})


@app.route('/delete_account', methods=['POST'])
def delete_account():
    if 'user_id' not in session:
        return redirect(url_for('index'))

    data = request.get_json()
    user_id = data.get('userId')

    cursor = mysql.connection.cursor()
    cursor.execute('DELETE FROM my_series WHERE user_id = %s', (user_id,))
    cursor.execute('DELETE FROM user_accounts WHERE id = %s', (user_id,))

    mysql.connection.commit()
    cursor.close()

    session.clear()

    return jsonify({'message': 'Account deleted successfully'})

#Profile end


# REST endpoints for series
@app.route('/series', methods=['GET'])
def get_all_series():
    user_id = session.get('user_id')
    if not user_id:
        return jsonify({'error': 'Unauthorized'}), 401


    cursor = mysql.connection.cursor()
    cursor.execute('SELECT id, title, year, seasons, genre, platform, picture, rating FROM my_series')
    series = cursor.fetchall()
    cursor.close()

    series_list = []
    for row in series:
        series_list.append({
            'id': row[0],
            'title': row[1],
            'year': row[2],
            'seasons': row[3],
            'genre': row[4],
            'platform': row[5],
            'picture': row[6],
            'rating': row[7]
        })

    return jsonify(series_list)

@app.route('/series', methods=['POST'])
def add_series():
    user_id = session.get('user_id')
    if not user_id:
        return jsonify({'error': 'Unauthorized'}), 401

    data = request.get_json() # Get JSON data from Request, instead of form data

    title = data.get('title')
    year = data.get('year')
    seasons = data.get('seasons')
    genre = data.get('genre')
    platform = data.get('platform')
    picture_base64 = data.get('picture') # Now expecting a base64 string
    rating = data.get('rating')

    if not title or not year or not seasons or not genre or not platform or not picture_base64 or not rating:
        return jsonify({'error': 'Missing data'}), 400


    picture_data = base64.b64decode(picture_base64) # decode the base64 string to bytes

    cursor = mysql.connection.cursor()
    cursor.execute('''
        INSERT INTO my_series (user_id, title, year, seasons, genre, platform, picture, rating)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    ''', (user_id, title, year, seasons, genre, platform, picture_data, rating))
    mysql.connection.commit()
    cursor.close()

    return jsonify({'success': True}), 201


@app.route('/series/<int:series_id>', methods=['DELETE'])
def delete_series(series_id):
    user_id = session.get('user_id')
    if not user_id:
        return jsonify({'error': 'Unauthorized'}), 401

    cursor = mysql.connection.cursor()
    cursor.execute('DELETE FROM my_series WHERE id = %s AND user_id = %s', (series_id, user_id))
    mysql.connection.commit()
    cursor.close()

    return jsonify({'success': True}), 200

@app.route('/search', methods=['GET'])
def search():
    return render_template('search.html')

@app.route('/search_handler', methods=['GET'])
def search_handler():
    query = request.args.get('query', '')
    title_filter = request.args.get('title', '')
    genre_filter = request.args.get('genre', '')
    platform_filter = request.args.get('platform', '')

    cursor = mysql.connection.cursor()
    cursor.execute("""
        SELECT id, title, year, seasons, genre, platform, picture, rating
        FROM my_series
        WHERE (title LIKE %s OR genre LIKE %s OR platform LIKE %s)
        AND (title LIKE %s)
        AND (genre LIKE %s)
        AND (platform LIKE %s)
    """, ('%' + query + '%', '%' + query + '%', '%' + query + '%', '%' + title_filter + '%', '%' + genre_filter + '%', '%' + platform_filter + '%'))
    my_series = cursor.fetchall()
    cursor.close()

    search_results = []
    for row in my_series:
        picture_base64 = base64.b64encode(row[6]).decode('utf-8') if row[6] else None
        search_results.append({
            'id': row[0],
            'title': row[1],
            'year': row[2],
            'seasons': row[3],
            'genre': row[4],
            'platform': row[5],
            'picture': picture_base64,
            'rating': row[7]
        })

    return render_template('search_results.html', series=search_results)

@app.route('/add_to_my_series', methods=['POST'])
def add_to_my_series():
    if 'user_id' not in session:
        return jsonify({'error': 'Unauthorized'}), 401

    data = request.get_json()
    series_id = data.get('series_id')
    user_id = session['user_id']

    cursor = mysql.connection.cursor()
    cursor.execute('SELECT id FROM my_series WHERE series_id = %s AND user_id = %s', (series_id, user_id))
    if cursor.fetchone():
        cursor.close()
        return jsonify({'error': 'Series already added to your list'}), 409

    cursor.execute('INSERT INTO my_series (series_id, user_id) VALUES (%s, %s)', (series_id, user_id))
    mysql.connection.commit()
    cursor.close()

    return jsonify({'success': True}), 201

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)  