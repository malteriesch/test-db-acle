Customizing Test-Db-Acle
========================

All aspects of TestDbAcle can be customised, it uses a very simple ServiceLocator to inject the various dependencies.
The TestDbAcle class is used as a simple facade that provides access to common functionality and all the components involved.
It is configured in AbstractTestCase::createDatabaseTestHelper and can be customised there.

It is possible to override AbstractTestCase::createDatabaseTestHelper and to provide to the TestDbAcle::create factory method as a second arument a service locator configuration to override the defaults.
As an optional third parameter a Configuration class can be specified additionally (\FactoriesInterfaceTestDbAcle\Config\FactoriesInterface) where all the default core factories are taken from.
See \TestDbAcle\Config\DefaultFactories for an example.


The custom configuration (2nd argument) is merged with the default configuration so it is relatively easy to selectively exchange any part without affecting others.

The values of the configuration array can either be a string, in which case the class just gets instantiated, or a simple factory that gets the service locator
as the only argument. 

An example configuration is as follows:

```php
    array(
           'pdoFacade'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $pdoFacade    = new Db\Mysql\Pdo\PdoFacade($serviceLocator->get('pdo'));
                $pdoFacade->disableForeignKeyChecks();
                $pdoFacade->enableExceptions();
                return $pdoFacade;
           },
           'dataInserter'=> function(\TestDbAcle\ServiceLocator $serviceLocator) {
                $dataInserter = new Db\DataInserter\DataInserter($serviceLocator->get('pdoFacade'), $serviceLocator->get('upsertBuilderFactory'));
                $dataInserter->addUpsertListener(new Db\DataInserter\Listeners\MysqlZeroPKListener($serviceLocator->get('pdoFacade'), $serviceLocator->get('tableList')));
                return $dataInserter;
           },
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
           'tableFactory'    => function(\TestDbAcle\ServiceLocator $serviceLocator) {
                return new \TestDbAcle\Db\Mysql\TableFactory($serviceLocator->get('pdoFacade'));
           },
           'tableList' => '\TestDbAcle\Db\TableList',
           'parser'    => '\TestDbAcle\Psv\PsvParser',
                   
        )
```


So if you do not want exceptions to be enabled for example, you can override AbstractTestCase::createDatabaseTestHelper as such:
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