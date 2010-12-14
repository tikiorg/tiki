<?php 
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once('lib/language/Language.php');
if ($prefs['lang_use_db'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": lang_use_db");
	$smarty->assign('error', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}

if ($tiki_p_edit_languages != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->assign('error', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}

$language = new Language;

// Called by the JQuery ajax request. No response expected.
if( isset( $_REQUEST['source'], $_REQUEST['trans'] ) && count($_REQUEST['source']) == count($_REQUEST['trans']) ) {
	$lang = $prefs['language'];
	if( empty( $lang ) ) {
		$lang = $prefs['site_language'];
	}

	foreach( $_REQUEST['trans'] as $k => $translation ) {
		$source = $_REQUEST['source'][$k];

		$language->updateTrans( $source, $translation );
	}

	exit;
}
