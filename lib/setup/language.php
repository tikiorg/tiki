<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ($prefs['feature_multilingual'] != 'y') { // change_language depends on feature_multilingual.
	$prefs['change_language'] = 'n';
}

if (isset($_REQUEST['switchLang'])) { // check can change lang + valid lang
	if ($prefs['change_language'] != 'y'
		|| !preg_match("/[a-zA-Z-_]*$/", $_REQUEST['switchLang'])
		|| !file_exists('lang/'.$_REQUEST['switchLang'].'/language.php'))
		unset($_REQUEST['switchLang']);
	elseif ($prefs['available_languages']) {
		if (count($prefs['available_languages']) >= 1 && !in_array($_REQUEST['switchLang'], $prefs['available_languages']))
			unset($_REQUEST['switchLang']);
	}
}

if (isset($_REQUEST['switchLang'])) {
	$prefs['language'] = $_REQUEST['switchLang'];
	if ($user && $prefs['feature_userPreferences'] == 'y') {
		$tikilib->set_user_preference($user, 'language', $prefs['language']);
	} else {
		$_SESSION['language'] = $prefs['language'];
	}
} elseif ( $prefs['change_language'] == 'y' && $prefs['feature_detect_language'] == 'y' and !$tikilib->userHasPreference('language')) {
	$browser_language = detect_browser_language();
	if ( ! empty($browser_language) ) {
		$prefs['language'] = $browser_language;
	}
}

if (empty($prefs['language']) || $prefs['change_language'] == 'n') {
	$prefs['language'] = $prefs['site_language']; // Override user-specific language
}

// Some languages need BiDi support. Add their code names here ...
$prefs['feature_bidi'] = in_array($prefs['language'], array('ar', 'he', 'fa')) ? 'y' : 'n';