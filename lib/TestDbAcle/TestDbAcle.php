<?php

namespace TestDbAcle;

class TestDbAcle
{
    
    protected $parser;
    protected $filterQueue;
    protected $dataInserter;
    protected $tableInfo;
    protected $pdoFacade;
    
    public function setupTables($psvContent, $additionalFilters = array())
    {
        $parsedTree = $this->parser->parsePsvTree($psvContent);
        foreach(array_keys($parsedTree) as $tableName){
            $this->tableInfo->addTableDescription($tableName, $this->pdoFacade->describeTable($tableName));
        }
        return $this->dataInserter->process($this->filterQueue->filterDataTree($parsedTree,$additionalFilters));
    }
    
    public function setupTablesWithPlaceholders($psvContent, $placeHolders = array())
    {
        return $this->setupTables($psvContent, array( new Filter\PlaceholderRowFilter($placeHolders)));
    }
    
    
  
    public function setParser(Psv\PsvParser $parser)
    {
        $this->parser=$parser;
    }
    
    public function getParser()
    {
        return $this->parser;
    }
    
    public function setPdoFacade(Db\PdoFacade $pdoFacade)
    {
        $this->pdoFacade=$pdoFacade;
    }
    
    public function getPdoFacade()
    {
        return $this->pdoFacade;
    }
    
    public function setTableInfo(Db\TableInfo $tableInfo)
    {
        $this->tableInfo=$tableInfo;
    }
    
    public function getTableInfo()
    {
        return $this->tableInfo;
    }
    
    public function setFilterQueue(Filter\FilterQueue $filterQueue)
    {
        $this->filterQueue=$filterQueue;
    }
    
    public function getFilterQueue()
    {
        return  $this->filterQueue;
    }
    
    public function setDataInserter(Db\DataInserter\DataInserter $dataInserter)
    {
        $this->dataInserter=$dataInserter;
    }
    
    public function getDataInserter()
    {
        return $this->dataInserter;
    }
    
       
    /**
     * @return TestDbAcle;
     */
    public static function create(\Pdo $pdo, $factoryOverrides = array())
    {
        $factories = array_merge(static::getDefaultFactories(),$factoryOverrides);
        $serviceLocator = new ServiceLocator($serviceLocator);
        $serviceLocator->set('pdo', $pdo);
        $serviceLocator->setFactories($factories);
        return $serviceLocator->get('testDbAcle');
    }
    
    public static function getDefaultFactories(){
        return array(
           'testDbAcle'=> function(\TestDbAcle\ServiceLocator $serviceLocator){
                $testDbAcle = new TestDbAcle();
                $testDbAcle->setParser($serviceLocator->get('parser'));
                $testDbAcle->setPdoFacade($serviceLocator->get('pdoFacade'));
                $testDbAcle->setTableInfo($serviceLocator->get('tableInfo'));
                $testDbAcle->setDataInserter($serviceLocator->get('dataInserter'));
                $testDbAcle->setFilterQueue($serviceLocator->get('filterQueue'));
                return $testDbAcle;
           },
           'pdoFacade'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $pdoFacade    = new Db\PdoFacade($serviceLocator->get('pdo'));
                $pdoFacade->disableForeignKeyChecks();
                $pdoFacade->enableExceptions();
                return $pdoFacade;
           },
           'dataInserter'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $dataInserter = new Db\DataInserter\DataInserter($serviceLocator->get('pdoFacade'));
                $dataInserter->addUpsertListener(new Db\DataInserter\Listeners\MysqlZeroPKListener($serviceLocator->get('pdoFacade'), $serviceLocator->get('tableInfo')));
                return $dataInserter;
           },
           'filterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = new Filter\FilterQueue();
                $filterQueue->addRowFilter(new Filter\AddDefaultValuesRowFilter($serviceLocator->get('tableInfo')));
                return $filterQueue;
           },
           'tableInfo' => '\TestDbAcle\Db\TableInfo',
           'parser'    => '\TestDbAcle\Psv\PsvParser',
                   
        );
    }
        
}