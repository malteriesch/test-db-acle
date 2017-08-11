<?php

namespace TestDbAcleTests\TestDbAcle\Filter;

class AddDefaultValuesRowFilterTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    protected $mockTableList;
    protected $filter;
    
    function setUp()
    {
        $describeStuff = array(
            array('Field' => 'stuff_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'col1', 'Type' => 'int', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col2', 'Type' => 'tinyint', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col3', 'Type' => 'date', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col4', 'Type' => 'datetime', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col5', 'Type' => 'text', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col6', 'Type' => 'tinytest', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col7', 'Type' => 'blob', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col8', 'Type' => 'tinyblob', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col9', 'Type' => 'varchar(255)', 'Null' => 'NO', 'Key' => '', 'Default' => 'foo', 'Extra' => ''),
        );

        $this->table = new \TestDbAcle\Db\Table\Table("stuff");
        foreach($describeStuff as $describedColumn) {
            $this->table->addColumn(new \TestDbAcle\Db\Mysql\Table\Column($describedColumn));
        }
        $this->mockTableList =  \Mockery::mock('\TestDbAcle\Db\TableList');
        $this->mockTableList->shouldReceive('getTable')->andReturn($this->table);
        $this->filter = new \TestDbAcle\Filter\AddDefaultValuesRowFilter($this->mockTableList);
    }
    
    public function teardown() {
        \Mockery::close();
    }
    
    function test_filter_addsNonNullableDefaultValues()
    {
        $this->assertEquals( array('col4'=>'2012-01-01','col1'=>'1'), $this->filter->filter("my_table", array('col4'=>'2012-01-01')));
        $this->assertEquals( array('col4'=>'2012-01-01','col1'=>'1','col9'=>''), $this->filter->filter("my_table", array('col9'=>'','col4'=>'2012-01-01')));
        $this->assertEquals( array('col9'=>'moo','col4'=>'2012-01-01','col1'=>'1'), $this->filter->filter("my_table", array('col9'=>'moo','col4'=>'2012-01-01')));
    }
}

?>
