<?php

/**
 * @TODO
 * move to AbstractBaseClass so all PHP versions are tested
 * check zero pk problem exists
 * check how top sort out the datetimes
 * check with different datetime formats (also mysql)
 * 
 */
abstract class SqlliteSmokeTest extends \TestDbAcle\PhpUnit\AbstractTestCase
{
    
    function providePdo()
    {
        return new \Pdo("sqlite::memory:");
    }
    
    function setup()
    {
        $this->getPdo()->exec('DROP TABLE IF EXISTS `address`');
        $this->getPdo()->exec('CREATE TABLE `address` (
                                `address_id` INTEGER PRIMARY KEY   AUTOINCREMENT,
                                `company` varchar(100) NOT NULL,
                                `address1` varchar(100) NOT NULL,
                                `address2` varchar(100) DEFAULT NULL,
                                `address3` varchar(100) DEFAULT NULL,
                                `city` varchar(100) NOT NULL,
                                `postcode` varchar(100) NOT NULL,
                                `country` varchar(100) NOT NULL,
                                `date_of_entry` DATETIME
                              )');

        
        $this->getPdo()->exec('DROP TABLE IF EXISTS `user`');
        $this->getPdo()->exec('CREATE TABLE `user` (
                                `user_id` int(11) NOT NULL,
                                `name` varchar(100) NOT NULL,
                                PRIMARY KEY (`user_id`)
                              )');
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_simpleSetupAndAssertTableStateContains()
    {
        
        $this->setupTables("
            [address]
            address_id  |company    
            1           |me         
            3           |you        

            [user]
            user_id |name
            1       |mary

        ");

        $exampleServiceSqllite = new ExampleServiceSqllite($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleServiceSqllite->addEntry("them");

        
        $this->assertTableStateContains("
            [address]
            address_id  |company    
            1           |me         
            3           |you       
            1000        |them       

            [user]
            user_id |name
            1       |mary
            ", array('NOW'=>date("Y-m-d")));
        try{
            $this->assertTableStateContains("
                [address]
                address_id  |company
                1           |me
                3           |Us
                1000        |them
                
                [user]
                user_id |name
                1       |mary
                ");
            $this->fail("assertTableStateContains fails if the information in the table is different ");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
                
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_simpleSetupAndAssertTableStateContains_WithZeroPrimaryKey()
    {
        
        $this->setupTables("
            [address]
            address_id  |company    
            0           |me         
            3           |you        

            [user]
            user_id |name
            1       |mary

        ",array('you'=>'John'));

        $exampleServiceSqllite = new ExampleServiceSqllite($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleServiceSqllite->addEntry("them");

        
        $this->assertTableStateContains("
            [address]
            address_id  |company   
            0           |me        
            3           |John      
            1000        |them      

            [user]
            user_id |name
            1       |mary
            ", array('NOW'=>date("Y-m-d")));
              
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand() with placeholders
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    
    function test_SetupAndAssertWithPlaceholders()
    {
        
        $this->setupTables("
            [address]
            address_id  |company   
            1           |me        
            3           |you       

            [user]
            user_id |name
            1       |mary

        ",array('you'=>'John'));

        $exampleServiceSqllite = new ExampleServiceSqllite($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleServiceSqllite->addEntry("them");

        
        $this->assertTableStateContains("
            [address]
            address_id  |company   
            1           |me        
            3           |John      
            1000        |them      

            [user]
            user_id |name
            1       |mary
            ");
       
                
    }
    
     /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand() with placeholders
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_DatetimeColumnsGetTruncatedWhenComparing()
    {
        
        $this->setupTables("
            [address]
            address_id  |company    |date_of_entry
            1           |me         |2001-01-01 08:50:00
            3           |you        |NULL

            [user]
            user_id |name
            1       |mary

        ",array('you'=>'John'));

        $exampleServiceSqllite = new ExampleServiceSqllite($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleServiceSqllite->addEntry("them");

        
        $this->assertTableStateContains("
            [address|truncateDates:date_of_entry]
            address_id  |company    |date_of_entry
            1           |me         |2001-01-01
            3           |John       |NULL
            1000        |them       |NOW

            [user]
            user_id |name
            1       |mary
            ", array('NOW'=>date("Y-m-d")));
                
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_AssertTableStateContains_handlesReservedKeywords()
    {
        $this->getPdo()->query('CREATE TABLE `select` (
                                `id` int(11) NOT NULL,
                                `select` varchar(100) NOT NULL,
                                PRIMARY KEY (`id`)
                              )');
        
        $this->setupTables("
            [select]
            id  |select    
            1   |foo


        ");

        $this->assertTableStateContains("
            [select]
            id  |select    
            1   |foo
            ");
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_AssertTableStateContains_handlesReservedKeywords_emptyTable()
    {
        $this->getPdo()->query('CREATE TABLE `select` (
                                `id` int(11) NOT NULL,
                                `select` varchar(100) NOT NULL,
                                PRIMARY KEY (`id`)
                              )');
        
        $this->setupTables("
            [select]
            id  |select    


        ");

        $this->assertTableStateContains("
            [select]
            id  |select    
            ");
    }
                             
}



class ExampleServiceSqllite
{
    protected $pdo;
    function __construct(\Pdo $pdo){
        $this->pdo = $pdo;
    }
    function addEntry($name)
    {
        $this->pdo->exec("insert into address (company, address1, city, postcode, country, date_of_entry) values ('".addslashes($name)."', 'foo', 'foo', 'foo' , 'foo', date())");
    }
}