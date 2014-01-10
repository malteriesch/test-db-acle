<?php


class ServiceLocatorTest extends \PHPUnit_Framework_TestCase 
{
    public function setup()
    {
        $this->serviceLocator = new \TestDbAcle\ServiceLocator();
        $this->serviceLocator->setFactories(
            array(
                'foo' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return new TestClass(); 
                },
                'moo' => 'Cow',
                'field' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return new Field($serviceLocator->get("moo")); 
                },
                
        ));
    }
    
    function test_SmokeTest()
    {
        
        $foo1  = $this->serviceLocator->get('foo');
        $this->assertTrue($foo1 instanceof TestClass);
        $foo2 = $this->serviceLocator->get('foo');
        $this->assertSame($foo1, $foo2);
        
        $cow1 = $this->serviceLocator->get('moo');
        $cow2 = $this->serviceLocator->get('moo');
        
        $this->assertTrue($cow1 instanceof Cow);
        $this->assertSame($cow1, $cow2);
        
        $expectedField = new Field($cow1);
        $field         = $this->serviceLocator->get('field');
        $this->assertEquals($expectedField, $field);
        $this->assertSame($cow1, $field->cow);
        
        $override= new StdClass();
        $this->serviceLocator->set('moo', $override);
        $this->assertSame($override, $this->serviceLocator->get('moo'), 'services can be overridden');
        $this->assertSame($cow1,     $this->serviceLocator->get('prototype.moo'), 'overides are shadowed by parent');
        $this->assertNull($this->serviceLocator->get('baz'), 'unreckognised services return null');
        
        
    }
}


class TestClass{
    
}

class Cow{
    
}

class Field{
    public $cow;
    function __construct(Cow $cow){
        $this->cow = $cow;
    }
}