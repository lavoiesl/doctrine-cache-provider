<?php

namespace Lavoiesl\Doctrine\CacheProvider;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class DoctrineCache extends CacheProvider
{
    protected $table;

    protected $fields = array();

    protected $statements = array(
        'fetch'     => null,
        'contains'  => null,
        'save'      => null,
        'delete'    => null,
        'deleteAll' => null,
    );

    protected $connection;

    public function __construct(Connection $connection, $table = 'cache', array $fields = array())
    {
        $this->connection = $connection;
        $this->table      = $table;
        $this->fields     = CacheSchema::getFields($fields);

        $this->prepareStatements();
    }

    protected function prepareStatements()
    {
        $id = $this->fields['id'];
        $fields = array($this->fields['id'], $this->fields['data'], $this->fields['expiration']);
        $fields = implode(', ', $fields);

        $this->statements['fetch']     = $this->connection->prepare("SELECT * FROM {$this->table} WHERE $id = :id");
        $this->statements['contains']  = $this->connection->prepare("SELECT $id FROM {$this->table} WHERE $id = :id");
        $this->statements['save']      = $this->connection->prepare("REPLACE INTO {$this->table} ($fields) VALUES (:id, :data, :expiration)");
        $this->statements['delete']    = $this->connection->prepare("DELETE FROM {$this->table} WHERE $id = :id");
        $this->statements['deleteAll'] = $this->connection->prepare("DELETE FROM {$this->table}");
    }

    private function process(array $row)
    {
        if (null !== $row[$this->fields['expiration']] && strtotime($row[$this->fields['expiration']]) <= time()) {
            $this->doDelete($row[$this->fields['id']]);

            return false;
        } else {
            return $row[$this->fields['data']];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $stmt = $this->statements['fetch'];

        $stmt->bindValue('id', $id);

        if ($stmt->execute() && ($data = $stmt->fetch())) {
            return $this->process($data);
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $stmt = $this->statements['contains'];

        $stmt->bindValue('id', $id);
        $result = $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $expiration = null;
        if (0 !== $lifeTime) {
            $expiration = date("Y-m-d H:i:s", time() + $lifeTime);
        }

        $stmt = $this->statements['save'];

        $stmt->bindValue('id', $id);
        $stmt->bindValue('data', $data);
        $stmt->bindValue('expiration', $expiration);

        return $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        $stmt = $this->statements['delete'];

        $stmt->bindValue('id', $id);

        return $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->statements['deleteAll']->execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }
}
