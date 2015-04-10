<?php

namespace TestDbAcle\Db\DataInserter\Sql;

class InsertBuilder extends UpsertBuilder {
    
    protected function escapeValues(&$value){
        $actualValue = addslashes($value['value']);
        if ($value['isExpression']) {
            $value = $actualValue;
        }else{
            $value = "'".$actualValue."'";
        }
    }

    public function getSql() {
        $columnNames = array_keys($this->columns);
        
        array_walk($columnNames, function(&$value) {
            $value = "`$value`";
        });
        
        $columnNamesImploded = implode(', ', $columnNames);
        $columns     = $this->getCopyOfColumnsForManipulation();

        array_walk($columns, array($this,'escapeValues'));
        
        

        $values = implode(', ', $columns);

        return "INSERT INTO `{$this->tablename}` ( {$columnNamesImploded} ) VALUES ( {$values} )";
    }

}

?>