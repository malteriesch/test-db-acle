<?php
namespace TestDbAcle\Db\Table;
class Column 
{
    protected $meta=array();
    
    function __construct(array $meta)
    {
        $this->meta = $meta;
    }
    
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
        return strpos($this->meta['Type'], 'date') !== false;
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
        return 'T';
    }
    
    public function isPrimaryKey()
    {
        return $this->meta['Key'] == 'PRI';
    }
}

