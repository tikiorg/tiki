<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'admin';
require_once ('tiki-setup.php');
$access->check_feature(array('feature_wiki_templates','feature_cms_templates','feature_file_galleries_templates'), '', 'features', true);

$templateslib = TikiLib::lib('template');

$auto_query_args = array('templateId');

//get_strings tra('Content Templates')

if (!isset($_REQUEST["templateId"])) {
	$_REQUEST["templateId"] = 0;
}
$smarty->assign('templateId', $_REQUEST["templateId"]);
if ($_REQUEST["templateId"]) {
	$info = $templateslib->get_template($_REQUEST["templateId"]);
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
	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'wiki_html')) {
		$info["section_wiki_html"] = 'y';
	} else {
		$info["section_wiki_html"] = 'n';
	}
	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'file_galleries')) {
		$info["section_file_galleries"] = 'y';
	} else {
		$info["section_file_galleries"] = 'n';
	}
	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'newsletters')) {
		$info["section_newsletters"] = 'y';
	} else {
		$info["section_newsletters"] = 'n';
	}
	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'events')) {
		$info["section_events"] = 'y';
	} else {
		$info["section_events"] = 'n';
	}
	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'admins')) {
		$info["section_admins"] = 'y';
	} else {
		$info["section_admin"] = 'n';
	}
	if ($templateslib->template_is_in_section($_REQUEST["templateId"], 'cms')) {
		$info["section_cms"] = 'y';
	} else {
		$info["section_cms"] = 'n';
	}
} else {
	$info = array();
	$info["name"] = '';
	$info['template_type'] = 'static';
	$info["content"] = '';
	$info["section_cms"] = 'n';
	$info["section_html"] = 'n';
	$info["section_wiki"] = 'n';
	$info["section_wiki_html"] = 'n';
	$info["section_file_galleries"] = 'n';
	$info["section_newsletters"] = 'n';
	$info["section_event"] = 'n';
}
$cat_type = 'template';
$cat_objid = $_REQUEST['templateId'];
include_once ("categorize_list.php");

