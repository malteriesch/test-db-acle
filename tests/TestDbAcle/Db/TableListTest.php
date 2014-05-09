<?php
namespace TestDbAcleTests\TestDbAcle\Db;

class TableListTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    protected $tableList;
    
    function setUp()
    {
        
        $this->tableList = new \TestDbAcle\Db\TableList();
        
    }
    
    function test_AddingAndGettingTables()
    {
        
        $testTable1 = \Mockery::mock('TestDbAcle\Db\Table\Table',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("table1");
        });
        
        $testTable2 = \Mockery::mock('TestDbAcle\Db\Table\Table',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("table2");
        });
        
        $this->tableList->addTable($testTable1);
        $this->tableList->addTable($testTable2);
        
        $this->assertSame($testTable1, $this->tableList->getTable('table1'));
        $this->assertSame($testTable2, $this->tableList->getTable('table2'));
        
        $this->assertNull($this->tableList->getTable('foo'));
    }
    
}
