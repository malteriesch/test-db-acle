<?php
namespace TestDbAcleTests\TestDbAcle\Db\Table;

class TableTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase 
{
    protected $mockColumns = [];
    
    function setup()
    {
        $this->mockColumns['PrimaryKey'] = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("col_pk");
            $mock->shouldReceive("isNullable")->withNoArgs()->andReturn(false);
            $mock->shouldReceive("isPrimaryKey")->withNoArgs()->andReturn(true);
        });
        
        $this->mockColumns['NonNull1'] = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("col_nn1");
            $mock->shouldReceive("isNullable")->withNoArgs()->andReturn(false);
            $mock->shouldReceive("isPrimaryKey")->withNoArgs()->andReturn(false);
        });
        
        $this->mockColumns['NonNull2'] = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("col_nn2");
            $mock->shouldReceive("isNullable")->withNoArgs()->andReturn(false);
            $mock->shouldReceive("isPrimaryKey")->withNoArgs()->andReturn(false);
        });
        
        $this->mockColumns['Null1'] = \Mockery::mock('TestDbAcle\Db\Mysql\Table\Column',function($mock) {
            $mock->shouldReceive("getName")->withNoArgs()->andReturn("col_null");
            $mock->shouldReceive("isNullable")->withNoArgs()->andReturn(true);
            $mock->shouldReceive("isPrimaryKey")->withNoArgs()->andReturn(false);
        });
        
        $this->table = new \TestDbAcle\Db\Table\Table("foo");
        
        $this->table->addColumn($this->mockColumns['PrimaryKey']);
        $this->table->addColumn($this->mockColumns['NonNull1']);
        $this->table->addColumn($this->mockColumns['NonNull2']);
        $this->table->addColumn($this->mockColumns['Null1']);
    }
    
    function test_getName()
    {
        $this->assertEquals('foo', $this->table->getName());
    }
    
    function test_getColumn()
    {
        $this->assertSame($this->mockColumns['NonNull2'], $this->table->getColumn('col_nn2'));
    }
    
    function test_getPrimaryKey()
    {
        $this->assertEquals("col_pk", $this->table->getPrimaryKey());
    }
    
    function test_getNonNullableColumns()
    {
        $this->assertEquals(array("col_pk", "col_nn1" ,"col_nn2"), $this->table->getNonNullableColumns());
    }
    
}