<?php

class UpdateBuilderTest extends \PHPUnit_Framework_TestCase
{

    function test_getSql() 
    {
        $updateBuilder = new \TestDbAcle\Db\Sql\UpdateBuilder("foo");
        $updateBuilder->addColumn("col1", "'I will be escaped'");
        $updateBuilder->addColumn("the_date", "now()",true);
        $updateBuilder->addCondition("first_name='cow'");
        $updateBuilder->addCondition("last_name='moo'");
        $this->assertEquals("UPDATE foo SET col1='\'I will be escaped\'', the_date=now() WHERE first_name='cow' AND last_name='moo'", $updateBuilder->getSql());
        $this->assertEquals("first_name='cow' AND last_name='moo'", $updateBuilder->getConditionSql());
        
    }
}