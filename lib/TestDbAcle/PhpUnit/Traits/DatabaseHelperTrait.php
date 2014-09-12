<?php

namespace TestDbAcle\PhpUnit\Traits;

//require that consuming class implements AbstractTestCaseInterface
trait DatabaseHelperTrait
{
   
    /**
     * Override to provide the PDO connection to the test database.
     * @returns \PDO
     */
    abstract public function providePdo();
    
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
        static $databaseHelper =  null;
        if (is_null($databaseHelper)){
            $databaseHelper = $this->createDatabaseTestHelper();
        }
        return $databaseHelper;
    }
    
    /**
     * returns the configured TestDbAcle database helper
     * @return \PDO
     */
    public function getPdo()
    {
        static $pdo = null;
        if (is_null($pdo)){
            $pdo = $this->providePdo();
        }
        return $pdo;
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
     * @param PsvString $psvContent the PSV formatted tables to setup
     * @param array $replace an optional hash list of replacements
     */
    protected function setupTables($psvContent, $replace = array())
    {
        $this->getDatabaseTestHelper()->runCommand(new \TestDbAcle\Commands\SetupTablesCommand($psvContent, $replace));
    }
    
    
    
    
    /**
     * Asserts that a table contains the values specified. Placeholders can be 
     * provided and datetime columns get truncated when taken out from the database to compare
     * 
     * @param PsvString $expectedPsv
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
     * @param PsvString $expectedPsv expected values as PSV
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