<?php
namespace TestDbAcle\Db\Mysql\Table;

class Column extends \TestDbAcle\Db\Table\AbstractColumn
{
    static protected $uniqunessIndex = 0;
    public function getName()
    {
        return $this->meta['Field'];
    }
    
    public function getDefault()
    {
        return $this->meta['Default'];
    }
    
    public function isAutoIncrement()
    {
        return $this->meta['Extra']=='auto_increment';
    }
    
    public function isDateTime()
    {
        return strpos($this->meta['Type'], 'date') !== false || strpos($this->meta['Type'], 'timestamp') !== false;
    }
    
    public function isNullable()
    {
        return $this->meta['Null'] == 'YES';
    }
    
    public function generateDefaultNullValue()
    {
      
        $columnType = strtolower($this->meta['Type']);
        if (strpos($columnType, 'int') !== false) {
            return '1';
        }

        if (strpos($columnType, 'date') !== false) {
            return '2000-01-01';
        }
        if (($this->meta['Key'] ?? null) == 'UNI') {
            self::$uniqunessIndex++;
            return 'T' . self::$uniqunessIndex;
        }
        return 'T';
    }
    
    public function isPrimaryKey()
    {
        return $this->meta['Key'] == 'PRI';
    }
}

