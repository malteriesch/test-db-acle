<?php
require_once(__DIR__.'/../../FunctionalBaseTestCaseUsingTraits.php');

class TraitsSmokeTest extends FunctionalBaseTestCaseUsingTraits 
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


        $this->setAutoIncrement('address', 1000);

        $this->pdo->exec("insert into address (company, date_of_entry) values ('them',now())");

        
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
    
                             
}
