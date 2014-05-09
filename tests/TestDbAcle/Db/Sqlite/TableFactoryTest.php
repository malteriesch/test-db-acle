<?php
namespace TestDbAcleTests\TestDbAcle\Db\Sqlite;

class TableFactoryTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    
    function test_createTable()
    {
        $tableFactory = new \TestDbAcle\Db\Sqlite\TableFactory(\Mockery::mock('\TestDbAcle\Db\AbstractPdoFacade'));
        $this->assertEquals(new \TestDbAcle\Db\Table\Table("foo"), $tableFactory->createTable("foo"));
    }
    
    function test_createColumn()
    {
        $tableFactory = new \TestDbAcle\Db\Sqlite\TableFactory(\Mockery::mock('\TestDbAcle\Db\AbstractPdoFacade'));
        $this->assertEquals(new \TestDbAcle\Db\Sqlite\Table\Column(array("foo")), $tableFactory->createColumn(array("foo")));
    }
}