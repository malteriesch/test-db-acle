<?php

namespace TestDbAcle\Db\DataInserter\Sql;

class UpdateBuilder extends UpsertBuilder {

    protected $conditions;
    
     public function __construct($tablename, $identityMap = array()) {
        parent::__construct($tablename);
        foreach(self::identityMapToConditionArray($identityMap) as $condition) {
            $this->addCondition($condition);
        }
    }
    
    public static function identityMapToConditionArray($identifyKeyMap)
    {
        $conditions = array();
        foreach($identifyKeyMap as $key=>$value){
            $conditions[]="`$key`='".addslashes($value)."'";
        }
        return $conditions;
    }
    
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
    
    protected function getConditionSql()
    {
        return implode(' AND ', $this->conditions);
    }

    public function getSql() {

        $columnNames = implode(', ', array_keys($this->columns));
        $columns = $this->getCopyOfColumnsForManipulation();

        array_walk($columns, function(&$value,$key){
            $actualValue = addslashes($value['value']);
            if ($value['isExpression']) {
                $value = "`$key`=$actualValue";
            } else {
                $value = "`$key`='$actualValue'";
            }
        });

        $valueString = implode(', ', $columns);
        
        $conditionString = $this->getConditionSql();
        

        return "UPDATE `{$this->tablename}` SET $valueString WHERE $conditionString";
    }

   
}

?>