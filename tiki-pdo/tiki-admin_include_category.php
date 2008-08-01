<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}



if (isset($_REQUEST["categorysetup"])) {
ask_ticket('admin-inc-category');

    $pref_toggles = array(
	"feature_categoryobjects",
	"feature_categorypath",
	"feature_search_show_forbidden_cat",
	"feature_category_reinforce",
	"feature_category_use_phplayers"
    );


    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

    $pref_simple_values = array(
        "categorypath_excluded"
    );

    foreach ($pref_simple_values as $svitem) {
        simple_set_value ($svitem);
    }

}


?>
