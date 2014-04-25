<?php

class TableTest extends \PHPUnit_Framework_TestCase 
{
    function setUp()
    {
   
        $describeStuff = array(
            array('Field' => 'stuff_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'col1', 'Type' => 'int', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col2', 'Type' => 'tinyint', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col3', 'Type' => 'date', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col4', 'Type' => 'datetime', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col5', 'Type' => 'text', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col6', 'Type' => 'tinytest', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col7', 'Type' => 'blob', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col8', 'Type' => 'tinyblob', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col9', 'Type' => 'varchar(255)', 'Null' => 'NO', 'Key' => '', 'Default' => 'foo', 'Extra' => ''),
        );

        $this->table = new TestDbAcle\Db\Table\Table("stuff", $describeStuff);
    }
    
    public function test_getPrimaryKey() {
        
        $tableWithPrimaryKey= new \TestDbAcle\Db\Table\Table("foo", array(
            array('Field' => 'user_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'first_name', 'Type' => 'varchar(255)', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
        ));
        
        $tableWithOutPrimaryKey= new \TestDbAcle\Db\Table\Table("foo", array(
            array('Field' => 'col2', 'Type' => 'tinyint', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col3', 'Type' => 'date', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
        ));
        
        $this->assertEquals('user_id', $tableWithPrimaryKey->getPrimaryKey());
        $this->assertEquals(null , $tableWithOutPrimaryKey->getPrimaryKey());
    }
    
    
     public function test_decorateWithNullPlaceHolders() {
        $this->assertEquals( array('col4'=>'2012-01-01','col1'=>'1','col9'=>'foo'), $this->table->getDecorateWithNullPlaceHolders(array('col9'=>'','col4'=>'2012-01-01')));
        $this->assertEquals( array('col9'=>'moo','col4'=>'2012-01-01','col1'=>'1'), $this->table->getDecorateWithNullPlaceHolders(array('col9'=>'moo','col4'=>'2012-01-01')));
    }
}