<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pagepermissions.php,v 1.17 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");

include_once ('lib/notifications/notificationlib.php');
include_once('lib/structures/structlib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

include_once ("tiki-pagesetup.php");

include_once ('lib/wiki/wikilib.php');
$creator = $wikilib->get_creator($page);
$smarty->assign('creator', $creator);

// Let creator set permissions
if ($wiki_creator_admin == 'y') {
	if ($creator && $user && ($creator == $user)) {
		$tiki_p_admin_wiki = 'y';

		$smarty->assign('tiki_p_admin_wiki', 'y');
	}
}

// Now check permissions to access this page
if ($tiki_p_admin_wiki != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot assign permissions for this page"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["addemail"])) {
	check_ticket('page-perms');
	$notificationlib->add_mail_event('wiki_page_changes', 'wikipage' . $_REQUEST["page"], $_REQUEST["email"]);
}

if (isset($_REQUEST["removeemail"])) {
	check_ticket('page-perms');
	$notificationlib->remove_mail_event('wiki_page_changes', 'wikipage' . $_REQUEST["page"], $_REQUEST["removeemail"]);
}

$emails = $notificationlib->get_mail_events('wiki_page_changes', 'wikipage' . $_REQUEST["page"]);
$smarty->assign('emails', $emails);

if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

// Process the form to assign a new permission to this page
if (isset($_REQUEST["assign"])) {
	$userlib->assign_object_permission($_REQUEST["group"], $page, 'wiki page', $_REQUEST["perm"]);
}
// Process the form to assign a new permission to this structure
if(isset($_REQUEST["assignstructure"])) {
        $pageInfoTree=array();
	$pageInfoTree=$structlib->s_get_structure_pages($structlib->get_struct_ref_id($page));
	foreach($pageInfoTree as $subPage) {
	  $userlib->assign_object_permission($_REQUEST["group"],$subPage["pageName"],'wiki page',$_REQUEST["perm"]);
        }
}
// Process the form to remove a permission from the page
if (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == 'remove') {
		$userlib->remove_object_permission($_REQUEST["group"], $page, 'wiki page', $_REQUEST["perm"]);
	}
// Process the form to remove a permission from the structure
	if($_REQUEST["action"] == 'removestructure') {
        	$pageInfoTree=array();
		$pageInfoTree=$structlib->s_get_structure_pages($structlib->get_struct_ref_id($page));
		foreach($pageInfoTree as $subPage) {
			$userlib->remove_object_permission($_REQUEST["group"],$subPage["pageName"],'wiki page',$_REQUEST["perm"]);
		}
	}
}

// Now we have to get the individual page permissions if any
$page_perms = $userlib->get_object_permissions($page, 'wiki page');
$smarty->assign_by_ref('page_perms', $page_perms);

// Get a list of groups
$groups = $userlib->get_groups(0, -1, 'groupName_desc');
$smarty->assign_by_ref('groups', $groups["data"]);

// Get a list of permissions
$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'wiki');
$smarty->assign_by_ref('perms', $perms["data"]);

ask_ticket('page-perms');

$smarty->assign('mid', 'tiki-pagepermissions.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("tiki.tpl");

?>
