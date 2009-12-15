<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// This is the start of a guideance wizard
// Damian aka damosoft aka TikiGod

function module_assistant_info() {
	return array(
		'name' => tra('TikiWiki assistant'),
		'description' => tra('Displays an assistant to guide new Tiki admins.'),
		'prefs' => array(),
		'params' => array()
	);
}


