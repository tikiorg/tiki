<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
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

