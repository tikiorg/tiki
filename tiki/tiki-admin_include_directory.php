<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_directory.php,v 1.4 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["directory"])) {
	check_ticket('admin-inc-directory');
	if (isset($_REQUEST["directory_validate_urls"]) && $_REQUEST["directory_validate_urls"] == "on") {
		$tikilib->set_preference('directory_validate_urls', 'y');

		$smarty->assign('directory_validate_urls', 'y');
	} else {
		$tikilib->set_preference('directory_validate_urls', 'n');

		$smarty->assign('directory_validate_urls', 'n');
	}

	$tikilib->set_preference('directory_columns', $_REQUEST["directory_columns"]);
	$tikilib->set_preference('directory_links_per_page', $_REQUEST["directory_links_per_page"]);
	$tikilib->set_preference('directory_open_links', $_REQUEST["directory_open_links"]);
	$smarty->assign('directory_columns', $_REQUEST['directory_columns']);
	$smarty->assign('directory_links_per_page', $_REQUEST['directory_links_per_page']);
	$smarty->assign('directory_open_links', $_REQUEST['directory_open_links']);
}
ask_ticket('admin-inc-directory');
?>
