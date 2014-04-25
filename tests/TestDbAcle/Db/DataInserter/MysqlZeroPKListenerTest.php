<?php

class MysqlZeroPKListenerTest extends \PHPUnit_Framework_TestCase {

    protected $mockPdoFacade;
    protected $mockTableInfo;
    
    protected function setup()
    {
        $this->mockPdoFacade = \Mockery::mock('\TestDbAcle\Db\PdoFacade');
        $this->mockTableInfo = \Mockery::mock('\TestDbAcle\Db\TableInfo');
        $this->listener = new \TestDbAcle\Db\DataInserter\Listeners\MysqlZeroPKListener($this->mockPdoFacade,  $this->mockTableInfo);
    }
    
    public function teardown() {
        \Mockery::close();
    }
    
    protected function configureMockTable($columnName, $expectedPrimaryKey)
    {
        $mockTable = \Mockery::mock('TestDbAcle\Db\Table\Table');
        $this->mockTableInfo->shouldReceive("getTable")->once()->with($columnName)->andReturn($mockTable);
        $mockTable->shouldReceive("getPrimaryKey")->once()->andReturn($expectedPrimaryKey);
    }
    
    public function test_afterUpsert_NoInsertBuilder()
    {
        $upsertBuilder = Mockery::mock('\TestDbAcle\Db\DataInserter\Sql\UpdateBuilder');
        $upsertBuilder->shouldReceive("getTableName")->never();
        
        $this->mockPdoFacade->shouldReceive("executeSql")->never();
        
        $this->listener->afterUpsert($upsertBuilder);
    }
    
    public function test_afterUpsert_NoPrimaryKey()
    {
        $upsertBuilder = Mockery::mock('\TestDbAcle\Db\DataInserter\Sql\InsertBuilder');
        $upsertBuilder->shouldReceive("getTableName")->once()->andReturn('user');
        
        $this->configureMockTable('user', null);
        
        $this->mockPdoFacade->shouldReceive("executeSql")->never();
        
        $this->listener->afterUpsert($upsertBuilder);
    }
    public function test_afterUpsert_PrimaryKeyValueIsNotZero()
    {
        $upsertBuilder = Mockery::mock('\TestDbAcle\Db\DataInserter\Sql\InsertBuilder');
        $upsertBuilder->shouldReceive("getTableName")->once()->andReturn('user');
        
        $this->configureMockTable('user', 'user_id');
        
        $upsertBuilder->shouldReceive("getColumn")->once()->with('user_id')->andReturn(3);
        
        $this->mockPdoFacade->shouldReceive("executeSql")->never();
        
        $this->listener->afterUpsert($upsertBuilder);
    }
    
    public function test_afterUpsert_PrimaryKeyValueIsNotNumeric()
    {
        $upsertBuilder = Mockery::mock('\TestDbAcle\Db\DataInserter\Sql\InsertBuilder');
        $upsertBuilder->shouldReceive("getTableName")->once()->andReturn('user');
        
        $this->configureMockTable('user', 'user_id');
        
        $upsertBuilder->shouldReceive("getColumn")->once()->with('user_id')->andReturn('some_user');
        
        $this->mockPdoFacade->shouldReceive("executeSql")->never();
        
        $this->listener->afterUpsert($upsertBuilder);
    }
    
    public function test_afterUpsert_success()
    {
        $upsertBuilder = Mockery::mock('\TestDbAcle\Db\DataInserter\Sql\InsertBuilder');
        $upsertBuilder->shouldReceive("getTableName")->once()->andReturn('user');
        
        $this->mockPdoFacade->shouldReceive("lastInsertId")->once()->andReturn(8);
        $this->mockPdoFacade->shouldReceive("executeSql")->once()->with("update user set user_id = 0 where user_id = 8");
        
        $this->configureMockTable('user', 'user_id');
        
        $upsertBuilder->shouldReceive("getColumn")->once()->with('user_id')->andReturn(0);
        $this->listener->afterUpsert($upsertBuilder);
    }
    
}
