<?php
class AddDefaultValuesRowFilterTest extends \PHPUnit_Framework_TestCase 
{
    protected $mockTableInfo;
    protected $filter;
    
    function setUp()
    {
        $this->mockTableInfo =  \Mockery::mock('\TestDbAcle\Db\TableInfo');
        $this->filter = new \TestDbAcle\Filter\AddDefaultValuesRowFilter($this->mockTableInfo);
    }
    
    public function teardown() {
        \Mockery::close();
    }
    
    function test_filter_addsNonNullableDefaultValues()
    {
        $inputRow = array(
            "id" => "1",
            "col2" => "moo",
            "col3" => "NULL"
        );
        
        $expected = array(
            "id" => "1",
            "col2" => "moo",
            "col3" => "NULL",
            'important_col' => 'DEFAULT_VALUE'
        );

        
        $mockTable = \Mockery::mock('TestDbAcle\Db\Table\Table');
        $this->mockTableInfo->shouldReceive("getTable")->once()->with('my_table')->andReturn($mockTable);
        $mockTable->shouldReceive("getDecorateWithNullPlaceHolders")->once()->with($inputRow)->andReturn($expected);
        
        
        $this->assertEquals( $expected, $this->filter->filter("my_table", $inputRow));
        
        
    }
}

?>
