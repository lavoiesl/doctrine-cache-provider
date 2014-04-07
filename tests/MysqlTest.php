<?php

namespace Lavoiesl\Doctrine\CacheProvider\Test;

class MysqlTest extends DoctrineCacheTest
{
    protected function getConnection()
    {
        $config = new \Doctrine\DBAL\Configuration();
        $params = array('host' => 'localhost', 'user' => 'test', 'password' => 'test', 'dbname' => 'test', 'driver' => 'pdo_mysql');
        $connection = \Doctrine\DBAL\DriverManager::getConnection($params, $config);

        return $connection;
    }
}
