<?php
namespace TestDbAcle\Db\Table;

class Table 
{
    protected $name;
    protected $primaryKey;
    protected $columns = array();
    protected $nonNullableColumns = array();
    
    function __construct($name, array $metaData) 
    {
        $this->name = $name;
        $this->parse($metaData);
    }
    
    protected function parse(array $metaData)
    {
        foreach ($metaData as $columnMeta) {
            $column = new \TestDbAcle\Db\Table\Column($columnMeta);
            
            $this->columns[$column->getName()] = $column;
            
            if (!$column->isNullable()) {
                $this->nonNullableColumns[] = $column->getName();
            } 
            
            if ($column->isPrimaryKey()) {
                $this->primaryKey = $column->getName();
            }
        }
    }
    
    public function getColumn($name)
    {
        if(isset($this->columns[$name])){
            return $this->columns[$name];
        }
        return null;
    }
    
    
    function getName() 
    {
        return $this->name;
    }
    
    function getPrimaryKey() 
    {
        return $this->primaryKey;
    }
    
     public function isDateTime($columnName)
    {
        return strpos($this->columns[$columnName]['Type'], 'date') !== false;
    }
    
    public function isNullable($columnName)
    {
        return !in_array($columnName, $this->nonNullableColumns);
    }
    
    protected function isUndefined($tableRow,$columnName){
        return !isset($tableRow[$columnName]);
    }
    
    protected function isRedefineable($tableRow, $columnName)
    {
        return $this->isUndefined($tableRow, $columnName) || !$tableRow[$columnName];
    }
   
    public function getDecorateWithNullPlaceHolders(array $tableRow)
    {

        foreach ($this->nonNullableColumns as $columnName) {
            $column = $this->columns[$columnName];
            if (($default = $column->getDefault()) && $this->isRedefineable($tableRow,$columnName)){
                $tableRow[$columnName] = $default;
            } elseif ($this->isUndefined($tableRow, $columnName) &&
                    !$column->isNullable() &&
                    !$column->isAutoIncrement()) {
                $tableRow[$columnName] = $column->generateDefaultNullValue();
            }
        }
        return $tableRow;
    }
    
    public static function generateDefaultNullValue($columnType)
    {
      
        $columnType = strtolower($columnType);
        if (strpos($columnType, 'int') !== false) {
            return '1';
        }

        if (strpos($columnType, 'date') !== false) {
            return '2000-01-01';
        }
        return 'T';
    }
}
