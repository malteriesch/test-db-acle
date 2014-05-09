<?php
namespace TestDbAcleTests\TestDbAcle\Psv\Table;

class MetaTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    
    public function test_Default()
    {
        $meta = new \TestDbAcle\Psv\Table\Meta();
        $this->assertFalse($meta->isReplaceMode());
        $this->assertEquals(array(), $meta->getIdentifyColumns());
    }
    
    public function test_Replace()
    {
        $meta = new \TestDbAcle\Psv\Table\Meta(array( 'mode'=> 'replace', 'identifiedBy' => array('first_name','last_name')));
        $this->assertTrue($meta->isReplaceMode());
        $this->assertEquals(array('first_name','last_name'), $meta->getIdentifyColumns());
    }
    
    public function test_TruncateDates_none()
    {
        $meta = new \TestDbAcle\Psv\Table\Meta(array());
        $this->assertEquals(array(), $meta->getTruncateDateColumns());
    }
    
    public function test_TruncateDates_some()
    {
        $meta = new \TestDbAcle\Psv\Table\Meta(array( 'truncateDates' => array('col_date1','col_date2')));
        $this->assertEquals(array('col_date1','col_date2'), $meta->getTruncateDateColumns());
    }
    
    public function test_TruncateDates_one()
    {
        $meta = new \TestDbAcle\Psv\Table\Meta(array( 'truncateDates' => 'col_date'));
        $this->assertEquals(array('col_date'), $meta->getTruncateDateColumns());
    }
}