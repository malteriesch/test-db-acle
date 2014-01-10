<?php

namespace TestDbAcle;

class TestDbAcle
{
    protected $parser;
    protected $filterQueue;
    protected $dataInserter;
    protected $tableInfo;
    protected $pdoFacade;
  
    function setParser(Psv\PsvParser $parser)
    {
        $this->parser=$parser;
    }
    
    function getParser()
    {
        return $this->parser;
    }
    
    function setPdoFacade(Db\PdoFacade $pdoFacade)
    {
        $this->pdoFacade=$pdoFacade;
    }
    
    function getPdoFacade()
    {
        return $this->pdoFacade;
    }
    
    function setTableInfo(Db\TableInfo $tableInfo)
    {
        $this->tableInfo=$tableInfo;
    }
    
    function getTableInfo()
    {
        return $this->tableInfo;
    }
    
    function setFilterQueue(Filter\FilterQueue $filterQueue)
    {
        $this->filterQueue=$filterQueue;
    }
    
    function getFilterQueue()
    {
        return  $this->filterQueue;
    }
    
    function setDataInserter(Db\DataInserter\DataInserter $dataInserter)
    {
        $this->dataInserter=$dataInserter;
    }
    
    function getDataInserter()
    {
        return $this->dataInserter;
    }
    
    function setupTables($psvContent, $additionalFilters = array())
    {
        $parsedTree = $this->parser->parsePsvTree($psvContent);
        foreach(array_keys($parsedTree) as $tableName){
            $this->tableInfo->addTableDescription($tableName, $this->pdoFacade->describeTable($tableName));
        }
        return $this->dataInserter->process($this->filterQueue->filterDataTree($parsedTree,$additionalFilters));
    }
    
       
    /**
     * @return TestDbAcle;
     */
    public static function create(\Pdo $pdo)
    {
        $testDbAcle = new TestDbAcle();
        
        $pdoFacade  = static::createConfiguredPdoFacade($pdo);
        
        $tableInfo  = new Db\TableInfo();
        
        $dataInserter = new Db\DataInserter\DataInserter($pdoFacade);
        $dataInserter->addUpsertListener(new Db\DataInserter\Listeners\MysqlZeroPKListener($pdoFacade, $tableInfo));
        
        $testDbAcle->setParser(new Psv\PsvParser());
        $testDbAcle->setPdoFacade($pdoFacade);
        $testDbAcle->setTableInfo($tableInfo);
        $testDbAcle->setDataInserter($dataInserter);

        static::setDefaultFilters($testDbAcle);
        
        return $testDbAcle;
    }
    /**
     * this method needs to be run after all other initialisations have been made
     * @param Db\PdoFacade $testDbAcle
     */
    protected static function setDefaultFilters(TestDbAcle $testDbAcle )
    {
        $filterQueue = new Filter\FilterQueue();
        $filterQueue->addRowFilter(new Filter\AddDefaultValuesRowFilter($testDbAcle->getTableInfo()));
        
        $testDbAcle->setFilterQueue($filterQueue);
    }
    
    protected static function createConfiguredPdoFacade(\Pdo $pdo)
    {
        $pdoFacade    = new Db\PdoFacade($pdo);
        $pdoFacade->disableForeignKeyChecks();
        $pdoFacade->enableExceptions();
        return $pdoFacade;
    }
    
}