$smarty->assign_by_ref('info', $info);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$templateslib->remove_template($_REQUEST["remove"]);
}
if (isset($_REQUEST["removesection"])) {
	$access->check_authenticity();
	$templateslib->remove_template_from_section($_REQUEST["rtemplateId"], $_REQUEST["removesection"]);
}
$smarty->assign('preview', 'n');
if (isset($_REQUEST["preview"])) {

	TikiLib::lib('access')->check_permission('edit_content_templates', 'Edit template', 'template', $_REQUEST['templateId']);

	$smarty->assign('preview', 'y');
	if (isset($_REQUEST["section_html"]) && $_REQUEST["section_html"] == 'on') {
		$info["section_html"] = 'y';
		$parsed = nl2br($_REQUEST["content"]);
	} else {
		$info["section_html"] = 'n';
		$parsed = $tikilib->parse_data($_REQUEST["content"], array('is_html' => $info['section_wiki_html'] === 'y'));
	}
	$smarty->assign('parsed', $parsed);
	if (isset($_REQUEST["section_wiki"]) && $_REQUEST["section_wiki"] == 'on') {
		$info["section_wiki"] = 'y';
	} else {
		$info["section_wiki"] = 'n';
	}
	if (isset($_REQUEST["section_file_galleries"]) && $_REQUEST["section_file_galleries"] == 'on') {
		$info["section_file_galleries"] = 'y';
	} else {
		$info["section_file_galleries"] = 'n';
	}
	if (isset($_REQUEST["section_newsletters"]) && $_REQUEST["section_newsletters"] == 'on') {
		$info["section_newsletters"] = 'y';
	} else {
		$info["section_newsletters"] = 'n';
	}
	if (isset($_REQUEST["section_events"]) && $_REQUEST["section_events"] == 'on') {
		$info["section_events"] = 'y';
	} else {
		$info["section_events"] = 'n';
	}
	if (isset($_REQUEST["section_cms"]) && $_REQUEST["section_cms"] == 'on') {
		$info["section_cms"] = 'y';
	} else {
		$info["section_cms"] = 'n';
	}
	$info["content"] = $_REQUEST["content"];
	$info["name"] = $_REQUEST["name"];
	$info['page_name'] = $_REQUEST['page_name'];
	$info['template_type'] = $_REQUEST['template_type'];
	$smarty->assign('info', $info);

	$cookietab = 2;
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-content-templates');
	$type = $_REQUEST['template_type'];

	if ( $type == 'page' ) {
		$content = 'page:' . $_REQUEST['page_name'];
	} else {
		$content = $_REQUEST["content"];
	}
	if (isset($_REQUEST["name"]) && $_REQUEST["name"] != "") {
		$tid = $templateslib->replace_template($_REQUEST["templateId"], $_REQUEST["name"], $content, $type);
		$smarty->assign("templateId", '0');
		$info["name"] = '';
		$info["content"] = '';
		$info["section_cms"] = 'n';
		$info["section_wiki"] = 'n';
		$info["section_wiki_html"] = 'n';
		$info["section_file_galleries"] = 'n';
		$info["section_newsletters"] = 'n';
		$info["section_events"] = 'n';
		$info["section_html"] = 'n';
		$smarty->assign('info', $info);
		if (isset($_REQUEST["section_cms"]) && $_REQUEST["section_cms"] == 'on') {
			$templateslib->add_template_to_section($tid, 'cms');
		} else {
			$templateslib->remove_template_from_section($tid, 'cms');
		}
		if (isset($_REQUEST["section_wiki"]) && $_REQUEST["section_wiki"] == 'on') {
			$templateslib->add_template_to_section($tid, 'wiki');
		} else {
			$templateslib->remove_template_from_section($tid, 'wiki');
		}
		if (isset($_REQUEST["section_wiki_html"]) && $_REQUEST["section_wiki_html"] == 'on') {
			$templateslib->add_template_to_section($tid, 'wiki_html');
		} else {
			$templateslib->remove_template_from_section($tid, 'wiki_html');
		}
		if (isset($_REQUEST["section_file_galleries"]) && $_REQUEST["section_file_galleries"] == 'on') {
			$templateslib->add_template_to_section($tid, 'file_galleries');
		} else {
			$templateslib->remove_template_from_section($tid, 'file_galleries');
		}
		if (isset($_REQUEST["section_newsletters"]) && $_REQUEST["section_newsletters"] == 'on') {
			$templateslib->add_template_to_section($tid, 'newsletters');
		} else {
			$templateslib->remove_template_from_section($tid, 'newsletters');
		}
		if (isset($_REQUEST["section_events"]) && $_REQUEST["section_events"] == 'on') {
			$templateslib->add_template_to_section($tid, 'events');
		} else {
			$templateslib->remove_template_from_section($tid, 'events');
		}
		if (isset($_REQUEST["section_html"]) && $_REQUEST["section_html"] == 'on') {
			$templateslib->add_template_to_section($tid, 'html');
		} else {
			$templateslib->remove_template_from_section($tid, 'html');
		}

		$cat_type = 'template';
		$cat_objid = $tid;
		$cat_desc = '';
		$cat_name = $_REQUEST["name"];
		$cat_href = "tiki-admin_content_templates.php?templateId=" . $cat_objid;
		include_once ("categorize.php");

		// Locking: only needed on new templates, ajax locks existing ones
		if ($prefs['lock_content_templates'] === 'y' && empty($_REQUEST['templateId'])) {
			if (!empty($_REQUEST['locked'])) {
				TikiLib::lib('attribute')->set_attribute('template', $tid, 'tiki.object.lock', $_REQUEST['locked']);
			}
		}

		$cookietab = 1;
	} else {
		$smarty->assign("templateId", '0');
		$info["name"] = '';
		$info["content"] = (isset($_REQUEST["content"]) && $_REQUEST["content"] != '') ? $_REQUEST["content"] : '' ;
		$info["section_cms"] = (isset($_REQUEST["section_cms"]) && $_REQUEST["section_cms"] == 'on') ? 'y' : 'n';
		$info["section_wiki"] = (isset($_REQUEST["section_wiki"]) && $_REQUEST["section_wiki"] == 'on') ? 'y' : 'n';
		$info["section_wiki_html"] = (isset($_REQUEST["section_wiki_html"]) && $_REQUEST["section_wiki_html"] == 'on') ? 'y' : 'n';
		$info["section_file_galleries"] = (isset($_REQUEST["section_file_galleries"]) && $_REQUEST["section_file_galleries"] == 'on') ? 'y' : 'n';
		$info["section_newsletters"] = (isset($_REQUEST["section_newsletters"]) && $_REQUEST["section_newsletters"] == 'on') ? 'y' : 'n' ;
		$info["section_events"] = (isset($_REQUEST["section_events"]) && $_REQUEST["section_events"] == 'on') ? 'y' : 'n';
		$info["section_html"] = (isset($_REQUEST["section_html"]) && $_REQUEST["section_html"] == 'on') ? 'y' : 'n';
		$smarty->assign('info', $info);
		$smarty->assign('emptyname', "true");

		$cookietab = 2;
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
$smarty->assign_by_ref('cant_pages', $channels["cant"]);

// wysiwyg decision
if ($_REQUEST['templateId']) {
	$info['is_html'] = $info['section_wiki_html'] === 'y' ? 1 : 0;
	$info['wysiwyg'] = $info['section_wiki_html'];
}
include 'lib/setup/editmode.php';
$info['section_wiki_html'] = $_SESSION['wysiwyg'];	//$info['is_html'] ? 'y' : 'n';

// Handles switching editor modes
$editlib = TikiLib::lib('edit');
if (isset($_REQUEST['mode_normal']) && $_REQUEST['mode_normal']=='y') {
	// Parsing page data as first time seeing html page in normal editor
	$smarty->assign('msg', "Parsing html to wiki");
	$info['content'] = $editlib->parseToWiki($_REQUEST["content"]);
	$smarty->assign('parsed', $parsed);
} elseif (isset($_REQUEST['mode_wysiwyg']) && $_REQUEST['mode_wysiwyg']=='y') {
	// Parsing page data as first time seeing wiki page in wysiwyg editor
	$smarty->assign('msg', "Parsing wiki to html");
	$info['content'] = $editlib->parseToWysiwyg($_REQUEST["content"]);
	$smarty->assign('parsed', $parsed);
}

// check edit/create perms
if ($_REQUEST['templateId']) {
	$perms = Perms::get(array('type' => 'template', 'object' => $_REQUEST['templateId']));
	$canEdit = $perms->edit_content_templates;
	if ($prefs['lock_content_templates'] === 'y' && $canEdit) {	// check for locked
		$lockedby = TikiLib::lib('attribute')->get_attribute('template', $_REQUEST['templateId'], 'tiki.object.lock');
		if ($lockedby && $lockedby === $user && $perms->lock_content_templates || ! $lockedby || $perms->admin_content_templates) {
			$canEdit = true;
		} else {
			$canEdit = false;
		}
	}
} else {
	$canEdit = ($tiki_p_admin_content_templates === 'y') || ($tiki_p_admin === 'y');	// create
}
$smarty->assign('canEdit', $canEdit);

$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-content-templates');
$wikilib = TikiLib::lib('wiki');
$plugins = $wikilib->list_plugins(true, 'editwiki');
$smarty->assign_by_ref('plugins', $plugins);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_content_templates.tpl');
$smarty->display("tiki.tpl");
