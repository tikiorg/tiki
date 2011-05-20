<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_quick_edit_info() {
	return array(
		'name' => tra('Quick Edit'),
		'description' => tra('Enables to quickly create or edit Wiki pages.'),
		'prefs' => array( 'feature_wiki' ),
		'params' => array(
			'templateId' => array(
				'name' => tra('Template identifier'),
				'description' => tra('If set to a template identifier, the specified template is used for creating new Wiki pages.') . " " . tra('Not set by default.')
			),
			'action' => array(
				'name' => 'FORM ACTION',
				'description' => tra('If set, send the form to the given location (relative to Tiki\'s root) for processing.') . " " . tra('Default:') . ' tiki-editpage.php'
			),
			'submit' => array(
				'name' => tra('SUBMIT label'),
				'description' => tra('The label on the button to submit the form.') . " " . tra('Default:') . " " . tra("Create/Edit")
			),
			'size' => array(
				'name' => tra('INPUT SIZE'),
				'description' => tra('Size attribute (horizontal, in characters) of the text input field for page names.') . " " . tra('Default:') . " 15",
				'filter' => 'int'
			),
			'mod_quickedit_heading' => array(
				'name' => tra('Heading'),
				'description' => tra('Optional heading to display at the top of the module\'s content.')
			),
			'addcategId' => array(
				'name' => tra('Category to preselect'),
				'description' => tra('If set, pages created through the module have this category prechecked to be categorized in.') . " " . tra('Not set by default.')
			),
			'customTip' => array(
				'name' => tra('Tip to be shown'),
				'description' => tra('Custom text to be shown as a tip at the top of the edit page'),
			),
			'customTipTitle' => array(
				'name' => tra('Title of tip to be shown'),
				'description' => tra('Custom title to be shown for the tip at the top of the edit page'),
			),
			'headerwiki' => array(
				'name' => tra('Custom header template'),
				'description' => tra('Wiki page to be used as a template to show content on top of edit page'),
			),
		)
	);
}

function module_quick_edit( $mod_reference, $module_params ) {
	global $smarty, $prefs;
	
	$smarty->assign('tpl_module_title', tra("Quick Edit a Wiki Page"));
	
	
	if (isset($module_params["templateId"])) {
		$templateId = $module_params["templateId"];
	} else {
		$templateId = false;
	}
	
	if (isset($module_params['action'])) {
		$qe_action = $module_params['action'];
	} else {
		$qe_action = 'tiki-editpage.php';
	}
	
	if (isset($module_params["submit"])) {
		$submit = $module_params["submit"];
	} else {
		$submit = tra('Create/Edit','',true);
	}

	$size = isset($module_params["size"]) ? $module_params["size"] : 15;

	if (isset($module_params["mod_quickedit_heading"])) {
		$mod_quickedit_heading = $module_params["mod_quickedit_heading"];
	} else {
		$mod_quickedit_heading = false;
	}
	if (isset($module_params["addcategId"])) {
		$addcategId = $module_params["addcategId"];
	} else {
		$addcategId = '';
	}
	if (isset($module_params["customTip"])) {
		$customTip = $module_params["customTip"];
	} else {
		$customTip = '';
	}
	if (isset($module_params["customTipTitle"])) {
		$customTipTitle = $module_params["customTipTitle"];
	} else {
		$customTipTitle = '';
	}
	if (isset($module_params["headerwiki"])) {
		$wikiHeaderTpl = $module_params["headerwiki"];
	} else {
		$wikiHeaderTpl = '';
	}

	$smarty->assign('wikiHeaderTpl', $wikiHeaderTpl);
	$smarty->assign('customTip', $customTip);
	$smarty->assign('customTipTitle', $customTipTitle);
	$smarty->assign('addcategId', $addcategId);
	$smarty->assign('size', $size);
	$smarty->assign('mod_quickedit_heading', $mod_quickedit_heading);
	$smarty->assign('templateId', $templateId);
	$smarty->assign('qe_action', $qe_action);
	$smarty->assign('submit', $submit);

	// Used for jQuery, which refers to the INPUT HTML element using an id which the following makes unique
	static $qe_usage_counter = 0;
	$smarty->assign('qefield', 'qe-' . ++$qe_usage_counter);
}
