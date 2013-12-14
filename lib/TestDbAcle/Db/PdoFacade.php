<?php

namespace TestDbAcle\Db;

class PdoFacade
{

    protected $pdo;

    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function clearTable($table)
    {
        $this->executeSql("TRUNCATE TABLE {$table}");
    }

    public function describeTable($table)
    {
        return $this->getQuery("DESCRIBE {$table}");
    }

    public function executeSql($sql)
    {
        $this->pdo->exec($sql);
    }

    public function getQuery($sql)
    {
        $result = $this->pdo->query($sql);
        if ($result) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }
        return null;
    }

}
