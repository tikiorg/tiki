<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_fgal.php,v 1.8 2004-03-29 21:26:28 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}


if (isset($_REQUEST["filegalset"])) {
	$tikilib->set_preference("home_file_gallery", $_REQUEST["homeFileGallery"]);

	$smarty->assign('home_file_gallery', $_REQUEST["homeFileGallery"]);
}

if (isset($_REQUEST["filegalfeatures"])) {
	check_ticket('admin-inc-fgal');
	if (isset($_REQUEST["feature_file_galleries_rankings"]) && $_REQUEST["feature_file_galleries_rankings"] == "on") {
		$tikilib->set_preference("feature_file_galleries_rankings", 'y');

		$smarty->assign("feature_file_galleries_rankings", 'y');
	} else {
		$tikilib->set_preference("feature_file_galleries_rankings", 'n');

		$smarty->assign("feature_file_galleries_rankings", 'n');
	}

	$tikilib->set_preference("fgal_match_regex", $_REQUEST["fgal_match_regex"]);
	$smarty->assign('fgal_match_regex', $_REQUEST["fgal_match_regex"]);
	$tikilib->set_preference("fgal_nmatch_regex", $_REQUEST["fgal_nmatch_regex"]);
	$smarty->assign('fgal_nmatch_regex', $_REQUEST["fgal_nmatch_regex"]);

        // Check for last character being a / or a \
        if (substr($_REQUEST["fgal_use_dir"], -1) != "\\" && substr($_REQUEST["fgal_use_dir"], -1) != "/" && $_REQUEST["fgal_use_dir"] != "")  {
                $_REQUEST["fgal_use_dir"] .= "/";
        }

	$tikilib->set_preference("fgal_use_db", $_REQUEST["fgal_use_db"]);
	$smarty->assign('fgal_use_db', $_REQUEST["fgal_use_db"]);
	$tikilib->set_preference("fgal_use_dir", $_REQUEST["fgal_use_dir"]);
	$smarty->assign('fgal_use_dir', $_REQUEST["fgal_use_dir"]);

	if (isset($_REQUEST["feature_file_galleries_comments"]) && $_REQUEST["feature_file_galleries_comments"] == "on") {
		$tikilib->set_preference("feature_file_galleries_comments", 'y');

		$smarty->assign("feature_file_galleries_comments", 'y');
	} else {
		$tikilib->set_preference("feature_file_galleries_comments", 'n');

		$smarty->assign("feature_file_galleries_comments", 'n');
	}
}

if (isset($_REQUEST["filegallistprefs"])) {
	check_ticket('admin-inc-fgal');
	if (isset($_REQUEST["fgal_list_name"])) {
		$tikilib->set_preference("fgal_list_name", 'y');

		$smarty->assign('fgal_list_name', 'y');
	} else {
		$tikilib->set_preference("fgal_list_name", 'n');

		$smarty->assign('fgal_list_name', 'n');
	}

	if (isset($_REQUEST["fgal_list_description"])) {
		$tikilib->set_preference("fgal_list_description", 'y');

		$smarty->assign('fgal_list_description', 'y');
	} else {
		$tikilib->set_preference("fgal_list_description", 'n');

		$smarty->assign('fgal_list_description', 'n');
	}

	if (isset($_REQUEST["fgal_list_created"])) {
		$tikilib->set_preference("fgal_list_created", 'y');

		$smarty->assign('fgal_list_created', 'y');
	} else {
		$tikilib->set_preference("fgal_list_created", 'n');

		$smarty->assign('fgal_list_created', 'n');
	}

	if (isset($_REQUEST["fgal_list_lastmodif"])) {
		$tikilib->set_preference("fgal_list_lastmodif", 'y');

		$smarty->assign('fgal_list_lastmodif', 'y');
	} else {
		$tikilib->set_preference("fgal_list_lastmodif", 'n');

		$smarty->assign('fgal_list_lastmodif', 'n');
	}

	if (isset($_REQUEST["fgal_list_user"])) {
		$tikilib->set_preference("fgal_list_user", 'y');

		$smarty->assign('fgal_list_user', 'y');
	} else {
		$tikilib->set_preference("fgal_list_user", 'n');

		$smarty->assign('fgal_list_user', 'n');
	}

	if (isset($_REQUEST["fgal_list_files"])) {
		$tikilib->set_preference("fgal_list_files", 'y');

		$smarty->assign('fgal_list_files', 'y');
	} else {
		$tikilib->set_preference("fgal_list_files", 'n');

		$smarty->assign('fgal_list_files', 'n');
	}

	if (isset($_REQUEST["fgal_list_hits"])) {
		$tikilib->set_preference("fgal_list_hits", 'y');

		$smarty->assign('fgal_list_hits', 'y');
	} else {
		$tikilib->set_preference("fgal_list_hits", 'n');

		$smarty->assign('fgal_list_hits', 'n');
	}
}

if (isset($_REQUEST["filegalcomprefs"])) {
	check_ticket('admin-inc-fgal');
	if (isset($_REQUEST["file_galleries_comments_per_page"])) {
		$tikilib->set_preference("file_galleries_comments_per_page", $_REQUEST["file_galleries_comments_per_page"]);

		$smarty->assign('file_galleries_comments_per_page', $_REQUEST["file_galleries_comments_per_page"]);
	}

	if (isset($_REQUEST["file_galleries_comments_default_ordering"])) {
		$tikilib->set_preference("file_galleries_comments_default_ordering", $_REQUEST["file_galleries_comments_default_ordering"]);

		$smarty->assign('file_galleries_comments_default_ordering', $_REQUEST["file_galleries_comments_default_ordering"]);
	}
}

$file_galleries = $tikilib->list_visible_file_galleries(0, -1, 'name_desc', 'admin', '');
$smarty->assign_by_ref('file_galleries', $file_galleries["data"]);

$smarty->assign("fgal_match_regex", $tikilib->get_preference("fgal_match_regex", ''));
ask_ticket('admin-inc-fgal');
?>
