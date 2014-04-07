<?php

namespace Lavoiesl\Doctrine\CacheProvider\Test;

use Lavoiesl\Doctrine\CacheProvider\DoctrineCache;
use Lavoiesl\Doctrine\CacheProvider\CacheSchema;
use Doctrine\Tests\Common\Cache\CacheTest;

abstract class DoctrineCacheTest extends CacheTest
{
    protected $connection;

    protected $provider;

    public function setUp()
    {
        $this->connection = $this->getConnection();

        try {
            $this->connection->connect();
        } catch (\PDOException $e) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of a working connection');
            return;
        }

        $this->assertGreaterThan(0, CacheSchema::createTable($this->connection));
        $this->assertEquals(0, CacheSchema::createTable($this->connection));

        $this->provider = new DoctrineCache($this->connection);
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, CacheSchema::dropTable($this->connection));

        $this->connection->close();
    }

    protected function _getCacheDriver()
    {
        return new DoctrineCache($this->connection);
    }

    public function testGetStats()
    {
        $stats = $this->_getCacheDriver()->getStats();

        $this->assertNull($stats);
    }

    abstract protected function getConnection();
}
