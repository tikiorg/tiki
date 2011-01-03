<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST['switchLang'])) { // check can change lang + valid lang
	if ($prefs['change_language'] != 'y' || $prefs['feature_multilingual'] != 'y'
		|| !preg_match("/[a-zA-Z-_]*$/", $_REQUEST['switchLang'])
		|| !file_exists('lang/'.$_REQUEST['switchLang'].'/language.php'))
		unset($_REQUEST['switchLang']);
	elseif ($prefs['available_languages']) {
		if (count($prefs['available_languages']) >= 1 && !in_array($_REQUEST['switchLang'], $prefs['available_languages']))
			unset($_REQUEST['switchLang']);
	}
}

//echo "U_INFO:".$u_info['prefs']['language']." S_PREFS:".$_SESSION['s_prefs']['language']." PREFS:".$prefs['language'];
if (isset($_REQUEST['switchLang'])) {
	$prefs['language'] = $_REQUEST['switchLang'];
	if ($user && $prefs['feature_userPreferences'] == 'y') {
		$tikilib->set_user_preference($user, 'language', $prefs['language']);
	} else {
		$_SESSION['language'] = $prefs['language'];
	}
} elseif ( $prefs['feature_multilingual'] == 'y' && $prefs['change_language'] == 'y' && $prefs['feature_detect_language'] == 'y' and !isset($u_info['prefs']['language'])and !isset($_SESSION['s_prefs']['language'])) {
	$browser_language = detect_browser_language();
	if ( ! empty($browser_language) ) {
		$prefs['language'] = $browser_language;
	}
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

if (empty($prefs['language'])) {
	$prefs['language'] = $prefs['site_language'];
}

if ($prefs['feature_multilingual'] == 'y' && $prefs['change_language'] == 'n') {
	$prefs['language'] = $_SESSION['s_prefs']['language'];
}

