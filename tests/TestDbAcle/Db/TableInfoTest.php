<?php

class TableInfoTest extends \PHPUnit_Framework_TestCase 
{
    protected $tableInfo;
    
    function setUp()
    {
        $describeUser = array(
            array('Field' => 'user_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'first_name', 'Type' => 'varchar(255)', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'last_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );

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


        $this->tableInfo = new \TestDbAcle\Db\TableInfo();
        $this->tableInfo->addTableDescription("user", $describeUser);
        $this->tableInfo->addTableDescription("stuff", $describeStuff);
    }

    public function test_isNullable() {
        $this->assertFalse($this->tableInfo->isNullable('user','user_id'));
        $this->assertFalse($this->tableInfo->isNullable('user','first_name'));
        $this->assertFalse($this->tableInfo->isNullable('stuff','stuff_id'));
        
        $this->assertTrue($this->tableInfo->isNullable('user','last_name'));
        $this->assertTrue($this->tableInfo->isNullable('stuff','col2'));
        
        
    }
    public function test_generateDefaultNullValue() {
        $this->assertEquals("1",$this->tableInfo->generateDefaultNullValue('INT'));
        $this->assertEquals("1",$this->tableInfo->generateDefaultNullValue('int'),"is case insensitive");
        $this->assertEquals("1",$this->tableInfo->generateDefaultNullValue('TINYINT'));
        $this->assertEquals("2000-01-01",$this->tableInfo->generateDefaultNullValue('DATE'));
        $this->assertEquals("2000-01-01",$this->tableInfo->generateDefaultNullValue('DATETIME'));
        $this->assertEquals("T",$this->tableInfo->generateDefaultNullValue('TEXT'));
        $this->assertEquals("T",$this->tableInfo->generateDefaultNullValue('TINYTEST'));
        $this->assertEquals("T",$this->tableInfo->generateDefaultNullValue('BLOB'));
        $this->assertEquals("T",$this->tableInfo->generateDefaultNullValue('TINYBLOB'));
        
        
    }
    public function test_decorateWithNullPlaceHolders() {
        $this->assertEquals( array('col4'=>'2012-01-01','col1'=>'1','col9'=>'foo'), $this->tableInfo->getDecorateWithNullPlaceHolders('stuff',array('col9'=>'','col4'=>'2012-01-01')));
        $this->assertEquals( array('col9'=>'moo','col4'=>'2012-01-01','col1'=>'1'), $this->tableInfo->getDecorateWithNullPlaceHolders('stuff',array('col9'=>'moo','col4'=>'2012-01-01')));
    }

}
