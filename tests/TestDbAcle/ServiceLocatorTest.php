<?php

namespace TestDbAcleTests\TestDbAcle;

class ServiceLocatorTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    public function setup()
    {
        $this->serviceLocator = new \TestDbAcle\ServiceLocator();
        $this->serviceLocator->setFactories(
            array(
                'foo' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return new TestClass(); 
                },
                'moo' => 'TestDbAcleTests\TestDbAcle\Cow',
                'field' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return new Field($serviceLocator->get("moo")); 
                },
                
        ));
    }
    
    function xtest_SmokeTest()
    {
        
        $foo1  = $this->serviceLocator->get('foo');
        $this->assertTrue($foo1 instanceof TestClass);
        $foo2 = $this->serviceLocator->get('foo');
        $this->assertSame($foo1, $foo2);
        
        $foo3 = $this->serviceLocator->createNew('foo');
        
        $this->assertEquals($foo1, $foo3);
        $this->assertNotSame($foo1, $foo3);
        
        $cow1 = $this->serviceLocator->get('moo');
        $cow2 = $this->serviceLocator->get('moo');
        
        $this->assertTrue($cow1 instanceof Cow);
        $this->assertSame($cow1, $cow2);
        
        $expectedField = new Field($cow1);
        $field         = $this->serviceLocator->get('field');
        $this->assertEquals($expectedField, $field);
        $this->assertSame($cow1, $field->cow);
        
        $override= new \StdClass();
        $this->serviceLocator->setService('moo', $override);
        $this->assertSame($override, $this->serviceLocator->get('moo'), 'services can be overridden');
        
        
        $this->assertNull($this->serviceLocator->get('baz'), 'unreckognised services return null');
    }
    
    function test_addFactories_areMerged_andExistingSavedAsPrototypes()
    {
        $this->serviceLocator->addFactories(
            array(
                'foo' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return 'foo overridden';
                },
        ));
        $this->assertEquals('foo overridden', $this->serviceLocator->get('foo'));
        $this->assertEquals(new TestClass(), $this->serviceLocator->get('prototype.foo'));
        $this->assertEquals(new \TestDbAcleTests\TestDbAcle\Cow(), $this->serviceLocator->get('moo'));
        
    }
    
    function test_addFactories_areMergedTwice_andExistingSavedAsPrototypes()
    {
        $this->serviceLocator->addFactories(
            array(
                'foo' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return 'foo overridden';
                },
        ));
        $this->serviceLocator->addFactories(
            array(
                'foo' => function(\TestDbAcle\ServiceLocator $serviceLocator){
                    return 'foo overridden again';
                },
        ));
        
        $this->assertEquals('foo overridden again', $this->serviceLocator->get('foo'));
        $this->assertEquals('foo overridden', $this->serviceLocator->get('prototype.foo'));
        $this->assertEquals(new TestClass(), $this->serviceLocator->get('prototype.prototype.foo'));
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
