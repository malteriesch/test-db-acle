<?php
namespace TestDbAcleTests\TestDbAcle\Db;

class AbstractTableFactoryTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    
    function test_createTableFromTableDescription()
    {
        $abstractTableFactory = \Mockery::mock('\TestDbAcle\Db\AbstractTableFactory[createTable,createColumn]', [\Mockery::mock('TestDbAcle\Db\AbstractPdoFacade')]);
        
        $col1 = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column');
        $col2 = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column');
        
        $mockTable = \Mockery::mock('TestDbAcle\Db\Mysql\Table\TestDbAcle\Db\Table\Table');
        
        $abstractTableFactory->shouldReceive("createTable")->once()->with('foo')->andReturn($mockTable)->ordered();
        
        $abstractTableFactory->shouldReceive("createColumn")->once()->with(['description col1'])->andReturn($col1)->ordered();
        $mockTable->shouldReceive("addColumn")->once()->with($col1)->ordered();

        $abstractTableFactory->shouldReceive("createColumn")->once()->with(['description col2'])->andReturn($col2)->ordered();
        $mockTable->shouldReceive("addColumn")->once()->with($col2)->ordered();
        
       
        $actualTable = $abstractTableFactory->createTableFromTableDescription("foo", [
            ['description col1'],
            ['description col2'],
        ]);
        $this->assertSame($mockTable, $actualTable);
    }
    
    function test_populateTableList()
    {
        $mockPdoFacade = \Mockery::mock('TestDbAcle\Db\AbstractPdoFacade');
        $mockPdoFacade->shouldReceive('describeTable')->once()->with("table1")->andReturn(["table description 1"]);
        $mockPdoFacade->shouldReceive('describeTable')->once()->with("table2")->andReturn(["table description 2"]);
        
        $abstractTableFactory = \Mockery::mock('\TestDbAcle\Db\AbstractTableFactory[createTableFromTableDescription]', [$mockPdoFacade]);
        
        $mockTableList = \Mockery::mock('TestDbAcle\Db\TableList');
        
        $mockTable1 = \Mockery::mock('TestDbAcle\Db\Table\Table');
        $mockTable2 = \Mockery::mock('TestDbAcle\Db\Table\Table');
        
        $abstractTableFactory->shouldReceive('createTableFromTableDescription')->once()->with("table1", ["table description 1"])->andReturn($mockTable1);
        $mockTableList->shouldReceive('addTable')->once()->with($mockTable1);
        
        $abstractTableFactory->shouldReceive('createTableFromTableDescription')->once()->with("table2", ["table description 2"])->andReturn($mockTable2);
        $mockTableList->shouldReceive('addTable')->once()->with($mockTable2);
      
        $abstractTableFactory->populateTableList(["table1", "table2"], $mockTableList);
    }
    
    function test_createTableList()
    {
        $mockPdoFacade = \Mockery::mock('TestDbAcle\Db\AbstractPdoFacade');
        $mockPdoFacade->shouldReceive('describeTable')->once()->with("table")->andReturn(["table description"]);
        
        $abstractTableFactory = \Mockery::mock('\TestDbAcle\Db\AbstractTableFactory[createTableFromTableDescription]', [$mockPdoFacade]);
        
        
        $mockTable = \Mockery::mock('TestDbAcle\Db\Table\Table');
        $mockTable->shouldReceive("getName")->andReturn("table");
        
        $abstractTableFactory->shouldReceive('createTableFromTableDescription')->once()->with("table", ["table description"])->andReturn($mockTable);
        
        $tableList = $abstractTableFactory->createTableList(["table"]);
        
        $expectedTableList = new \TestDbAcle\Db\TableList();
        $expectedTableList->addTable($mockTable);
        
      //  $this->assertEquals($expectedTableList, $tableList->getTable("table"));
    }
   
}