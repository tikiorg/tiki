<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_siteid.php,v 1.6 2006-08-29 20:19:02 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Site Identity Settings
if (isset($_REQUEST["siteidentityset"])) {
 check_ticket('admin-inc-siteid');
 
 if (isset($_REQUEST["alter_tiki_prefs_table"])) {
	 $alter_result = alterprefs();
 }

 	$pref_toggles = array(
  			"feature_sitemycode",
				"feature_breadcrumbs",
				"feature_siteloclabel",
				"feature_sitelogo",
				"feature_sitenav",
				"feature_sitead",
				"feature_sitesearch",
				"feature_sitemenu",
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

    $pref_byref_values = array(
        "feature_siteloc",
        "feature_sitetitle",
        "feature_sitedesc"
    );

    foreach ($pref_byref_values as $britem) {
        byref_set_value ($britem);
    }

}

ask_ticket('admin-inc-siteid');
?>
