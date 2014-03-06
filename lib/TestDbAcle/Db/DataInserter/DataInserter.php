<?php

namespace TestDbAcle\Db\DataInserter;

class DataInserter
{

    /**
     * @var \TestDbAcle\Db\PdoFacade
     */
    protected $pdoFacade;
    /* @var $listeners UpsertListenerInterface */
    protected $listeners = array();

    public function __construct(\TestDbAcle\Db\PdoFacade $pdoFacade)
    {
        $this->pdoFacade = $pdoFacade;
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
        $identifyMap = array();
        
        foreach ((array) $identifiedBy as $idColumn) {
            $identifyMap[$idColumn]=$valuesToBeInserted[$idColumn];
        }

        if($this->pdoFacade->recordExists($tableName, $identifyMap)){
            return new Sql\UpdateBuilder($tableName,$identifyMap);
        }else{
            return new Sql\InsertBuilder($tableName);
        }
        
    }

    protected function insertValues($upsertBuilder, $tableName, $valuesToBeInserted)
    {
        foreach ($valuesToBeInserted as $columnName => $columnValue) {
            if ($columnValue == 'NULL' || is_null($columnValue)) {
                $upsertBuilder->addColumn($columnName, 'NULL', true);
            } else {
                $upsertBuilder->addColumn($columnName, $columnValue);
            }
        }
        $this->pdoFacade->executeSql($upsertBuilder->GetSql());
        $this->onAfterUpsert($upsertBuilder);
        
    }

    public function replaceIntoTable($tableName, $content, $identifiedBy)
    {
        foreach ($content as $valuesToBeInserted) {
            $this->insertValues($this->getUpserter($tableName, $identifiedBy, $valuesToBeInserted), $tableName, $valuesToBeInserted);
        }
    }

    public function clearAndInsertTable($tableName, $content)
    {//@TODO should be done via a table-wide filter
        $this->pdoFacade->clearTable($tableName);

        if (count($content) == 0) {
            return;
        }

        foreach ($content as $valuesToBeInserted) {
            $this->insertValues(new Sql\InsertBuilder($tableName), $tableName, $valuesToBeInserted);
        }
    }

    protected function onAfterUpsert($upsertBuilder)
    {
        foreach($this->listeners as $listener)
        {
            $listener->afterUpsert($upsertBuilder);
        }
    }
    
    public function addUpsertListener(UpsertListenerInterface $listener)
    {
        $this->listeners[] = $listener;
    }
    
}

?>