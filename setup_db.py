import mysql.connector
import os

def init_db():
    try:
        conn = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'localhost'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', '')
        )
        cursor = conn.cursor()
        
        with open('schema.sql', 'r') as f:
            schema = f.read()
        statements = schema.split(';')
        for statement in statements:
            if statement.strip():
                try:
                    cursor.execute(statement)
                    conn.commit()
                    print(f"Executed: {statement[:50]}...")
                except mysql.connector.Error as err:
                    print(f"Error executing statement: {err}")
        
        print("Database initialized successfully!")
        cursor.close()
        conn.close()
    except mysql.connector.Error as err:
        print(f"Error: {err}")

if __name__ == '__main__':
    init_db()
