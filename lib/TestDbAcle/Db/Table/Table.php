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
    
    public function getNonNullableColumns()
    {
        return $this->nonNullableColumns;
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
    
   
}
