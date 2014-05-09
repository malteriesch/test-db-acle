<?php

namespace TestDbAcleTests\TestDbAcle\Filter;

class PlaceholderRowFilterTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    protected $mockTableInfo;
    protected $filter;
    
    function setUp()
    {
        $this->mockTableInfo =  \Mockery::mock('\TestDbAcle\Db\TableInfo');
        $this->filter = new \TestDbAcle\Filter\PlaceholderRowFilter(array("foo"=>"Moo"));
    }
    
    public function teardown() {
        \Mockery::close();
    }
    
    function test_filter_replacesWithPlaceholderValues()
    {
        $inputRow = array(
            "id" => "1",
            "col2" => "foo",
            "col3" => "NULL"
        );
        
        $expected = array(
            "id" => "1",
            "col2" => "Moo",
            "col3" => "NULL",
        );

        $this->assertEquals( $expected, $this->filter->filter("my_table", $inputRow));
        
        
    }
}

?>
