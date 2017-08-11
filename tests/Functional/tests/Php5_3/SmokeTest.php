<?php


class SmokeTest extends \TestDbAcleTests\Functional\FunctionalBaseTestCase 
{
    
    
    function setup()
    {
        parent::setup();
        $this->getPdo()->query('CREATE TEMPORARY TABLE `address` (
                                `address_id` int(11) NOT NULL AUTO_INCREMENT,
                                `company` varchar(100) NOT NULL,
                                `address1` varchar(100) NOT NULL,
                                `address2` varchar(100) DEFAULT NULL,
                                `address3` varchar(100) DEFAULT NULL,
                                `city` varchar(100) NOT NULL,
                                `postcode` varchar(100) NOT NULL,
                                `country` varchar(100) NOT NULL,
                                `date_of_entry` DATETIME,
                                PRIMARY KEY (`address_id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');


        $this->getPdo()->query('CREATE TEMPORARY TABLE `user` (
                                `user_id` int(11) NOT NULL AUTO_INCREMENT,
                                `name` varchar(100) NOT NULL,
                                PRIMARY KEY (`user_id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');
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

        $exampleService = new ExampleService($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleService->addEntry("them");

        
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
    function test_SetupCanAppendData()
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


        $this->setupTables("
            [address|mode:append]
            company |city
            them    |london

        ");

        $this->assertTableStateContains("
            [address]
            address_id  |company    |city
            1           |me         |T #T is the default for non-null text
            3           |you        |T
            4           |them       |london

            [user]
            user_id |name
            1       |mary
            ", array('NOW'=>date("Y-m-d")));
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

        $exampleService = new ExampleService($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleService->addEntry("them");

        
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

        $exampleService = new ExampleService($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleService->addEntry("them");

        
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
    
    function test_SetupAndAssertWithNullColumn()
    {
        
        $this->setupTables("
            [address]
            address_id  |address2   
            1           |NULL        
            3           |here      
            1000        |there      

        ");

        $this->assertTableStateContains("
            [address]
            address_id  |address2   
            1           |NULL        
            3           |here      
            1000        |there      

            ");
        $countResult = $this->pdo->query("select count(*) n from address where address2 is NULL")->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals(1, $countResult['n']);
    }

    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand() with placeholders
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     *
     * @link https://github.com/malteriesch/test-db-acle/issues/10
     */

    function test_Setup_Bug_ReplaceModeOverwritesNonNullColumns()
    {

        $this->setupTables("
            [address]
            address_id  |address1       |postcode
            1           |val 1          |foo1
            3           |val 2          |foo2
            1000        |val3           |foo3

        ");

        $this->setupTables("
            [address|mode:replace;identifiedBy:address_id]
            address_id  |address1   
            3           |val 2 amended       
        ");

        $this->assertTableStateContains("
            [address]
           address_id   |address1           |postcode
            1           |val 1              |foo1
            3           |val 2 amended      |foo2
            1000        |val3               |foo3

            ");
    }

    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand() with placeholders
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     *
     * @link https://github.com/malteriesch/test-db-acle/issues/10
     */

    function test_Setup_Bug_2()
    {

        $this->setupTables("
            [address]
            address_id  |address2       |postcode
            1           |val 1          |foo1
            3           |val 2          |foo2
            1000        |val3           |foo3

        ");

        $this->setupTables("
            [address|mode:replace;identifiedBy:address_id]
            address_id  |address1   
            3           |val 2 amended       
        ");

        $this->assertTableStateContains("
            [address]
            address_id  |address2       |postcode
            1           |val 1          |foo1
            3           |val 2          |foo2
            1000        |val3           |foo3

            ");
    }


    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand() with placeholders
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::SetAutoIncrementCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     *
     */

    function test_Setup_ReplaceMode_takesByDefaultPrimaryColumnForIdentification()
    {

        $this->setupTables("
            [address]
            address_id  |address1       |postcode
            1           |val 1          |foo1
            3           |val 2          |foo2
            1000        |val3           |foo3

        ");

        $this->setupTables("
            [address|mode:replace]
            address_id  |address1   
            3           |val 2 amended       
        ");

        $this->assertTableStateContains("
            [address]
            address_id   |address1           |postcode
            1           |val 1              |foo1
            3           |val 2 amended      |foo2
            1000        |val3               |foo3

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

        $exampleService = new ExampleService($this->getPdo());

        $this->setAutoIncrement('address', 1000);

        $exampleService->addEntry("them");

        
        $this->assertTableStateContains("
            [address]
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
    function test_AssertTableStateContains_handlesEmptyTables()
    {
        
        $this->setupTables("
            [address]
            address_id  |company    

            [user]

        ");

        $this->assertTableStateContains("
            [address]
            address_id  |company    
            

            [user]
           
            ");
             
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_AssertTableStateContains_handlesReservedKeywords()
    {
        $this->getPdo()->query('CREATE TEMPORARY TABLE `select` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `select` varchar(100) NOT NULL,
                                PRIMARY KEY (`id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');
        
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
        $this->getPdo()->query('CREATE TEMPORARY TABLE `select` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `select` varchar(100) NOT NULL,
                                PRIMARY KEY (`id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');
        
        $this->setupTables("
            [select]
            id  |select    


        ");

        $this->assertTableStateContains("
            [select]
            id  |select    
            ");
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_AssertTableStateContains_handlesEmptyTables_raisesExceptionIfEmptyExpectedButNotEmpty()
    {
        
        $this->setupTables("
            [address]
            address_id  |company    

            [user]
            user_id |name
            1       |foo

        ");
        
        try{
            $this->assertTableStateContains("
            [address]
            address_id  |company    
            

            [user]
            user_id |name
           
            ");
            $this->fail("assertTableStateContains fails if the information in the table is different ");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
             
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterTableStateByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_AssertTableStateContains_handlesEmptyTables_raisesExceptionIfEmptyExpectedButNotEmpty_EmptyTableSpecifiedWithoutColumns()
    {
        
        $this->setupTables("
            [user]
            user_id |name
            1       |foo

        ");
        
        try{
            $this->assertTableStateContains("
            [user]
            user_id |name
           
            ");
            $this->fail("assertTableStateContains fails if the information in the table is different ");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
             
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterArrayByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_assertArrayContainsPsv()
    {
        
        $exampleArray = array(
            array('address_id' => 5, 'company' => 10, 'foo' =>1000),
            array('address_id' => 7, 'company' => 17, 'foo' =>1007),
        );
        
        $this->assertArrayContainsPsv("
            address_id  |company   
            5           |10
            7           |17
            ", $exampleArray, array(), "tests pass if the information in the array is the same for the relevant keys");
        
        
        $this->assertArrayContainsPsv("
            address_id  |company   
            5           |COMPANY_1
            ADDRESS_1   |17
            ", $exampleArray, array( 'COMPANY_1' => 10, 'ADDRESS_1' => 7), "tests pass if the information in the array is the same for the relevant keys -0 using placeholders");
        
        
        try{
            $this->assertArrayContainsPsv("
            address_id  |company   
            5           |10
            7           |16
            ", $exampleArray);
            $this->fail("assertTableStateContains fails if the information in the array is different for the relevant keys");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
        
        try{
            $this->assertArrayContainsPsv("
            address_id  |company   
            5           |COMPANY_1
            ADDRESS_1   |17
            ", $exampleArray, array( 'COMPANY_1' => 10, 'ADDRESS_1' => 8));
            $this->fail("assertTableStateContains fails if the information in the array is different for the relevant keys - using placeholders");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
        
        
        try{
            $this->assertArrayContainsPsv("
            address_id  |company   
            ", $exampleArray);
            $this->fail("assertTableStateContains fails if the information in the array is empty but actual results are not");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
        
        try{
            $this->assertArrayContainsPsv("
            address_id  |company   
            1           |5
            ", array());
            $this->fail("assertTableStateContains fails if the information in the array is not but actual results are");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
             
    }
    
    /**
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::execute()
     * @covers \TestDbAcle\Commands\FilterTableStateByPsvCommand::FilterArrayByPsvCommand()
     * @covers \TestDbAcle::getDefaultFactories()
     */
    function test_StandradDefaults()
    {

        $this->getPdo()->query('CREATE TEMPORARY TABLE `time_stuff` (
                                `time_stuff_id` int(11) NOT NULL AUTO_INCREMENT,
                                `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                PRIMARY KEY (`time_stuff_id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');

        $this->setupTables("
            [time_stuff]
            time_stuff_id    
            10
            
        ");

        $this->assertTableStateContains("
            [time_stuff]
            time_stuff_id       |last_updated
            10                  |NOW
            
        ",array('NOW'=>date("Y-m-d")));
    }


}

class ExampleService
{
    protected $pdo;
    function __construct(\Pdo $pdo){
        $this->pdo = $pdo;
    }
    function addEntry($name)
    {
        $this->pdo->exec("insert into address (company, date_of_entry) values ('".addslashes($name)."',now())");
    }
}