<?php

namespace TestDbAcle\Db;

class PdoFacade
{

    protected $pdo;

    function __construct($pdo)
    {
        
        $this->pdo = $pdo;
    }
    
    public function enableExceptions(){
        $this->pdo->setAttribute(\Pdo::ATTR_ERRMODE, \Pdo::ERRMODE_EXCEPTION);
    }
    
    public function disableForeignKeyChecks(){
        $this->pdo->query("SET FOREIGN_KEY_CHECKS = 0");
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

    public function recordExists($table, array $identityMap)
    {
        $conditions = array();
        foreach($identityMap as $key=>$value){
            $conditions[]="$key='".addslashes($value)."'";
        }
        $numberOfRecords = $this->getQuery("SELECT COUNT(*) N FROM $table WHERE ".implode(" AND ", \TestDbAcle\Db\Sql\UpdateBuilder::identityMapToConditionArray($identityMap)));
       
        return $numberOfRecords[0]['N'] > 0;
        
    }
}
