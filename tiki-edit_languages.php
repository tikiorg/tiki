<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once('lib/language/Language.php');

$access->check_feature('lang_use_db');
$access->check_permission('tiki_p_edit_languages');

if (!empty ($_REQUEST['interactive_translation_mode']) && $tiki_p_edit_languages == 'y'){
	require_once("lib/multilingual/multilinguallib.php");
	$_SESSION['interactive_translation_mode']=$_REQUEST['interactive_translation_mode'];	
	if ($_REQUEST['interactive_translation_mode']=='off')  
		$cachelib->empty_cache('templates_c');
}
if (!isset($_SESSION['interactive_translation_mode']))
	$smarty->assign('interactive_translation_mode','off');
else
	$smarty->assign('interactive_translation_mode',$_SESSION['interactive_translation_mode']);

if (isset($_REQUEST["imp_language"])) {
	$imp_language = preg_replace('/\.\./','',$_REQUEST['imp_language']);
}

// Import
if (isset($_REQUEST["import"])) {
	check_ticket('import-lang');
	
	// first delete each record from language db table where the lang matches (if any)
	$query = "select `source` from `tiki_language` where `lang`=?";
	$result = $tikilib->query($query, array($imp_language));
	while ($res = $result->fetchRow()) {
		$query = "delete from `tiki_language` where `lang`=?";
		$result = $tikilib->query($query, array($imp_language));
	}
	
	// now we can start the import
	if (!isset(${"lang_$imp_language"})) {
		init_language($imp_language);
	}

	$impmsg = tra("Imported:")." lang/$imp_language/language.php";

	while (list($key, $val) = each(${"lang_$imp_language"})) {
		$query = "insert into `tiki_language` (`source`, `lang`, `tran`) values (?,?,?)";
		$result = $tikilib->query($query, array($key,$imp_language,$val), -1, -1, false);
	}

	$smarty->assign('impmsg', $impmsg);
}

// Get available languages
$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);

$db_languages = Language::getDbTranslatedLanguages();
$db_languages = $tikilib->format_language_list($db_languages);
$smarty->assign_by_ref('db_languages', $db_languages);

// check if is possible to write to lang/
// TODO: check if each language file is writable instead of the whole lang/ dir
if (is_writable('lang/')) {
	$smarty->assign('langIsWritable', true);
} else {
	$smarty->assign('langIsWritable', false);
}

// preserving variables
if (isset($_REQUEST["edit_language"])) {
	$smarty->assign('edit_language', $_REQUEST["edit_language"]);
	$edit_language = $_REQUEST["edit_language"];
} else {
	$smarty->assign('edit_language', $prefs['language']);
	$edit_language = $prefs['language'];
}

if (isset($_REQUEST["whataction"])) {
	$smarty->assign('whataction', $_REQUEST["whataction"]);
} else {
	$smarty->assign('whataction', '');
}

// Adding strings
if (isset($_REQUEST["add_tran"])) {
	check_ticket('edit-languages');
	$add_tran_source = $_REQUEST["add_tran_source"];
	$add_tran_tran = $_REQUEST["add_tran_tran"];

	if (strlen($add_tran_source) != 0 && strlen($add_tran_tran) != 0) {
#		$add_tran_source = htmlentities(strip_tags($add_tran_source), ENT_NOQUOTES, "UTF-8");
		$add_tran_source = strip_tags($add_tran_source);
#		$add_tran_tran = htmlentities(strip_tags($add_tran_tran), ENT_NOQUOTES, "UTF-8"); // we don't want htmlentities() here (converts e.g. é to &eauml;) -- luci
		$add_tran_tran = strip_tags($add_tran_tran);
		$query = "delete from `tiki_language` where binary `source` = ? and `lang` = ?";
		$tikilib->query($query,array($add_tran_source,$edit_language));
		$query = "insert into `tiki_language` (`source`, `lang`, `tran`, `changed`) values (?,?,?,?)";
		$tikilib->query($query,array($add_tran_source,$edit_language,$add_tran_tran,1));
		// remove from untranslated Table
		$query = "delete from `tiki_untranslated` where binary `source`=? and `lang`=?";
		$tikilib->query($query,array($add_tran_source,$edit_language));
	}
}

