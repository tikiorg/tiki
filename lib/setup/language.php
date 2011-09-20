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

// Detect browser language
if ( $prefs['change_language'] == 'y' && $prefs['feature_detect_language'] == 'y' and !$tikilib->userHasPreference('language')) {
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