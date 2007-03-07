<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_copyright.php,v 1.1 2007-03-07 14:23:07 gillesm Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["copyrightfeature"])) {
	check_ticket('admin-inc-copyright');
	$pref_toggles = array(
		"copyright_optional",
		"copyright_default",
		"copyright_wiki_parsed",
		"copyright_wiki_semi_parsed"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	simple_set_value('copyright_toolbar_skin');
	simple_set_value('copyright_toolbar');
}

ask_ticket('admin-inc-copyright');
?>
