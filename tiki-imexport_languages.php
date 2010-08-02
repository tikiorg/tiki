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
	// TODO: check if the lang to import to db is not the same as the currently used one, otherwise the import fails with PHP error "Variable passed to each() is not an array or object"
	
	// first delete each record from language db table where the lang matches (if any)
	$query = "select `source` from `tiki_language` where `lang`=?";
	$result = $tikilib->query($query, array($imp_language));
	while ($res = $result->fetchRow()) {
		$query = "delete from `tiki_language` where `lang`=?";
		$result = $tikilib->query($query, array($imp_language));
	}
	// delete also record for the lang from the languages db table
	$query = "delete from `tiki_languages` where `lang`=?";
	$result = $tikilib->query($query, array($imp_language));
	
	// now we can start the import
	include_once ('lang/' . $imp_language . '/language.php');

	$impmsg = tra("Imported:")." lang/$imp_language/language.php";
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
	header ("Content-encoding: UTF-8");
	echo $data;
	exit (0);
	$smarty->assign('expmsg', $expmsg);
}

// Delete
if (isset($_REQUEST["delete"])) {
	/* TODO: the delete lang code from the import part should go probably here instead with confirmation step and related changes in the tpl */
} 

// edit source string
if (isset($_REQUEST["edit_source"])) {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("Only the administrator can upload language files."));
		$smarty->display("error.tpl");
		die;
	}	
	check_ticket('import-lang');
	$orig_source = $_REQUEST["orig_source"];
	$new_source = $_REQUEST["new_source"];
	$query_cant = "select count(*)  from `tiki_language` where `source`=?";
	$cant = $tikilib->getOne($query_cant,$orig_source);
	if ($cant) {
		$query = "update `tiki_language` set `source`=? where `source`=?";
		$result = $tikilib->query($query,array($new_source,$orig_source));
		$editsourcemsg = $cant . ' ' . $orig_source . ' changed to ' . $new_source;
	} else {
		$editsourcemsg = 'String not found';
	}
	$smarty->assign('editsourcemsg', $editsourcemsg);
}

ask_ticket('import-lang');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-imexport_languages.tpl');
$smarty->display("tiki.tpl");
