<?php

class TraitsSmokeTest extends \TestDbAcleTests\Functional\FunctionalBaseTestCaseUsingTraits
{
    
    
    function setup()
    {
        $this->getPdo()->query('DROP TEMPORARY TABLE IF EXISTS address');
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

        $this->getPdo()->query('DROP TEMPORARY TABLE IF EXISTS user');
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


        $this->setAutoIncrement('address', 1000);

        $this->getPdo()->exec("insert into address (company, date_of_entry) values ('them',now())");

        
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
                             
}
