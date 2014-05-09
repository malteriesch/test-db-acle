<?php
namespace TestDbAcle\PhpUnit\Mocks;

class MockablePdo extends \PDO {

    public function __construct() {
        
    }

    public static function createMock(\PHPUnit_Framework_TestCase $testCase, $methods = array()) {
        $className = '\TestDbAcle\PhpUnit\Mocks\MockablePdo';
        $mock = $testCase->getMock($className, $methods, array(), "_Mock_" . uniqid('MockablePdo'));
        return $mock;
    }
}