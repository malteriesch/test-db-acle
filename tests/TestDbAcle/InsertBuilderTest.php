<?php

class InsertBuilderTest extends \PHPUnit_Framework_TestCase
{

    function test_getSql() 
    {
        $insertBuilder = new \TestDbAcle\Db\Sql\InsertBuilder("foo");
        $insertBuilder->addColumn("col1", "'I will be escaped'");
        $insertBuilder->addColumn("the_date", "now()",true);
        $this->assertEquals("INSERT INTO foo ( col1, the_date ) VALUES ( '\'I will be escaped\'', now() )", $insertBuilder->getSql());
    }
}