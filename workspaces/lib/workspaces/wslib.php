<?php
/** \file
 * $Id: 
 * \brief WorkSpaces support class
 *
 */

//this script may only be included - so its better to die if called directly.

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

//Actually this is not included in any file, besides this is the categories lib
//I'm using it in order to get an idea of how to make use of them or modify them from here

global $objectlib;require_once("lib/objectlib.php");

class WSLib extends ObjectLib {

	function WSLib($db) {
		parent::ObjectLib($db);
	}

?>
