<?php
namespace TestDbAcleTests\TestDbAcle\Db\DataInserter\Sql;

class UpdateBuilderTest extends \PHPUnit_Framework_TestCase
{

    function test_getSql() 
    {
        $updateBuilder = new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder("foo");
        $updateBuilder->addColumn("col1", "'I will be escaped'");
        $updateBuilder->addColumn("the_date", "now()",true);
        $updateBuilder->addColumn("col2", null);
        $updateBuilder->addColumn("col3", "NULL");
        $updateBuilder->addCondition("first_name='cow'");
        $updateBuilder->addCondition("last_name='moo'");
        $this->assertEquals("UPDATE `foo` SET `col1`='\'I will be escaped\'', `the_date`=now(), `col2`=NULL, `col3`=NULL WHERE first_name='cow' AND last_name='moo'", $updateBuilder->getSql());
        
    }
    
    function test_getSql_WithIdentityMapInConstructor() 
    {
        $updateBuilder = new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder("foo",array('first_name'=>'cow','last_name'=>'moo'));
        $updateBuilder->addColumn("col1", "'I will be escaped'");
        $updateBuilder->addColumn("the_date", "now()",true);
        $this->assertEquals("UPDATE `foo` SET `col1`='\'I will be escaped\'', `the_date`=now() WHERE `first_name`='cow' AND `last_name`='moo'", $updateBuilder->getSql());
        
    }
}