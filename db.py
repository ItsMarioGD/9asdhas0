import mysql.connector
import os

def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'localhost'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', ''),
            database=os.getenv('DB_NAME', 'medi_agenda_db')
        )
        return conn
    except mysql.connector.Error as err:
        print(f"Error connecting to database: {err}")
        if err.errno == 1049: 
             try:
                conn = mysql.connector.connect(
                    host=os.getenv('DB_HOST', 'localhost'),
                    user=os.getenv('DB_USER', 'root'),
                    password=os.getenv('DB_PASSWORD', '')
                )
                return conn
             except mysql.connector.Error as err2:
                 print(f"Error connecting to server: {err2}")
                 return None
        return None
