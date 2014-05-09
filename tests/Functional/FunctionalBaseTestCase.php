<?php
namespace TestDbAcleTests\Functional;

class FunctionalBaseTestCase extends \TestDbAcle\PhpUnit\AbstractTestCase
{
    function providePdo()
    {
       $config = include(__DIR__."/config.php");
       return new \Pdo("mysql:dbname={$config['db_name']};host={$config['db_host']}",$config['db_user'],$config['db_password']);
    }
}
