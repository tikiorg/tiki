<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_wysiwyg.php,v 1.1.2.3 2008-03-19 14:00:42 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["wysiwygfeatures"])) {
	check_ticket('admin-inc-wysiwyg');
	$pref_toggles = array(
		"wysiwyg_optional",
		"wysiwyg_default",
		'wysiwyg_memo',
		"wysiwyg_wiki_parsed",
		"wysiwyg_wiki_semi_parsed",
	);
	if (isset($_REQUEST['restore']) && $_REQUEST['restore'] == 'on') {
		$tikilib->delete_preference('wysiwyg_toolbar');
	} else {
		simple_set_value('wysiwyg_toolbar');
	}

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	simple_set_value('wysiwyg_toolbar_skin');
}

ask_ticket('admin-inc-wysiwyg');
?>
