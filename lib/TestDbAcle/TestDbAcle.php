<?php

namespace TestDbAcle;

class TestDbAcle
{
    
    protected $parser;
    protected $filterQueue;
    protected $dataInserter;
    protected $tableList;
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
        $serviceLocator = new ServiceLocator(array_merge(static::getDefaultFactories($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)),$factoryOverrides));
        $serviceLocator->set('pdo', $pdo);
        
        $testDbAcle->setServiceLocator($serviceLocator);
        
        return $testDbAcle;
    }
    
    public static function getDefaultFactories($pdoDriverName){
       
        if($pdoDriverName=="sqlite"){
            return array_merge(static::getDefaultFactoriesAllDrivers(), static::getDefaultFactoriesSqlite());
        }else{
            return array_merge(static::getDefaultFactoriesAllDrivers(), static::getDefaultFactoriesMysql());
        }
        
    }
    
    public static function getDefaultFactoriesAllDrivers(){
       
        return array(
           
           'dataInserterFilterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = $serviceLocator->get('filterQueue');
                $filterQueue->addRowFilter(new Filter\AddDefaultValuesRowFilter($serviceLocator->get('tableList')));
                return $filterQueue;
           },
           'filterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = new Filter\FilterQueue();
                return $filterQueue;
           },
           'upsertBuilderFactory'    => function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($serviceLocator->get('pdoFacade'));
           },
           
           'tableList' => '\TestDbAcle\Db\TableList',
           'parser'    => '\TestDbAcle\Psv\PsvParser',
                   
        );
    }
        
    public static function getDefaultFactoriesMysql(){
       
        return array(
            'dataInserter'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $dataInserter = new Db\DataInserter\DataInserter($serviceLocator->get('pdoFacade'), $serviceLocator->get('upsertBuilderFactory'));
                $dataInserter->addUpsertListener(new Db\DataInserter\Listeners\MysqlZeroPKListener($serviceLocator->get('pdoFacade'), $serviceLocator->get('tableList')));
                return $dataInserter;
           },
           'pdoFacade'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $pdoFacade    = new Db\Mysql\Pdo\PdoFacade($serviceLocator->get('pdo'));
                $pdoFacade->disableForeignKeyChecks();
                $pdoFacade->enableExceptions();
                return $pdoFacade;
           },
           'tableFactory'    => function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\Mysql\TableFactory($serviceLocator->get('pdoFacade'));
           },
                   
        );
}
    
    public static function getDefaultFactoriesSqlite(){
       
        return array(
           'dataInserter'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new Db\DataInserter\DataInserter($serviceLocator->get('pdoFacade'), $serviceLocator->get('upsertBuilderFactory'));
           },
                   
           'pdoFacade'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $pdoFacade    = new \TestDbAcle\Db\Sqlite\Pdo\PdoFacade($serviceLocator->get('pdo'));
                $pdoFacade->disableForeignKeyChecks();
                $pdoFacade->enableExceptions();
                return $pdoFacade;
           },
           'tableFactory'    => function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\Sqlite\TableFactory($serviceLocator->get('pdoFacade'));
           },
                   
        );
    }
    
}
