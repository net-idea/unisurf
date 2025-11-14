# Dockerfile
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    oniguruma-dev \
    libxml2-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Yarn
RUN npm install -g yarn

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package.json & yarn.lock
COPY package.json yarn.lock webpack.config.js ./

# Install Node dependencies
RUN yarn install --frozen-lockfile

# Copy project
COPY . .

# Generate autoloader
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# Permissions
RUN chown -R www-data:www-data /var/www/html/var

# Expose port (not needed in FPM, but for clarity)
EXPOSE 9000

CMD ["php-fpm"]
