<?php

require 'vendor/autoload.php';

http_response_code(201);

header('x-custom-header: Hello World');

echo 'Hello World';
