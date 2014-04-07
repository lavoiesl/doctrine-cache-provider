<?php

namespace Lavoiesl\Doctrine\CacheProvider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class CacheSchema
{
    protected static $defaultFields = array(
        'id'         => 'name',
        'data'       => 'data',
        'expiration' => 'expiration',
    );

    public static function getFields(array $fields = array())
    {
        return array_merge(static::$defaultFields, $fields);
    }

    public static function getSchema($table = 'cache', array $fields = array())
    {
        $fields = static::getFields($fields);
        $schema = new Schema;

        $table = $schema->createTable($table);
        $table->addColumn($fields['id'], 'string', array('length' => 127));
        $table->addColumn($fields['data'], 'text', array('length' => pow(2, 24) - 1));
        $table->addColumn($fields['expiration'], 'datetime', array('notnull' => false));
        $table->setPrimaryKey(array($fields['id']));

        return $schema;
    }

    public static function createTable(Connection $connection, $table = 'cache', array $fields = array())
    {
        $sm = $connection->getSchemaManager();
        $fromSchema = $sm->createSchema();
        $toSchema = static::getSchema($table, $fields);

        $sqls = $fromSchema->getMigrateToSql($toSchema, $connection->getDatabasePlatform());
        foreach ($sqls as $sql) {
            $connection->exec($sql);
        }

        return count($sqls);
    }

    public static function dropTable(Connection $connection, $table = 'cache', array $fields = array())
    {
        $schema = static::getSchema();

        $sqls = $schema->toDropSql($connection->getDatabasePlatform());
        foreach ($sqls as $sql) {
            $connection->exec($sql);
        }

        return count($sqls);
    }
}
