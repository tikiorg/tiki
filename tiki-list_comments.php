<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-list_comments.php,v 1.2.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once('tiki-setup.php');
include_once('lib/commentslib.php');
$commentslib = new Comments($dbTiki);

if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$list_types = array();
if ($prefs['feature_wiki_comments'] == 'y')
	$list_types['wiki page'] = 'n';
if ($prefs['feature_article_comments'] == 'y')
	$list_types['article'] = 'n';
if ($prefs['feature_blog_comments'] == 'y' || $prefs['feature_blogposts_comments'] == 'y')
	$list_types['blog'] = 'n';
if ($prefs['feature_file_galleries_comments'] == 'y')
	$list_types['file gallery'] = 'n';
if ($prefs['feature_image_galleries_comments'] == 'y')
	$list_types['image gallery'] = 'n';
if ($prefs['feature_poll_comments'] == 'y')
	$list_types['poll'] = 'n';
if ($prefs['feature_faq_comments'] == 'y')
	$list_types['faq'] = 'n';
//if ($prefs['feature_forums'] == 'y')
//	$list_types['forum'] = 'n';
$string_types = '';
if (isset($_REQUEST['types'])) {
	foreach ($_REQUEST['types'] as $type) {
		$list_types[$type] = 'y';
		$string_types .= "&amp;types[]=$type";
	}
	$smarty->assign('string_types', $string_types);
} else {
	foreach ($list_types as $type=>$selected) {
		$list_types[$type] = 'y';
		$_REQUEST['types'][] = $type;
	}
}
$smarty->assign('list_types', $list_types);
$smarty->assign('types', $_REQUEST['types']);
if (in_array('blog', $_REQUEST['types']) && !in_array('post', $_REQUEST['types']) && $prefs['feature_blogposts_comments'] =='y')
		$_REQUEST['types'][] = 'post';

if (isset($_REQUEST['remove'])) {
	check_ticket('list_comments');
	$area = 'removecomment';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		foreach ($_REQUEST["checked"] as $remove) {
			$commentslib->remove_comment($remove);
		}
	} elseif ($prefs['feature_ticketlib2'] == 'y') {
		$ch = "";
		foreach ($_REQUEST['checked'] as $c) {
			$ch .= "&amp;checked[]=".urlencode($c);
		}
		foreach ($_REQUEST['types'] as $c) {
			$ch .= "&amp;types[]=".urlencode($c);
		}
		if (isset($_REQUEST['find']))
			$ch .= '&amp;find='.$_REQUEST['find'];
		if (isset($_REQUEST['sort_mode']))
			$ch .= '&amp;sort_mode='.$_REQUEST['sort_mode'];
		key_get($area, '', "tiki-list_comments.php?remove=y&amp;$ch");
	} else
		key_get($area);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'commentDate_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
if (isset($_REQUEST["offset"])) {
	$offset = $_REQUEST["offset"];
} else {
	$offset = 0;
}
$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = strip_tags($_REQUEST["find"]);
} else {
	$find = '';
}
$smarty->assign('find', $find);

$comments = $commentslib->get_all_comments($_REQUEST['types'], $offset, $maxRecords, $sort_mode, $find, 'y');
$smarty->assign_by_ref('comments', $comments['data']);
$smarty->assign_by_ref('cant', $comments['cant']);

ask_ticket('list_comments');
$smarty->assign('mid', 'tiki-list_comments.tpl');
$smarty->display("tiki.tpl");

?>
