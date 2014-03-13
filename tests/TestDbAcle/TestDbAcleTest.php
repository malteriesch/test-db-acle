<?php


class TestDbAcleTest extends \PHPUnit_Framework_TestCase {
    
    public function teardown() {
        \Mockery::close();
    }
    
    protected function createConfiguredTestDbAcleWithExpectations($expectedPsv, $additionalFilters = array())
    {
        $parser        = \Mockery::mock('TestDbAcle\Psv\PsvParser');
        $filterQueue   = \Mockery::mock('TestDbAcle\Filter\FilterQueue');
        $dataTableInfo = \Mockery::mock('TestDbAcle\Db\TableInfo');
        $dataInserter  = \Mockery::mock('TestDbAcle\Db\DataInserter\DataInserter');
        
        
        $afterParsing   = array('table1'=>array('parsed1'),'table2'=>array('parsed1'));
        $afterFiltering = array('table1'=>array('filtered1'),'table2'=>array('filtered2'));
        
        $describedTable1= array('table information 1');
        $describedTable2= array('table information 2');
        
        $mockPdoFacade = \Mockery::mock('TestDbAcle\Db\PdoFacade');
        $mockPdoFacade->shouldReceive('describeTable')->once()->with('table1')->andReturn($describedTable1);
        $mockPdoFacade->shouldReceive('describeTable')->once()->with('table2')->andReturn($describedTable2);

        $parser->shouldReceive('parsePsvTree')->once()->with($expectedPsv)->andReturn($afterParsing)->ordered();
        
        $filterQueue->shouldReceive('filterDataTree')->once()->with($afterParsing, $additionalFilters)->andReturn($afterFiltering)->ordered();

        $dataTableInfo->shouldReceive('addTableDescription')->with('table1', $describedTable1);
        $dataTableInfo->shouldReceive('addTableDescription')->with('table2', $describedTable2);
            

        $dataInserter->shouldReceive('process')->once()->with($afterFiltering);
        
        
        $testDbAcle = new \TestDbAcle\TestDbAcle();
        $testDbAcle->setParser($parser);
        $testDbAcle->setFilterQueue($filterQueue);
        $testDbAcle->setTableInfo($dataTableInfo);
        $testDbAcle->setDataInserter($dataInserter);
        $testDbAcle->setPdoFacade($mockPdoFacade);
        return $testDbAcle;
    }
    
    function test_runCommand(){
        $mockServiceLocator = \Mockery::mock('\TestDbAcle\ServiceLocator');
        
        $testDbAcle = new \TestDbAcle\TestDbAcle();
        $testDbAcle->setServiceLocator($mockServiceLocator);
        
        $mockCommand = \Mockery::mock('TestDbAcle\Commands\CommandInterface');
        $mockCommand->shouldReceive('initialise')->once()->with($mockServiceLocator)->ordered();
        $mockCommand->shouldReceive('execute')->once()->withNoArgs()->ordered();
        
        $testDbAcle->runCommand($mockCommand);
        
        
    }
    
    function test_create(){
        
        $expectedTestDbAcle = new \TestDbAcle\TestDbAcle();
        $mockPdo = MockablePdo::createMock($this);
                      
        $serviceLocator = new \TestDbAcle\ServiceLocator(\TestDbAcle\TestDbAcle::getDefaultFactories());
        $serviceLocator->set('pdo', $mockPdo);
        
        $expectedTestDbAcle->setServiceLocator($serviceLocator);
        $testDbAcle = \TestDbAcle\TestDbAcle::create($mockPdo);
                
        $this->assertEquals($expectedTestDbAcle, $testDbAcle);
        
    }
    
    function test_create_with_defaultOverriden(){
        
        $mockPdo = MockablePdo::createMock($this);
                      
        $testDbAcle = \TestDbAcle\TestDbAcle::create($mockPdo, array(
                    'dataInserter' => function($serviceLocator) {
                        return 'foo';
                    }
        ));
        
        
        $this->assertEquals("foo", $testDbAcle->getServiceLocator()->get('dataInserter'));
        
    }
    
    
    
    
}
//@TODO use \TestDbAcle\PhpUnit\Mocks\MockablePdo, at the moment somehow it only works if defined below.... investigate.
class MockablePdo extends \PDO {

    public function __construct() {
        
    }

    public static function createMock(\PHPUnit_Framework_TestCase $testCase, $methods = array()) {
        $className = 'MockablePdo';
        $mock = $testCase->getMock($className, $methods, array(), "_Mock_" . uniqid($className));
        return $mock;
    }
}