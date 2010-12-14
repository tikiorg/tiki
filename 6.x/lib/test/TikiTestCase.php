<?php
/*
 * Created on Jan 30, 2009
 *
 * Parent class of all test cases. For some reason PHPUnit doesn't deal
 * well with globals, so $backupGlobals should be set to false. 
 * Use this class to set other PHPUnit variables, as needed. 
 * 
 */
 
require_once('PHPUnit/Framework/TestCase.php'); 
 
abstract class TikiTestCase extends PHPUnit_Framework_TestCase
{
 	protected $backupGlobals = FALSE;	
}
