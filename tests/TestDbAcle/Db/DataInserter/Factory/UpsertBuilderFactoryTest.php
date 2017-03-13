<?php
namespace TestDbAcleTests\TestDbAcle\Db\DataInserter\Factory;

class UpsertBuilderFactoryTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    function test_getUpserter_returnsInsertBuilderByDefault()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");
        $psvTable = new \TestDbAcle\Psv\Table\Table("foo");
        $dbTable = new \TestDbAcle\Db\Table\Table("foo");
        $this->assertEquals($expectedBuilder, $factory->getUpserter($psvTable, $dbTable));
    }
    
    function test_getUpserter_returnsUpdateBuilderIfReplaceModeAndRecordExists()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder("foo", array('first_name'=>'john', 'last_name' => 'Smith'));

        $psvTable = new \TestDbAcle\Psv\Table\Table("foo",
                    array(),
                    new \TestDbAcle\Psv\Table\Meta(array(
                        'mode'=> 'replace',
                        'identifiedBy' => array('first_name', 'last_name')
        )));

        $dbTable = new \TestDbAcle\Db\Table\Table("foo");

        $mockPdoFacade->shouldReceive('recordExists')->once()->with('foo', array('first_name' => 'john', 'last_name' => 'Smith'))->andReturn(true);
        
        $this->assertEquals($expectedBuilder, $factory->getUpserter($psvTable, $dbTable, array('first_name'=>'john', 'last_name' => 'Smith', 'foo' => 'cheese')));
    }

    function test_getUpserter_returnsUpdateBuilderIfReplaceModeAndRecordExists_usesPrimaryKeyIfNoOtherColumnsSpecified()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder("foo", array('foo_id' =>10));

        $psvTable = new \TestDbAcle\Psv\Table\Table(
            "foo",
            [],
            new \TestDbAcle\Psv\Table\Meta(
                [
                    'mode'         => 'replace',
                ]
            )
        );

        $dbTable = \Mockery::mock('\TestDbAcle\Db\Table\Table');
        $dbTable->shouldReceive('getPrimaryKey')->andReturn('foo_id');

        $mockPdoFacade->shouldReceive('recordExists')->once()->with('foo', array('foo_id' => 10))->andReturn(true);

        $this->assertEquals($expectedBuilder, $factory->getUpserter($psvTable, $dbTable, array('foo_id' =>10, 'first_name'=>'john', 'last_name' => 'Smith', 'foo' => 'cheese')));
    }
    
    function test_getUpserter_returnsInsertBuilderIfReplaceModeAndRecordDoesNotExist()
    {
        $mockPdoFacade =  \Mockery::mock('\TestDbAcle\Db\Mysql\Pdo\PdoFacade');
        $factory = new \TestDbAcle\Db\DataInserter\Factory\UpsertBuilderFactory($mockPdoFacade);
        $expectedBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");

        $psvTable = new \TestDbAcle\Psv\Table\Table("foo",
                    array(),
                    new \TestDbAcle\Psv\Table\Meta(array(
                        'mode'=> 'replace',
                        'identifiedBy' => array('first_name', 'last_name')
        )));

        $dbTable = new \TestDbAcle\Db\Table\Table("foo");
        
        $mockPdoFacade->shouldReceive('recordExists')->once()->with('foo', array('first_name' => 'john', 'last_name' => 'Doe'))->andReturn(false);
        
        $this->assertEquals($expectedBuilder, $factory->getUpserter($psvTable, $dbTable, array('first_name'=>'john', 'last_name' => 'Doe', 'foo' => 'cheese')));
    }
}