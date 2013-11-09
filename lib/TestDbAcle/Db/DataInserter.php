<?php

namespace TestDbAcle\Db;

class DataInserter {

    protected $tableInfo;

    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo = null) {
        $this->pdo = $pdo;
        $this->tableInfo = new TableInfo();
    }

    public function process($dataTree) {
        
        foreach (array_keys($dataTree) as $tableName) {
            $this->tableInfo->addTableDescription($tableName,$this->getQuery('describe ' . $tableName));
        }
        
        foreach ($dataTree as $tableName => $content) { 
            $this->processTable($tableName, $content['data']);
        }
    }


    public function processTable($tableName, $content) {
        
        $this->clearTable($tableName);
        
        if (count($content)==0) {
            return;
        }

        foreach ($content as $valuesToBeInserted) {
            
            $insertBuilder      = new Sql\InsertBuilder($tableName);

            foreach ($this->tableInfo->getDecorateWithNullPlaceHolders($tableName, $valuesToBeInserted) as $columnName => $columnValue) {
                if ($columnValue == 'NULL' && $this->tableInfo->isNullable($tableName,$columnName)) {
                    $insertBuilder->addColumn($columnName, 'NULL', true);
                } else {
                    $insertBuilder->addColumn($columnName, $columnValue);
                }
            }
            $this->executeSql($insertBuilder->GetSql());
        }
        
        
    }

    protected function clearTable($table) {
        $this->executeSql("TRUNCATE TABLE {$table}");
    }

    protected function executeSql($sql)
    {
        $this->pdo->exec($sql);
    }
    
    protected function getQuery($sql)
    {
        $result = $this->pdo->query($sql);
        if ($result){
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }
        return null;
    }
}


?>