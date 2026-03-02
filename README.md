# ScamShield - AI Powered Job Checker

ScamShield is a web-based application designed to help users identify potentially fraudulent job offers using AI-powered text and image analysis.

## Features

- **Text Analysis**: Paste a job description to check for scam patterns.
- **Image Analysis**: Upload a job poster or screenshot to extract text using OCR and analyze it.
- **Dashboard**: Track your job checking history and see overall statistics.
- **Modern UI**: Dark glassmorphic design for a premium feel.

## Tech Stack

- **Frontend**: PHP, HTML, CSS (Vanilla), JavaScript.
- **Backend API**: Python (Flask).
- **AI/ML**: Scikit-learn (MultinomialNB), NLTK, Pytesseract for OCR.
- **Database**: MySQL.

## Setup Instructions

### Prerequisites
- PHP 8.x (XAMPP/WAMP recommended)
- Python 3.x
- Tesseract OCR installed on your system.

### 1. Database Setup
- Import the `scamshield_db.sql` file (found in the root directory) into your MySQL database using phpMyAdmin or the `mysql` command line.
- Update `php_app/db.php` with your database credentials (host, username, password, database name).

### 2. Python API Setup
- Navigate to the `python_api` directory.
- Install dependencies:
  ```bash
  pip install -r requirements.txt
  ```
- Start the API:
  ```bash
  python app.py
  ```

### 3. Frontend Setup
- Copy the project to your web server directory (e.g., `htdocs`).
- Access the application via `http://localhost/ScamShield/php_app/`.

## License
MIT
