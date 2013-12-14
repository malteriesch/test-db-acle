<?php

namespace TestDbAcle\Db;

class DataInserter
{

    protected $tableInfo;

    /**
     * @var \TestDbAcle\Db\PdoFacade
     */
    private $pdoFacade;

    public function __construct(\TestDbAcle\Db\PdoFacade $pdoFacade = null, \TestDbAcle\Db\TableInfo $tableInfo)
    {
        $this->pdoFacade = $pdoFacade;
        $this->tableInfo = $tableInfo;
    }

    public function process($dataTree)
    {
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

    protected function getUpserter($tableName, $identifiedBy, $valuesToBeInserted)
    {
        $updateBuilder = new Sql\UpdateBuilder($tableName);
        foreach ((array) $identifiedBy as $idColumn) {
            $updateBuilder->addCondition("$idColumn='{$valuesToBeInserted[$idColumn]}'");
        }

        $conditionString = $updateBuilder->getConditionSql();
        $numberOfRecords = $this->pdoFacade->getQuery("SELECT COUNT(*) N FROM $tableName WHERE $conditionString");
        if ($numberOfRecords[0]['N'] == 0) {
            return new Sql\InsertBuilder($tableName);
        }

        return $updateBuilder;
    }

    protected function insertValues($upsertBuilder, $tableName, $valuesToBeInserted)
    {
        foreach ($valuesToBeInserted as $columnName => $columnValue) {
            if ($columnValue == 'NULL') {
                $upsertBuilder->addColumn($columnName, 'NULL', true);
            } else {
                $upsertBuilder->addColumn($columnName, $columnValue);
            }
        }
        $this->pdoFacade->executeSql($upsertBuilder->GetSql());
    }

    public function replaceIntoTable($tableName, $content, $identifiedBy)
    {
        foreach ($content as $valuesToBeInserted) {
            $this->insertValues($this->getUpserter($tableName, $identifiedBy, $valuesToBeInserted), $tableName, $valuesToBeInserted);
        }
    }

    public function clearAndInsertTable($tableName, $content)
    {
        $this->pdoFacade->clearTable($tableName);

        if (count($content) == 0) {
            return;
        }

        foreach ($content as $valuesToBeInserted) {
            $this->insertValues(new Sql\InsertBuilder($tableName), $tableName, $valuesToBeInserted);
        }
    }

}

?>