<?php

// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== FALSE) {
  header('location: index.php');
  exit;
	die();
}
//init the smarty settings that needs $prefs(prefs is not init in setup_smarty)

if ($prefs['log_tpl'] == 'y') {
	$smarty->load_filter('pre', 'log_tpl');
}
if ( $prefs['feature_sefurl_filter'] == 'y' ) {
  require_once ('tiki-sefurl.php');
  $smarty->register_outputfilter('filter_out_sefurl');
}

