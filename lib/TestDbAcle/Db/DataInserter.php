<?php

namespace TestDbAcle\Db;

class DataInserter
{

    protected $tableInfo;

    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo = null)
    {
        $this->pdo = $pdo;
        $this->tableInfo = new TableInfo();
    }

    public function process($dataTree)
    {

        foreach (array_keys($dataTree) as $tableName) {
            $this->tableInfo->addTableDescription($tableName, $this->getQuery('describe ' . $tableName));
        }

        foreach ($dataTree as $tableName => $content) {
            $this->processTable($tableName, $content['data'], $content['meta']);
        }
    }

    public function processTable($tableName, $content, $meta)
    {
        if (isset($meta['mode']) && $meta['mode'] == 'replace') {
            $this->replaceIntoTable($tableName, $content, $meta['identifiedBy']);
        } else {
            $this->clearAndInsertTable($tableName, $content);
        }
    }

    protected function getUpserter($tableName,$identifiedBy,$valuesToBeInserted)
    {
        $updateBuilder = new Sql\UpdateBuilder($tableName);
        foreach ($identifiedBy as $idColumn) {
            $updateBuilder->addCondition("$idColumn='{$valuesToBeInserted[$idColumn]}'");
        }

        $conditionString = $updateBuilder->getConditionSql();
        $numberOfRecords = $this->getQuery("SELECT COUNT(*) N FROM $tableName WHERE $conditionString");
        if ($numberOfRecords[0]['N'] == 0) {
            return new Sql\InsertBuilder($tableName);
        }
        
        return $updateBuilder;
        
    }

    protected function insertValues($upsertBuilder,$tableName,$valuesToBeInserted)
    {
        foreach ($this->tableInfo->getDecorateWithNullPlaceHolders($tableName, $valuesToBeInserted) as $columnName => $columnValue) {
            if ($columnValue == 'NULL' && $this->tableInfo->isNullable($tableName, $columnName)) {
                $upsertBuilder->addColumn($columnName, 'NULL', true);
            } else {
                $upsertBuilder->addColumn($columnName, $columnValue);
            }
        }
        $this->executeSql($upsertBuilder->GetSql());
    }
    
    public function replaceIntoTable($tableName, $content, $identifiedBy)
    {
        foreach ($content as $valuesToBeInserted) {
            $this->insertValues($this->getUpserter($tableName,$identifiedBy,$valuesToBeInserted),$tableName,$valuesToBeInserted);
        }
    }

    public function clearAndInsertTable($tableName, $content)
    {
        $this->clearTable($tableName);

        if (count($content) == 0) {
            return;
        }

        foreach ($content as $valuesToBeInserted) {
            $this->insertValues(new Sql\InsertBuilder($tableName),$tableName,$valuesToBeInserted);
        }
    }

    protected function clearTable($table)
    {
        $this->executeSql("TRUNCATE TABLE {$table}");
    }

    protected function executeSql($sql)
    {
        $this->pdo->exec($sql);
    }

    protected function getQuery($sql)
    {
        $result = $this->pdo->query($sql);
        if ($result) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }
        return null;
    }

}

?>