//Selection for untranslated Strings and edit translations
if (isset($_REQUEST["whataction"])) {
	$whataction = $_REQUEST["whataction"];
} else {
	$whataction = "";
}

if ($whataction == "edit_rec_sw" || $whataction == "edit_tran_sw") {

	check_ticket('edit-languages');
	//check if user has translated something
	if (isset($_REQUEST["tr_recnum"])) {
		for ($i = 0; $i <= $prefs['maxRecords']; $i++) {
			// Handle edits in translate recorded
			if (isset($_REQUEST["edit_rec_$i"])) {
				if (strlen($_REQUEST["edit_rec_tran_$i"]) > 0 && strlen($_REQUEST["edit_rec_source_$i"]) > 0) {
					$query = "insert into `tiki_language` (`source`, `lang`, `tran`, `changed`) values(?,?,?,?)";
					$result = $tikilib->query($query,array($_REQUEST["edit_rec_source_$i"],$edit_language,$_REQUEST["edit_rec_tran_$i"],1));
					$query = "delete from `tiki_untranslated` where binary `source`=? and lang=?";
					$result = $tikilib->query($query,array($_REQUEST["edit_rec_source_$i"],$edit_language));
				// No error checking necessary
				}
			} elseif (isset($_REQUEST["edt_tran_$i"])) {
				// Handle edits in edit translations
				if (strlen($_REQUEST["edit_edt_tran_$i"]) > 0 && strlen($_REQUEST["edit_edt_source_$i"]) > 0) {
#					$_REQUEST["edit_edt_tran_$i"] = strip_tags($_REQUEST["edit_edt_tran_$i"]); // yes, we even don't want striptags() for existing translations as some already have html tags included and we want to keep them, right ?
					$query = "update `tiki_language` set `tran`=?, `changed`=? where binary `source`=? and `lang`=?";
					$result = $tikilib->query($query,array($_REQUEST["edit_edt_tran_$i"],1,$_REQUEST["edit_edt_source_$i"],$edit_language));

					//if ($result->numRows()== 0 ) 
					if (!isset($result)) {
						$query = "insert into `tiki_language` (`source`, `lang`, `tran`, `changed`) values(?,?,?,?)";
						$result = $tikilib->query($query,array($_REQUEST["edit_edt_source_$i"],$edit_language,$_REQUEST["edit_edt_tran_$i"],1));
					}
				}
			} elseif (isset($_REQUEST["del_tran_$i"])) {
				// Handle deletes here
				if (strlen($_REQUEST["edit_edt_source_$i"]) > 0) {
					$query = "delete from `tiki_language` where binary `source`=? and `lang`=?";
					$result = $tikilib->query($query,array($_REQUEST["edit_edt_source_$i"],$edit_language));
				}
			}
		} // end of for ...
		// for resetting untranslated
		if (isset($_REQUEST["tran_reset"])) {
			$query = "delete from `tiki_untranslated`";
			$result = $tikilib->query($query);
		}
	}

	//show only a selection of maxRecords records
	if (!isset($_REQUEST["tr_recnum"]) || isset($_REQUEST["tran_search_sm"]) || isset($_REQUEST["langaction"])) {
		$smarty->assign('tr_recnum', 0);

		$tr_recnum = 0;
	} else {
		$tr_recnum = $_REQUEST["tr_recnum"];

		if (isset($_REQUEST["morerec"])) {
			$tr_recnum += $prefs['maxRecords'];
		}

		if (isset($_REQUEST["lessrec"])) {
			$tr_recnum -= $prefs['maxRecords'];
		}

		$smarty->assign('tr_recnum', $tr_recnum);
	}

	//Handle searches
	$squery = "";
	$squeryedit = "";
	$squeryrec = "";
	$bindvars = array($edit_language);
	$bindvars2 = array($edit_language);

	if (isset($_REQUEST["tran_search"])) {
		$tran_search = $_REQUEST['tran_search'];
		if (strlen($tran_search) > 0) {
			$smarty->assign('tran_search', $tran_search);
			$transe = "%".$tran_search."%";
			$squeryedit = " and (`source` like ? or `tran` like ?)";
			$squeryrec = " and `source` like ?";
			$bindvars[] = $transe;
			$bindvars[] = $transe;
			$bindvars2[] = $transe;
		}
	}

	//get array from db
	if (!isset($tr_recnum)) $tr_recnum = 0;

	$aquery = sprintf(" order by source limit %d,%d", $tr_recnum, $maxRecords);
	$sort_mode = "source_asc";

	if ($whataction == "edit_tran_sw") {
		$query = "select `source`, `tran` from `tiki_language` where `lang`=? $squeryedit order by ".$tikilib->convertSortMode($sort_mode);
		$nquery = "select count(*) from `tiki_language` where `lang`=? $squeryedit";
		$untr_numrows= $tikilib->getOne($nquery,$bindvars);
	        $result = $tikilib->query($query,$bindvars,$maxRecords,$tr_recnum);
	} elseif ($whataction == "edit_rec_sw") {
		$query = "select `source` from `tiki_untranslated` where `lang`=? $squeryrec order by ".$tikilib->convertSortMode($sort_mode);
		$nquery = "select count(*) from `tiki_untranslated` where `lang`=? $squeryrec";
		$untr_numrows= $tikilib->getOne($nquery,$bindvars2);
	        $result = $tikilib->query($query,$bindvars2,$maxRecords,$tr_recnum);
	}

	$smarty->assign('untr_numrows', $untr_numrows);

	//$i=$tr_recnum;
	if ($whataction == "edit_rec_sw") {
		$untranslated = array();

		$i = 0;

		while ($res = $result->fetchRow()) {
			$untranslated[] = $res["source"];

			$i++;
		}

		$smarty->assign_by_ref('untranslated', $untranslated);
	} elseif ($whataction == "edit_tran_sw") {
		$untranslated = array();

		$translation = array();
		$i = 0;

		while ($res = $result->fetchRow()) {
			$untranslated[] = $res["source"];

			$translation[] = $res["tran"];
			$i++;
		}

		$smarty->assign_by_ref('untranslated', $untranslated);
		$smarty->assign_by_ref('translation', $translation);
	}
}

