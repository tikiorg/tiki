<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
require_once ('tiki-setup.php');
$artlib = TikiLib::lib('art');
$access->check_feature('feature_articles');

// PERMISSIONS: NEEDS p_admin or tiki_p_articles_admin_types
$access->check_permission(array('tiki_p_articles_admin_types'));

if (isset($_REQUEST["add_type"])) {
	$artlib->add_type($_REQUEST["new_type"]);
} elseif (isset($_REQUEST["remove_type"])) {
	$access->check_authenticity();
	$artlib->remove_type($_REQUEST["remove_type"]);
} elseif (isset($_REQUEST["update_type"])) {
	foreach (array_keys($_REQUEST["type_array"]) as $this_type) {
		if (!isset($_REQUEST["use_ratings"][$this_type]))								$_REQUEST["use_ratings"][$this_type] = 'n';
		if (!isset($_REQUEST["show_pre_publ"][$this_type]))							$_REQUEST["show_pre_publ"][$this_type] = 'n';
		if (!isset($_REQUEST["show_post_expire"][$this_type]))					$_REQUEST["show_post_expire"][$this_type] = 'n';
		if (!isset($_REQUEST["heading_only"][$this_type]))							$_REQUEST["heading_only"][$this_type] = 'n';
		if (!isset($_REQUEST["allow_comments"][$this_type]))						$_REQUEST["allow_comments"][$this_type] = 'n';
		if (!isset($_REQUEST["comment_can_rate_article"][$this_type]))	$_REQUEST["comment_can_rate_article"][$this_type] = 'n';
		if (!isset($_REQUEST["show_image"][$this_type]))								$_REQUEST["show_image"][$this_type] = 'n';
		if (!isset($_REQUEST["show_avatar"][$this_type]))								$_REQUEST["show_avatar"][$this_type] = 'n';
		if (!isset($_REQUEST["show_author"][$this_type]))								$_REQUEST["show_author"][$this_type] = 'n';
		if (!isset($_REQUEST["show_pubdate"][$this_type]))							$_REQUEST["show_pubdate"][$this_type] = 'n';
		if (!isset($_REQUEST["show_expdate"][$this_type]))							$_REQUEST["show_expdate"][$this_type] = 'n';
		if (!isset($_REQUEST["show_reads"][$this_type]))								$_REQUEST["show_reads"][$this_type] = 'n';
		if (!isset($_REQUEST["show_size"][$this_type]))									$_REQUEST["show_size"][$this_type] = 'n';
		if (!isset($_REQUEST["show_topline"][$this_type]))							$_REQUEST["show_topline"][$this_type] = 'n';
		if (!isset($_REQUEST["show_subtitle"][$this_type]))							$_REQUEST["show_subtitle"][$this_type] = 'n';
		if (!isset($_REQUEST["show_image_caption"][$this_type]))				$_REQUEST["show_image_caption"][$this_type] = 'n';
		if (!isset($_REQUEST["show_linkto"][$this_type]))								$_REQUEST["show_linkto"][$this_type] = 'n';
		if (!isset($_REQUEST["creator_edit"][$this_type]))							$_REQUEST["creator_edit"][$this_type] = 'n';

		$artlib->edit_type(
			$this_type,
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
			$_REQUEST["creator_edit"][$this_type]
		);

		// Add custom attributes
		if ($prefs["article_custom_attributes"] == 'y' && !empty($_REQUEST["new_attribute"][$this_type])) {
			$ok = $artlib->add_article_type_attribute($this_type, $_REQUEST["new_attribute"][$this_type]);
			if (!$ok) {
				$smarty->assign('msg', tra("Failed to add attribute"));
				$smarty->display("error.tpl");
				die;
			}
		}
	}
}

$types = $artlib->list_types();

if ($prefs["article_custom_attributes"] == 'y') {
	if (isset($_REQUEST["att_type"]) && isset($_REQUEST["att_remove"])) {
		$artlib->delete_article_type_attribute($_REQUEST["att_type"], $_REQUEST["att_remove"]);
	}
	foreach ($types as &$t) {
		$t["attributes"] = $artlib->get_article_type_attributes($t["type"], 'relationId ASC');
	}
}

$smarty->assign('types', $types);

include_once ('tiki-section_options.php');

// the strings below are used to localize the article types in the template file
//get_strings tr('Article') tr('Review') tr('Event') tr('Classified')
$smarty->assign('mid', 'tiki-article_types.tpl');
$smarty->display("tiki.tpl");
