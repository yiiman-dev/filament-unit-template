FROM php:8.3.20

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive

# -------------------------------------------------------
# Base dependencies
# -------------------------------------------------------
RUN apt-get update && apt-get install -y \
    git curl wget nano micro \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libbz2-dev libpq-dev \
    libxml2-dev libxslt-dev libicu-dev \
    libonig-dev libcurl4-openssl-dev \
    pkg-config libssl-dev libffi-dev \
    libreadline-dev libsodium-dev \
    libsystemd-dev autoconf g++ make \
    postgresql-client gnupg \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------------------------------
# Add Google Chrome repo
# -------------------------------------------------------
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add - && \
    sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google.list'

# -------------------------------------------------------
# Install Chrome + required dependencies
# -------------------------------------------------------
RUN apt-get update && apt-get install -y \
    google-chrome-stable \
    libglib2.0-0 libnss3 libatk1.0-0 libatk-bridge2.0-0 \
    libcups2 libdrm2 libxkbcommon0 libxcomposite1 \
    libxdamage1 libxfixes3 libxrandr2 libgbm1 \
    libasound2 libpangocairo-1.0-0 libpango-1.0-0 \
    libcairo2 libx11-xcb1 libxext6 libxss1 libxtst6 \
    fonts-liberation \
    --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------------------------------
#  Install Custom fonts
# -------------------------------------------------------
COPY ./public/fonts/shabnam/* /usr/share/fonts/truetype/custom/
COPY ./public/fonts/vazir/* /usr/share/fonts/truetype/custom/
RUN fc-cache -f -v


# -------------------------------------------------------
# Install Composer
# -------------------------------------------------------
RUN curl -sS https://getcomposer.org/download/2.2.6/composer.phar -o /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

# -------------------------------------------------------
# PHP Extensions
# -------------------------------------------------------
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
        bz2 calendar ctype curl dom exif ffi fileinfo filter ftp \
        gettext iconv intl mbstring opcache pcntl pdo pdo_pgsql pgsql \
        phar posix session shmop simplexml sockets sodium \
        sysvmsg sysvsem sysvshm xml xsl zip gd

# -------------------------------------------------------
# Node.js
# -------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_19.x | bash - && \
    apt-get install -y nodejs && \
    node -v && npm -v

# -------------------------------------------------------
# PHP Extensions via PECL
# -------------------------------------------------------
RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN pecl install redis && docker-php-ext-enable redis

# -------------------------------------------------------
# PHP memory limit and upload size
# -------------------------------------------------------
RUN echo "memory_limit = 1G" > /usr/local/etc/php/conf.d/memory-limit.ini && \
    echo "upload_max_filesize = 512M" >> /usr/local/etc/php/conf.d/memory-limit.ini && \
    echo "post_max_size = 512M" >> /usr/local/etc/php/conf.d/memory-limit.ini

# -------------------------------------------------------
# Copy project
# -------------------------------------------------------
COPY . .

RUN composer update --no-interaction --prefer-dist --optimize-autoloader
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data storage bootstrap/cache

RUN npm install

EXPOSE 8000

RUN chmod +x /var/www/html/entrypoint.sh
ENTRYPOINT ["/var/www/html/entrypoint.sh"]
