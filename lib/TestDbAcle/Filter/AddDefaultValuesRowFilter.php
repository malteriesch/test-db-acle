<?php
namespace TestDbAcle\Filter;

class AddDefaultValuesRowFilter implements RowFilter {
    
    protected $tableInfo;
    
    public function __construct(\TestDbAcle\Db\TableInfo $tableInfo)
    {
        $this->tableInfo = $tableInfo;
    }
    
    public function filter($tableName, array $row)
    {
        return $this->tableInfo->getDecorateWithNullPlaceHolders($tableName, $row);
    }
    
}

