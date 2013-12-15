<?php

require_once(__DIR__.'/../Mocks/MockablePdo.php');

class PdoFacadeTest extends \PHPUnit_Framework_TestCase {
    
    protected $pdoFacade;
    protected $mockPdo;
    
    protected function createMock($className, $methods = array(), $constructorParameters = array()) {
        $mock = $this->getMock($className, $methods, $constructorParameters, "_Mock_" . uniqid($className));
        return $mock;
    }

    protected function createMockStatement($returnedData) {
        $mockPdoStatement = $this->createMock('PDOStatement', array('fetchAll'));
        $mockPdoStatement->expects($this->any())
                ->method('fetchAll')
                ->with(\PDO::FETCH_ASSOC)
                ->will($this->returnValue($returnedData));
        return $mockPdoStatement;
    }
    
    
    
    protected function setUp()
    {
        $this->mockPdo = $this->createMock('MockablePDO', array('prepare', 'exec', 'query'));
        $this->pdoFacade = new \TestDbAcle\Db\PdoFacade($this->mockPdo);
    }
    
    function test_clearTable()
    {
        $this->mockPdo->expects($this->once())->method('exec')->with("TRUNCATE TABLE user"); 
        $this->pdoFacade->clearTable('user');
    }
    
    
    function test_getQuery()
    {
        $returnedRows = array(array('row1',array('row2')));
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('select * from foo')->will($this->returnValue($this->createMockStatement($returnedRows)));
        
        $this->assertEquals($returnedRows, $this->pdoFacade->getQuery('select * from foo'));
    }
    
    function test_getQuery_empty()
    {
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('select * from foo')->will($this->returnValue(false));
        
        $this->assertNull( $this->pdoFacade->getQuery('select * from foo'));
    }
    
    function test_describeTable()
    {
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('DESCRIBE user')->will($this->returnValue($this->createMockStatement(array('table description'))));
        
        $this->assertEquals(array('table description'), $this->pdoFacade->describeTable('user'));
    }
}