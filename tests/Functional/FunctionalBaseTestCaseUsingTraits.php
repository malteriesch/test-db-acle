<?php
namespace TestDbAcleTests\Functional;

class FunctionalBaseTestCaseUsingTraits extends \PHPUnit_Framework_TestCase implements \TestDbAcle\PhpUnit\AbstractTestCaseInterface
{
    use \TestDbAcle\PhpUnit\Traits\DatabaseHelperTrait;
    
    function providePdo()
    {
        $config = include(__DIR__."/config.php");
        return new \Pdo("mysql:dbname={$config['db_name']};host={$config['db_host']}",$config['db_user'],$config['db_password']);
    }
}
