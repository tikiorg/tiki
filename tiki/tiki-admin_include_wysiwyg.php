<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_wysiwyg.php,v 1.1.2.2 2008-03-10 20:36:03 sylvieg Exp $

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
		"wysiwyg_wiki_parsed",
		"wysiwyg_wiki_semi_parsed"
	);
	if (isset($_REQUEST['restore']) && $_REQUEST['restore'] == 'on') {
		$_REQUEST['wysiwyg_toolbar'] = "FitWindow,Templates,-,Cut,Copy,Paste,PasteText,PasteWord,Print,SpellCheck
	Undo,Redo,-,Find,Replace,SelectAll,RemoveFormat,-,Table,Rule,Smiley,SpecialChar,PageBreak,ShowBlocks
	/
	JustifyLeft,JustifyCenter,JustifyRight,JustifyFull,-,OrderedList,UnorderedList,Outdent,Indent,Blockquote
	Bold,Italic,Underline,StrikeThrough,-,Subscript,Superscript,-,tikilink,Link,Unlink,Anchor,-,tikiimage,Flash
	/
	Style,FontName,FontSize,-,TextColor,BGColor,-,Source";
	}

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	simple_set_value('wysiwyg_toolbar_skin');
	simple_set_value('wysiwyg_toolbar');
}

ask_ticket('admin-inc-wysiwyg');
?>
