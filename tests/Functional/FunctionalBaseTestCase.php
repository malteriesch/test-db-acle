<?php

class FunctionalBaseTestCase extends TestDbAcle\PhpUnit\AbstractTestCase
{
    protected $pdo;
    
    function getPdo()
    {
        if (!isset($this->pdo)){
            $config = include(__DIR__."/config.php");
            $this->pdo = new \Pdo("mysql:dbname={$config['db_name']};host={$config['db_host']}",$config['db_user'],$config['db_password']);
        }
        return $this->pdo;
    }

}
