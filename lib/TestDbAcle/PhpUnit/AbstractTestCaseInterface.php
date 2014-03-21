<?php
namespace TestDbAcle\PhpUnit;

interface AbstractTestCaseInterface
{
    
    
    /**
     * Override to provide the PDO connection to the test database.
     * @returns \PDO
     */
    public function createPdo();
    
    
    /**
     * returns the configured TestDbAcle database helper
     * @return \TestDbAcle\TestDbAcle
     */
    public function getDatabaseTestHelper();
}

