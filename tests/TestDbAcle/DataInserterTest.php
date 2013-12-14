<?php
require_once(__DIR__.'/Mocks/MockablePdo.php');

class DataInserterTest extends \PHPUnit_Framework_TestCase {

    //@TODO refactor: Simplify and test by using mocked PdoFacade and TableInfo instead
    
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

    public function test_process() {
        $tableInfo = new \TestDbAcle\Db\TableInfo();
        
        $userTableDescribe = array(
            array('Field' => 'user_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'first_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => 'foo', 'Extra' => ''),
            array('Field' => 'last_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );
        
        

        $stuffTableDescribe = array(
            array('Field' => 'stuff_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'col1', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col2', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col3', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );
        
        $emptyTableTableDescribe = array(array('Field' => 'id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'));

        $tableInfo->addTableDescription('user' ,$userTableDescribe);
        $tableInfo->addTableDescription('stuff',$stuffTableDescribe);
        $tableInfo->addTableDescription('emptyTable',$emptyTableTableDescribe);


        $mockPdo = $this->createMock('MockablePDO', array('prepare', 'exec', 'query'));

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

        $mockPdo->expects($this->at(0))->method('exec')->with("TRUNCATE TABLE user"); 
        $mockPdo->expects($this->at(1))->method('exec')->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '10', 'john', 'miller' )");
        $mockPdo->expects($this->at(2))->method('exec')->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '20', 'stu', 'Smith' )");
        $mockPdo->expects($this->at(3))->method('exec')->with("TRUNCATE TABLE stuff");
        $mockPdo->expects($this->at(4))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '1', 'moo', NULL )");
        $mockPdo->expects($this->at(5))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '30', 'miaow', 'boo' )");
        $mockPdo->expects($this->at(6))->method('exec')->with("TRUNCATE TABLE emptyTable");

        $dataInserter = new \TestDbAcle\Db\DataInserter(new \TestDbAcle\Db\PdoFacade($mockPdo), $tableInfo);
        $dataInserter->process($dataTree);
    }
    
    public function test_process_withReplace() {
        
        $tableInfo = new \TestDbAcle\Db\TableInfo();
        
        $userTableDescribe = array(
            array('Field' => 'user_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'first_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'last_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );

        $stuffTableDescribe = array(
            array('Field' => 'stuff_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'col1', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col2', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col3', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );

        $emptyTableTableDescribe = array(array('Field' => 'id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'));

        $tableInfo->addTableDescription('user' ,$userTableDescribe);
        $tableInfo->addTableDescription('stuff',$stuffTableDescribe);
        $tableInfo->addTableDescription('emptyTable',$emptyTableTableDescribe);

        $mockPdo = $this->createMock('MockablePDO', array('prepare', 'exec', 'query'));


        $mockPdo->expects($this->any())
                ->method('query')
                ->will($this->returnValueMap(
                                array(
                                    array("SELECT COUNT(*) N FROM user WHERE first_name='john' AND last_name='miller'",$this->createMockStatement(array(array('N'=>1)))),
                                    array("SELECT COUNT(*) N FROM user WHERE first_name='stu' AND last_name='Smith'",$this->createMockStatement(array(array('N'=>1)))),
                                    array("SELECT COUNT(*) N FROM user WHERE first_name='stuart' AND last_name='Smith'",$this->createMockStatement(array(array('N'=>0)))),
                                )
        ));



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

        $mockPdo->expects($this->at(1))->method('exec')->with("UPDATE user SET user_id='10', first_name='john', last_name='miller' WHERE first_name='john' AND last_name='miller'");
        $mockPdo->expects($this->at(3))->method('exec')->with("UPDATE user SET user_id='20', first_name='stu', last_name='Smith' WHERE first_name='stu' AND last_name='Smith'");
        $mockPdo->expects($this->at(5))->method('exec')->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '30', 'stuart', 'Smith' )");
        $mockPdo->expects($this->at(6))->method('exec')->with("TRUNCATE TABLE stuff");
        $mockPdo->expects($this->at(7))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '1', 'moo', NULL )");
        $mockPdo->expects($this->at(8))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '30', 'miaow', 'boo' )");
        $mockPdo->expects($this->at(9))->method('exec')->with("TRUNCATE TABLE emptyTable");

        $dataInserter = new \TestDbAcle\Db\DataInserter(new \TestDbAcle\Db\PdoFacade($mockPdo), $tableInfo);
        $dataInserter->process($dataTree);
    }
    public function test_process_withReplace_OneIdentifiedByFieldOnly() {
        $tableInfo = new \TestDbAcle\Db\TableInfo();
        
        $userTableDescribe = array(
            array('Field' => 'user_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'first_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'last_name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );

        $stuffTableDescribe = array(
            array('Field' => 'stuff_id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'),
            array('Field' => 'col1', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col2', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => ''),
            array('Field' => 'col3', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => NULL, 'Extra' => '')
        );

        $emptyTableTableDescribe = array(array('Field' => 'id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment'));

        $tableInfo->addTableDescription('user' ,$userTableDescribe);
        $tableInfo->addTableDescription('stuff',$stuffTableDescribe);
        $tableInfo->addTableDescription('emptyTable',$emptyTableTableDescribe);

        $mockPdo = $this->createMock('MockablePDO', array('prepare', 'exec', 'query'));

        $mockPdoStatementUser = $this->createMockStatement($userTableDescribe);
        $mockPdoStatementStuff = $this->createMockStatement($stuffTableDescribe);
        $mockPdoStatementEmptyTable = $this->createMockStatement(array(array('Field' => 'id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment')));

        $mockPdo->expects($this->any())
                ->method('query')
                ->will($this->returnValueMap(
                                array(
                                    array("SELECT COUNT(*) N FROM user WHERE first_name='john'",$this->createMockStatement(array(array('N'=>1)))),
                                    array("SELECT COUNT(*) N FROM user WHERE first_name='stu'",$this->createMockStatement(array(array('N'=>1)))),
                                    array("SELECT COUNT(*) N FROM user WHERE first_name='stuart'",$this->createMockStatement(array(array('N'=>0)))),
                                )
        ));



        $dataTree = array(
            "user" => array(
                'meta' => array(
                    'mode'=> 'replace',
                     'identifiedBy' => 'first_name',
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

        $mockPdo->expects($this->at(1))->method('exec')->with("UPDATE user SET user_id='10', first_name='john', last_name='miller' WHERE first_name='john'");
        $mockPdo->expects($this->at(3))->method('exec')->with("UPDATE user SET user_id='20', first_name='stu', last_name='Smith' WHERE first_name='stu'");
        $mockPdo->expects($this->at(5))->method('exec')->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '30', 'stuart', 'Smith' )");
        $mockPdo->expects($this->at(6))->method('exec')->with("TRUNCATE TABLE stuff");
        $mockPdo->expects($this->at(7))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '1', 'moo', NULL )");
        $mockPdo->expects($this->at(8))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '30', 'miaow', 'boo' )");
        $mockPdo->expects($this->at(9))->method('exec')->with("TRUNCATE TABLE emptyTable");

        $dataInserter = new \TestDbAcle\Db\DataInserter(new \TestDbAcle\Db\PdoFacade($mockPdo), $tableInfo);
        $dataInserter->process($dataTree);
    }

}
