composer require slim/slim:4.0.0

php -S localhost:8080 -t public public/index.php

php -S 192.168.1.20:8080 -t public public/index.php


//,





//ADD TO HT ACCESS
RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

//JWT
composer require tuupola/slim-jwt-auth

//LOG JWT
composer require monolog/monolog

//BASE 62
composer require tuupola/base62