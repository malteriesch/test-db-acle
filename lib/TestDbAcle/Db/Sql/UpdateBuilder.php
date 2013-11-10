<?php

namespace TestDbAcle\Db\Sql;

class UpdateBuilder extends UpsertBuilder {

    protected $conditions;
    
    protected function escapeValues(&$value) {
        $actualValue = addslashes($value['value']);
        if ($value['isExpression']) {
            $value = $actualValue;
        } else {
            $value = "'" . $actualValue . "'";
        }
    }

    public function addCondition($condition) {

        $this->conditions[] = $condition;
    }
    
    public function getConditionSql()
    {
        return implode(' AND ', $this->conditions);
    }

    public function getSql() {

        $columnNames = implode(', ', array_keys($this->columns));
        $columns = $this->getCopyOfColumnsForManipulation();

        array_walk($columns, function(&$value,$key){
            $actualValue = addslashes($value['value']);
            if ($value['isExpression']) {
                $value = "$key=$actualValue";
            } else {
                $value = "$key='$actualValue'";
            }
            return "$key=$value";
        });

        $valueString = implode(', ', $columns);
        
        $conditionString = $this->getConditionSql();
        

        return "UPDATE {$this->tablename} SET $valueString WHERE $conditionString";
    }

}

?>