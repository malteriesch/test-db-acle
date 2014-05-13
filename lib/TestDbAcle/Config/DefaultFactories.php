<?php
namespace TestDbAcle\Config;

class DefaultFactories  implements \TestDbAcle\Config\FactoriesInterface
{
    public function getFactories($pdoDriverName){
       
        if($pdoDriverName=="sqlite"){
            return array_merge($this->getFactoriesAllDrivers(), $this->getFactoriesSqlite());
        }else{
            return array_merge($this->getFactoriesAllDrivers(), $this->getFactoriesMysql());
        }
        
    }
    
    public function getFactoriesAllDrivers(){
       
        return array(
           
           'dataInserterFilterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = $serviceLocator->get('filterQueue');
                $filterQueue->addRowFilter(new \TestDbAcle\Filter\AddDefaultValuesRowFilter($serviceLocator->get('tableList')));
                return $filterQueue;
           },
           'filterQueue'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $filterQueue = new \TestDbAcle\Filter\FilterQueue();
                return $filterQueue;
           },
           'upsertBuilderFactory'    => function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($serviceLocator->get('pdoFacade'));
           },
           
           'tableList' => '\TestDbAcle\Db\TableList',
           'parser'    => '\TestDbAcle\Psv\PsvParser',
                   
        );
    }
        
    public function getFactoriesMysql(){
       
        return array(
            'dataInserter'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $dataInserter = new \TestDbAcle\Db\DataInserter\DataInserter($serviceLocator->get('pdoFacade'), $serviceLocator->get('upsertBuilderFactory'));
                $dataInserter->addUpsertListener(new \TestDbAcle\Db\DataInserter\Listeners\MysqlZeroPKListener($serviceLocator->get('pdoFacade'), $serviceLocator->get('tableList')));
                return $dataInserter;
           },
           'pdoFacade'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $pdoFacade    = new \TestDbAcle\Db\Mysql\Pdo\PdoFacade($serviceLocator->get('pdo'));
                $pdoFacade->disableForeignKeyChecks();
                $pdoFacade->enableExceptions();
                return $pdoFacade;
           },
           'tableFactory'    => function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\Mysql\TableFactory($serviceLocator->get('pdoFacade'));
           },
                   
        );
}
    
    public static function getFactoriesSqlite(){
       
        return array(
           'dataInserter'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\DataInserter\DataInserter($serviceLocator->get('pdoFacade'), $serviceLocator->get('upsertBuilderFactory'));
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

