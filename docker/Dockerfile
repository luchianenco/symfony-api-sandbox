FROM phusion/baseimage:0.9.19

RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

ENV HOME /root

ENV TIMEZONE Europe/Berlin
ENV APP_ROOT /app

VOLUME $APP_ROOT

CMD ["/sbin/my_init"]

# Nginx-PHP Installation
RUN apt-get update
RUN apt-get install -y vim curl wget build-essential python-software-properties
RUN add-apt-repository -y ppa:ondrej/php
RUN apt-get update
RUN apt-get install -y --force-yes libcurl4-gnutls-dev php7.0-cli php7.0-fpm php7.0-mysql php7.0-pgsql php7.0-sqlite php7.0-curl\
		       php7.0-gd php7.0-mcrypt php7.0-intl php7.0-imap php7.0-tidy php-pear php7.0-dev php7.0-xsl\
		       libxml2-dev git mercurial unzip libz-dev libicu-dev nano pkg-config\
		       php7.0-soap php7.0-opcache php7.0-mbstring

RUN sed -i "s|;date.timezone =.*|date.timezone = $TIMEZONE|" /etc/php/7.0/fpm/php.ini
RUN sed -i "s|;date.timezone =.*|date.timezone = $TIMEZONE|" /etc/php/7.0/cli/php.ini

# Install Composer:
RUN curl -s http://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

RUN apt-get install -y nginx

RUN echo "daemon off;" >> /etc/nginx/nginx.conf
RUN sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/7.0/fpm/php-fpm.conf
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini

# Add github and bitbucket keys to known hosts
RUN ssh-keyscan -t rsa github.com 2>&1 >> /root/.ssh/known_hosts
RUN ssh-keyscan -t rsa bitbucket.org 2>&1 >> /root/.ssh/known_hosts

EXPOSE 80

ENV COMPOSER_HOME /var/www

WORKDIR /

# Setup PHP

# Increase upload size:
RUN sed -i "s|upload_max_filesize =.*|upload_max_filesize = 100M|" /etc/php/7.0/fpm/php.ini
RUN sed -i "s|post_max_size =.*|post_max_size = 101M|" /etc/php/7.0/cli/php.ini

# Increase memor_limit
RUN sed -i "s|memory_limit =.*|memory_limit = 1500M|" /etc/php/7.0/fpm/php.ini
RUN sed -i "s|memory_limit =.*|memory_limit = 1500M|" /etc/php/7.0/cli/php.ini

RUN sed -i "s|max_execution_time =.*|max_execution_time = 0|" /etc/php/7.0/fpm/php.ini
RUN sed -i "s|max_execution_time =.*|max_execution_time = 0|" /etc/php/7.0/cli/php.ini

ENV RELATIVE_APP_ROOT web
ENV PHP_INDEX_FILE app_dev.php

ADD default         /etc/nginx/sites-available/default
RUN sed -i "s|<relative_app_root>|${RELATIVE_APP_ROOT}|" /etc/nginx/sites-available/default
RUN sed -i "s|<base_app>|${APP_ROOT}|" /etc/nginx/sites-available/default
RUN sed -i "s|<index_php>|${PHP_INDEX_FILE}|" /etc/nginx/sites-available/default

RUN mkdir           /etc/service/nginx
ADD nginx.sh        /etc/service/nginx/run
RUN chmod +x        /etc/service/nginx/run
RUN mkdir           /etc/service/phpfpm
ADD php.sh          /etc/service/phpfpm/run
RUN chmod +x        /etc/service/phpfpm/run
RUN mkdir           /run/php/

VOLUME /var
ENV HOME /var/www
RUN usermod -s /bin/bash www-data
RUN usermod -d /var/www/ www-data
WORKDIR $APP_ROOT
