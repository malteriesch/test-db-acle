<?php
namespace TestDbAcle\PhpUnit;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase implements AbstractTestCaseInterface
{
    /* @var $databaseTestHelper \TestDbAcle\TestDbAcle */
    protected $databaseTestHelper = null;
    
    /* @var $pdo \Pdo */
    protected $pdo = null;
    
    /**
     * Override to provide the PDO connection to the test database.
     * @returns \PDO
     */
    abstract public function providePdo();
    
    function getPdo()
    {
        if(is_null($this->pdo)){
            $this->pdo = $this->providePdo();
        }
        return $this->pdo;
    }
    /**
     * used to create the TestDbAcle database helper
     * @return \TestDbAcle\TestDbAcle
     */
    protected function createDatabaseTestHelper()
    {
        return \TestDbAcle\TestDbAcle::create($this->getPdo());
    }
    
    
    /**
     * returns the configures TestDbAcle database helper
     * @return \TestDbAcle\TestDbAcle
     */
    public function getDatabaseTestHelper()
    {
        if(is_null($this->databaseTestHelper)){
            $this->databaseTestHelper = $this->createDatabaseTestHelper();
        }
        return $this->databaseTestHelper;
    }

    /**
     * Sets up the autoincrement for a table
     * 
     * @param String $table the name of the table
     * @param Integer $nextIncrement the next increment
     */
    protected function setAutoIncrement($table, $nextIncrement)
    {
        $this->getDatabaseTestHelper()->runCommand(new \TestDbAcle\Commands\SetAutoIncrementCommand($table, $nextIncrement));
        
    }
    
    /**
     * 
     * 
     * @param String $psvContent the PSV formatted tables to setup
     * @param array $replace an optional hash list of replacements
     */
    protected function setupTables($psvContent, $replace = array())
    {
        $this->getDatabaseTestHelper()->runCommand(new \TestDbAcle\Commands\SetupTablesCommand($psvContent, $replace));
    }
    
    /**
     * If this method gets overridden, parent::Setup() needs to be called as the first line of the overiding method
     */
    function setUp()
    {
        if(!isset($this->databaseTestHelper)){
            $this->databaseTestHelper = $this->createDatabaseTestHelper();
        }
    }
    
    /**
     * Asserts that a table contains the values specified. Placeholders can be 
     * provided and datetime columns get truncated when taken out from the database to compare
     * 
     * @param String $expectedPsv
     * @param array $placeHolders
     * @param String $message an optional assert message
     */
    protected function assertTableStateContains( $expectedPsv, $placeHolders = array(), $message = '' )
    {
        $command = new \TestDbAcle\Commands\FilterTableStateByPsvCommand($expectedPsv, $placeHolders);
        $this->getDatabaseTestHelper()->runCommand($command);
        $this->assertEquals($command->getExpectedData(), $command->getActualData(), $message);
    }

    /**
     * Asserts that array contains the values specified. Placeholders can be 
     * provided 
     * 
     * @param String $expectedPsv expected values as PSV
     * @param array $actual actual values
     * @param array $placeHolders
     * @param String $message an optional assert message
     */
    protected function assertArrayContainsPsv( $expectedPsv, $actual, $placeHolders = array(), $message = '' )
    {
        $command = new \TestDbAcle\Commands\FilterArrayByPsvCommand($expectedPsv, $actual, $placeHolders);
        $this->getDatabaseTestHelper()->runCommand($command);
        $this->assertEquals($command->getExpectedData(), $command->getActualData(), $message);
    }
    
}