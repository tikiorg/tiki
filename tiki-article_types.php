<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-article_types.php,v 1.7 2003-11-17 15:44:28 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

if ($feature_articles != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

// PERMISSIONS: NEEDS p_admin
if ($tiki_p_admin_cms != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
} else {
	$type = '';
}
if (isset($_REQUEST["use_ratings"])) {
        $use_ratings = $_REQUEST["use_ratings"];
} else {
        $use_ratings = '';
}
if (isset($_REQUEST["show_pre_publ"])) {
	$show_pre_publ = $_REQUEST["show_pre_publ"];
} else {
	$show_pre_publ = '';
}
if (isset($_REQUEST["show_post_expire"])) {
	$show_post_expire = $_REQUEST["show_post_expire"];
} else {
	$show_post_expire = 'y';
}
if (isset($_REQUEST["heading_only"])) {
	$heading_only = $_REQUEST["heading_only"];
} else {
	$heading_only = '';
}
if (isset($_REQUEST["allow_comments"])) {
	$allow_comments = $_REQUEST["allow_comments"];
} else {
	$allow_comments = 'y';
}
if (isset($_REQUEST["comment_can_rate_article"])) {
	$comment_can_rate_article = $_REQUEST["comment_can_rate_article"];
} else {
	$comment_can_rate_article = 'y';
}
if (isset($_REQUEST["show_image"])) {
	$show_image = $_REQUEST["show_image"];
} else {
	$show_image = 'y';
}
if (isset($_REQUEST["show_avatar"])) {
	$show_avatar = $_REQUEST["show_avatar"];
} else {
	$show_avatar = '';
}
if (isset($_REQUEST["show_author"])) {
	$show_author = $_REQUEST["show_author"];
} else {
	$show_author = 'y';
}
if (isset($_REQUEST["show_pubdate"])) {
	$show_pubdate = $_REQUEST["show_pubdate"];
} else {
	$show_pubdate = 'y';
}
if (isset($_REQUEST["show_expdate"])) {
	$show_expdate = $_REQUEST["show_expdate"];
} else {
	$show_expdate = '';
}
if (isset($_REQUEST["show_reads"])) {
	$show_reads = $_REQUEST["show_reads"];
} else {
	$show_reads = 'y';
}
if (isset($_REQUEST["show_size"])) {
	$show_size = $_REQUEST["show_size"];
} else {
	$show_size = 'y';
}
if (isset($_REQUEST["creator_edit"])) {
	$creator_edit = $_REQUEST["creator_edit"];
} else {
	$creator_edit = 'y';
}

if (isset($_REQUEST["addtype"])) {
	$artlib->add_edit_type($type, $use_ratings, $show_pre_publ, $show_post_expire, $heading_only, $allow_comments, $comment_can_rate_article, $show_image, $show_avatar, $show_author, $show_pubdate, $show_expdate, $show_reads, $show_size, $creator_edit);
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
$smarty->display("tiki.tpl");

?>