if (isset($_REQUEST["exp_language"])) {
	$exp_language = $_REQUEST["exp_language"];
	$language = new Language($exp_language);
	$smarty->assign('exp_language', $exp_language);
} else {
	$smarty->assign('exp_language', $prefs['language']);
}

// Export
if (isset($_REQUEST['downloadFile'])) {
	check_ticket('import-lang');
	$data = $language->createCustomFile();
	header ("Content-type: application/unknown");
	header ("Content-Disposition: inline; filename=language.php");
	header ("Content-encoding: UTF-8");
	echo $data;
	exit (0);
}

// Write to language.php
if (isset($_REQUEST['exportToLanguage']) && $tiki_p_admin == 'y') {
	try {
		$stats = $language->writeLanguageFile();
	} catch (Exception $e) {
		$smarty->assign('msg', $e->getMessage());
		$smarty->display('error.tpl');
		die;
	}

	$expmsg = sprintf(tra('Wrote %d new strings and updated %d to lang/%s/language.php'), $stats['new'], $stats['modif'], $language->lang);
	$smarty->assign('expmsg', $expmsg);
}

ask_ticket('edit-languages');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$headerlib->add_cssfile('css/admin.css');

$headtitle = tra('Edit languages');
$description = tra('Edit or export/import languages');
$crumbs[] = new Breadcrumb($headtitle, $description, '', '', '');
$headtitle = breadcrumb_buildHeadTitle($crumbs);
$smarty->assign('headtitle', $headtitle);
$smarty->assign('trail', $crumbs);

$smarty->assign('mid', 'tiki-edit_languages.tpl');
$smarty->display("tiki.tpl");
