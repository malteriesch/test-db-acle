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
    
    function test_setupTables()
    {
        
        $psv="
            [user]
            user_id |first_name |last_name  |company_id
            10      |Joe        |Bloggs     |1
            20      |Tommy      |Jones      |1
            
            [company]
            company_id  |name
            1           |foo
            
        ";
        
        $testDbAcle = $this->createConfiguredTestDbAcleWithExpectations($psv);
        $testDbAcle->setUpTables($psv);
        
    }
    
    function test_setupTables_WithAdditionalFilters()
    {
        
        $psv="
            [user]
            user_id |first_name |last_name  |company_id
            10      |Joe        |Bloggs     |1
            20      |Tommy      |Jones      |1
            
            [company]
            company_id  |name
            1           |foo
            
        ";
        $additionalFilters = array('filter1','filter2');
        
        $testDbAcle = $this->createConfiguredTestDbAcleWithExpectations($psv, $additionalFilters);
        $testDbAcle->setUpTables($psv, $additionalFilters);
        
    }
    
    function test_create_withDefaults()
    {
        $mockPdo = MockablePdo::createMock($this, array('prepare', 'exec', 'query','setAttribute'));
        $mockPdo->expects($this->once())->method('setAttribute')->with(\Pdo::ATTR_ERRMODE, \Pdo::ERRMODE_EXCEPTION);
        
        $mockPdo->expects($this->once())->method('query')->with("SET FOREIGN_KEY_CHECKS = 0");
        
        $expectedMockPdoFacade = new \TestDbAcle\Db\PdoFacade($mockPdo);
        
        $expectedTableInfo = new \TestDbAcle\Db\TableInfo();
        
        
        $expectedParser       = new \TestDbAcle\Psv\PsvParser();
        $expectedFilterQueue  = new \TestDbAcle\Filter\FilterQueue();
        $expectedFilterQueue->addRowFilter(new \TestDbAcle\Filter\AddDefaultValuesRowFilter($expectedTableInfo));
                
        $expectedDataInserter = new \TestDbAcle\Db\DataInserter\DataInserter($expectedMockPdoFacade);
        $expectedDataInserter->addUpsertListener(new \TestDbAcle\Db\DataInserter\Listeners\MysqlZeroPKListener($expectedMockPdoFacade, $expectedTableInfo));
        
        $testDbacle = \TestDbAcle\TestDbAcle::create($mockPdo);
        
        $this->assertEquals($expectedParser, $testDbacle->getParser());
        $this->assertEquals($expectedMockPdoFacade, $testDbacle->getPdoFacade());
        $this->assertEquals($expectedTableInfo, $testDbacle->getTableInfo());
        $this->assertEquals($expectedFilterQueue, $testDbacle->getFilterQueue());
        $this->assertEquals($expectedDataInserter, $testDbacle->getDataInserter());
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