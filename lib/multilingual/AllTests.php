<?php

// This file illustrates how we can create suites of suites of tests.

require_once 'PHPUnit/Framework.php';
 

class  SomeTests1 extends PHPUnit_Framework_TestCase
{
   public function test_do_nothing() {
      // this test does nothing.
   }
}
 
//$x = new SomeTests1(); 
 
 
class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('JustFoolingAround');
 
        $test = new SomeTests1('SomeTests1');
        $suite->addTest($test);
        // ...
 
        return $suite;
    }
}
?>