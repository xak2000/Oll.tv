# PHPUnit test docs

## Install packages

You need to install packages:

```
composer install
```

## Config

Then prepare your config:

```php
<?php

/**
 * Test config
 * @var array
 */
$config = array(
    'login' => 'login',                   // ispAPI login
    'pass' => 'pass',                     // ispAPI password
    'test' => true,                       // ispAPI test mode flag
    'log' => __DIR__.'/path/to/test.log', // log file
    'log_level' => 1                      // log level
);
```

## Run tests

Run standart PHPUnit command from tests directory:

```
phpunit --bootstrap vendor/autoload.php class.OllTvTest.php
```
