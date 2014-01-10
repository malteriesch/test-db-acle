<?php

class DataInserterTest extends \PHPUnit_Framework_TestCase {

    public function teardown() {
        \Mockery::close();
    }
    
    public function test_process() {
        
        $mockPdoFacade =  Mockery::mock('\TestDbAcle\Db\PdoFacade');

        $dataTree = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array("user_id" => "10",
                        "first_name" => "john",
                        "last_name" => "miller"),
                    array("user_id" => "20",
                        "first_name" => "stu",
                        "last_name" => "Smith")
                ),
            ),
            "stuff" => array(
                'meta' => array(),
                'data' => array(
                    array("col1" => "1",
                        "col2" => "moo",
                        "col3" => "NULL"),
                    array("col1" => "30",
                        "col2" => "miaow",
                        "col3" => "boo")),
            ),
            'emptyTable' => array('meta' => array(),'data'=>array())
        );

        $mockPdoFacade->shouldReceive('clearTable')->once()->with('user')->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '10', 'john', 'miller' )")->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '20', 'stu', 'Smith' )")->ordered();
        $mockPdoFacade->shouldReceive('clearTable')->once()->with('stuff')->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '1', 'moo', NULL )")->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '30', 'miaow', 'boo' )")->ordered();
        $mockPdoFacade->shouldReceive('clearTable')->once()->with('emptyTable')->ordered();
        
        $dataInserter = new \TestDbAcle\Db\DataInserter\DataInserter($mockPdoFacade);
        $dataInserter->process($dataTree);
    }
    
    public function test_process_upsertEventsGetCalled() {
        
        $mockPdoFacade =  Mockery::mock('\TestDbAcle\Db\PdoFacade');

        $dataTree = array(
            "user" => array(
                'meta' => array(),
                'data' => array(
                    array("user_id" => "10",
                        "first_name" => "john",
                        "last_name" => "miller"),
                    array("user_id" => "20",
                        "first_name" => "stu",
                        "last_name" => "Smith")
                ),
            ),
        );
        
        $checkUpserterClosure = function ($upserter){
            $this->assertTrue($upserter instanceOf \TestDbAcle\Db\DataInserter\Sql\InsertBuilder);
            $this->assertEquals('user', $upserter->getTableName());
            return true;
        };
        
        $testListener1 = \Mockery::mock('\TestDbAcle\Db\DataInserter\UpsertListenerInterface');
        $testListener2 = \Mockery::mock('\TestDbAcle\Db\DataInserter\UpsertListenerInterface');

        $mockPdoFacade->shouldReceive('clearTable')->once()->with('user')->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '10', 'john', 'miller' )")->ordered();
        $testListener1->shouldReceive('afterUpsert')->once()->with(\Mockery::on($checkUpserterClosure));
        $testListener2->shouldReceive('afterUpsert')->once()->with(\Mockery::on($checkUpserterClosure));
        
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '20', 'stu', 'Smith' )")->ordered();
        $testListener1->shouldReceive('afterUpsert')->once()->with(\Mockery::on($checkUpserterClosure));
        $testListener2->shouldReceive('afterUpsert')->once()->with(\Mockery::on($checkUpserterClosure));
        
        $dataInserter = new \TestDbAcle\Db\DataInserter\DataInserter($mockPdoFacade);
        $dataInserter->addUpsertListener($testListener1);
        $dataInserter->addUpsertListener($testListener2);
       
        $dataInserter->process($dataTree);
    }
   
    public function test_process_withReplace() {
        
        $mockPdoFacade =  Mockery::mock('\TestDbAcle\Db\PdoFacade');

        $dataTree = array(
            "user" => array(
                'meta' => array(
                    'mode'=> 'replace',
                     'identifiedBy' => array('first_name','last_name')
                ),
                'data' => array(
                    array("user_id" => "10",
                        "first_name" => "john",
                        "last_name" => "miller"),
                    array("user_id" => "20",
                        "first_name" => "stu",
                        "last_name" => "Smith"),
                    array("user_id" => "30",
                        "first_name" => "stuart",
                        "last_name" => "Smith")
                ),
                
            ),
            "stuff" => array(
                'meta' => array(),
                'data' => array(
                    array("col1" => "1",
                        "col2" => "moo",
                        "col3" => "NULL"),
                    array("col1" => "30",
                        "col2" => "miaow",
                        "col3" => "boo")),
            ),
            'emptyTable' => array('meta' => array(),'data'=>array())
        );
        $mockPdoFacade->shouldReceive('recordExists')->once()->with("user",array('first_name'=>'john','last_name'=>'miller'))->andReturn(true)->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("UPDATE user SET user_id='10', first_name='john', last_name='miller' WHERE first_name='john' AND last_name='miller'")->ordered();
        $mockPdoFacade->shouldReceive('recordExists')->once()->with("user",array('first_name'=>'stu','last_name'=>'Smith'))->andReturn(true)->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("UPDATE user SET user_id='20', first_name='stu', last_name='Smith' WHERE first_name='stu' AND last_name='Smith'")->ordered();
        $mockPdoFacade->shouldReceive('recordExists')->once()->with("user",array('first_name'=>'stuart','last_name'=>'Smith'))->andReturn(false)->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '30', 'stuart', 'Smith' )")->ordered();
        $mockPdoFacade->shouldReceive('clearTable')->once()->with('stuff')->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '1', 'moo', NULL )")->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '30', 'miaow', 'boo' )")->ordered();
        $mockPdoFacade->shouldReceive('clearTable')->once()->with('emptyTable')->ordered();
        
        $dataInserter = new \TestDbAcle\Db\DataInserter\DataInserter($mockPdoFacade);
        $dataInserter->process($dataTree);
    }
    
    public function test_process_withReplace_OneIdentifiedByFieldOnly() {
        
        $mockPdoFacade =  Mockery::mock('\TestDbAcle\Db\PdoFacade');

        $dataTree = array(
            "user" => array(
                'meta' => array(
                    'mode'=> 'replace',
                     'identifiedBy' => array('first_name')
                ),
                'data' => array(
                    array("user_id" => "10",
                        "first_name" => "john",
                        "last_name" => "miller"),
                    array("user_id" => "20",
                        "first_name" => "stu",
                        "last_name" => "Smith"),
                    array("user_id" => "30",
                        "first_name" => "stuart",
                        "last_name" => "Smith")
                ),
                
            ),
        );
        
        
        $mockPdoFacade->shouldReceive('recordExists')->once()->with("user",array('first_name'=>'john'))->andReturn(true)->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("UPDATE user SET user_id='10', first_name='john', last_name='miller' WHERE first_name='john'")->ordered();
        $mockPdoFacade->shouldReceive('recordExists')->once()->with("user",array('first_name'=>'stu'))->andReturn(true)->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("UPDATE user SET user_id='20', first_name='stu', last_name='Smith' WHERE first_name='stu'")->ordered();
        $mockPdoFacade->shouldReceive('recordExists')->once()->with("user",array('first_name'=>'stuart'))->andReturn(false)->ordered();
        $mockPdoFacade->shouldReceive('executeSql')->once()->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '30', 'stuart', 'Smith' )")->ordered();
        
        $dataInserter = new \TestDbAcle\Db\DataInserter\DataInserter($mockPdoFacade);
        $dataInserter->process($dataTree);
    }
    
}
