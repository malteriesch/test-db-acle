<?php
namespace TestDbAcle\Db\Table;

class Table 
{
    protected $name;
    protected $primaryKey;
    protected $columns            = array();
    protected $nonNullableColumns = array();
    
    function __construct($name) 
    {
        $this->name = $name;
    }
    
    function getName() 
    {
        return $this->name;
    }
    
    public function addColumn(\TestDbAcle\Db\Table\AbstractColumn $column)
    {
        $this->columns[$column->getName()] = $column;

        if (!$column->isNullable()) {
            $this->nonNullableColumns[] = $column->getName();
        } 

        if ($column->isPrimaryKey()) {
            $this->primaryKey = $column->getName();
        }
    }
    
    public function getColumn($name)
    {
        if(isset($this->columns[$name])){
            return $this->columns[$name];
        }
        return null;
    }
    
    function getPrimaryKey() 
    {
        return $this->primaryKey;
    }
    
    public function getNonNullableColumns()
    {
        return $this->nonNullableColumns;
    }
   
}
