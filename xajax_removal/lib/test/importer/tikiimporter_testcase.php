<?php
 
require_once('PHPUnit/Extensions/OutputTestCase.php'); 
 
/** 
 * @group importer
 */
abstract class TikiImporter_TestCase extends PHPUnit_Extensions_OutputTestCase
{
 	protected $backupGlobals = FALSE;	
}
