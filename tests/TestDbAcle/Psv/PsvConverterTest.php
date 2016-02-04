<?php
namespace TestDbAcleTests\TestDbAcle\Psv;

class PsvConverterTest extends \TestDbAcleTests\TestDbAcle\BaseTestCase{

    function test_format() {

        $psvConverter = new \TestDbAcle\Psv\PsvConverter();

        $expectedPsv = trim("
id   |first_name   |last_name
10   |john         |miller
20   |stu          |Smith
    	");

        $arrayToFormat = array(
            array("id" => "10",
                "first_name" => "john",
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        
        $this->assertEquals($expectedPsv, $psvConverter->format($arrayToFormat));
    }

    function test_format_WithNull() {

        $psvConverter = new \TestDbAcle\Psv\PsvConverter();

        $expectedPsv = trim("
id   |first_name   |last_name
10   |NULL         |miller
20   |stu          |Smith
    	");

        $arrayToFormat = array(
            array("id" => "10",
                "first_name" => null,
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => "Smith"),
        );
        $this->assertEquals($expectedPsv, $psvConverter->format($arrayToFormat));
    }

    function test_format_WithFalseAndTrue
    () {

        $psvConverter = new \TestDbAcle\Psv\PsvConverter();

        $expectedPsv = trim("
id   |first_name   |last_name
10   |FALSE        |miller
20   |stu          |TRUE
    	");

        $arrayToFormat = array(
            array("id" => "10",
                "first_name" => false,
                "last_name" => "miller"),
            array("id" => "20",
                "first_name" => "stu",
                "last_name" => true),
        );
        $this->assertEquals($expectedPsv, $psvConverter->format($arrayToFormat));
    }

    function test_format_Empty() {

        $psvConverter = new \TestDbAcle\Psv\PsvConverter();

        $expectedPsv = "";

        $arrayToFormat = array();
        $this->assertEquals($expectedPsv, $psvConverter->format($arrayToFormat));
    }
}
