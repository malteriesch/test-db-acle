<?php

namespace TestDbAcle\Db\Mysql\Pdo;

class PdoFacade extends \TestDbAcle\Db\AbstractPdoFacade
{ 
    
    public function disableForeignKeyChecks(){
        $this->pdo->query("SET FOREIGN_KEY_CHECKS = 0");
    }

    public function setAutoIncrement($table, $nextIncrement){
        $this->pdo->query("ALTER TABLE `$table` AUTO_INCREMENT = $nextIncrement");
    }

    public function clearTable($table)
    {
        $this->executeSql("TRUNCATE TABLE `{$table}`");
    }

    public function softClearTable($table)
    {
        $this->executeSql("DELETE FROM `{$table}`");
    }

    public function describeTable($table)
    {
        return $this->getQuery("DESCRIBE `{$table}`");
    }
}
