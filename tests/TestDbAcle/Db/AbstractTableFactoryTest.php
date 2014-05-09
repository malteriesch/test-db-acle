<?php
namespace TestDbAcleTests\TestDbAcle\Db;

class AbstractTableFactoryTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    
    function test_createTableFromTableDescription()
    {
        $abstractTableFactory = \Mockery::mock('\TestDbAcle\Db\AbstractTableFactory[createTable,createColumn]', array(\Mockery::mock('TestDbAcle\Db\AbstractPdoFacade')));
        
        $col1 = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column');
        $col2 = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column');
        
        $mockTable = \Mockery::mock('TestDbAcle\Db\Mysql\Table\TestDbAcle\Db\Table\Table');
        
        $abstractTableFactory->shouldReceive("createTable")->once()->with('foo')->andReturn($mockTable)->ordered();
        
        $abstractTableFactory->shouldReceive("createColumn")->once()->with(array('description col1'))->andReturn($col1)->ordered();
        $mockTable->shouldReceive("addColumn")->once()->with($col1)->ordered();

        $abstractTableFactory->shouldReceive("createColumn")->once()->with(array('description col2'))->andReturn($col2)->ordered();
        $mockTable->shouldReceive("addColumn")->once()->with($col2)->ordered();
        
       
        $actualTable = $abstractTableFactory->createTableFromTableDescription("foo", array(
            array('description col1'),
            array('description col2'),
        ));
        $this->assertSame($mockTable, $actualTable);
    }
    
    function test_populateTableList()
    {
        $mockPdoFacade = \Mockery::mock('TestDbAcle\Db\AbstractPdoFacade');
        $mockPdoFacade->shouldReceive('describeTable')->once()->with("table1")->andReturn(array("table description 1"));
        $mockPdoFacade->shouldReceive('describeTable')->once()->with("table2")->andReturn(array("table description 2"));
        
        $abstractTableFactory = \Mockery::mock('\TestDbAcle\Db\AbstractTableFactory[createTableFromTableDescription]', array($mockPdoFacade));
        
        $mockTableList = \Mockery::mock('TestDbAcle\Db\TableList');
        
        $mockTable1 = \Mockery::mock('TestDbAcle\Db\Table\Table');
        $mockTable2 = \Mockery::mock('TestDbAcle\Db\Table\Table');
        
        $abstractTableFactory->shouldReceive('createTableFromTableDescription')->once()->with("table1", array("table description 1"))->andReturn($mockTable1);
        $mockTableList->shouldReceive('addTable')->once()->with($mockTable1);
        
        $abstractTableFactory->shouldReceive('createTableFromTableDescription')->once()->with("table2", array("table description 2"))->andReturn($mockTable2);
        $mockTableList->shouldReceive('addTable')->once()->with($mockTable2);
      
        $abstractTableFactory->populateTableList(array("table1", "table2"), $mockTableList);
    }
    
    function test_createTableList()
    {
        $mockPdoFacade = \Mockery::mock('TestDbAcle\Db\AbstractPdoFacade');
        $mockPdoFacade->shouldReceive('describeTable')->once()->with("table")->andReturn(array("table description"));
        
        $abstractTableFactory = \Mockery::mock('\TestDbAcle\Db\AbstractTableFactory[createTableFromTableDescription]', array($mockPdoFacade));
        
        
        $mockTable = \Mockery::mock('TestDbAcle\Db\Table\Table');
        $mockTable->shouldReceive("getName")->andReturn("table");
        
        $abstractTableFactory->shouldReceive('createTableFromTableDescription')->once()->with("table", array("table description"))->andReturn($mockTable);
        
        $tableList = $abstractTableFactory->createTableList(array("table"));
        
        $expectedTableList = new \TestDbAcle\Db\TableList();
        $expectedTableList->addTable($mockTable);
        
        $this->assertEquals($mockTable, $tableList->getTable("table"));
    }
   
}