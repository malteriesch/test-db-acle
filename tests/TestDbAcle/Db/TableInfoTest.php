<?php

class TableInfoTest extends \PHPUnit_Framework_TestCase 
{
    protected $tableInfo;
    
    function setUp()
    {
        
        $this->tableInfo = new \TestDbAcle\Db\TableInfo();
        
    }
    
    function test_AddingAndGettingTables()
    {
        $testTable1 = new TestDbAcle\Db\Table\Table("user", array(
            array('Field' => 'user_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'first_name', 'Type' => 'varchar(255)', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'last_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        ));
        
        $testTable2 = new TestDbAcle\Db\Table\Table("stuff", array(
            array('Field' => 'stuff_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'col1', 'Type' => 'int', 'Null' => 'NO', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col2', 'Type' => 'tinyint', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
        ));
        $this->tableInfo->addTable($testTable1);
        $this->tableInfo->addTable($testTable2);
        
        $this->assertSame($testTable1, $this->tableInfo->getTable('user'));
        $this->assertSame($testTable2, $this->tableInfo->getTable('stuff'));
        
        $this->assertNull($this->tableInfo->getTable('foo'));
    }
    
}
