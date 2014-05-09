<?php
namespace TestDbAcleTests\TestDbAcle\Db\Mysql;

class TableFactoryTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    
    function test_createTable()
    {
        $tableFactory = new \TestDbAcle\Db\Mysql\TableFactory(\Mockery::mock('\TestDbAcle\Db\AbstractPdoFacade'));
        $this->assertEquals(new \TestDbAcle\Db\Table\Table("foo"), $tableFactory->createTable("foo"));
    }
    
    function test_createColumn()
    {
        $tableFactory = new \TestDbAcle\Db\Mysql\TableFactory(\Mockery::mock('\TestDbAcle\Db\AbstractPdoFacade'));
        $this->assertEquals(new \TestDbAcle\Db\Mysql\Table\Column(["foo"]), $tableFactory->createColumn(["foo"]));
    }
}