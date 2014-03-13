<?php

class FunctionalBaseTestCaseUsingTraits extends \PHPUnit_Framework_TestCase implements \TestDbAcle\PhpUnit\AbstractTestCaseInterface
{
    use \TestDbAcle\PhpUnit\Traits\DatabaseHelperTrait;
    
    protected $pdo;
    
    
    function getPdo()
    {
        if (!isset($this->pdo)){
            $config = include(__DIR__."/config.php");
            $this->pdo = new \Pdo("mysql:dbname={$config['db_name']};host={$config['db_host']}",$config['db_user'],$config['db_password']);
        }
        return $this->pdo;
    }

   /**
     * returns the configures TestDbAcle database helper
     * @return \TestDbAcle\TestDbAcle
     */
    public function getDatabaseTestHelper()
    {
        return $this->databaseTestHelper;
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

}
