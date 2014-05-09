<?php
namespace TestDbAcle\Filter;

class AddDefaultValuesRowFilter implements RowFilter {
    
    protected $tableList;
    
    public function __construct(\TestDbAcle\Db\TableList $tableList)
    {
        $this->tableList = $tableList;
    }
    
    public function filter($tableName, array $row)
    {
        $table = $this->tableList->getTable($tableName);
        foreach ($table->getNonNullableColumns() as $columnName) {
            $column = $table->getColumn($columnName);
            if (($default = $column->getDefault()) && $this->isRedefineable($row,$columnName)){
                $row[$columnName] = $default;
            } elseif ($this->isUndefined($row, $columnName) && !$column->isAutoIncrement()) {
                $row[$columnName] = $column->generateDefaultNullValue();
            }
        }
        return $row;
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

        
    }
    
}

