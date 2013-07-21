<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


$section = 'wiki page';
require_once ('tiki-setup.php');
require_once('lib/ckeditor_tiki/wysiwyglib.php');

if ($prefs['feature_wysiwyg_inline'] == 'y') {

	if ($_SESSION['edit_wysiwyg_inline'] === 'y') {
		$_SESSION['edit_wysiwyg_inline'] = 'n';
		$wysiwyglib->shutdownInlineEditor();
	} else {
		$_SESSION['edit_wysiwyg_inline'] = 'y';
		$wysiwyglib->setupInlineEditor($_REQUEST['page']);
	}
} else {
	$_SESSION['edit_wysiwyg_inline'] = 'n';
	$wysiwyglib->shutdownInlineEditor();
}

// Return to referrer
header('Location: '.$_SERVER['HTTP_REFERER']);


