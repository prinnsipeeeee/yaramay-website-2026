FROM php:8.2-apache

# Install MySQL PDO extension (SQLite PDO is built into php:8.2-apache)
RUN docker-php-ext-install pdo_mysql

# Enable Apache modules required by .htaccess security & routing rules
RUN a2enmod rewrite headers

# Allow .htaccess overrides inside /var/www/html so data/ & uploads/ protections work
RUN { \
      echo '<Directory /var/www/html>'; \
      echo '    Options -Indexes +FollowSymLinks'; \
      echo '    AllowOverride All'; \
      echo '    Require all granted'; \
      echo '</Directory>'; \
    } > /etc/apache2/conf-available/yaramay-app.conf \
    && a2enconf yaramay-app

# Configure PHP limits and security defaults (portal supports up to 8MB GCash receipts)
RUN { \
      echo 'expose_php = Off'; \
      echo 'display_errors = Off'; \
      echo 'log_errors = On'; \
      echo 'upload_max_filesize = 10M'; \
      echo 'post_max_size = 12M'; \
      echo 'memory_limit = 256M'; \
      echo 'max_execution_time = 60'; \
    } > /usr/local/etc/php/conf.d/yaramay.ini

# Copy application files
WORKDIR /var/www/html
COPY . /var/www/html/

# Copy and prepare entrypoint script (strip CRLF safely for Windows git checkouts)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure writable data and uploads directories
RUN mkdir -p /var/www/html/data/sessions /var/www/html/uploads/receipts \
    && chown -R www-data:www-data /var/www/html/data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/data /var/www/html/uploads

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
