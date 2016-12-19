<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Parent class of all test cases that use the database.
 */
 
//require_once (version_compare(PHPUnit_Runner_Version::id(), '3.5.0', '>=')) ? 'PHPUnit/Autoload.php' : 'PHPUnit/Framework.php';
 
abstract class TikiDatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
	static private $pdo = null;
	
	private $conn = null;
	
 	public function getConnection()
 	{
 		require(dirname(__FILE__) . '/local.php');

 		if ($this->conn === null) {
 			if (self::$pdo === null) {
 				self::$pdo = new PDO("$db_tiki:host=$host_tiki;dbname=$dbs_tiki", $user_tiki, $pass_tiki);
 			}
 			$this->conn = $this->createDefaultDBConnection(self::$pdo);
 		}
 		
 		return $this->conn;
 	}
}
