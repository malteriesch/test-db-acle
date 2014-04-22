<?php

namespace TestDbAcle\Db\DataInserter\Sql;

abstract class UpsertBuilder {

    
    var $tablename;
    var $columns = array();

    public function __construct($tablename) {

        $this->tablename = $tablename;
    }

    public function addColumn($name, $value, $isExpression = false) {

        if (is_null($value) || $value =="NULL"){
            $this->columns[$name] = array("value" => "NULL", "isExpression" => true);
        }else{
            $this->columns[$name] = array("value" => $value, "isExpression" => $isExpression);
        }
    }
    
    public function getColumn($name) {

        if(isset($this->columns[$name])){
            return $this->columns[$name]['value'];
        }
        return null;
    }
    
    public function getTableName()
    {
        return $this->tablename;
    }

    protected function getCopyOfColumnsForManipulation() {
        return $this->columns;
    }

    abstract public function GetSql();
    
}

?>