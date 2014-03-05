<?php

namespace TestDbAcle\Db\DataInserter\Listeners;

class MysqlZeroPKListener implements \TestDbAcle\Db\DataInserter\UpsertListenerInterface
{
    protected $pdoFacadeFacade;
    protected $tableInfo;
    
    public function __construct(\TestDbAcle\Db\PdoFacade $pdoFacade, \TestDbAcle\Db\TableInfo $tableInfo)
    {
        $this->pdoFacadeFacade = $pdoFacade;
        $this->tableInfo = $tableInfo;
    }

    public function  afterUpsert(\TestDbAcle\Db\DataInserter\Sql\UpsertBuilder $upsertBuilder)
    {
        if (!$upsertBuilder instanceof \TestDbAcle\Db\DataInserter\Sql\InsertBuilder){
            return;
        }
        
        $table      = $upsertBuilder->getTableName();
        $primaryKey = $this->tableInfo->getPrimaryKey($table);
        
        if ($primaryKey == null){
            return;
        }
        $primaryKeyValue = $upsertBuilder->getColumn($primaryKey);
        
        if (!is_numeric($primaryKeyValue) || $primaryKeyValue != 0){
            return;
        }
           
        $this->pdoFacadeFacade->executeSql("update {$table} set $primaryKey = 0 where $primaryKey = " . $this->pdoFacadeFacade->lastInsertId());
    }

}