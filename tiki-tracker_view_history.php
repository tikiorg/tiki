<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'trackers';
require_once ('tiki-setup.php');

$access->check_feature('feature_trackers');

$trklib = TikiLib::lib('trk');

$auto_query_args = array('offset', 'itemId', 'fieldId', 'filter');

if (!empty($_REQUEST['itemId'])) {
	$item_info = $trklib->get_item_info($_REQUEST['itemId']);
	$perms = Perms::get(array('type'=>'tracker', 'object'=> $item_info['trackerId']));
	if (!$perms->view_trackers) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('You do not have permission to view this page.'));
		$smarty->display('error.tpl');
		die;
	}
	$fieldId = empty($_REQUEST['fieldId'])?0: $_REQUEST['fieldId'];
	$smarty->assign_by_ref('fieldId', $fieldId);
	$filter = array();
	if (!empty($_REQUEST['version'])) {
		$filter['version'] = $_REQUEST['version'];
	}
	$smarty->assign_by_ref('filter', $filter);
	$offset = empty($_REQUEST['offset'])? 0: $_REQUEST['offset'];
	$smarty->assign('offset', $offset);

	$history = $trklib->get_item_history($item_info, $fieldId, $filter, $offset, $prefs['maxRecords']);
	$smarty->assign_by_ref('history', $history['data']);
	$smarty->assign_by_ref('cant', $history['cant']);

	foreach ($history['data'] as $i=>$hist) {
		if (empty($field_option[$hist['fieldId']])) {
			$field_option[$hist['fieldId']] = $trklib->get_tracker_field($hist['fieldId']);
		}
	}
	$smarty->assign_by_ref('item_info', $item_info);
	$smarty->assign_by_ref('field_option', $field_option);
}

$tiki_actionlog_conf = TikiDb::get()->table('tiki_actionlog_conf');
$logging = $tiki_actionlog_conf->fetchCount(
	array(
		'objectType' => 'trackeritem',
		'action' =>  $tiki_actionlog_conf->in(array('Created','Updated')),
		'status' =>  $tiki_actionlog_conf->in(array('y','v')),
	)
);
$smarty->assign('logging', $logging);

$smarty->assign('mid', 'tiki-tracker_view_history.tpl');
$smarty->display('tiki.tpl');
