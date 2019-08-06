FROM php:7.2.7-fpm
RUN docker-php-ext-install sockets bcmath
# RUN usermod -u 1000 www-data

COPY ./exec.sh /exec.sh
RUN chmod 0755 /exec.sh

# Install cron
RUN apt-get update && apt-get -y install cron

RUN touch /var/log/cron.log

# Setup cron job
RUN (crontab -l ; echo "* * * * * /bin/bash /exec.sh") | crontab

# Run the command on container startup
CMD ["cron", "-f"]
