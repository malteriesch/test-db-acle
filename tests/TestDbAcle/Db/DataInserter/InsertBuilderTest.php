<?php

class InsertBuilderTest extends \PHPUnit_Framework_TestCase
{

    function test_getSql() 
    {
        $insertBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");
        $insertBuilder->addColumn("col1", "'I will be escaped'");
        $insertBuilder->addColumn("the_date", "now()",true);
        $this->assertEquals("INSERT INTO foo ( col1, the_date ) VALUES ( '\'I will be escaped\'', now() )", $insertBuilder->getSql());
    }
    
    function test_getColumn() 
    {
        $insertBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");
        $insertBuilder->addColumn("col1", 'moo');
        $this->assertEquals("moo", $insertBuilder->getColumn("col1"));
    }
    
    function test_getTableName() 
    {
        $insertBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");
        $this->assertEquals("foo", $insertBuilder->getTableName());
    }
}