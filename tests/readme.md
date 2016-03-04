# PHPUnit test docs

## Install packages

You need to install packages:

```
composer install
```

## Config

Then prepare your config:

```php
$config = array(
    'login' => 'login',
    'pass' => 'pass',
    'test' => true,
    'log' => __DIR__.'/path/to/test.log',
    'log_level' => 1
);
```

## Run tests

Run standart PHPUnit command from tests directory:

```
phpunit --bootstrap vendor/autoload.php class.OllTvTest.php
```
