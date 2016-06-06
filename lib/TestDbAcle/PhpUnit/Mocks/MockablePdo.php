<?php
namespace TestDbAcle\PhpUnit\Mocks;

class MockablePdo extends \PDO {

    public function __construct() {
        
    }

    public static function createMock(\PHPUnit_Framework_TestCase $testCase, $methods = array()) {
        $className = 'TestDbAcle\PhpUnit\Mocks\MockablePdo';
        $mock = $testCase->getMockBuilder($className)
            ->setMethods($methods)
            ->setMockClassName("_Mock_" . uniqid('MockablePdo'))
            ->getMock();
        return $mock;
    }
}