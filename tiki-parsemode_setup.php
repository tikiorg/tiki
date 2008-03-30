<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-parsemode_setup.php,v 1.5.2.1 2008-01-15 21:06:59 nkoth Exp $

if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) != FALSE) { header('location: index.php'); exit; }

global $parsemode_setup;
$parsemode_setup = 'y';
$is_html = false;
if ($prefs['feature_wysiwyg'] == 'y') {
	if ((isset($_REQUEST['wysiwyg']) and $_REQUEST['wysiwyg'] == 'y' and $prefs['wysiwyg_optional'] == 'y') or ($prefs['wysiwyg_optional'] == 'n' or ($prefs['wysiwyg_default'] == 'y' and !isset($_REQUEST['wysiwyg'])))) {
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
	if (isset($_REQUEST['preview']) || isset($_REQUEST['edit'])) {
		if (isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on") {
			$is_html = true;
		}
	} else {
		if ((isset($info['is_html']) and $info['is_html'])) {
			$is_html = true;
		}
	}
}
?>
