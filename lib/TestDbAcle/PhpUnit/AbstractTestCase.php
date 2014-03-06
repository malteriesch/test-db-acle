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
    
    protected function assertTableStateContains( $expectedPsv, $placeHolders = array(), $message = '' )
    {
        $expectedData         = array();
        $actualData           = array();
        $parsedTree           = $this->getDatabaseTestHelper()->getParser()->parsePsvTree($expectedPsv);
        $filterQueue          = new \TestDbAcle\Filter\FilterQueue();
        $filterQueue->addRowFilter(new \TestDbAcle\Filter\PlaceholderRowFilter($placeHolders));
        $filteredParsedTree   = $filterQueue->filterDataTree($parsedTree);

        $tableInfo = $this->getDatabaseTestHelper()->getTableInfo();
        $pdoFacade = $this->getDatabaseTestHelper()->getPdoFacade();
        
        foreach(array_keys($filteredParsedTree) as $tableName){
            $tableInfo->addTableDescription($tableName, $pdoFacade->describeTable($tableName));
        }

        foreach($filteredParsedTree as $tableName=>$tableData){
            $expectedData[$tableName]=$tableData['data'];
            $columnsToSelect = array_keys($expectedData[$tableName][0]);
            $data = $this->pdo->query("select ".implode(", ",$columnsToSelect)." from $tableName")->fetchAll(\PDO::FETCH_ASSOC);
            $actualData[$tableName] = $this->truncateDatetimeFields($tableName, $data, $tableInfo);
        }
        $this->assertEquals($expectedData, $actualData, $message);
    }

    protected function truncateDatetimeFields($tableName, $tableData, \TestDbAcle\Db\TableInfo $tableInfo)
    {
        $filtered = array();
        foreach($tableData as $dataRow){
            $newFilteredRow = array();
            foreach($dataRow as $columnName=>$value){
                if ($tableInfo->isDateTime($tableName, $columnName) && $value ){
                    $newFilteredRow[$columnName] = date("Y-m-d", strtotime($value));
                }else{
                    $newFilteredRow[$columnName] = $value;
                }
            }
            $filtered[]=$newFilteredRow;
        }
        return $filtered;
    }
}