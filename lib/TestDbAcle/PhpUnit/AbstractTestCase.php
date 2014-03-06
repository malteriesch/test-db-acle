<?php
namespace TestDbAcle\PhpUnit;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase 
{
    /* @var $databaseTestHelper \TestDbAcle\TestDbAcle */
    protected $databaseTestHelper;
    
    
    
    abstract public function getPdo();
    
    protected function createDatabaseTestHelper()
    {
        return \TestDbAcle\TestDbAcle::create($this->getPdo());
    }
    /* @var $databaseTestHelper \TestDbAcle\TestDbAcle */
    protected function getDatabaseTestHelper()
    {
        return $this->databaseTestHelper;
    }

    protected function setAutoIncrement($table, $nextIncrement)
    {
        $this->getDatabaseTestHelper()->getPdoFacade()->setAutoIncrement($table, $nextIncrement);
    }
    
    protected function setupTables($psvContent, $replace = array())
    {
        $this->getDatabaseTestHelper()->setupTablesWithPlaceholders($psvContent, $replace);
    }
    
    function setUp()
    {
        if(!isset($this->databaseTestHelper)){
            $this->databaseTestHelper = $this->createDatabaseTestHelper();
        }
        return $this->databaseTestHelper;
    }
    
    protected function assertTableStateContains( $expectedPsv, $message = '' )
    {
        $expectedData = array();
        $actualData   = array();
        
        foreach($this->getDatabaseTestHelper()->getParser()->parsePsvTree($expectedPsv) as $tableName=>$tableData){
            $expectedData[$tableName]=$tableData['data'];
            $columnsToSelect = array_keys($expectedData[$tableName][0]);
            $data = $this->pdo->query("select ".implode(", ",$columnsToSelect)." from $tableName")->fetchAll(\PDO::FETCH_ASSOC);
            $actualData[$tableName] = $data;
        }
        
       $this->assertEquals($expectedData,$actualData);
    }
}