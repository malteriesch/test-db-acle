<?php
class FilterQueueTest extends \PHPUnit_Framework_TestCase 
{
    protected $mockTableInfo;
    protected $filterQueue;
    
    function setUp()
    {
        $this->filterQueue = new \TestDbAcle\Filter\FilterQueue();
    }
    
    public function teardown() {
        \Mockery::close();
    }
    
    function test_filterDataTree_withOneRowFilter()
    {
        
        $dataTree = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array('user.row1'),
                    array('user.row2')
                ),
            ),
            "stuff" => array(
                'meta' => array(),
                'data' => array(
                    array('stuff.row1'),
                    array('stuff.row2')),
            ),
            'emptyTable' => array('meta' => array(),'data'=>array())
        );
        
        $mockRowFilter1 = \Mockery::mock('\TestDbAcle\Filter\RowFilter');
        
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row1"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row1'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row2"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row2'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('stuff', array("stuff.row1"))->ordered()
                       ->andReturn(array('stuff.filtered_filter1_row1'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('stuff', array("stuff.row2"))->ordered()
                       ->andReturn(array('stuff.filtered_filter1_row2'));

        

        $expected = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array('user.filtered_filter1_row1'),
                    array('user.filtered_filter1_row2')
                ),
            ),
            "stuff" => array(
                'meta' => array(),
                'data' => array(
                    array('stuff.filtered_filter1_row1'),
                    array('stuff.filtered_filter1_row2')),
            ),
            'emptyTable' => array('meta' => array(),'data'=>array())
        );
       
       $this->filterQueue->addRowFilter($mockRowFilter1);
        
       $this->assertEquals( $expected, $this->filterQueue->filterDataTree($dataTree));
        
    }
    
    
    function test_filterDataTree_withTwoRowFilters()
    {
        
        $dataTree = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array('user.row1'),
                    array('user.row2')
                ),
            ),
            "stuff" => array(
                'meta' => array(),
                'data' => array(
                    array('stuff.row1'),
                    array('stuff.row2')),
            ),
            'emptyTable' => array('meta' => array(),'data'=>array())
        );
        
        $mockRowFilter1 = \Mockery::mock('\TestDbAcle\Filter\RowFilter');
        
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row1"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row1'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row2"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row2'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('stuff', array("stuff.row1"))->ordered()
                       ->andReturn(array('stuff.filtered_filter1_row1'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('stuff', array("stuff.row2"))->ordered()
                       ->andReturn(array('stuff.filtered_filter1_row2'));

        
        $mockRowFilter2 = \Mockery::mock('\TestDbAcle\Filter\RowFilter');

        $mockRowFilter2->shouldReceive('filter')->once()->with('user', array("user.filtered_filter1_row1"))->ordered()
                        ->andReturn(array('user.filtered_filter2_row1'));
        $mockRowFilter2->shouldReceive('filter')->once()->with('user', array("user.filtered_filter1_row2"))->ordered()
                       ->andReturn(array('user.filtered_filter2_row2'));
        $mockRowFilter2->shouldReceive('filter')->once()->with('stuff', array("stuff.filtered_filter1_row1"))->ordered()
                       ->andReturn(array('stuff.filtered_filter2_row1'));
        $mockRowFilter2->shouldReceive('filter')->once()->with('stuff', array("stuff.filtered_filter1_row2"))->ordered()
                       ->andReturn(array('stuff.filtered_filter2_row2'));

        $expected = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array('user.filtered_filter2_row1'),
                    array('user.filtered_filter2_row2')
                ),
            ),
            "stuff" => array(
                'meta' => array(),
                'data' => array(
                    array('stuff.filtered_filter2_row1'),
                    array('stuff.filtered_filter2_row2')),
            ),
            'emptyTable' => array('meta' => array(),'data'=>array())
        );
       
       $this->filterQueue->addRowFilter($mockRowFilter1);
       $this->filterQueue->addRowFilter($mockRowFilter2);
        
       $this->assertEquals( $expected, $this->filterQueue->filterDataTree($dataTree));
        
    }
    
    function test_filterDataTree_withAdditionalRowFilters()
    {
        $dataTree = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array('user.row1'),
                ),
            ),
        );
        
        $mockRowFilter1 = \Mockery::mock('\TestDbAcle\Filter\RowFilter');
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row1"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row1'));
        
        $mockRowFilter2 = \Mockery::mock('\TestDbAcle\Filter\RowFilter');
        $mockRowFilter2->shouldReceive('filter')->once()->with('user', array("user.filtered_filter1_row1"))->ordered()
                        ->andReturn(array('user.filtered_filter2_row1'));

        $expected = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array('user.filtered_filter2_row1'),
                ),
            ),
        );
       
       $this->filterQueue->addRowFilter($mockRowFilter1);
        
       $this->assertEquals( $expected, $this->filterQueue->filterDataTree($dataTree, array($mockRowFilter2)));
        
    }
}

?>
