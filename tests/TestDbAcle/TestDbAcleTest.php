<?php
require_once(__DIR__.'/Mocks/MockablePdo.php');

class TestDbAcleTest extends \PHPUnit_Framework_TestCase {
    
    public function teardown() {
        \Mockery::close();
    }
    
    protected function createMock($className, $methods = array(), $constructorParameters = array()) {
        $mock = $this->getMock($className, $methods, $constructorParameters, "_Mock_" . uniqid($className));
        return $mock;
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
        
        $parser        = \Mockery::mock('TestDbAcle\Psv\PsvParser');
        $filterQueue   = \Mockery::mock('TestDbAcle\Filter\FilterQueue');
        $dataTableInfo = \Mockery::mock('TestDbAcle\Db\TableInfo');
        $dataInserter  = \Mockery::mock('TestDbAcle\Db\DataInserter');
        
        
        $afterParsing   = array('table1'=>array('parsed1'),'table2'=>array('parsed1'));
        $afterFiltering = array('table1'=>array('filtered1'),'table2'=>array('filtered2'));
        
        $describedTable1= array('table information 1');
        $describedTable2= array('table information 2');
        
        $mockPdoFacade = \Mockery::mock('TestDbAcle\Db\PdoFacade');
        $mockPdoFacade->shouldReceive('describeTable')->once()->with('table1')->andReturn($describedTable1);
        $mockPdoFacade->shouldReceive('describeTable')->once()->with('table2')->andReturn($describedTable2);

        $parser->shouldReceive('parsePsvTree')->once()->with($psv)->andReturn($afterParsing)->ordered();
        $filterQueue->shouldReceive('filterDataTree')->once()->with($afterParsing)->andReturn($afterFiltering)->ordered();

        $dataTableInfo->shouldReceive('addTableDescription')->with('table1', $describedTable1);
        $dataTableInfo->shouldReceive('addTableDescription')->with('table2', $describedTable2);
            

        $dataInserter->shouldReceive('process')->once()->with($afterFiltering);
        
        
        
        $testDbAcle = new \TestDbAcle\TestDbAcle();
        $testDbAcle->setParser($parser);
        $testDbAcle->setFilterQueue($filterQueue);
        $testDbAcle->setTableInfo($dataTableInfo);
        $testDbAcle->setDataInserter($dataInserter);
        $testDbAcle->setPdoFacade($mockPdoFacade);
        
        $testDbAcle->setUpTables($psv);
        
    }
    
    function test_create_withDefaults()
    {
        $mockPdo = $this->createMock('MockablePDO');
        $expectedMockPdoFacade = new \TestDbAcle\Db\PdoFacade($mockPdo);
        
        $expectedTableInfo = new \TestDbAcle\Db\TableInfo();
        
        
        $expectedParser       = new \TestDbAcle\Psv\PsvParser();
        $expectedFilterQueue  = new \TestDbAcle\Filter\FilterQueue();
        $expectedFilterQueue->addRowFilter(new \TestDbAcle\Filter\AddDefaultValuesRowFilter($expectedTableInfo));
                
        $expectedDataInserter = new \TestDbAcle\Db\DataInserter($expectedMockPdoFacade, $expectedTableInfo);
        
        $testDbacle = \TestDbAcle\TestDbAcle::create($mockPdo);
        
        $this->assertEquals($expectedParser, $testDbacle->getParser());
        $this->assertEquals($expectedMockPdoFacade, $testDbacle->getPdoFacade());
        $this->assertEquals($expectedTableInfo, $testDbacle->getTableInfo());
        $this->assertEquals($expectedFilterQueue, $testDbacle->getFilterQueue());
        $this->assertEquals($expectedDataInserter, $testDbacle->getDataInserter());
    }
}