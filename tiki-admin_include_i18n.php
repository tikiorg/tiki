<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false,null,true);
$smarty->assign_by_ref("languages", $languages);


if (isset($_REQUEST["i18nsetup"])) { 	
ask_ticket('admin-inc-i18n');

	if( isset( $_REQUEST['available_languages'] )
	 && count( $_REQUEST['available_languages'] ) > 0 )
	{
		$_REQUEST['change_language'] = 'on';
	}

    $pref_toggles = array(
		"feature_multilingual",
		"feature_best_language",
        "feature_detect_language",
		"change_language",		
		"feature_user_watches_translations",
        "lang_use_db",
		"record_untranslated",
		"feature_babelfish",
		"feature_babelfish_logo",
    );


    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }	

	simple_set_value( 'language' );
	simple_set_value( 'available_languages', '', true );
}

?>
