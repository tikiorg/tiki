<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_siteid.php,v 1.2 2005-01-22 22:54:52 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Site Identity Settings
if (isset($_REQUEST["siteidentityset"])) {
 check_ticket('admin-inc-siteid');
 
 if (isset($_REQUEST["alter_tiki_prefs_table"])) {
	 $alter_result=true;

	// This needs moving to a lib!!! - Damosoft
	 
	 if (!$tikilib->query( "ALTER TABLE `tiki_preferences` MODIFY `value` BLOB", array())) {
		 $alter_result=false;
	 }
	 if ($alter_result!=true) {
        $smarty->assign("msg", tra('Altering database table failed'));
        $smarty->display("error.tpl");
        die;
	 }
 }

 	$pref_toggles = array(
  			"feature_sitemycode",
				"feature_siteloc",
				"feature_sitelogo",
				"feature_sitenav",
				"feature_sitead",
				"feature_sitesearch",
				"sitemycode_publish",
				"sitead_publish"
    );

    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

 	$pref_simple_values = array(
				"sitelogo_src",
				"sitelogo_bgcolor",
				"sitelogo_title",
				"sitelogo_alt",
				"sitemycode",
				"sitead",
				"sitenav"
    );

    foreach ($pref_simple_values as $svitem) {
        simple_set_value ($svitem);
    }
}

ask_ticket('admin-inc-siteid');
?>
