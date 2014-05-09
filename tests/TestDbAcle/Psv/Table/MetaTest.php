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
}