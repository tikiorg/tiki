<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_fgal.php,v 1.21 2006-12-12 17:22:54 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/filegals/filegallib.php');

if (isset($_REQUEST["filegalset"])) {
    simple_set_value("home_file_gallery");
}

if (isset($_REQUEST["filegalfeatures"])) {
	check_ticket('admin-inc-fgal');

	simple_set_toggle("feature_file_galleries_rankings");
	simple_set_toggle("feature_file_galleries_batch");
	simple_set_value("fgal_match_regex");
	simple_set_value("fgal_nmatch_regex");

        // Check for last character being a / or a \
        if (substr($_REQUEST["fgal_use_dir"], -1) != "\\" && substr($_REQUEST["fgal_use_dir"], -1) != "/" && $_REQUEST["fgal_use_dir"] != "")  {
                $_REQUEST["fgal_use_dir"] .= "/";
        }

        // Check for last character being a / or a \
        if (substr($_REQUEST["fgal_podcast_dir"], -1) != "\\" && substr($_REQUEST["fgal_podcast_dir"], -1) != "/" && $_REQUEST["fgal_podcast_dir"] != "")  {
                $_REQUEST["fgal_podcast_dir"] .= "/";
        }
		if (substr($_REQUEST["fgal_batch_dir"], -1) != "\\" && substr($_REQUEST["fgal_batch_dir"], -1) != "/" && $_REQUEST["fgal_batch_dir"] != "") {
			$_REQUEST["fgal_batch_dir"] .= "/";
		}

	simple_set_value("fgal_use_db");
	simple_set_value("fgal_use_dir");
	simple_set_value("fgal_podcast_dir");
	simple_set_value("fgal_batch_dir");

	simple_set_toggle("feature_file_galleries_comments");
	simple_set_toggle("feature_file_galleries_author");
	simple_set_toggle("fgal_allow_duplicates");
}

if (isset($_REQUEST["filegallistprefs"])) {
	check_ticket('admin-inc-fgal');

	simple_set_toggle("fgal_list_name");
	simple_set_toggle("fgal_list_description");
	simple_set_toggle("fgal_list_type");
	simple_set_toggle("fgal_list_created");
	simple_set_toggle("fgal_list_lastmodif");
	simple_set_toggle("fgal_list_user");
	simple_set_toggle("fgal_list_files");
	simple_set_toggle("fgal_list_hits");
	simple_set_toggle('fgal_list_parent');
	$_REQUEST['fgal_sort_mode'] = (empty($_REQUEST['sortorder'])?'created':$_REQUEST['sortorder']).'_'.(empty($_REQUEST['sortdirection'])?'desc':$_REQUEST['sortdirection']);
	$fgal_sort_mode = $_REQUEST['fgal_sort_mode'];
	simple_set_value('fgal_sort_mode');
}

if (isset($_REQUEST["filegalcomprefs"])) {
	check_ticket('admin-inc-fgal');

	simple_set_value("file_galleries_comments_per_page");
	simple_set_value("file_galleries_comments_default_ordering");
}

if (isset($_REQUEST["filegalhandlers"])) {
	check_ticket('admin-inc-fgal');
	$mimes = $_REQUEST["mimes"];
	foreach($mimes as $mime=>$cmd) {
		if (empty($cmd))
			$filegallib->delete_file_handler($mime);
		else
			$filegallib->change_file_handler($mime,$cmd);
	}
	if (!empty($_REQUEST['newMime']) && !empty($_REQUEST['newCmd'])) {
		$filegallib->change_file_handler($_REQUEST['newMime'],$_REQUEST['newCmd']);
	}
	if (isset($_REQUEST["fgal_enable_auto_indexing"])) {
		$tikilib->set_preference("fgal_enable_auto_indexing", 'y');

		$smarty->assign('fgal_enable_auto_indexing', 'y');
	} else {
		$tikilib->set_preference("fgal_enable_auto_indexing", 'n');

		$smarty->assign('fgal_enable_auto_indexing', 'n');
	}
}

if (isset($_REQUEST["filegalredosearch"])) {
	$filegallib->reindex_all_files_for_search_text();
}

if (!empty($fgal_sort_mode)) {
	preg_match('/(.*)_(asc|desc)/', $fgal_sort_mode, $matches);
	$smarty->assign('sortorder',$matches[1]);
	$smarty->assign('sortdirection', $matches[2]);
} else {
	$smarty->assign('sortorder', 'created');
	$smarty->assign('sortdirection', 'desc');
}
$options_sortorder = array(tra('Creation Date')=>'created', tra('Name')=>'name', tra('Last modification date')=>'lastModif', tra('Hits')=>'hits', tra('Owner') => 'user', tra('Description') => 'description');
$smarty->assign_by_ref('options_sortorder', $options_sortorder);

$handlers = $filegallib->get_file_handlers();
ksort($handlers);
$smarty->assign("fgal_handlers",$handlers);

$file_galleries = $tikilib->list_visible_file_galleries(0, -1, 'name_desc', 'admin', '');
$smarty->assign_by_ref('file_galleries', $file_galleries["data"]);

$smarty->assign("fgal_match_regex", $tikilib->get_preference("fgal_match_regex", ''));
ask_ticket('admin-inc-fgal');
?>
