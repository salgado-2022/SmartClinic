FROM php:8.2-apache

# Install system dependencies (unzip for Composer, git for source installs)
RUN apt-get update && apt-get install -y unzip git && rm -rf /var/lib/apt/lists/*

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Install PCOV extension for code coverage (faster than Xdebug for coverage-only)
RUN pecl install pcov && docker-php-ext-enable pcov

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to the app/ directory
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Update Apache configuration to use the DocumentRoot and allow .htaccess overrides
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i '/<Directory \/var\/www\/html>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf || true
RUN sed -i '/<Directory ${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf || true

# Ensure AllowOverride All is set for the document root
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom-directory.conf \
    && a2enconf custom-directory

# Create build/reports directory for test output (JUnit XML)
RUN mkdir -p /var/www/build/reports

# Copy application source code
COPY ./app /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
