FROM php:7.3-fpm

RUN apt-get update && apt-get install -y \
        build-essential \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
        libfreetype6 \
        libfreetype6-dev \
        locales \
        zip \
        vim \
        jpegoptim optipng pngquant gifsicle \
        unzip \
        git \
        curl \
        libzip-dev zip unzip && \
        docker-php-ext-configure zip --with-libzip && \
        docker-php-ext-install zip && \
        docker-php-ext-install mbstring && \
        docker-php-ext-install gd && \
        php -m | grep -q 'zip' \
    && docker-php-ext-install pdo pdo_mysql

RUN apt-get update \
&& docker-php-ext-install pdo pdo_mysql

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#==============================================================================
# Phalcon:
#==============================================================================

ENV PHALCON_VERSION=4.0.0
ENV PHALCON_DEVTOOLS_VERSION=4.0.0

# Copy phalcon configration
COPY ./docker/php-fpm/phalcon.ini /usr/local/etc/php/conf.d/phalcon.ini.disable

# cphalcon
RUN curl -LO https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz \
    && tar xzf v${PHALCON_VERSION}.tar.gz \
    && docker-php-ext-install ${PWD}/cphalcon-${PHALCON_VERSION}/build/php7/64bits \
    && mv /usr/local/etc/php/conf.d/phalcon.ini.disable /usr/local/etc/php/conf.d/phalcon.ini \
    && rm -rf v${PHALCON_VERSION}.tar.gz cphalcon-${PHALCON_VERSION}

# devtools
RUN curl -LO https://github.com/phalcon/phalcon-devtools/archive/v${PHALCON_DEVTOOLS_VERSION}.tar.gz \
    && tar xzf v${PHALCON_DEVTOOLS_VERSION}.tar.gz \
    && mv phalcon-devtools-${PHALCON_DEVTOOLS_VERSION} /usr/local/phalcon-devtools \
    && ln -s /usr/local/phalcon-devtools/phalcon /usr/local/bin/phalcon \
    && chmod ugo+x /usr/local/bin/phalcon \
    && rm -f v${PHALCON_DEVTOOLS_VERSION}.tar.gz

# php-psr
RUN git clone https://github.com/jbboehr/php-psr.git \
    && cd php-psr \
    && /usr/local/bin/phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config \
    && make \
    && make test \
    && make install
#==============================================================================

# Change current user to root
USER root

# Workdir
WORKDIR /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
