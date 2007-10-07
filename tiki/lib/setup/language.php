<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/language.php,v 1.2 2007-10-07 18:44:18 sylvieg Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if (!empty($_SESSION['language'])) {
	$saveLanguage = $_SESSION['language']; // if register_globals is on variable and _SESSION are the same
}

if ( $feature_detect_language == 'y' ) {
	$browser_language = detect_browser_language();
	if ( ! empty($browser_language) ) {
		$language = $browser_language;
		$smarty->assign('language', $language);
	}
}

if (isset($_REQUEST['switchLang'])) {
	if ($change_language != 'y'
		|| !preg_match("/[a-zA-Z-_]*$/", $_REQUEST['switchLang'])
		|| !file_exists('lang/'.$_REQUEST['switchLang'].'/language.php'))
		unset($_REQUEST['switchLang']);
	elseif ($available_languages) {
		$a = unserialize($available_languages);
		if (count($a) >= 1 && !in_array($_REQUEST['switchLang'], $a))
			unset($_REQUEST['switchLang']);
	}
}

if ( $feature_userPreferences == 'y' && $user ) {
	if ( $change_language == 'y' ) {
		if (isset($_REQUEST['switchLang'])) {
			$language = $_REQUEST['switchLang'];
			$tikilib->set_user_preference($user, 'language', $language);
		} else {
			$user_language = $tikilib->get_user_preference($user, 'language', $language);
			if ($user_language && $language != $user_language && file_exists("lang/$user_language/language.php")) {
				$language = $user_language;
			}
		}
	}
}

if (!$user) {
	if (isset($_REQUEST['switchLang'])) {
		$language = $_REQUEST['switchLang'];
		$_SESSION['language'] = $language;
		$smarty->assign('language', $language);
	} elseif  (!empty($saveLanguage)) { // users not logged that change the preference
		$language = $saveLanguage;
		$smarty->assign('language', $language);
	}
} elseif (!empty($saveLanguage) && $feature_userPreferences != 'y' && $change_language == 'y') {
	$language = $saveLanguage;
	$smarty->assign('language', $language);
}

if ( $lang_use_db != 'y' ) {
    // check if needed!!!
    global $lang;
}

/*
 * Some languages needs BiDi support. Add their code names here ...
 */
if ( $language == 'ar' || $language == 'he' || $language == 'fa' ) {
	$feature_bidi = 'y';
	$smarty->assign('feature_bidi', $feature_bidi);
}
?>
