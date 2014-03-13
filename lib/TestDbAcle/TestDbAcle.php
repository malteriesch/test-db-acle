<?php

namespace TestDbAcle;

class TestDbAcle
{
    
    protected $parser;
    protected $filterQueue;
    protected $dataInserter;
    protected $tableInfo;
    protected $pdoFacade;
    protected $serviceLocator;
    
    public function runCommand(\TestDbAcle\Commands\CommandInterface $command)
    {
        $command->initialise($this->serviceLocator);
        $command->execute();
    }
  
    public function setServiceLocator(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->serviceLocator=$serviceLocator;
    }
    
    /**
     * @return \TestDbAcle\ServiceLocator
     */
    public function getServiceLocator()
    {
        return  $this->serviceLocator;
    }
       
    /**
     * @return TestDbAcle;
     */
    public static function create(\Pdo $pdo, $factoryOverrides = array())
    {
        
        $testDbAcle = new TestDbAcle();
        $serviceLocator = new ServiceLocator(array_merge(static::getDefaultFactories(),$factoryOverrides));
        $serviceLocator->set('pdo', $pdo);
        
        $testDbAcle->setServiceLocator($serviceLocator);
        
        return $testDbAcle;
    }
    
    public static function getDefaultFactories(){
        return array(
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
           'dataInserterFilterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = $serviceLocator->get('filterQueue');
                $filterQueue->addRowFilter(new Filter\AddDefaultValuesRowFilter($serviceLocator->get('tableInfo')));
                return $filterQueue;
           },
           'filterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = new Filter\FilterQueue();
                return $filterQueue;
           },
           'tableInfo' => '\TestDbAcle\Db\TableInfo',
           'parser'    => '\TestDbAcle\Psv\PsvParser',
                   
        );
    }
        
}