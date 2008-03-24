<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_ads.php,v 1.1.2.1 2008-03-16 16:57:50 luciash Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["adssetup"])) {
    ask_ticket('admin-inc-ads');

    $pref_toggles = array(
	"feature_sitead",
	"sitead_publish"
    );

    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

    $pref_simple_values = array(
	"sitead"
    );

    foreach ($pref_simple_values as $svitem) {
        simple_set_value ($svitem);
    }

}

?>