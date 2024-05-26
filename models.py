from db_connection import get_db_connection

def create_user(name, email, password):
    conn = get_db_connection()
    if conn:
        cursor = conn.cursor()
        cursor.execute("INSERT INTO user_accounts (name, email, password) VALUES (%s, %s, %s)",
                       (name, email, password))
        conn.commit()
        conn.close()

def get_user_by_email(email):
    conn = get_db_connection()
    if conn:
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM user_accounts WHERE email = %s", (email,))
        user = cursor.fetchone()
        conn.close()
        return user
    return None
