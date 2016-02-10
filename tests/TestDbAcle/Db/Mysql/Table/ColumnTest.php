<?php
namespace TestDbAcleTests\TestDbAcle\Db\Mysql\Table;

class ColumnTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase
{
    
    public function test_getName() {
        
        $column= new \TestDbAcle\Db\Mysql\Table\Column(array('Field' => 'user_id'));
        $this->assertEquals('user_id', $column->getName());
    }
   
    public function test_isDateTime() {
        
        $columnDate = new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'date'));
        $columnDateTime = new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'datetime'));
        $columnTimestamp = new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'timestamp'));
        $columnTinyBlob = new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'tinyblob'));

        $this->assertTrue($columnDate->isDateTime());
        $this->assertTrue($columnDateTime->isDateTime());
        $this->assertTrue($columnTimestamp->isDateTime());
        $this->assertFalse($columnTinyBlob->isDateTime());
        
    }
    
    public function test_isNullable() {
        
        $columnNullable = new \TestDbAcle\Db\Mysql\Table\Column(array('Null' => 'YES'));
        $columnNotNullable = new \TestDbAcle\Db\Mysql\Table\Column(array('Null' => 'NO'));
        
        $this->assertTrue($columnNullable->isNullable());
        $this->assertFalse($columnNotNullable->isNullable());
    }
    
    public function test_isPrimaryKey() {
        $columnPrimaryKey = new \TestDbAcle\Db\Mysql\Table\Column(array('Key' => 'PRI'));
        $columnNoPrimaryKey = new \TestDbAcle\Db\Mysql\Table\Column(array('Key' => ''));
        
        $this->assertTrue($columnPrimaryKey->isPrimaryKey());
        $this->assertFalse($columnNoPrimaryKey->isPrimaryKey());
    }
    
    public function test_getDefault()
    {
        $column = new \TestDbAcle\Db\Mysql\Table\Column(array('Default' => 'foo'));
        $this->assertEquals('foo', $column->getDefault());
    }
    
    public function test_isAutoIncrement()
    {
        $column1 = new \TestDbAcle\Db\Mysql\Table\Column(array('Extra' => 'auto_increment'));
        $column2 = new \TestDbAcle\Db\Mysql\Table\Column(array('Extra' => ''));
        $this->assertTrue($column1->isAutoIncrement());
        $this->assertFalse($column2->isAutoIncrement());
    }
    
    public function provider_generateDefaultNullValue()
    {
        return array(
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'INT')), "1"),
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'int')), "1"),//is case insensitive
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'DATE')), "2000-01-01"),
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'DATETIME')), "2000-01-01"),
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'TEXT')), "T"),
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'TINYTEXT')), "T"),
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'BLOB')), "T"),
          array(new \TestDbAcle\Db\Mysql\Table\Column(array('Type' => 'TINYBLOB')), "T"),
        );
    }
    
    /**
     * @dataProvider provider_generateDefaultNullValue
     */
    public function test_generateDefaultNullValue(\TestDbAcle\Db\Mysql\Table\Column $column ,$expected) {
        $this->assertEquals($expected, $column->generateDefaultNullValue());
    }
    
}