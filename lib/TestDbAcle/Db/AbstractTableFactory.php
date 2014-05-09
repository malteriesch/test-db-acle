<?php
namespace TestDbAcle\Db;
abstract class AbstractTableFactory
{

    /**
     *
     * @var \TestDbAcle\Db\AbstractPdoFacade
     */
    protected $pdoFacade;

    public abstract function createTable($name);
    public abstract function createColumn(array $description);
    
    
    public function __construct(\TestDbAcle\Db\AbstractPdoFacade $pdoFacade)
    {
        $this->pdoFacade = $pdoFacade;
    }
    
    public function createTableFromTableDescription($name, array $tableDescription)
    {
        $table = $this->createTable($name);
        
        foreach($tableDescription as $columnDescription){
            $table->addColumn($this->createColumn($columnDescription));
        }
        
        return $table;
    }
    
    public function populateTableInfo(array $tableNames, \TestDbAcle\Db\TableInfo $tableInfo )
    {
        foreach($tableNames as $tableName){
            $tableInfo->addTable($this->createTableFromTableDescription($tableName, $this->pdoFacade->describeTable($tableName)));
        }
    }
    
    public function createTableInfo(array $tableNames )
    {
        $tableInfo=new \TestDbAcle\Db\TableInfo();
        $this->populateTableInfo($tableNames, $tableInfo);
        return $tableInfo;
    }
}