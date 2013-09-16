<?php
namespace TestDbAcle\Db\Sql; 
abstract class UpsertBuilder {
	
	var $tablename;
	var $columns = array();
	
	public function __construct( $tablename ) {
		
		$this->tablename = $tablename;
	}
	
	public function addColumn( $name, $value, $isExpression=false ) {
		
		$this->columns[ $name ] = array("value"=>$value,"isExpression"=>$isExpression);
	}
	
	protected function getCopyOfColumnsForManipulation() {
		return $this->columns;
	}
	
	abstract public function GetSql();
	
}
?>