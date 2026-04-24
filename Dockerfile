FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod headers

# Copy application code
COPY . /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Copy Apache configuration
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/smartwatch.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/smartwatch.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/smartwatch.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/smartwatch.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/smartwatch.conf

RUN a2enconf smartwatch

# Expose port 80
EXPOSE 80

# Set working directory
WORKDIR /var/www/html

# Default command
CMD ["apache2-foreground"]
