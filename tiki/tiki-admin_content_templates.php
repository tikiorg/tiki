<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_content_templates.php,v 1.7 2003-08-07 04:33:56 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/templates/templateslib.php');

if ($tiki_p_edit_content_templates != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["templateId"])) {
	$_REQUEST["templateId"] = 0;
}

$smarty->assign('templateId', $_REQUEST["templateId"]);

if ($_REQUEST["templateId"]) {
	$info = $tikilib->get_template($_REQUEST["templateId"]);

	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'html')) {
		$info["section_html"] = 'y';
	} else {
		$info["section_html"] = 'n';
	}

	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'wiki')) {
		$info["section_wiki"] = 'y';
	} else {
		$info["section_wiki"] = 'n';
	}

	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'newsletters')) {
		$info["section_newsletters"] = 'y';
	} else {
		$info["section_newsletters"] = 'n';
	}

	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'cms')) {
		$info["section_cms"] = 'y';
	} else {
		$info["section_cms"] = 'n';
	}
} else {
	$info = array();

	$info["name"] = '';
	$info["content"] = '';
	$info["section_cms"] = 'n';
	$info["section_html"] = 'n';
	$info["section_wiki"] = 'n';
	$info["section_newsletters"] = 'n';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	$templateslib->remove_template($_REQUEST["remove"]);
}

if (isset($_REQUEST["removesection"])) {
	$templateslib->remove_template_from_section($_REQUEST["rtemplateId"], $_REQUEST["removesection"]);
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');

	if (isset($_REQUEST["section_html"]) && $_REQUEST["section_html"] == 'on') {
		$info["section_html"] = 'y';

		$parsed = nl2br($_REQUEST["content"]);
	} else {
		$info["section_html"] = 'n';

		$parsed = $tikilib->parse_data($_REQUEST["content"]);
	}

	$smarty->assign('parsed', $parsed);

	if (isset($_REQUEST["section_wiki"]) && $_REQUEST["section_wiki"] == 'on') {
		$info["section_wiki"] = 'y';
	} else {
		$info["section_wiki"] = 'n';
	}

	if (isset($_REQUEST["section_newsletters"]) && $_REQUEST["section_newsletters"] == 'on') {
		$info["section_newsletters"] = 'y';
	} else {
		$info["section_newsletters"] = 'n';
	}

	if (isset($_REQUEST["section_cms"]) && $_REQUEST["section_cms"] == 'on') {
		$info["section_cms"] = 'y';
	} else {
		$info["section_cms"] = 'n';
	}

	$info["content"] = $_REQUEST["content"];
	$info["name"] = $_REQUEST["name"];
	$smarty->assign('info', $info);
}

if (isset($_REQUEST["save"])) {
	$tid = $templateslib->replace_template($_REQUEST["templateId"], $_REQUEST["name"], $_REQUEST["content"]);

	$smarty->assign("templateId", '0');
	$info["name"] = '';
	$info["content"] = '';
	$info["section_cms"] = 'n';
	$info["section_wiki"] = 'n';
	$info["section_newsletters"] = 'n';
	$info["section_html"] = 'n';
	$smarty->assign('info', $info);

	if (isset($_REQUEST["section_html"]) && $_REQUEST["section_html"] == 'on') {
		$templateslib->add_template_to_section($tid, 'html');
	} else {
		$templateslib->remove_template_from_section($tid, 'html');
	}

	if (isset($_REQUEST["section_wiki"]) && $_REQUEST["section_wiki"] == 'on') {
		$templateslib->add_template_to_section($tid, 'wiki');
	} else {
		$templateslib->remove_template_from_section($tid, 'wiki');
	}

	if (isset($_REQUEST["section_newsletters"]) && $_REQUEST["section_newsletters"] == 'on') {
		$templateslib->add_template_to_section($tid, 'newsletters');
	} else {
		$templateslib->remove_template_from_section($tid, 'newsletters');
	}

	if (isset($_REQUEST["section_cms"]) && $_REQUEST["section_cms"] == 'on') {
		$templateslib->add_template_to_section($tid, 'cms');
	} else {
		$templateslib->remove_template_from_section($tid, 'cms');
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
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

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $templateslib->list_all_templates($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

// Display the template
$smarty->assign('mid', 'tiki-admin_content_templates.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>