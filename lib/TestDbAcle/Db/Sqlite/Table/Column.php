<?php
namespace TestDbAcle\Db\Sqlite\Table;
class Column  extends \TestDbAcle\Db\Table\AbstractColumn
{
    
    public function getName()
    {
        return $this->meta['name'];
    }
    
    public function getDefault()
    {
        return $this->meta['dflt_value'];
    }
    
    public function isAutoIncrement()
    {
        return $this->isPrimaryKey();
    }
    
    public function isDateTime()
    {
        return false;
    }
    
    public function isNullable()
    {
        return $this->meta['notnull'] == '0';
    }
    
    public function generateDefaultNullValue()
    {
      
        $columnType = strtolower($this->meta['type']);
        if (strpos($columnType, 'int') !== false) {
            return '1';
        }
        return 'T';
    }
    
    public function isPrimaryKey()
    {
        return $this->meta['pk'] == '1';
    }
}

