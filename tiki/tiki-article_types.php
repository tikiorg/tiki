<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-article_types.php,v 1.1 2003-10-28 03:58:29 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

if ($feature_articles != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// PERMISSIONS: NEEDS p_admin
if ($tiki_p_admin_cms != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST["addtype"])) {
	$artlib->add_edit_type($_REQUEST["type"], $_REQUEST["use_ratings"], $_REQUEST["show_pre_publ"], $_REQUEST["show_post_expire"], $_REQUEST["heading_only"], $_REQUEST["allow_comments"], $_REQUEST["show_image"], $_REQUEST["show_avatar"], $_REQUEST["show_author"], $_REQUEST["show_pubdate"], $_REQUEST["show_expdate"], $_REQUEST["show_reads"]);
}

if (isset($_REQUEST["type"])) {
	$artlib->get_type($_REQUEST["type"]);
}

if (isset($_REQUEST["remove"])) {
	$artlib->remove_type($_REQUEST["remove"]);
}

$types = $artlib->list_types();

$smarty->assign('types', $types);

$smarty->assign('mid', 'tiki-article_types.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
