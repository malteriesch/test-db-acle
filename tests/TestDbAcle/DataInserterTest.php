<?php

class DataInserterTest extends \PHPUnit_Framework_TestCase
{
    protected function createMock($className, $methods= array(), $constructorParameters = array())
    {
         $mock = $this->getMock( $className,
                $methods,
                $constructorParameters,
                "_Mock_".uniqid($className) );
        return $mock;
    }
    
    protected function createMockStatement($returnedData)
    {
        $mockPdoStatement = $this->createMock('PDOStatement', array('fetchAll'));
        $mockPdoStatement->expects($this->any())
                                        ->method('fetchAll')
                                        ->with(\PDO::FETCH_ASSOC)
                                        ->will($this->returnValue($returnedData));
        return $mockPdoStatement;
    }
 
    public function test_process()
    {
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
        
        
        
        $mockPdo = $this->createMock('MockPDO', array('prepare','exec','query'));
        
        $mockPdoStatementUser = $this->createMockStatement($userTableDescribe);
        $mockPdoStatementStuff = $this->createMockStatement($stuffTableDescribe);
        $mockPdoStatementEmptyTable = $this->createMockStatement(array( array('Field' => 'id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => NULL, 'Extra' => 'auto_increment')));
        
        $mockPdo->expects($this->any())
                ->method('query')
                ->will($this->returnValueMap(
                    array(
                        array('describe user', $mockPdoStatementUser),
                        array('describe stuff', $mockPdoStatementStuff),
                        array('describe emptyTable', $mockPdoStatementEmptyTable),
                    )
        ));
        
        
       
        $dataTree = array(
            "user" => array(
                array("user_id"       => "10",
                    "first_name" => "john",
                    "last_name"  => "miller"),
                array("user_id"       => "20",
                    "first_name" => "stu",
                    "last_name"  => "Smith")),
            "stuff" => array(
                array("col1" => "1",
                    "col2"   => "moo",
                    "col3"   => "NULL"),
                array("col1" => "30",
                    "col2"   => "miaow",
                    "col3"   => "boo")),
            'emptyTable'=>array()
        );
        
        //call positions 0 - 2 are the query method mocked above
        $mockPdo->expects($this->at(3))->method('exec')->with("TRUNCATE TABLE user");//
        $mockPdo->expects($this->at(4))->method('exec')->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '10', 'john', 'miller' )");
        $mockPdo->expects($this->at(5))->method('exec')->with("INSERT INTO user ( user_id, first_name, last_name ) VALUES ( '20', 'stu', 'Smith' )");
        $mockPdo->expects($this->at(6))->method('exec')->with("TRUNCATE TABLE stuff");
        $mockPdo->expects($this->at(7))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '1', 'moo', NULL )");
        $mockPdo->expects($this->at(8))->method('exec')->with("INSERT INTO stuff ( col1, col2, col3 ) VALUES ( '30', 'miaow', 'boo' )");
        $mockPdo->expects($this->at(9))->method('exec')->with("TRUNCATE TABLE emptyTable");
        
        $dataInserter = new \TestDbAcle\Db\DataInserter($mockPdo);
        $dataInserter->process($dataTree);
       
        
        
    }
}


class MockPDO extends PDO
{
    public function __construct ()
    {}
    
 
}