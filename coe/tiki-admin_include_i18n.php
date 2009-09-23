<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false, null, true);
$smarty->assign_by_ref("languages", $languages);
if (!empty($_REQUEST['custom']) && !empty($_REQUEST['custom_lang'])) {
	ask_ticket('admin-inc-i18n');
	$custom_file = 'lang/' . $_REQUEST['custom_lang'] . '/';
	if (!empty($tikidomain)) $custom_file.= "$tikidomain/";
	$custom_file.= "custom.php";
	$custom_translation = file_get_contents($custom_file);
	if (empty($custom_translation)) {
		$custom_translation = file_get_contents('lang/fr/custom.php_example');
	}
	$smarty->assign_by_ref('custom_translation', $custom_translation);
	$smarty->assign_by_ref('custom_lang', $_REQUEST['custom_lang']);
}
if (!empty($_REQUEST['custom_save']) && !empty($_REQUEST['custom_lang'])) {
	ask_ticket('admin-inc-i18n');
	$ok = false;
	foreach($languages as $l) {
		if ($l['value'] == $_REQUEST['custom_lang']) {
			$ok = true;
			break;
		}
	}
	if (!$ok) {
		$smarty->assign('custom_error', 'param');
	} elseif (eval(str_replace(array(
		'<?php',
		'?>'
	) , '', $_REQUEST['custom_translation'])) === false) {
		$smarty->assign_by_ref('custom_lang', $_REQUEST['custom_lang']);
		$smarty->assign_by_ref('custom_translation', $_REQUEST['custom_translation']);
		$smarty->assign('custom_error', 'parse');
	} else {
		$custom_file = 'lang/' . $_REQUEST['custom_lang'] . '/';
		if (!empty($tikidomain)) $custom_file.= "$tikidomain/";
		$custom_file.= "custom.php";
		$smarty->assign('custom_file', $custom_file);
		if (!($fp = fopen($custom_file, 'w+'))) {
			$smarty->assign('custom_error', 'file');
		} else {
			if (!fwrite($fp, $_REQUEST['custom_translation'])) {
				$smarty->assign('custom_error', 'file');
			}
			fclose($fp);
			global $cachelib;
			include_once ('lib/cache/cachelib.php');
			$cachelib->erase_dir_content("templates_c/$tikidomain");
		}
	}
}
