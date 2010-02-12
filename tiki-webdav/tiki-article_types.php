<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

$smarty->assign('headtitle',tra('Admin Article Types'));

$access->check_feature('feature_articles');

// PERMISSIONS: NEEDS p_admin or tiki_p_articles_admin_types
$access->check_permission(array('tiki_p_articles_admin_types'));

if(isset($_REQUEST["add_type"])) {
	$artlib->add_type($_REQUEST["new_type"]);
}
elseif(isset($_REQUEST["remove_type"])) {
	$area = "delarticletype";
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$artlib->remove_type($_REQUEST["remove_type"]);
	} else {
		key_get($area);
	}
}
elseif(isset($_REQUEST["update_type"])) {
	foreach(array_keys($_REQUEST["type_array"]) as $this_type) {
		if (!isset($_REQUEST["use_ratings"][$this_type])) {$_REQUEST["use_ratings"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_pre_publ"][$this_type])) {$_REQUEST["show_pre_publ"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_post_expire"][$this_type])) {$_REQUEST["show_post_expire"][$this_type] = 'n';}
		if (!isset($_REQUEST["heading_only"][$this_type])) {$_REQUEST["heading_only"][$this_type] = 'n';}
		if (!isset($_REQUEST["allow_comments"][$this_type])) {$_REQUEST["allow_comments"][$this_type] = 'n';}
		if (!isset($_REQUEST["comment_can_rate_article"][$this_type])) {$_REQUEST["comment_can_rate_article"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_image"][$this_type])) {$_REQUEST["show_image"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_avatar"][$this_type])) {$_REQUEST["show_avatar"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_author"][$this_type])) {$_REQUEST["show_author"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_pubdate"][$this_type])) {$_REQUEST["show_pubdate"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_expdate"][$this_type])) {$_REQUEST["show_expdate"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_reads"][$this_type])) {$_REQUEST["show_reads"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_size"][$this_type])) {$_REQUEST["show_size"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_topline"][$this_type])) {$_REQUEST["show_topline"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_subtitle"][$this_type])) {$_REQUEST["show_subtitle"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_image_caption"][$this_type])) {$_REQUEST["show_image_caption"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_linkto"][$this_type])) {$_REQUEST["show_linkto"][$this_type] = 'n';}
		if (!isset($_REQUEST["show_lang"][$this_type])) {$_REQUEST["show_lang"][$this_type] = 'n';}
		if (!isset($_REQUEST["creator_edit"][$this_type])) {$_REQUEST["creator_edit"][$this_type] = 'n';}
		$artlib->edit_type($this_type, 
				$_REQUEST["use_ratings"][$this_type], 
				$_REQUEST["show_pre_publ"][$this_type], 
				$_REQUEST["show_post_expire"][$this_type], 
				$_REQUEST["heading_only"][$this_type], 
				$_REQUEST["allow_comments"][$this_type], 
				$_REQUEST["comment_can_rate_article"][$this_type], 
				$_REQUEST["show_image"][$this_type], 
				$_REQUEST["show_avatar"][$this_type], 
				$_REQUEST["show_author"][$this_type], 
				$_REQUEST["show_pubdate"][$this_type], 
				$_REQUEST["show_expdate"][$this_type], 
				$_REQUEST["show_reads"][$this_type], 
				$_REQUEST["show_size"][$this_type], 
				$_REQUEST["show_topline"][$this_type], 
				$_REQUEST["show_subtitle"][$this_type], 
				$_REQUEST["show_linkto"][$this_type], 
				$_REQUEST["show_image_caption"][$this_type], 
				$_REQUEST["show_lang"][$this_type], 
				$_REQUEST["creator_edit"][$this_type]);
	}
}

$types = $artlib->list_types();
$smarty->assign('types', $types);

include_once ('tiki-section_options.php');

$smarty->assign('mid', 'tiki-article_types.tpl');
$smarty->display("tiki.tpl");
