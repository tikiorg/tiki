<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_copyright.php,v 1.4.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.


if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if ( isset($_REQUEST["setcopyright"]) ) {
        check_ticket('admin-inc-copyright');

	$pref_toggles = array(
		"wiki_feature_copyrights",
		"blogues_feature_copyrights",
		"faqs_feature_copyrights",
		"articles_feature_copyrights",
	);

	$pref_byref_values = array(
		"wikiLicensePage",
		"wikiSubmitNotice",
	);

	foreach ($pref_toggles as $toggle) simple_set_toggle ($toggle);
	foreach ($pref_byref_values as $value) byref_set_value ($value);
}

ask_ticket('admin-inc-copyright');
