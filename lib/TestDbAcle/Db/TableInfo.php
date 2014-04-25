<?php

namespace TestDbAcle\Db;

class TableInfo
{

    protected $nonNullableColumns = array();
    protected $nullableColumns    = array();
    protected $primaryKeys        = array();
    protected $tableDescriptions  = array();
    
    
    protected $tables = array();
    
    public function addTable(\TestDbAcle\Db\Table\Table $table)
    {
        $this->tables[$table->getName()] = $table;
    }
    
    public function getTable($name)
    {
        if (isset($this->tables[$name])){
            return $this->tables[$name];
        }
        return null;
    }
}
