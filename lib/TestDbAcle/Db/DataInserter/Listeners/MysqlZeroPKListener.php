<?php

namespace TestDbAcle\Db\DataInserter\Listeners;

class MysqlZeroPKListener implements \TestDbAcle\Db\DataInserter\UpsertListenerInterface
{
    protected $pdoFacadeFacade;
    protected $tableList;
    
    public function __construct(\TestDbAcle\Db\AbstractPdoFacade $pdoFacade, \TestDbAcle\Db\TableList $tableList)
    {
        $this->pdoFacadeFacade = $pdoFacade;
        $this->tableList = $tableList;
    }

    public function  afterUpsert(\TestDbAcle\Db\DataInserter\Sql\UpsertBuilder $upsertBuilder)
    {
        if (!$upsertBuilder instanceof \TestDbAcle\Db\DataInserter\Sql\InsertBuilder){
            return;
        }
        
        $tableName = $upsertBuilder->getTableName();
        
        $table      = $this->tableList->getTable($tableName);
        
        if ($table == null){
            return;
        }
        
        $primaryKey = $table->getPrimaryKey();
        
        if($primaryKey==null){
            return;
        }
        $primaryKeyValue = $upsertBuilder->getColumn($primaryKey);
        
        if (!is_numeric($primaryKeyValue) || $primaryKeyValue != 0){
            return;
        }
        $this->pdoFacadeFacade->executeSql("update {$tableName} set $primaryKey = 0 where $primaryKey = " . $this->pdoFacadeFacade->lastInsertId());
    }

}