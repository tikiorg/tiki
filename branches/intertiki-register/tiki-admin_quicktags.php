<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_quicktags.php,v 1.22.2.1 2008-01-29 12:38:33 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/quicktags/quicktagslib.php');

if ($tiki_p_admin != 'y' && $tiki_p_admin_quicktags != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$auto_query_args = array('tagId','category','sort_mode');

if (!isset($_REQUEST["tagId"])) {
	$_REQUEST["tagId"] = 0;
}
$smarty->assign('tagId', $_REQUEST["tagId"]);

$smarty->assign('table_headers', array(
	'taglabel' => tra('Label'),
	'taginsert' => tra('Insert'),
	'tagicon' => tra('Icon'),
	'tagcategory' => tra('Category'),
));

if ($_REQUEST["tagId"]) {
	$info = $quicktagslib->get_quicktag($_REQUEST["tagId"]);
} else {
	$info = array();

	$info["taglabel"] = '';
	$info['taginsert'] = '';
	$info['tagicon'] = '';
}


if (isset($_REQUEST["remove"])) {
	$area = "delquicktag";
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$quicktagslib->remove_quicktag($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	$quicktagslib->replace_quicktag($_REQUEST["tagId"], $_REQUEST["taglabel"], $_REQUEST['taginsert'], $_REQUEST['tagicon'],$_REQUEST['tagcategory']);

	$info = array();
	$info['taglabel'] = '';
	$info['taginsert'] = '';
	$info['tagicon'] = '';
	$info['tagcategory'] = '';
	$smarty->assign('name', '');
}

$smarty->assign('info', $info);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'tagId_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (isset($_REQUEST["category"])) {
	$category = $_REQUEST["category"];
        if ($category == "All") $category = '';
} else {
	$category = '';
}

$smarty->assign('category', $category);
$smarty->assign_by_ref('sort_mode', $sort_mode);

$quicktags = $quicktagslib->list_quicktags($offset, $maxRecords, $sort_mode, $find, $category);
$smarty->assign('cant', $quicktags['cant']);
$smarty->assign_by_ref('quicktags', $quicktags["data"]);

$icon_path = array("images","img/icons","img/icn", "pics/icons");
$list_icons = $quicktagslib->list_icons($icon_path);
$smarty->assign_by_ref('list_icons', $list_icons);

//Need a method to find out, which quicktags are used in Tiki
$list_categories = array('wiki', 'newsletters', 'maps', 'trackers', 'calendar', 'blogs', 'articles', 'faqs', 'forums');
$smarty->assign_by_ref('list_categories', $list_categories);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
	
if ( $prefs['feature_ajax'] == 'y' ) {
	global $ajaxlib;
	require_once('lib/ajax/ajaxlib.php');
	$ajaxlib->registerTemplate('tiki-admin_quicktags_content.tpl');
	$ajaxlib->registerTemplate('tiki-admin_quicktags_edit.tpl');
}

// Display the template
$smarty->assign('mid', 'tiki-admin_quicktags.tpl');
$smarty->display("tiki.tpl");

?>
