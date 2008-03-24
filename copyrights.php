<?php

// $Header: /cvsroot/tikiwiki/tiki/copyrights.php,v 1.18 2007-10-12 07:55:23 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

if ($prefs['wiki_feature_copyrights'] != 'y') {
	$smarty->assign('msg', tra("The copyright management feature is not enabled."));

	$smarty->display("error.tpl");
	die;
}


$access->check_permission(array('tiki_p_edit_copyrights'), tra("Copyright management"));

include_once ("lib/copyrights/copyrightslib.php");
global $dbTiki;
$copyrightslib = new CopyrightsLib($dbTiki);

if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('page', $_REQUEST["page"]);
$page = $_REQUEST["page"];

if (isset($_REQUEST['addcopyright'])) {
	if ($prefs['wiki_feature_copyrights'] == 'y' && isset($_REQUEST['copyrightTitle']) && isset($_REQUEST['copyrightYear'])
		&& isset($_REQUEST['copyrightAuthors']) && !empty($_REQUEST['copyrightYear']) && !empty($_REQUEST['copyrightTitle'])) {
		$copyrightYear = $_REQUEST['copyrightYear'];

		$copyrightTitle = $_REQUEST['copyrightTitle'];
		$copyrightAuthors = $_REQUEST['copyrightAuthors'];
		$copyrightslib->add_copyright($page, $copyrightTitle, $copyrightYear, $copyrightAuthors, $user);
	} else {
		$msg = tra("You must supply all the information, including title and year.");
		$access->display_error(basename(__FILE__), $msg);
	}
}

if (isset($_REQUEST['editcopyright'])) {
	if ($prefs['wiki_feature_copyrights'] == 'y' && isset($_REQUEST['copyrightTitle']) && isset($_REQUEST['copyrightYear'])
		&& isset($_REQUEST['copyrightAuthors']) && !empty($_REQUEST['copyrightYear']) && !empty($_REQUEST['copyrightTitle'])) {
		$copyrightId = $_REQUEST['copyrightId'];

		$copyrightYear = $_REQUEST['copyrightYear'];
		$copyrightTitle = $_REQUEST['copyrightTitle'];
		$copyrightAuthors = $_REQUEST['copyrightAuthors'];
		$copyrightslib->edit_copyright($copyrightId, $copyrightTitle, $copyrightYear, $copyrightAuthors, $user);
	} else {
		$msg = tra("You must supply all the information, including title and year.");
		$access->display_error(basename(__FILE__), $msg);
	}
}

if (isset($_REQUEST['action']) && isset($_REQUEST['copyrightId'])) {
	if ($_REQUEST['action'] == 'up') {
		$copyrightslib->up_copyright($_REQUEST['copyrightId']);
	} elseif ($_REQUEST['action'] == 'down') {
		$copyrightslib->down_copyright($_REQUEST['copyrightId']);
	} elseif ($_REQUEST['action'] == 'delete') {
		$area = 'delcopyright';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$copyrightslib->remove_copyright($_REQUEST['copyrightId']);
		} else {
			key_get($area);
		}
	}
}

$copyrights = $copyrightslib->list_copyrights($_REQUEST["page"]);
$smarty->assign('copyrights', $copyrights["data"]);

// Display the template
$smarty->assign('mid', 'copyrights.tpl');
$smarty->display("tiki.tpl");

?>
