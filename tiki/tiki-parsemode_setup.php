<?php

if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) != FALSE) { header('location: index.php'); exit; }

$is_html = false;
if ($feature_wysiwyg == 'y') {
	if ((isset($_REQUEST['wysiwyg']) and $_REQUEST['wysiwyg'] == 'y' and $wysiwyg_optional == 'y') or ($wysiwyg_optional == 'n' or ($wysiwyg_default == 'y' and !isset($_REQUEST['wysiwyg'])))) {
		$_SESSION['wysiwyg'] = 'y';
	} elseif ($wysiwyg_optional == 'y' and isset($_REQUEST['wysiwyg']) and $_REQUEST['wysiwyg'] == 'n') {
		$_SESSION['wysiwyg'] = 'n';
	}
	$is_html = true;
} elseif ($feature_wiki_allowhtml == 'y' and ($tiki_p_admin == 'y' or $tiki_p_use_HTML == 'y')) {
	if ((isset($info['is_html']) and $info['is_html']) or (isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on")) {
		$is_html = true;
	}
}
?>
