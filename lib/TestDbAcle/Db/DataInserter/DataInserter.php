<?php

namespace TestDbAcle\Db\DataInserter;

use TestDbAcle\Db\DataInserter\Sql\UpsertBuilder;

class DataInserter
{

    /**
     * @var \TestDbAcle\Db\Mysql\Pdo\PdoFacade
     */
    protected $pdoFacade;
    
    /**
     *
     * @var \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory
     */
    protected $upsertBuilderFactory;


    /**
     * @var $tableList \TestDbAcle\Db\TableList
     */
    protected $tableList;

    /* @var $listeners UpsertListenerInterface */
    protected $listeners = array();

    public function __construct(\TestDbAcle\Db\AbstractPdoFacade $pdoFacade, \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory $upsertBuilderFactory, \TestDbAcle\Db\TableList $tableList)
    {
        $this->pdoFacade            = $pdoFacade;
        $this->upsertBuilderFactory = $upsertBuilderFactory;
        $this->tableList        = $tableList;
    }

    public function process(\TestDbAcle\Psv\PsvTree $tableList)
    {
        foreach ($tableList->getTables() as $table) {
            $this->processTable($table);
        }
    }

    public function processTable(\TestDbAcle\Psv\Table\Table $table)
    {
        if(!$table->getMeta()->isReplaceMode() && !$table->getMeta()->isAppendMode()){
            $this->pdoFacade->clearTable($table->getName());
        }
        foreach ($table->toArray() as $valuesToBeInserted) {
            $this->insertValues($this->upsertBuilderFactory->getUpserter($table, $this->tableList->getTable($table->getName()), $valuesToBeInserted), $valuesToBeInserted);
        }
    }

    protected function insertValues(UpsertBuilder $upsertBuilder, $valuesToBeInserted)
    {
        foreach ($valuesToBeInserted as $columnName => $columnValue) {
            $upsertBuilder->addColumn($columnName, $columnValue);
        }
        $this->pdoFacade->executeSql($upsertBuilder->getSql());
        $this->onAfterUpsert($upsertBuilder);
        
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