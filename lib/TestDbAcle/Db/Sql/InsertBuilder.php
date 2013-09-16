<?php

namespace TestDbAcle\Db\Sql;

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

        $columnNames = implode(', ', array_keys($this->columns));
        $columns     = $this->getCopyOfColumnsForManipulation();

        array_walk($columns, array($this,'escapeValues'));

        $values = implode(', ', $columns);

        return "INSERT INTO {$this->tablename} ( {$columnNames} ) VALUES ( {$values} )";
    }

}

?>