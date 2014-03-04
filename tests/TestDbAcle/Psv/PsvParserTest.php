<?php

class PsvParserTest extends \PHPUnit_Framework_TestCase {

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

        $expectedArray = 
            array("expression1" => array(
                'meta' => array(
                    'mode'=> 'replace',
                     'identifiedBy' => array('first_name','last_name')
                ),
                'data' => array(
                    array("id" => "10",
                        "first_name" => "john",
                        "last_name" => "[miller]"),
                    array("id" => "20",
                        "first_name" => "stu",
                        "last_name" => 'Smith"'))
            ),
            "expression2" => array(
                'meta' => array(),
                'data' => array(
                    array("col1" => "1",
                        "col2" => "moo",
                        "col3" => "foo"),
                    array("col1" => "30",
                        "col2" => "miaow",
                        "col3" => "boo"))),
            'empty' => array('meta'=>array(),'data'=>array())
        );
        $this->assertEquals($expectedArray, $psvParser->parsePsvTree($psvToParse));
    }

}
