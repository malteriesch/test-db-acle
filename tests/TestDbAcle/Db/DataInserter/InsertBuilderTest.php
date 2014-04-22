<?php

class InsertBuilderTest extends \PHPUnit_Framework_TestCase
{

    function test_getSql() 
    {
        $insertBuilder = new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder("foo");
        $insertBuilder->addColumn("col1", "'I will be escaped'");
        $insertBuilder->addColumn("the_date", "now()",true);
        $insertBuilder->addColumn("col2", null);
        $insertBuilder->addColumn("col3", "NULL");
        $this->assertEquals("INSERT INTO foo ( col1, the_date, col2, col3 ) VALUES ( '\'I will be escaped\'', now(), NULL, NULL )", $insertBuilder->getSql());
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