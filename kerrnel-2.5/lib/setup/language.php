<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// clear bl if bl is 'n' for backward compatibility
if (isset($_REQUEST['bl']) && $_REQUEST['bl'] == 'n') {
	unset($_REQUEST['bl']);
}

if (!empty($_SESSION['language'])) {
	$saveLanguage = $_SESSION['language']; // if register_globals is on variable and _SESSION are the same
}

if ( $prefs['feature_detect_language'] == 'y' and !isset($u_info['prefs']['language'])) {
	$browser_language = detect_browser_language();
	if ( ! empty($browser_language) ) {
		$prefs['language'] = $browser_language;
	}
}

if (isset($_REQUEST['switchLang'])) {
	if ($prefs['change_language'] != 'y'
		|| !preg_match("/[a-zA-Z-_]*$/", $_REQUEST['switchLang'])
		|| !file_exists('lang/'.$_REQUEST['switchLang'].'/language.php'))
		unset($_REQUEST['switchLang']);
	elseif ($prefs['available_languages']) {
		if (count($prefs['available_languages']) >= 1 && !in_array($_REQUEST['switchLang'], $prefs['available_languages']))
			unset($_REQUEST['switchLang']);
	}
}

if ( $prefs['feature_userPreferences'] == 'y' && $user ) {
	if ( $prefs['change_language'] == 'y' && isset($_REQUEST['switchLang']) ) {
		$prefs['language'] = $_REQUEST['switchLang'];
		$tikilib->set_user_preference($user, 'language', $prefs['language']);
	}
}

if (!$user) {
	if (isset($_REQUEST['switchLang'])) {
		$prefs['language'] = $_REQUEST['switchLang'];
		$_SESSION['language'] = $prefs['language'];
	} elseif  (!empty($saveLanguage)) { // users not logged that change the preference
		$prefs['language'] = $saveLanguage;
	}
} elseif (!empty($saveLanguage) && $prefs['feature_userPreferences'] != 'y' && $prefs['change_language'] == 'y') {
	$prefs['language'] = $saveLanguage;
}

if ( $prefs['lang_use_db'] != 'y' ) {
    // check if needed!!!
    global $lang;
}

/*
 * Some languages needs BiDi support. Add their code names here ...
 */
if ( $prefs['language'] == 'ar' || $prefs['language'] == 'he' || $prefs['language'] == 'fa' ) {
	$prefs['feature_bidi'] = 'y';
} else {
	$prefs['feature_bidi'] = 'n';
}

