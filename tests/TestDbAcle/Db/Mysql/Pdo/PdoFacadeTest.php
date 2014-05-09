<?php
namespace TestDbAcleTests\TestDbAcle\Db\Mysql\Pdo;

class PdoFacadeTest extends \TestDbAcleTests\TestDbAcle\Db\BasePdoFacadeTestCase  
{
    
    
    protected function setUp()
    {
        $this->mockPdo = \TestDbAcle\PhpUnit\Mocks\MockablePdo::createMock($this, array('prepare', 'exec', 'query','setAttribute', 'lastInsertId'));
        
        $this->pdoFacade = new \TestDbAcle\Db\Mysql\Pdo\PdoFacade($this->mockPdo);
    }
    
    function test_disableForeignKeyChecks()
    {
        $this->mockPdo->expects($this->once())
                ->method('query')
                ->with("SET FOREIGN_KEY_CHECKS = 0");
        
        $this->pdoFacade->disableForeignKeyChecks();
    }

    function test_setAutoIncrement()
    {
        $this->mockPdo->expects($this->once())
                ->method('query')
                ->with("ALTER TABLE address AUTO_INCREMENT = 100");

        $this->pdoFacade->setAutoIncrement("address", 100);
    }
    
    
    function test_clearTable()
    {
        $this->mockPdo->expects($this->once())->method('exec')->with("TRUNCATE TABLE user"); 
        $this->pdoFacade->clearTable('user');
    }
    
    
    function test_describeTable()
    {
        $this->mockPdo->expects($this->once())
                      ->method('query')->with('DESCRIBE user')->will($this->returnValue($this->createMockStatement(array('table description'))));
        
        $this->assertEquals(array('table description'), $this->pdoFacade->describeTable('user'));
    }
    
}