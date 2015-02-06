<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: TranslationOfTest.php 47827 2013-10-01 19:14:28Z alain_desilets $

require_once('lib/test/TestRunnerWithBaseline.php');
require_once('lib/debug/Tracer.php');

class TestRunnerWithBaselineTest extends PHPUnit_Framework_TestCase
{
    public $runner;
    private $old_cmdline = null;

    protected function setUp()
    {
        global $argv;

        $this->runner = new TestRunnerWithBaseLine();

        /* Remember what the command line args were, because some of
           the tests below may need to mess around with them, and we
           want to restore them to their original state afterwards.
        */
        $this->old_cmdline = $argv;
    }

    protected function tearDown()
    {
        $argv = $this->old_cmdline;
    }


    /**
     * @dataProvider dataProvider_process_phpunit_log_data
     */
    public function test_process_phpunit_log_data($log_data, $exp_failures, $exp_errors, $exp_pass, $message)
    {
        $issues = $this->runner->process_phpunit_log_data($log_data);
        $this->assertEquals($exp_failures, $issues['failures'],
                "$message\nThe list of FAILURES was not collected properly.");

        $this->assertEquals($exp_errors, $issues['errors'],
            "$message\nThe list of ERRORS was not collected properly.");

        $this->assertEquals($exp_pass, $issues['pass'],
            "$message\nThe list of PASS was not collected properly.");

    }

    public function dataProvider_process_phpunit_log_data()
    {
        return
            array(

              array(
                  array(
                      array('event' => 'test', 'test' => 'SomeClass::testSomeMethodThatPasses', 'status' => 'pass'),
                  ),
                  array(),
                  array(),
                  array('SomeClass::testSomeMethodThatPasses'),
                  "Case 0 description: No failures nor errors, just a pass."
              ),

              array(
                  array(
                      array('event' => 'test', 'test' => 'SomeClass::testSomeMethodThatFails', 'status' => 'fail'),
                      array('event' => 'test', 'test' => 'SomeOtherClass::testSomeMethodThatRaisesErrors', 'status' => 'error')
                  ),
                  array('SomeClass::testSomeMethodThatFails'),
                  array('SomeOtherClass::testSomeMethodThatRaisesErrors'),
                  array(),
                  "Case 1 description: one failure and one error."
              ),

              array(
                  array(
                      array('event' => 'test', 'test' => 'SomeClass::testSomeMethodThatFails', 'status' => 'fail'),
                      array('event' => 'test', 'test' => 'SomeClass::testSomeOtherMethodThatFails', 'status' => 'fail')
                  ),
                  array('SomeClass::testSomeMethodThatFails', 'SomeClass::testSomeOtherMethodThatFails'),
                  array(),
                  array(),
                  "Case 2 description: more than one issue of type failure."
                ),

              array(
                  array(
                      array('event' => 'test', 'test' => 'SomeClass::testSomeMethodThatRaiseAnError', 'status' => 'error'),
                      array('event' => 'test', 'test' => 'SomeClass::testSomeOtherMethodThatRaisesAnError', 'status' => 'error')
                  ),
                  array(),
                  array('SomeClass::testSomeMethodThatRaiseAnError', 'SomeClass::testSomeOtherMethodThatRaisesAnError'),
                  array(),
                  "Case 3 description: more than one issue of type error."
              ),

              array(
                array(
                    array('event' => 'testStart', 'test' => 'SomeClass::testSomeTestThatNeverFinished'),
                ),
                array('SomeClass::testSomeTestThatNeverFinished'),
                array(),
                array(),
                "Case 4 description: A test for which a 'testStart' event is issued, but no event is issued for the test results. Assume that this is a failure."
              ),

            );
    }

    /**
     * @dataProvider dataProvider_compare_two_test_runs
     */
    public function test_compare_two_test_runs($baseline_issues, $current_issues, $exp_differences, $message)
    {
        $got_differences = $this->runner->compare_two_test_runs($baseline_issues, $current_issues);
        $this->assertEquals($exp_differences, $got_differences,
                "$message\nDifferences against the baseline were not correct.");
    }

    public function dataProvider_compare_two_test_runs()
    {
        return
            array(
              array(

                  array('failures' => array(), 'errors' => array(), 'pass' => array()),
                  array('failures' => array('SomeClass::testThatFails'), 'errors' => array('SomeClass::testThatRaisesError'),
                        'pass' => array()),
                  array(
                      'failures_introduced' => array('SomeClass::testThatFails'),
                      'failures_fixed' => array(),
                      'errors_introduced' => array('SomeClass::testThatRaisesError'),
                      'errors_fixed' => array()
                  ),
                  "Case description: Introduced one error and one failure."
              ),

              array(
                  array('failures' => array('SomeClass::testThatFails'), 'errors' => array('SomeClass::testThatRaisesError', 'pass' => array())),
                  array('failures' => array(), 'errors' => array(),
                      'pass' => array('SomeClass::testThatFails', 'SomeClass::testThatRaisesError')),
                  array(
                      'failures_introduced' => array(),
                      'failures_fixed' => array('SomeClass::testThatFails'),
                      'errors_introduced' => array(),
                      'errors_fixed' => array('SomeClass::testThatRaisesError')
                  ),
                  "Case description: Fixed one error and one failure."
              ),

                array(
                    array('failures' => array('SomeClass::testThaUsedToFail'), 'errors' => array('SomeClass::testThatUsedToRaiseAnError'),
                          'pass' => array()),
                    array('failures' => array('SomeClass::testThatStartedFailing'), 'errors' => array('SomeClass::testThatStartedRaisingAnError'),
                          'pass' => array('SomeClass::testThaUsedToFail', 'SomeClass::testThatUsedToRaiseAnError')),
                    array(
                        'failures_introduced' => array('SomeClass::testThatStartedFailing'),
                        'failures_fixed' => array('SomeClass::testThaUsedToFail'),
                        'errors_introduced' => array('SomeClass::testThatStartedRaisingAnError'),
                        'errors_fixed' => array('SomeClass::testThatUsedToRaiseAnError')
                    ),
                    "Case description: Fixed one error and one failure AND introduced one of each."
                ),


            );
    }
}
