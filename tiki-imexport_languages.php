<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_permission('tiki_p_edit_languages');

$query = "select `lang` from `tiki_languages`";
$result = $tikilib->query($query,array());
$languages = array();

while ($res = $result->fetchRow()) {
	$languages[] = $res["lang"];
}

// Lookup translated names for the languages
$languages = $tikilib->format_language_list($languages);
$smarty->assign_by_ref('languages',$languages);

if (isset($_REQUEST["exp_language"])) {
	$exp_language = $_REQUEST["exp_language"];

	$smarty->assign('exp_language', $exp_language);
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
	header ("Content-encoding: UTF-8");
	echo $data;
	exit (0);
	$smarty->assign('expmsg', $expmsg);
}

ask_ticket('import-lang');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-imexport_languages.tpl');
$smarty->display("tiki.tpl");
