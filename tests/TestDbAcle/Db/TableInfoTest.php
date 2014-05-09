<?php
namespace TestDbAcleTests\TestDbAcle\Db;

class TableInfoTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    protected $tableInfo;
    
    function setUp()
    {
        
        $this->tableInfo = new \TestDbAcle\Db\TableInfo();
        
    }
    
    function test_AddingAndGettingTables()
    {
        
        $testTable1 = \Mockery::mock('TestDbAcle\Db\Table\Table',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("table1");
        });
        
        $testTable2 = \Mockery::mock('TestDbAcle\Db\Table\Table',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("table2");
        });
        
        $this->tableInfo->addTable($testTable1);
        $this->tableInfo->addTable($testTable2);
        
        $this->assertSame($testTable1, $this->tableInfo->getTable('table1'));
        $this->assertSame($testTable2, $this->tableInfo->getTable('table2'));
        
        $this->assertNull($this->tableInfo->getTable('foo'));
    }
    
}
