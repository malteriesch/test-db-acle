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
    
    public function populateTableList(array $tableNames, \TestDbAcle\Db\TableList $tableList )
    {
        foreach($tableNames as $tableName){
            $tableList->addTable($this->createTableFromTableDescription($tableName, $this->pdoFacade->describeTable($tableName)));
        }
    }
    
    public function createTableList(array $tableNames )
    {
        $tableList=new \TestDbAcle\Db\TableList();
        $this->populateTableList($tableNames, $tableList);
        return $tableList;
    }
}