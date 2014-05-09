<?php

namespace TestDbAcleTests\TestDbAcle\Db;
class AbstractPdoFacadeTest extends \TestDbAcleTests\TestDbAcle\Db\BasePdoFacadeTestCase 
{
    
    protected function setUp()
    {
        $this->mockPdo = \TestDbAcle\PhpUnit\Mocks\MockablePdo::createMock($this, array('prepare', 'exec', 'query','setAttribute', 'lastInsertId'));
        
        $this->pdoFacade = $this->getMockForAbstractClass('TestDbAcle\Db\AbstractPdoFacade',array($this->mockPdo));
    }
    
    function test_enableExceptions()
    {
        $this->mockPdo->expects($this->once())
                ->method('setAttribute')
                ->with(\Pdo::ATTR_ERRMODE, \Pdo::ERRMODE_EXCEPTION);
        
        $this->pdoFacade->enableExceptions();
    }
    
  
    function test_getQuery()
    {
        $returnedRows = array(array('row1',array('row2')));
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('select * from foo')->will($this->returnValue($this->createMockStatement($returnedRows)));
        
        $this->assertEquals($returnedRows, $this->pdoFacade->getQuery('select * from foo'));
    }
    
    function test_executeSql()
    {
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('update foo');
        
        $this->pdoFacade->getQuery('update foo');
    }
    
    function test_getQuery_empty()
    {
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('select * from foo')->will($this->returnValue(false));
        
        $this->assertNull( $this->pdoFacade->getQuery('select * from foo'));
    }
    
    function test_recordExists_True(){
        $this->mockPdo->expects($this->once())
                      ->method('query')->with("SELECT COUNT(*) N FROM user WHERE first_name='john' AND last_name='miller'")->will($this->returnValue($this->createMockStatement(array(array('N'=>1)))));
        
        $this->assertTrue($this->pdoFacade->recordExists('user', array('first_name'=>'john','last_name'=>'miller')));
        
    }
    
    function test_recordExists_False(){
        $this->mockPdo->expects($this->once())
                      ->method('query')->with("SELECT COUNT(*) N FROM user WHERE first_name='john' AND last_name='miller'")->will($this->returnValue($this->createMockStatement(array(array('N'=>0)))));
        
        $this->assertFalse($this->pdoFacade->recordExists('user', array('first_name'=>'john','last_name'=>'miller')));
    }
    
    function test_lastInsertId()
    {
        $this->mockPdo->expects($this->once())
                      ->method('lastInsertId')->will($this->returnValue(11));
        
        $this->assertEquals(11, $this->pdoFacade->lastInsertId());
    }
    
}