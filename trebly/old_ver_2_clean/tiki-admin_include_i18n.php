<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false, null, true);
$smarty->assign_by_ref("languages", $languages);

$ok = true;
if (!empty($_REQUEST['custom_save'])) {
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
	} else {
		$smarty->assign_by_ref('custom_lang', $_REQUEST['custom_lang']);
		$custom_file = 'lang/' . $_REQUEST['custom_lang'] . '/';
		if (!empty($tikidomain)) $custom_file.= "$tikidomain/";
		$custom_file.= "custom.php";
		$smarty->assign('custom_file', $custom_file);
		$custom_code = "<?php\r\n\$lang_custom = array(\r\n";
		foreach ($_REQUEST['from'] as $i=>$from) {
			if (!empty($from)) {
				$custom_code .= '"'.str_replace('"','\\"', $from).'" => "'.str_replace('"','\\"', $_REQUEST['to'][$i])."\",\r\n";
			}
		}
		$custom_code .= ");\r\n";
		$custom_code .= '$lang = $lang_custom + $lang;';
		if (!($fp = fopen($custom_file, 'w+'))) {
			$ok = false;
			$smarty->assign('custom_error', 'file');
		} else {
			if (!fwrite($fp, $custom_code)) {
				$ok = false;
				$smarty->assign('custom_error', 'file');
			}
			fclose($fp);
			global $cachelib; include_once ('lib/cache/cachelib.php');
			$cachelib->empty_cache('templates_c');
			$smarty->assign('custom_ok', 'y');
		}
	}
	if (!$ok) {
		$smarty->assign_by_ref('to', $_REQUEST['to']);
		$smarty->assign_by_ref('from', $_REQUEST['from']);
	}
}
if (!empty($_REQUEST['custom_lang'])) {
	ask_ticket('admin-inc-i18n');
	$custom_file = 'lang/' . $_REQUEST['custom_lang'] . '/';
	if (!empty($tikidomain)) $custom_file.= "$tikidomain/";
	$custom_file.= "custom.php";
	if (file_exists($custom_file)) {
		$lang = array();
		include ($custom_file);
		$smarty->assign_by_ref('custom_translation', $lang_custom);
	//} elseif (!is_writable($custom_file)) {
		//$smarty->assign('custom_error', 'file');
		//$smarty->assign('custom_file', $custom_file);
	}
	$smarty->assign_by_ref('custom_lang', $_REQUEST['custom_lang']);
	if ($ok) {
		$to = array('', '', '', '','','','','','','','');
		$smarty->assign('to', $to);
		$smarty->assign('from', $to);
	}
}

