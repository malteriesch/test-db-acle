<?php
require_once(__DIR__.'/../FunctionalBaseTestCase.php');

class SmokeTest extends FunctionalBaseTestCase {
    
    function test_Test()
    {
        $this->getPdo()->query('CREATE TEMPORARY TABLE `address` (
                                `address_id` int(11) NOT NULL AUTO_INCREMENT,
                                `company` varchar(100) NOT NULL,
                                `address1` varchar(100) NOT NULL,
                                `address2` varchar(100) DEFAULT NULL,
                                `address3` varchar(100) DEFAULT NULL,
                                `city` varchar(100) NOT NULL,
                                `postcode` varchar(100) NOT NULL,
                                `country` varchar(100) NOT NULL,
                                PRIMARY KEY (`address_id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');


        $this->getPdo()->query('CREATE TEMPORARY TABLE `user` (
                                `user_id` int(11) NOT NULL AUTO_INCREMENT,
                                `name` varchar(100) NOT NULL,
                                PRIMARY KEY (`user_id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');
        
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
            ");
        try{
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
            $this->fail("assertTableStateContains fails if the information in the table is different ");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e){
          //do nothing
        }
                
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
        $this->pdo->exec("insert into address (company) values ('".addslashes($name)."')");
    }
}