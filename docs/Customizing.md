Customizing Test-Db-Acle
========================

All aspects of TestDbAcle can be customised, it uses a very simple ServiceLocator to inject the various dependencies.
The TestDbAcle class is used as a simple facade that provides access to common functionality and all the components involved.
It is configured in AbstractTestCase::createDatabaseTestHelper and can be customised there.

It is possible to override AbstractTestCase::createDatabaseTestHelper and provide to the TestDbAcle::create factory methods as a second arument a service locator configuration.
This service locator is a very simplified version of one inspired by the Zend Framework 2.

The custom configuration is merged with the one provided as the second argument so it is easy to selectively override any part without affecting others.

The values of the configuration array can either be a string, in which case the class just gets instantiated, or a simple factory that gets the service locator
as only argument. 

The default configuration is as follows, this can be used as an example for the service locator usage:

```php
    array(
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
```




So if you do not want exeptions to be enabled for example, you can override AbstractTestCase::createDatabaseTestHelper as such:
```php
    protected function createDatabaseTestHelper()
    {
        return \TestDbAcle\TestDbAcle::create($this->getPdo(), array(
            'pdoFacade'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $pdoFacade    = new Db\PdoFacade($serviceLocator->get('pdo'));
                $pdoFacade->disableForeignKeyChecks();
                return $pdoFacade;
           },
        ));
    }
```

Similarly you can additional filters and such.