<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-imexport_languages.php,v 1.7 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($lang_use_db != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_edit_languages != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$query = "select lang from tiki_languages";
$result = $tikilib->query($query);
$languages = array();

while ($res = $result->fetchRow()) {
	$languages[] = $res["0"];
}

$smarty->assign_by_ref('languages', $languages);

// Get available languages from Disk
$languages_files = array();
$languages_files = $tikilib->list_languages();
$smarty->assign_by_ref('languages_files', $languages_files);

// Save Variables
if (isset($_REQUEST["imp_language"])) {
	$imp_language = $_REQUEST["imp_language"];

	$smarty->assign('imp_language', $imp_language);
}

if (isset($_REQUEST["exp_language"])) {
	$exp_language = $_REQUEST["exp_language"];

	$smarty->assign('exp_language', $exp_language);
}

// Import
if (isset($_REQUEST["import"])) {
	include_once ('lang/' . $imp_language . '/language.php');

	$impmsg = "Included lang/" . $imp_language . "/language.php";
	$query = "insert into tiki_languages values ('" . $imp_language . "','')";
	$result = $tikilib->query($query, array(), -1, -1, false);

	while (list($key, $val) = each($lang)) {
		$query = "insert into tiki_language values ('" . addslashes($key). "','" . $imp_language . "','" . addslashes($val). "')";

		$result = $tikilib->query($query, array(), -1, -1, false);
	}

	$smarty->assign('impmsg', $impmsg);
}

// Export
if (isset($_REQUEST["export"])) {
	$query = "select source, tran from tiki_language where lang='" . $exp_language . "'";

	$result = $tikilib->query($query);
	$data = "<?php\n\$lang=Array(\n";

	while ($res = $result->fetchRow()) {
		$data = $data . "\"" . $res["0"] . "\" => \"" . $res["1"] . "\",\n";
	}

	$data = $data . ");\n?>";
	header ("Content-type: application/unknown");
	header ("Content-Disposition: inline; filename=language.php");
	echo $data;
	exit (0);
	$smarty->assign('expmsg', $expmsg);
}

$smarty->assign('mid', 'tiki-imexport_languages.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>