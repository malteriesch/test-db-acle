<?php

namespace TestDbAcle;

class TestDbAcle
{
    protected $parser;
    protected $filterQueue;
    protected $dataInserter;
    protected $tableInfo;
    protected $pdoFacade;
  
    function setParser(\TestDbAcle\Psv\PsvParser $parser)
    {
        $this->parser=$parser;
    }
    
    function getParser()
    {
        return $this->parser;
    }
    
    function setPdoFacade(\TestDbAcle\Db\PdoFacade $pdoFacade)
    {
        $this->pdoFacade=$pdoFacade;
    }
    
    function getPdoFacade()
    {
        return $this->pdoFacade;
    }
    
    function setTableInfo(\TestDbAcle\Db\TableInfo $tableInfo)
    {
        $this->tableInfo=$tableInfo;
    }
    
    function getTableInfo()
    {
        return $this->tableInfo;
    }
    
    function setFilterQueue(\TestDbAcle\Filter\FilterQueue $filterQueue)
    {
        $this->filterQueue=$filterQueue;
    }
    
    function getFilterQueue()
    {
        return  $this->filterQueue;
    }
    
    function setDataInserter(\TestDbAcle\Db\DataInserter $dataInserter)
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
     * @return \TestDbAcle\TestDbAcle;
     */
    public static function create(\Pdo $pdo)
    {
        $pdo->setAttribute(\Pdo::ATTR_ERRMODE, \Pdo::ERRMODE_EXCEPTION);
        $pdo->query("SET FOREIGN_KEY_CHECKS = 0");
        
        $testDbAcle = new TestDbAcle();
        
        $pdoFacade    = new \TestDbAcle\Db\PdoFacade($pdo);
        $tableInfo    = new \TestDbAcle\Db\TableInfo();
        $dataInserter = new \TestDbAcle\Db\DataInserter($pdoFacade, $tableInfo);
        
        $filterQueue = new \TestDbAcle\Filter\FilterQueue();
        $filterQueue->addRowFilter(new \TestDbAcle\Filter\AddDefaultValuesRowFilter($tableInfo));
        
        $testDbAcle->setParser(new \TestDbAcle\Psv\PsvParser());
        $testDbAcle->setPdoFacade($pdoFacade);
        $testDbAcle->setTableInfo($tableInfo);
        $testDbAcle->setFilterQueue($filterQueue);
        $testDbAcle->setDataInserter($dataInserter);
        
        return $testDbAcle;
    }
    
}