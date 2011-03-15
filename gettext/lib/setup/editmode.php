<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) != FALSE) {
	header('location: index.php');
	exit;
}
global $parsemode_setup, $tiki_p_admin, $tiki_p_use_HTML, $prefs, $info, $jitRequest;
$parsemode_setup = 'y';
$is_html = false;
if ($prefs['feature_wysiwyg'] == 'y' && $prefs['javascript_enabled'] == 'y') {
	if (isset($_REQUEST['mode_wysiwyg']) && $_REQUEST['mode_wysiwyg']=='y' and $prefs['wysiwyg_optional'] == 'y') {
		$_SESSION['wysiwyg'] = 'y';
	} elseif (isset($_REQUEST['mode_normal']) && $_REQUEST['mode_normal']=='y' and $prefs['wysiwyg_optional'] == 'y') {
		$_SESSION['wysiwyg'] = 'n';
	} elseif ((isset($_REQUEST['wysiwyg']) and $_REQUEST['wysiwyg'] == 'y' and $prefs['wysiwyg_optional'] == 'y')) {
		$_SESSION['wysiwyg'] = 'y';
	} elseif ((isset($_REQUEST['wysiwyg']) and $_REQUEST['wysiwyg'] == 'n' and $prefs['wysiwyg_optional'] == 'y')) {
		$_SESSION['wysiwyg'] = 'n';
	} elseif ($prefs['wysiwyg_optional'] == 'n') {
		$_SESSION['wysiwyg'] = 'y';
	} elseif ($prefs['wysiwyg_memo'] == 'y' and !empty($info['wysiwyg'])) {
		$_SESSION['wysiwyg'] = $info['wysiwyg'];
	} elseif ($prefs['wysiwyg_default'] == 'y' and !isset($_REQUEST['wysiwyg'])) {
		$_SESSION['wysiwyg'] = 'y';
	} elseif ($prefs['wysiwyg_optional'] == 'y' and isset($_REQUEST['wysiwyg']) and $_REQUEST['wysiwyg'] == 'n') {
		$_SESSION['wysiwyg'] = 'n';
	}
} else {
	$_SESSION['wysiwyg'] = 'n';
}
if ($_SESSION['wysiwyg'] == 'y') {
	$is_html = true;
} elseif ($prefs['feature_wiki_allowhtml'] == 'y' and ($tiki_p_admin == 'y' or $tiki_p_use_HTML == 'y')) {
	if (isset($_REQUEST['preview']) || isset($jitRequest['edit'])) {
		if (isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on") {
			$is_html = true;
		}
	} else {
		if (isset($info['is_html']) and $info['is_html']) {
			$is_html = true;
		}
	}
}
if (isset($jitRequest['edit'])) {
	// Restore the property for the rest of the script
	if ($is_html) {
		$_REQUEST['edit'] = $jitRequest->edit->xss();
	} else {
		$_REQUEST['edit'] = $jitRequest->edit->wikicontent();
	}
}
