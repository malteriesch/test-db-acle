<?php
namespace TestDbAcleTests\TestDbAcle\Db\DataInserter\Factory;

class UpsertBuilderFactoryTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    function test_getUpserter_returnsInsertBuilderByDefault()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");
        $table = new \TestDbAcle\Psv\Table\Table("foo");
        $this->assertEquals($expectedBuilder, $factory->getUpserter($table));
    }
    
    function test_getUpserter_returnsUpdateBuilderIfReplaceModeAndRecordExists()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder("foo", array('first_name'=>'john', 'last_name' => 'Smith'));

        $table = new \TestDbAcle\Psv\Table\Table("foo", 
                    array(),
                    new \TestDbAcle\Psv\Table\Meta(array(
                        'mode'=> 'replace',
                        'identifiedBy' => array('first_name', 'last_name')
        )));
        
        $mockPdoFacade->shouldReceive('recordExists')->once()->with('foo', array('first_name' => 'john', 'last_name' => 'Smith'))->andReturn(true);
        
        $this->assertEquals($expectedBuilder, $factory->getUpserter($table, array('first_name'=>'john', 'last_name' => 'Smith', 'foo' => 'cheese')));
    }
    
    function test_getUpserter_returnsInsertBuilderIfReplaceModeAndRecordDoesNotExist()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");

        $table = new \TestDbAcle\Psv\Table\Table("foo", 
                    array(),
                    new \TestDbAcle\Psv\Table\Meta(array(
                        'mode'=> 'replace',
                        'identifiedBy' => array('first_name', 'last_name')
        )));
        
        $mockPdoFacade->shouldReceive('recordExists')->once()->with('foo', array('first_name' => 'john', 'last_name' => 'Doe'))->andReturn(false);
        
        $this->assertEquals($expectedBuilder, $factory->getUpserter($table, array('first_name'=>'john', 'last_name' => 'Doe', 'foo' => 'cheese')));
    }
}