<?php

namespace TestDbAcle\Db\DataInserter;

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
    
    /* @var $listeners UpsertListenerInterface */
    protected $listeners = array();

    public function __construct(\TestDbAcle\Db\AbstractPdoFacade $pdoFacade, \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory $upsertBuilderFactory)
    {
        $this->pdoFacade            = $pdoFacade;
        $this->upsertBuilderFactory = $upsertBuilderFactory;
    }

    public function process(\TestDbAcle\Psv\PsvTree $tableList)
    {
        foreach ($tableList->getTables() as $table) {
            $this->processTable($table);
        }
    }

    public function processTable(\TestDbAcle\Psv\Table\Table $table)
    {
        if(!$table->getMeta()->isReplaceMode()){
            $this->pdoFacade->clearTable($table->getName());
        }
        foreach ($table->toArray() as $valuesToBeInserted) {
            $this->insertValues($this->upsertBuilderFactory->getUpserter($table, $valuesToBeInserted), $valuesToBeInserted);
        }
    }

    protected function insertValues($upsertBuilder, $valuesToBeInserted)
    {
        foreach ($valuesToBeInserted as $columnName => $columnValue) {
            $upsertBuilder->addColumn($columnName, $columnValue);
        }
        $this->pdoFacade->executeSql($upsertBuilder->GetSql());
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