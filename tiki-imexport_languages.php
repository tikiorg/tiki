<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-imexport_languages.php,v 1.20.2.1 2007-10-22 23:19:08 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($tiki_p_edit_languages != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$query = "select `lang` from `tiki_languages`";
$result = $tikilib->query($query,array());
$languages = array();

while ($res = $result->fetchRow()) {
	$languages[] = $res["lang"];
}

// Lookup translated names for the languages
$languages = $tikilib->format_language_list($languages);
$smarty->assign_by_ref('languages',$languages);

// Get available languages from Disk
$languages_files = array();
$languages_files = $tikilib->list_languages();
$smarty->assign_by_ref('languages_files', $languages_files);

// Save Variables
if (isset($_REQUEST["imp_language"])) {
	$imp_language = preg_replace('/\.\./','',$_REQUEST['imp_language']);

	$smarty->assign('imp_language', $imp_language);
}

if (isset($_REQUEST["exp_language"])) {
	$exp_language = $_REQUEST["exp_language"];

	$smarty->assign('exp_language', $exp_language);
}

// Import
if (isset($_REQUEST["import"])) {
	check_ticket('import-lang');
	include_once ('lang/' . $imp_language . '/language.php');

	$impmsg = "Included lang/" . $imp_language . "/language.php";
	$query = "insert into `tiki_languages` values (?,?)";
	$result = $tikilib->query($query, array($imp_language,''), -1, -1, false);

	while (list($key, $val) = each($lang)) {
		$query = "insert into `tiki_language` values (?,?,?)";
		$result = $tikilib->query($query, array($key,$imp_language,$val), -1, -1, false);
	}

	$smarty->assign('impmsg', $impmsg);
}

// Export
if (isset($_REQUEST["export"])) {
	check_ticket('import-lang');
	$query = "select `source`, `tran` from `tiki_language` where `lang`=?";
	$result = $tikilib->query($query,array($exp_language));
	$data = "<?php\n\$lang=Array(\n";

	while ($res = $result->fetchRow()) {
	    $source = str_replace('"', '\\"', $res['source']);
	    $source = str_replace('$', '\\$', $source);
	    $tran = str_replace('"', '\\"', $res['tran']);
	    $tran = str_replace('$', '\\$', $tran);
	    $data = $data . "\"" . $source . "\" => \"" . $tran . "\",\n";
	}

	$data = $data . ");\n?>";
	header ("Content-type: application/unknown");
	header ("Content-Disposition: inline; filename=language.php");
	echo $data;
	exit (0);
	$smarty->assign('expmsg', $expmsg);
}
ask_ticket('import-lang');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-imexport_languages.tpl');
$smarty->display("tiki.tpl");

?>
