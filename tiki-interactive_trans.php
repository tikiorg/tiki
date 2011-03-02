<?php 
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once('lib/language/Language.php');

$access->check_feature('lang_use_db');
$access->check_permission('tiki_p_edit_languages');

// start interactive translation session
if (!empty($_REQUEST['interactive_translation_mode'])) {
	$_SESSION['interactive_translation_mode'] = $_REQUEST['interactive_translation_mode'];	
	if ($_REQUEST['interactive_translation_mode'] == 'off') {
		$cachelib->empty_cache('templates_c');
	}

	header('Location: ' . $_SESSION['last_mid_php']);
	exit;
}

/* Called by the JQuery ajax request. No response expected.
 * Save strings translated using interactive translation to database.
 */ 
if( isset( $_REQUEST['source'], $_REQUEST['trans'] ) && count($_REQUEST['source']) == count($_REQUEST['trans']) ) {
	$language = new Language;
	
	foreach( $_REQUEST['trans'] as $k => $translation ) {
		$source = $_REQUEST['source'][$k];

		$language->updateTrans( $source, $translation );
	}

	exit;
}
