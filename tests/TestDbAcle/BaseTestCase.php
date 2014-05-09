<?php

namespace TestDbAcleTests\TestDbAcle;

class BaseTestCase extends \PHPUnit_Framework_TestCase  
{
    public function teardown()
    {
        \Mockery::close();
    }
}
