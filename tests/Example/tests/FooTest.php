<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    
    function test_Test()
    {
        return;
        $testDbAcle = \TestDbAcle\TestDbAcle::create(new \Pdo("mysql:dbname=walt_tests;host=localhost",'root',''));
        $testDbAcle->setupTables("
            [address]
            address_id  |company
            1           |me
            3           |you
        ");
        
        $testDbAcle->setupTables("
            [address|mode:replace;identifiedBy:address_id]
            address_id  |company
            3           |me
            4           |you
        ");
    }
    
}