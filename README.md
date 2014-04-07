# Doctrine Cache Provider

[![Build Status](https://travis-ci.org/lavoiesl/doctrine-cache-provider.svg?branch=master)](https://travis-ci.org/lavoiesl/doctrine-cache-provider)

Library to add a Doctrine Provider to allow Doctrine Cache to connect to databases.

This can be useful for a poor man's distributed cache system.

Uses Doctrine DBAL and `serialize`.

See http://doctrine-dbal.readthedocs.org/en/latest/reference/configuration.html

## Usage

```php
<?php
use Lavoiesl\Doctrine\CacheProvider\DoctrineCache;

// Doctrine config
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = array(
    'dbname' => 'mydb',
    'user' => 'user',
    'password' => 'secret',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);


// DoctrineCache config
$fields = array(
    'id'         => 'name',
    'data'       => 'data',
    'expiration' => 'expiration',
);

$cache = new DoctrineCache($conn, 'my_table_cache', $fields);
$cache->save('key', $value, $ttl);
$cache->fetch('key');

?>
```

## Configuration

You need to have a working database and the proper table. A schema tool is included:

```php
<?php
use Lavoiesl\Doctrine\CacheProvider\CacheSchema;

CacheSchema::createTable($connection, 'my_table_cache', $fields);
?>
```

## Running tests

To run the MySQL test, you need to have a `test` database with user `test` and password `test`.

## TODO

Include tests for other Doctrine drivers

## Author

 * [Sébastien Lavoie](http://blog.lavoie.sl/)
 * [WeMakeCustom](http://www.wemakecustom.com/)