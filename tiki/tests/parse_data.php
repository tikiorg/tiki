<?php
class ParseDataTest extends PHPUnit_TestCase {
    // contains the object handle of the string class
    var $t;
    // constructor of the test suite
    function ParseDataTest($name) {
       $this->PHPUnit_TestCase($name);
    }
    // called before the test functions will be executed    
    // this function is defined in PHPUnit_TestCase and overwritten 
    // here
    function setUp() {
      global $tikilib;
      $this->t = $tikilib;
    }
    // called after the test functions are executed    
    // this function is defined in PHPUnit_TestCase and overwritten 
    // here    
    function tearDown() {
        // delete your instance
        unset($this->t);
    }
    // test the toString function
    function test1() {
        $input = file_get_contents('tests/parse_data/test1');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test1.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test1.output');
        $this->assertEquals($result,$expected);                       
    }
    function test2() {
        $input = file_get_contents('tests/parse_data/test2');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test2.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test2.output');
        $this->assertEquals($result,$expected);                       
    }
    function test3() {
        $input = file_get_contents('tests/parse_data/test3');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test3.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test3.output');
        $this->assertEquals($result,$expected);                       
    }
    function test4() {
        $input = file_get_contents('tests/parse_data/test4');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test4.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test4.output');
        $this->assertEquals($result,$expected);                       
    }
    function test5() {
        $input = file_get_contents('tests/parse_data/test5');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test5.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test5.output');
        $this->assertEquals($result,$expected);                       
    }
    function test6() {
        $input = file_get_contents('tests/parse_data/test6');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test6.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test6.output');
        $this->assertEquals($result,$expected);                       
    }
    function test7() {
        $input = file_get_contents('tests/parse_data/test7');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test7.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test7.output');
        $this->assertEquals($result,$expected);                       
    }
    function test8() {
        $input = file_get_contents('tests/parse_data/test8');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test8.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test8.output');
        $this->assertEquals($result,$expected);                       
    }
    function test9() {
        $input = file_get_contents('tests/parse_data/test9');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test9.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test9.output');
        $this->assertEquals($result,$expected);                       
    }
    function test10() {
        $input = file_get_contents('tests/parse_data/test10');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test10.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test10.output');
        $this->assertEquals($result,$expected);                       
    }
    function test11() {
        $input = file_get_contents('tests/parse_data/test11');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test11.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test11.output');
        $this->assertEquals($result,$expected);                       
    }
    function test12() {
        $input = file_get_contents('tests/parse_data/test12');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test12.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test12.output');
        $this->assertEquals($result,$expected);                       
    }
    function test13() {
        $input = file_get_contents('tests/parse_data/test13');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test13.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test13.output');
        $this->assertEquals($result,$expected);                       
    }
    function test14() {
        $input = file_get_contents('tests/parse_data/test14');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test14.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test14.output');
        $this->assertEquals($result,$expected);                       
    }
    function test15() {
        $input = file_get_contents('tests/parse_data/test15');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test15.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test15.output');
        $this->assertEquals($result,$expected);                       
    }
    function test16() {
        $input = file_get_contents('tests/parse_data/test16');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test16.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test16.output');
        $this->assertEquals($result,$expected);                       
    }
    function test17() {
        $input = file_get_contents('tests/parse_data/test17');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test17.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test17.output');
        $this->assertEquals($result,$expected);                       
    }
    function test18() {
        $input = file_get_contents('tests/parse_data/test18');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test18.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test18.output');
        $this->assertEquals($result,$expected);                       
    }
    function test19() {
        $input = file_get_contents('tests/parse_data/test19');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test19.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test19.output');
        $this->assertEquals($result,$expected);                       
    }
    function test20() {
        $input = file_get_contents('tests/parse_data/test20');
        $result = $this->t->parse_data($input);
        $fp = fopen('tests/parse_data/test20.output','w');
          fwrite($fp,$result);
          fclose($fp);
        $expected = file_get_contents('tests/parse_data/test20.output');
        $this->assertEquals($result,$expected);                       
    }
    
    
            
  }
$suite = new PHPUnit_TestSuite("ParseDataTest");
$result = PHPUnit::run($suite);
echo $result -> toHTML();
?>