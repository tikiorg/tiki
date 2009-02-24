<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_freetags.php,v 1.9.2.5 2008-02-18 14:03:29 lphuberdeau Exp $

require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST['save'])) {
	check_ticket('admin-inc-sefurl');
	
	simple_set_toggle('feature_sefurl');
	simple_set_toggle('feature_sefurl_filter');
}

ask_ticket('admin-inc-sefurl');
?>
