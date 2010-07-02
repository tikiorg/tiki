<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'sheet';
$tiki_sheet_div_style = '';
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');
$auto_query_args = array(
	'sheetId',
	'readdate',
	'mode',
	'parse',
	'simple',
	'height',
);
$access->check_feature('feature_sheet');

if (!isset($_REQUEST['sheetId'])) {
	$smarty->assign('msg', tra("A SheetId is required."));
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_view_sheet')) {
	$smarty->assign('msg', tra("Access Denied") . ": feature_sheet");
	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST['parse'])) {
	$_REQUEST['parse'] = 'y';
}

if (isset($_REQUEST['height'])) {
	 $tiki_sheet_div_style .= 'height:'.$_REQUEST['height'].';';
}

if ( $tiki_sheet_div_style) {
	$smarty->assign('tiki_sheet_div_style',  $tiki_sheet_div_style);
}

if ($tiki_p_edit_sheet == 'y' && $_REQUEST['parse'] == 'edit' && $prefs['feature_jquery_sheet'] == 'y') {	// edit button clicked in parse mode
	$_REQUEST['parse'] = 'n';
	$headerlib->add_jq_onready('
if (typeof ajaxLoadingShow == "function") {
	ajaxLoadingShow("role_main");
}
setTimeout (function () { $jq("#edit_button").click(); }, 500);
', 500);
} else if ((!isset($_REQUEST['simple']) || $_REQUEST['simple'] == 'n') && $prefs['feature_jquery_sheet'] == 'y') {
	$headerlib->add_jq_onready('if (typeof ajaxLoadingShow == "function") {
	ajaxLoadingShow("role_main");
}
setTimeout (function () { $jq("div.tiki_sheet").tiki("sheet", "",{editable:false});}, 500);
', 500);
}
$smarty->assign('sheetId', $_REQUEST["sheetId"]);
$smarty->assign('chart_enabled', (function_exists('imagepng') || function_exists('pdf_new')) ? 'y' : 'n');
// Individual permissions are checked because we may be trying to edit the gallery
// Init smarty variables to blank values
//$smarty->assign('theme','');
$info = $sheetlib->get_sheet_info($_REQUEST["sheetId"]);
$subsheets = $sheetlib->get_sheet_subsheets($_REQUEST["sheetId"]);
if ($tiki_p_admin == 'y' || $tiki_p_admin_sheet == 'y' || ($user && $user == $info['author']) || $tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_edit_sheet')) $tiki_p_edit_sheet = 'y';
else $tiki_p_edit_sheet = 'n';
$smarty->assign('tiki_p_edit_sheet', $tiki_p_edit_sheet);
$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);
$smarty->assign('page_mode', 'view');
// Process the insertion or modification of a gallery here
$grid = new TikiSheet($_REQUEST["sheetId"]);
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit' && $tiki_p_edit_sheet != 'y' && $tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y') {
	$smarty->assign('msg', tra("Access Denied") . ": feature_sheet");
	$smarty->display("error.tpl");
	die;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['xjxfun'])) {
	if ($tiki_p_edit_sheet != 'y' && $tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y') {
		$smarty->assign('msg', tra("Access Denied") . ": feature_sheet");
		$smarty->display("error.tpl");
		die;
	}
	if (!empty($_REQUEST['s'])) {					// ********* AJAX save request from jQuery.sheet
		$data =  json_decode($_REQUEST['s']);
		$rc =  '';
		if (is_array($data)) {
			foreach ($data as $d) {
				$handler = new TikiSheetHTMLTableHandler($d);
				$res = $grid->import($handler);
				// Save the changes
				$rc .= strlen($rc) === 0 ? '' : ', ';
				if ($res) {
					$id = $d->metadata->sheetId;
					if (!$id) {
						$id = $sheetlib->replace_sheet( 0, $info['title'] . ' subsheet' . $_REQUEST["sheetId"], '', $user, $_REQUEST["sheetId"] );
						$rc .= tra('new') . ' ';
					}
					if ($id) {
						$handler = new TikiSheetDatabaseHandler($id);
						$grid->export($handler);
						$rc .= $grid->getColumnCount() . ' x ' . $grid->getRowCount() . ' ' . tra('sheet') . " (id=$id)";
					}
					if (!empty($d->metadata->title)) {
						$sheetlib->set_sheet_title($id, $d->metadata->title);
					}
				}
			}
		}
		die($res ?  tra('Saved'). ': ' . $rc : tra('Save failed'));
	}
	
	// Load data from the form
	$handler = new TikiSheetFormHandler;
	if (!$grid->import($handler)) $grid = new TikiSheet;
	// Save the changes
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	$grid->export($handler);
	// Load the layout settings from the database
	$grid = new TikiSheet;
	$grid->import($handler);
	$smarty->assign('grid_content', $grid->getTableHtml());
} else {
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	$date = time();
	if (!empty($_REQUEST['readdate'])) {
		$date = $_REQUEST['readdate'];
		if (!is_numeric($date)) $date = strtotime($date);
		if ($date == - 1) $date = time();
	}
	$smarty->assign('read_date', $date);
	$handler->setReadDate($date);
	$grid->import($handler);
	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') {
		$handler = new TikiSheetFormHandler;
		ob_start();
		$grid->export($handler);
		$smarty->assign('init_grid', ob_get_contents());
		ob_end_clean();
		$smarty->assign('page_mode', 'edit');
		if ($prefs['feature_contribution'] == 'y') {
			$contributionItemId = $_REQUEST['sheetId'];
			include_once ('contribution.php');
		}
	} else {
		$html = $grid->getTableHtml();
		if (count($subsheets) > 0) {
			foreach ($subsheets as $sub) {
				$handler = new TikiSheetDatabaseHandler($sub['sheetId']);
				$handler->setReadDate($date);
				$grid = new TikiSheet($sub['sheetId'], true);
				$grid->import($handler);
				$html .= $grid->getTableHtml();
			}
		}
		$smarty->assign('grid_content', $html);
		$smarty->assign('subsheet_cant', count($subsheets));
		$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
		$grid->import($handler);
	}
}
if ($prefs['feature_jquery_sheet'] == 'y') {
		if ($prefs['feature_contribution'] == 'y') {
			$contributionItemId = $_REQUEST['sheetId'];
			include_once ('contribution.php');
		}
	// need to be in non-parsed mode to edit the sheet
	if ($_REQUEST['parse'] == 'y') {
		$smarty->assign('editReload', true);
	} else {
		$smarty->assign('editReload', false);
		$headerlib->add_jq_onready('
$jq("#edit_button").click( function () {
	var $a = $jq(this).find("a");
	if ($a.text() != editSheetButtonLabel2) {

		if ($jq.sheet.instance.length > 0 && $jq.sheet.instance[0].s.allowToggleState) {
			$jq.sheet.instance = [];
		}
		var options = {title: $jq("#sheetTools").html(), urlSave: "tiki-view_sheets.php?sheetId='.$_REQUEST['sheetId'].'"};
		$jq("div.tiki_sheet").tiki("sheet", "", options);

		$a.attr("temp", $a.text());
		$a.text(editSheetButtonLabel2);
		$jq("#edit_button").parent().find(".button:not(#edit_button), .rbox").hide();
		$jq("#save_button").show();
		if (typeof ajaxLoadingHide == "function") {
			ajaxLoadingHide();
		}
	} else {
		if (!$jq.sheet.instance[0].isDirty ? true : confirm("Are you sure you want to finish editing?  All unsaved changes will be lost.")) {
			window.location.replace(window.location.href.replace("parse=edit", "parse=y"));
		}
	}
	return false;
});
$jq("#save_button").click( function () {
	$jq.sheet.instance[0].evt.cellEditDone();
	$jq.sheet.saveSheet(0, true);
	return false;
}).hide();

window.toggleFullScreen = function(areaname) {
	$jq.sheet.instance[0].toggleFullScreen();
}

window.showFeedback = function(message, delay, redirect) {
	if (typeof delay == "undefined") { delay = 5000; }
	if (typeof redirect == "undefined") { redirect = false; }
	$fbsp = $jq("#feedback span");
	$fbsp.html(message).show();
	window.setTimeout( function () { $fbsp.fadeOut("slow", function () { $fbsp.html("&nbsp;"); }); }, delay);
	// if called from save button via saveSheet:success, then exit edit page mode
	if (redirect) {
		window.setTimeout( function () { $fbsp.html("Redirecting...").show(); }, 1000);
		window.setTimeout( function () { window.location.replace(window.location.href.replace("parse=edit", "parse=y")); }, 1500);
	}
};

window.setEditable = function(isEditable) {
	$jq.sheet.instance[0].s.editable = isEditable;
	if (isEditable) {
		$jq("#save_button").show();
		//$jq("#edit_button a").click( function () { window.location.replace(window.location.href); return false; } );
	} else {
		setTimeout( function(){ $jq("#jSheetControls").hide(); }, 200);
		$jq("#save_button").hide();
		$jq("#edit_button a").click( function () { window.location.replace(window.location.href); return false; } );
	}
};
');
	}
}

$smarty->assign('parseValues', $grid->parseValues);

$smarty->assign('semUser', '');
if ($prefs['feature_warn_on_edit'] == 'y') {
	if ($tikilib->semaphore_is_set($_REQUEST['sheetId'], $prefs['warn_on_edit_time'] * 60, 'sheet') && ($semUser = $tikilib->get_semaphore_user($_REQUEST['sheetId'], 'sheet')) != $user) {
		$editconflict = 'y';
		$smarty->assign('semUser', $semUser);
	} else {
		$editconflict = 'n';
	}
	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') {
		$_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']] = $tikilib->semaphore_set($_REQUEST['sheetId'], 'sheet');
	} elseif (isset($_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']])) {
		$tikilib->semaphore_unset($_REQUEST['sheetId'], $_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']]);
		unset($_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']]);
	}
} else {
		$editconflict = 'n';
}
$smarty->assign('editconflict', $editconflict);
$headerlib->add_cssfile('lib/sheet/style.css', 10);
//$cat_type = 'sheet';
//$cat_objid = $_REQUEST["sheetId"];
//include_once ("categorize_list.php");
include_once ('tiki-section_options.php');
ask_ticket('sheet');
// Display the template
$smarty->assign('mid', 'tiki-view-sheets.tpl');
$smarty->display("tiki.tpl");
