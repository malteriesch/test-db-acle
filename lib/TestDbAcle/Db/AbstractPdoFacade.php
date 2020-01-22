<?php

namespace TestDbAcle\Db;

abstract class AbstractPdoFacade
{

    protected $pdo;

    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function enableExceptions(){
        $this->pdo->setAttribute(\Pdo::ATTR_ERRMODE, \Pdo::ERRMODE_EXCEPTION);
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

    public function recordExists($table, array $identityMap)
    {
        $conditions = array();
        foreach($identityMap as $key=>$value){
            $conditions[]="$key='".addslashes($value)."'";
        }
        $numberOfRecords = $this->getQuery("SELECT COUNT(*) N FROM `$table` WHERE ".implode(" AND ", \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder::identityMapToConditionArray($identityMap)));
       
        return $numberOfRecords[0]['N'] > 0;
        
    }
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    
    abstract public function setAutoIncrement($table, $nextIncrement);
    abstract public function disableForeignKeyChecks();
    abstract public function clearTable($table);
    abstract public function softClearTable($table);
    abstract public function describeTable($table);
    
}
