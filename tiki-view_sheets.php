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

$info = $sheetlib->get_sheet_info($_REQUEST['sheetId']);

$sheetlib->setupJQuerySheet();

if (empty($info)) {
	$smarty->assign('Incorrect parameter');
	$smarty->display('error.tpl');
	die;
}

$objectperms = Perms::get( 'sheet', $_REQUEST['sheetId']);
if ($user && $user == $info['author']) {
	$objectperms->view_sheet = 1;
	$objectperms->edit_sheet = 1;
	$objectperms->tiki_p_view_sheet = 1;
	$objectperms->tiki_p_edit_sheet = 1;
}

if ($tiki_p_admin != 'y' && !$objectperms->view_sheet) {
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('objectperms', $objectperms);

if (isset($_REQUEST['height'])) {
	 $tiki_sheet_div_style .= 'height:'.$_REQUEST['height'].';';
}

if ( $tiki_sheet_div_style) {
	$smarty->assign('tiki_sheet_div_style',  $tiki_sheet_div_style);
}

if (!isset($_REQUEST['parse'])) {
	$_REQUEST['parse'] = 'y';
}
if (isset($_REQUEST['parse'])) {
	switch($_REQUEST['parse']) {
		case 'edit':
			$smarty->assign('edit',  true);
			break;
		case 'y':
			$smarty->assign('editReload', 'y');
			break;
	}
}

$smarty->assign('sheetId', $_REQUEST["sheetId"]);
$smarty->assign('chart_enabled', (function_exists('imagepng') || function_exists('pdf_new')) ? 'y' : 'n');
$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);
$smarty->assign('page_mode', 'view');
// Process the insertion or modification of a gallery here
$grid = new TikiSheet($_REQUEST["sheetId"]);
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit' && !$objectperms->edit_sheet && $tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("Permission denied") . ": feature_sheet");
	$smarty->display("error.tpl");
	die;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!$objectperms->edit_sheet && $tiki_p_admin != 'y') {
		$smarty->assign('msg', tra('Permission denied'));
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
						if (!empty($d->metadata->title)) {
							$t = $d->metadata->title;
						} else {
							$t = $info['title'] . ' subsheet'; 
						}
						$id = $sheetlib->replace_sheet( 0, $t, '', $user, $_REQUEST["sheetId"] );
						$rc .= tra('new') . ' ';
						$handler = new TikiSheetHTMLTableHandler($d);
						$res = $grid->import($handler);
					}
					if ($id && $res) {
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
	
} else {
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	
	//We make $date an array so that we can load multi sheets
	$dates = array(time());
	if (!empty($_REQUEST['readdate'])) {
		$i = 0;
		foreach ( explode("|", $_REQUEST['readdate'] ) as $dateStr ) {
			if ( $dateStr ) {
				$dates[$i] = $dateStr;
				if (!is_numeric($dates[$i])) $dates[$i] = strtotime($dates[$i]);
				if ($dates[$i] == - 1) $dates[$i] = time();
			}
			$i++;
		}
	}
	
	//If in edit mode, force singe date to show up
	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') {
		$smarty->assign('read_date', $dates[0]);
		$handler->setReadDate($dates[0]);
		$grid->import($handler);
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
		//In view mode, we can show all of them if we want :)
		
		$tableHtml = array();
		$i = 0;
		
		foreach ( $dates as $date ) {
			$smarty->assign('read_date', $date);
			$handler->setReadDate($date);
			$grid->import($handler);

			//ensure that sheet isn't being edited, then parse values if needed
			$grid->parseValues = ($grid->parseValues && $_REQUEST['parse'] != 'edit' ? true : false);
			
			$tableHtml[$i] = $grid->getTableHtml( true, $date );
			$i++;
		}
		
		if (isset($_REQUEST['sheetonly']) && $_REQUEST['sheetonly'] == 'y') {
			foreach( $tableHtml as $table ) {
				echo $table;
			}
			die;
		}
		$smarty->assign('grid_content', $tableHtml);
		$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
		$grid->import($handler);
	}
}
if ($prefs['feature_contribution'] == 'y') {
	$contributionItemId = $_REQUEST['sheetId'];
	include_once ('contribution.php');
}

$headerlib->add_jq_onready('
	$.sheet.tikiOptions = $.extend($.sheet.tikiOptions, {
		editable: ("'. $_REQUEST['parse'].'" == "edit" ? true : false)
	});
	
	var tikiSheet = $("div.tiki_sheet").sheet($.sheet.tikiOptions);
	tikiSheet.id = "'.$_REQUEST['sheetId'].'";
	
	$.sheet.manageState(tikiSheet);
	
	$("#edit_button a")
		.click(function() {
			$.sheet.manageState(tikiSheet, "y", "edit");
			return false;
		});
						
	$("#save_button a")
		.click( function () {
			$.sheet.saveSheet("tiki-view_sheets.php?sheetId=" + tikiSheet.id, false, function() {
				$.sheet.manageState(tikiSheet, "y", "");
			});
			
			return false;
		});
');

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
//$cat_type = 'sheet';
//$cat_objid = $_REQUEST["sheetId"];
//include_once ("categorize_list.php");
include_once ('tiki-section_options.php');
ask_ticket('sheet');
// Display the template
$smarty->assign('mid', 'tiki-view_sheets.tpl');
$smarty->display("tiki.tpl");
