<?php

namespace Lavoiesl\Doctrine\CacheProvider\Test;

class MysqlTest extends DoctrineCacheTest
{
    protected function getConnection()
    {
        $config = new \Doctrine\DBAL\Configuration();
        if (getenv('TRAVIS')) {
            $params = array('host' => 'localhost', 'user' => 'travis', 'dbname' => 'dcacheprovider_test', 'driver' => 'pdo_mysql');
        } else {
            $params = array('host' => 'localhost', 'user' => 'test', 'password' => 'test', 'dbname' => 'test', 'driver' => 'pdo_mysql');
        }

        $connection = \Doctrine\DBAL\DriverManager::getConnection($params, $config);

        return $connection;
    }
}
