<?php

namespace TestDbAcle\Db;

class TableInfo
{

    protected $nonNullableColumns = array();
    protected $nullableColumns    = array();
    protected $primaryKeys        = array();
    protected $tableDescriptions  = array();

    public function addTableDescription($table, $tableDescription)
    {

        if (!isset($this->tableDescriptions[$table])) {
            foreach ($tableDescription as $columnDescription) {
                $this->tableDescriptions[$table][$columnDescription['Field']] = $columnDescription;
            }
        }

        foreach ($tableDescription as $columnInfo) {
            if ($columnInfo['Null'] == 'NO') {
                $this->nonNullableColumns[$table][] = $columnInfo['Field'];
            } else {
                
            }
            if ($columnInfo['Key'] == 'PRI') {
                $this->primaryKeys[$table] = $columnInfo['Field'];
            }
        }
    }
    
    public function getPrimaryKey($table)
    {
        if (isset($this->primaryKeys[$table])){
            return $this->primaryKeys[$table];
        }
        return null;
    }

    public function generateDefaultNullValue($columnType)
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

    public function getDecorateWithNullPlaceHolders($tableName, array $tableRow)
    {

        foreach ($this->nonNullableColumns[$tableName] as $columnName) {
            $columnDescription = $this->tableDescriptions[$tableName][$columnName];
            if (!is_null($columnDescription['Default']) && (!isset($tableRow[$columnName]) || !$tableRow[$columnName])) {
                $tableRow[$columnName] = $columnDescription['Default'];
            } elseif (!isset($tableRow[$columnName]) &&
                    $columnDescription['Null'] == 'NO' &&
                    $columnDescription['Extra'] != "auto_increment") {
                $tableRow[$columnName] = $this->generateDefaultNullValue($columnDescription['Type']);
            }
        }
        return $tableRow;
    }

    public function isNullable($tableName, $columnName)
    {
        return !in_array($columnName, $this->nonNullableColumns[$tableName]);
    }

}
