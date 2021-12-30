FROM bref/php-81-fpm

COPY ./src /var/task

CMD ["index.php"]

