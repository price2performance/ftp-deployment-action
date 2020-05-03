FROM php:7.4-apache

LABEL "com.github.actions.name"="FTP Deployment Action"
LABEL "com.github.actions.description"="Use dg/ftp-deployment in your Github Actions."
LABEL "com.github.actions.icon"="upload"
LABEL "com.github.actions.color"="red"

#LABEL "repository"=""
#LABEL "homepage"=""
#LABEL "maintainer"=""


RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

RUN apt-get install -y zip unzip libzip-dev libcurl4-openssl-dev openssl libssh2-1-dev

RUN docker-php-ext-install zip

RUN apt-get install -y libssh2-1-dev libssh2-1
RUN pecl install ssh2-1.2
RUN docker-php-ext-enable ssh2

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#WORKDIR /
RUN composer create-project dg/ftp-deployment
RUN mv /var/www/html/ftp-deployment /ftp-deployment
ENV PATH=$PATH:/ftp-deployment

ADD entrypoint.sh /entrypoint.sh
COPY provide-config.php /ftp-deployment/provide-config.php
ENTRYPOINT ["/entrypoint.sh"]