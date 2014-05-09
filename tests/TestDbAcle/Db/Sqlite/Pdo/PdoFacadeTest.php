<?php
namespace TestDbAcleTests\TestDbAcle\Db\Sqlite\Pdo;

class PdoFacadeTest extends \TestDbAcleTests\TestDbAcle\Db\BasePdoFacadeTestCase  
{
    
    
    protected function setUp()
    {
        $this->mockPdo = \TestDbAcle\PhpUnit\Mocks\MockablePdo::createMock($this, array('prepare', 'exec', 'query','setAttribute', 'lastInsertId'));
        
        $this->pdoFacade = new \TestDbAcle\Db\Sqlite\Pdo\PdoFacade($this->mockPdo);
    }
    
    function test_disableForeignKeyChecks()
    {        
        $this->pdoFacade->disableForeignKeyChecks();
    }

    function test_setAutoIncrement()
    {
        $this->mockPdo->expects($this->once())
                ->method('query')
                ->with("UPDATE sqlite_sequence SET seq=100-1 WHERE name='address';");

        $this->pdoFacade->setAutoIncrement("address", 100);
    }
    
    
    function test_clearTable()
    {
        $this->mockPdo->expects($this->once())->method('exec')->with("DELETE FROM user;DELETE FROM sqlite_sequence WHERE name='user'"); 
        $this->pdoFacade->clearTable('user');
    }
    
    
    function test_describeTable()
    {
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('PRAGMA table_info(user)')->will($this->returnValue($this->createMockStatement(array('table description'))));
        
        $this->assertEquals(array('table description'), $this->pdoFacade->describeTable('user'));
    }
    
}