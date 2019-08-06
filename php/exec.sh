#!/bin/bash

CONTADOR=0
while [  $CONTADOR -lt 6 ]; do

  log_num=$(exec shuf -i 100-1000 -n 1)

  echo $(date) " - LOG NUM: $log_num" >> /var/log/cron.log

  /usr/local/bin/php /var/www/html/src/log_publisher.php $log_num >> /var/log/cron.log

  let CONTADOR=CONTADOR+1; 

  sleep 10
done
