<?php
namespace TestDbAcleTests\TestDbAcle\Psv;

class PsvParserTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase{

    function test_parsePsv() {

        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "
        id  |first_name   |last_name    | #comment
        10  |john         |miller       |ignored
        20  |stu          |Smith        |ignore
    	";

        $expectedArray = array(
            array("id" => "10",
                "first_name" => "john",
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertEquals($expectedArray, $psvParser->parsePsv($psvToParse));
    }

    function test_parsePsv_WithNull() {

        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "
        id  |first_name   |last_name    | #comment
        10  |NULL         |miller       |ignored
        20  |stu          |Smith        |ignore
    	";

        $expectedArray = array(
            array("id" => "10",
                "first_name" => NULL,
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertSame($expectedArray, $psvParser->parsePsv($psvToParse));
    }

    function test_parsePsv_WithEscapedCharacters() {

        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = '
        id  |first_name   |last_name        | #comment
        10  |NULL         |\[\|\#miller\\\] |ignored
        20  |stu          |Smith            |ignore
        ';

        $expectedArray = array(
            array("id" => "10",
                "first_name" => NULL,
                "last_name" => "[|#miller\]"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertSame($expectedArray, $psvParser->parsePsv($psvToParse));
    }
    
    function test_parsePsv_WithNull_AndCommentInLastHeader() {

        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "
        id  |first_name  #moo  |last_name    
        10  |NULL  #nul        |miller       
        20  |stu               |Smith        
    	";

        $expectedArray = array(
            array("id" => "10",
                "first_name" => NULL,
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertSame($expectedArray, $psvParser->parsePsv($psvToParse));
    }
    
    function test_parsePsv_WithNull_AndCommentInHeaders() {

        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "
        id  |first_name    |last_name    #comment
        10  |NULL  #nul    |miller       
        20  |stu           |Smith        
    	";

        $expectedArray = array(
            array("id" => "10",
                "first_name" => NULL,
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertSame($expectedArray, $psvParser->parsePsv($psvToParse));
    }
    
    function test_parsePsv_WithNull_AndInlineComment() {

        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "
        id  |first_name    |last_name    | #comment
        10  |NULL  #nul    |miller       |ignored
        20  |stu           |Smith        |ignore
    	";

        $expectedArray = array(
            array("id" => "10",
                "first_name" => NULL,
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertSame($expectedArray, $psvParser->parsePsv($psvToParse));
    }

    function test_parsePsvTree() {
        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "
        [expression1|mode:replace;identifiedBy:first_name,last_name]
        id  |first_name   |last_name
        10  |john         |\[miller\]
        #----------------------------------
        #these 3 lines are igored
        #----------------------------------
        20  |stu          |Smith\"

          [ expression2]
         col1  |col2    |col3
         1     |moo  #inline comment    |foo
         30     |miaow          |boo #another inline comment
         #the misaligned  above should be parsed ok as if it were aligned
         
#comments can be inserted anywhere between tables
        
         [empty]
         
#comments can be the last thing
        ";

        $parsedTree = $psvParser->parsePsvTree($psvToParse);
        
        
        $this->assertEquals(
            array(
                array("id" => "10",
                    "first_name" => "john",
                    "last_name" => "[miller]"),
                array("id" => "20",
                    "first_name" => "stu",
                    "last_name" => 'Smith"'),
                ), 
                $parsedTree->getTable(0)->toArray());
        
        $this->assertEquals('expression1', $parsedTree->getTable(0)->getName());
        
        $this->assertEquals('expression2', $parsedTree->getTable(1)->getName());
        
        $this->assertEquals(
            array(
                array("col1" => "1",
                    "col2" => "moo",
                    "col3" => "foo"),
                array("col1" => "30",
                    "col2" => "miaow",
                    "col3" => "boo")),
             $parsedTree->getTable(1)->toArray());
        
        $this->assertEquals(array(
                       'mode'=> 'replace',
                       'identifiedBy' => array('first_name','last_name') ), $parsedTree->getTable(0)->getMeta()->toArray());
        
        $this->assertEquals(array(), $parsedTree->getTable(1)->getMeta()->toArray());

        $this->assertEquals(array (
                'expression1' =>
                    array (
                        0 =>
                            array (
                                'id' => '10',
                                'first_name' => 'john',
                                'last_name' => '[miller]',
                            ),
                        1 =>
                            array (
                                'id' => '20',
                                'first_name' => 'stu',
                                'last_name' => 'Smith"',
                            ),
                    ),
                'expression2' =>
                    array (
                        0 =>
                            array (
                                'col1' => '1',
                                'col2' => 'moo',
                                'col3' => 'foo',
                            ),
                        1 =>
                            array (
                                'col1' => '30',
                                'col2' => 'miaow',
                                'col3' => 'boo',
                            ),
                    ),
                'empty' =>
                    array (
                    ),
            ), $parsedTree->toArray());

        $this->assertEquals(array(0, 1, 2), $parsedTree->getTableIndexes());
        $this->assertEquals(array('expression1','expression2','empty'), $parsedTree->getTableNames());


    }


    function test_parsePsvTree_withOneColumnOfZeros() {
        $psvParser = new \TestDbAcle\Psv\PsvParser();

        $psvToParse = "

          [ expression2]
         col1
         0
         0
        ";

        $parsedTree = $psvParser->parsePsvTree($psvToParse);




        $this->assertEquals(array (
            'expression2' =>
                array (
                    0 =>
                        array (
                            'col1' => '0',
                        ),
                    1 =>
                        array (
                            'col1' => '0',
                        ),
                ),
        ), $parsedTree->toArray());

    }
}
