FROM php:7.2.7-fpm
RUN docker-php-ext-install sockets bcmath 
# RUN usermod -u 1000 www-data

# Copy app files
COPY ./app /var/www/html

# cron job task
COPY ./exec.sh /exec.sh
RUN chmod 0755 /exec.sh

# Install cron
RUN apt-get update && apt-get -y install cron zip unzip

# run compsoer install
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN composer install --no-dev --no-interaction -o

# Create log file
RUN touch /var/log/cron.log

# Setup cron job
RUN (crontab -l ; echo "* * * * * /bin/bash /exec.sh") | crontab

# Run the command on container startup
CMD ["cron", "-f"]
