import mysql.connector
import os

# SENSITIVE DATA REMOVED FOR GITHUB SAFETY
# Use these connection details on your local machine if needed
config = {
    'user': 'avnadmin',
    'password': '', # REMOVED: Please enter your Aiven password here manually
    'host': 'mysql-24172f92-think-programming.l.aivencloud.com',
    'port': 27029,
}

def try_read_sql(file_path):
    encodings = ['utf-8', 'utf-16', 'utf-8-sig', 'latin-1']
    for enc in encodings:
        try:
            with open(file_path, 'r', encoding=enc) as f:
                return f.read(), enc
        except (UnicodeDecodeError, UnicodeError):
            continue
    raise Exception("Could not decode SQL file with common encodings.")

try:
    if not config['password']:
        print("Error: Please set the 'password' in the script first.")
        exit(1)

    # 1. Connect without DB to create it
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()
    cursor.execute("CREATE DATABASE IF NOT EXISTS scamshield_db")
    print("Database scamshield_db created or already exists.")
    conn.close()

    # 2. Connect to the new DB
    config['database'] = 'scamshield_db'
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()

    # 3. Read and execute SQL file
    sql_file = r'c:\xampp\htdocs\ScamShield\scamshield_db.sql'
    if not os.path.exists(sql_file):
        print(f"Error: {sql_file} not found.")
        exit(1)

    print(f"Importing {sql_file}...")
    content, used_enc = try_read_sql(sql_file)
    print(f"Detected encoding: {used_enc}")
    
    sql_commands = content.split(';')
        
    for command in sql_commands:
        if command.strip():
            try:
                cursor.execute(command)
            except Exception as e:
                if "already exists" not in str(e).lower():
                    print(f"Warning skipping command: {str(e)}")
    
    conn.commit()
    print("Import successful!")
    conn.close()

except Exception as e:
    print(f"Migration Failed: {str(e)}")
