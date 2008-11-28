<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}



if (isset($_REQUEST["modulesetup"])) {
ask_ticket('admin-inc-module');

    $pref_toggles = array(
	"feature_modulecontrols",
	"user_assigned_modules",
	"modallgroups",
	"modseparateanon", // MGvK
    );


    $pref_byref_values = array(
	"user_flip_modules"
    );




    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

    foreach ($pref_byref_values as $britem) {
        byref_set_value ($britem);
    }


}


?>