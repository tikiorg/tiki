<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (version_compare(PHP_VERSION, '5.1.0', '>=') and $prefs['feature_pear_date'] != 'y' and function_exists("date_create"))  {
	require_once('tikidate-php5.php');
} else {
	require_once('tikidate-pear-date.php');
}

