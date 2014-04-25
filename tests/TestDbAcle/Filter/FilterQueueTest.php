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
        $dataTree = new \TestDbAcle\Psv\Table\TableList();
        
        $dataTree->addTable(
                    new \TestDbAcle\Psv\Table\Table('user', array(
                        array('user.row1'),
                        array('user.row2')
                )));
        
        $dataTree->addTable(
                    new \TestDbAcle\Psv\Table\Table('stuff', array(
                        array('stuff.row1'),
                        array('stuff.row2')
                )));
        
        $dataTree->addTable(
                    new \TestDbAcle\Psv\Table\Table('emptyTable'));
        
        
        $mockRowFilter1 = \Mockery::mock('\TestDbAcle\Filter\RowFilter');
        
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row1"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row1'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('user', array("user.row2"))->ordered()
                       ->andReturn(array('user.filtered_filter1_row2'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('stuff', array("stuff.row1"))->ordered()
                       ->andReturn(array('stuff.filtered_filter1_row1'));
        $mockRowFilter1->shouldReceive('filter')->once()->with('stuff', array("stuff.row2"))->ordered()
                       ->andReturn(array('stuff.filtered_filter1_row2'));

        
       
       $this->filterQueue->addRowFilter($mockRowFilter1);
        
       $filteredTableList = $this->filterQueue->filterDataTree($dataTree);
       
       $this->assertEquals( array(
                    array('user.filtered_filter1_row1'),
                    array('user.filtered_filter1_row2')
                ), $filteredTableList->getTable('user')->toArray());
       
       $this->assertEquals( array(
                    array('stuff.filtered_filter1_row1'),
                    array('stuff.filtered_filter1_row2')), $filteredTableList->getTable('stuff')->toArray());
       $this->assertEquals( array(), $filteredTableList->getTable('emptyTable')->toArray());
        
    }
    
    
    function test_filterDataTree_withTwoRowFilters()
    {
        
        $dataTree = new \TestDbAcle\Psv\Table\TableList();
        
        $dataTree->addTable(
                    new \TestDbAcle\Psv\Table\Table('user', array(
                    array('user.row1'),
                    array('user.row2')
                )));
        
        $dataTree->addTable(
                    new \TestDbAcle\Psv\Table\Table('stuff', array(
                    array('stuff.row1'),
                    array('stuff.row2'))));
        
        $dataTree->addTable(
                    new \TestDbAcle\Psv\Table\Table('emptyTable'));
        
       
        
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

       
       $this->filterQueue->addRowFilter($mockRowFilter1);
       $this->filterQueue->addRowFilter($mockRowFilter2);
       
       $filteredTableList = $this->filterQueue->filterDataTree($dataTree);
       
       $this->assertEquals(  array(
                    array('user.filtered_filter2_row1'),
                    array('user.filtered_filter2_row2')
                ), $filteredTableList->getTable('user')->toArray());
       
       $this->assertEquals( array(
                    array('stuff.filtered_filter2_row1'),
                    array('stuff.filtered_filter2_row2')), $filteredTableList->getTable('stuff')->toArray());
       $this->assertEquals( array(), $filteredTableList->getTable('emptyTable')->toArray());
       
        
    }
    
  
}

?>
