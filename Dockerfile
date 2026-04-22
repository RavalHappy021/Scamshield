# 1. Base Image: PHP 8.2 with Apache (Debian Bullseye)
FROM php:8.2-apache

# 2. Install Python, Tesseract OCR, and Supervisord
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    tesseract-ocr \
    libtesseract-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# 3. Configure Apache for Port 7860 (Hugging Face Default)
RUN sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# 4. Set Working Directory
WORKDIR /var/www/html

# 5. Configure Apache (Enable mod_rewrite for modern PHP apps)
RUN a2enmod rewrite
COPY . /var/www/html/

# 6. Setup Python Environment
WORKDIR /var/www/html/python_api
RUN pip3 install --no-cache-dir -r requirements.txt
RUN pip3 install --no-cache-dir gunicorn supervisor

# 7. Final App Directory Permissions
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 8. Port Exposures
# Hugging Face routes traffic to port 7860
EXPOSE 7860 5000

# 9. Use Supervisord to run both Apache and Gunicorn
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start Supervisord in foreground
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
