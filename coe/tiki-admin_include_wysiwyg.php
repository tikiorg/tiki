<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
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
		"wysiwyg_htmltowiki",
	);
	foreach($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	simple_set_value('wysiwyg_toolbar_skin');
}
ask_ticket('admin-inc-wysiwyg');
