<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-ranking.php,v 1.5 2003-10-08 03:53:08 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($feature_ranking != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_ranking");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view pages"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["limit"])) {
	$limit = 10;
} else {
	$limit = $_REQUEST["limit"];
}

$smarty->assign_by_ref('limit', $limit);

$ranking = $tikilib->get_top_pages($limit);
$smarty->assign_by_ref('ranking', $ranking);

// Display the template
$smarty->assign('mid', 'tiki-ranking.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
