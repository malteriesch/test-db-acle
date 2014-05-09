<?php

namespace TestDbAcle\Db\Sqlite\Pdo;

class PdoFacade extends \TestDbAcle\Db\AbstractPdoFacade
{

    public function describeTable($table)
    {
        return $this->getQuery("PRAGMA table_info({$table})");
    }
    
    public function clearTable($table)
    {
        $this->executeSql("DELETE FROM {$table};DELETE FROM sqlite_sequence WHERE name='$table'");
    }
    
    public function setAutoIncrement($table, $nextIncrement){
        $this->pdo->query("UPDATE sqlite_sequence SET seq=$nextIncrement-1 WHERE name='$table';");
    }

    public function disableForeignKeyChecks()
    {
        
    }

}
