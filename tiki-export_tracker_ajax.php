<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

@ini_set('max_execution_time', 0); //will not work if safe_mode is on
require_once('tiki-setup.php');
require_once('lib/smarty_tiki/modifier.tiki_short_datetime.php');
$access->check_feature(array('feature_trackers','feature_ajax'));

if (!isset($_REQUEST['trackerId'])) {
	$smarty->assign('msg', tra('No tracker indicated'));
	$smarty->display('error.tpl');
	die;
}

include_once 'tiki-export_tracker_monitor.php';

$monitor_filename = $prefs['tmpDir'].'/tracker_'.$_REQUEST['trackerId'].'_monitor.json';
if (is_file($monitor_filename)) {
	$stat_array = unserialize(file_get_contents($monitor_filename));
	if ($stat_array['status'] === 'finish') {
		unset($stat_array);
	}
}
if (empty($stat_array)) {
	$stat_array = array();
	saveStatus(array('user' => $user, 'status' => 'init', 'msg' => 'Starting...'));
}

include_once('lib/trackers/trackerlib.php');

$tracker_info = $trklib->get_tracker($_REQUEST['trackerId']);
if (empty($tracker_info)) {
	$smarty->assign('msg', tra('No tracker indicated'));
	$smarty->assign('msg', tra('No tracker indicated'));
	die;
}
if ($t = $trklib->get_tracker_options($_REQUEST['trackerId'])) {
	$tracker_info = array_merge($tracker_info,$t);
}

$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info);
$access->check_permission('tiki_p_export_tracker');

$filters = array();
if (!empty($_REQUEST['listfields'])) {
	if (is_string($_REQUEST['listfields'])) {
		$filters['fieldId'] = preg_split('/[,:]/', $_REQUEST['listfields']);
	} elseif (is_array($_REQUEST['listfields'])) {
		$filters['fieldId'] = $_REQUEST['listfields'];
	}
} elseif (isset($_REQUEST['which']) && $_REQUEST['which'] == 'ls') {
	$filters['or'] = array('isSearchable'=>'y', 'isTblVisible'=>'y');
} elseif (isset($_REQUEST['which']) && $_REQUEST['which'] == 'list') {
	$filters['isTblVisible'] = 'y';
}
if ($tiki_p_admin_trackers != 'y') {
	$filters['isHidden'] = array('n', 'c');
}
if ($tiki_p_tracker_view_ratings != 'y') {
	$filters['not'] = array('type'=>'s');
}
$filters['not'] = array('type'=>'h');

$fields = $trklib->list_tracker_fields($_REQUEST['trackerId'], 0, -1, 'position_asc', '', true, $filters);
$listfields = array();
foreach ($fields['data'] as $field) {
	$listfields[$field['fieldId']] = $field;
}

if (!isset($_REQUEST['which'])) {
	$_REQUEST['which'] = 'all';
}
if (!isset($_REQUEST['status'])) {
	$_REQUEST['status'] = '';
}
if (!isset($_REQUEST['initial'])) {
	$_REQUEST['initial'] = '';
}
$filterFields = '';
$values = '';
$exactValues = '';
foreach ($_REQUEST as $key =>$val) {
	if (substr($key, 0, 2) == 'f_' && !empty($val) && (!is_array($val) || !empty($val[0]))) {
		$fieldId = substr($key, 2);
		$filterFields[] = $fieldId;
		if (isset($_REQUEST["x_$fieldId"]) && $_REQUEST["x_$fieldId"] == 't' ) {
			$exactValues[] = '';
			$values[] = urldecode($val);
		} else {
			$exactValues[] = urldecode($val);
			$values[] = '';
		}
	}
}

if (isset($_REQUEST['showStatus'])) {
	$showStatus = $_REQUEST['showStatus'] == 'on'? true : false;
} else {
	$showStatus = false;
}

if (isset($_REQUEST['showItemId'])) {
	$showItemId = $_REQUEST['showItemId'] == 'on'? true : false;
} else {
	$showItemId = false;
}

if (isset($_REQUEST['showCreated'])) {
	$showCreated = $_REQUEST['showCreated'] == 'on'? true : false;
} else {
	$showCreated = false;
}

if (isset($_REQUEST['showLastModif'])) {
	$showLastModif = $_REQUEST['showLastModif'] == 'on'? true : false;
} else {
	$showLastModif = false;
}

if (isset($_REQUEST['parse'])) {
	$parse = $_REQUEST['parse'] == 'on'? true : false;
} else {
	$parse = false;
}

if (empty($_REQUEST['encoding'])) {
	$_REQUEST['encoding'] = 'ISO-8859-1';
}
$encoding = $_REQUEST['encoding'];
if (empty($_REQUEST['separator'])) {
	$_REQUEST['separator'] = ',';
}
$separator = $_REQUEST['separator'];
if (empty($_REQUEST['delimitorL'])) {
	$_REQUEST['delimitorL'] = '"';
}
$delimitorL = $_REQUEST['delimitorL'];
if (empty($_REQUEST['delimitorR'])) {
	$_REQUEST['delimitorR'] = '"';
}
$delimitorR = $_REQUEST['delimitorR'];
if (empty($_REQUEST['CR'])) {
	$_REQUEST['CR'] = '%%%';
}
$CR = $_REQUEST['CR'];

$fp = null;
$temp_filename = $prefs['tmpDir'].'/tracker_'.$_REQUEST['trackerId'].'.csv';
if ($_REQUEST['debug']) {
	$fp = fopen($temp_filename, 'w');
	echo 'output:'.$temp_filename;
}

include_once 'lib/core/Zend/Log.php';
include_once 'lib/core/Zend/Log/Writer/Stream.php';

$writer = new Zend_Log_Writer_Stream($prefs['tmpDir'].'/tracker_export.log');
$logger = new Zend_Log($writer);

$logger->info('------------- start mem used: ' . round(memory_get_usage(true)/1024, 3));

saveStatus(array('status' => 'header', 'msg' => '', 'current' => 0));

session_write_close();

if (empty($fp)) {
	$trklib->write_export_header();
}

if ($tracker_info['defaultOrderKey'] == -1)
	$sort_mode = 'lastModif';
elseif ($tracker_info['defaultOrderKey'] == -2)
	$sort_mode = 'created';
elseif ($tracker_info['defaultOrderKey'] == -3)
	$sort_mode = 'itemId';
elseif (isset($tracker_info['defaultOrderKey'])) {
	$sort_mode = 'f_'.$tracker_info['defaultOrderKey'];
} else {
	$sort_mode = 'itemId';
}
if (isset($tracker_info['defaultOrderDir'])) {
	$sort_mode.= "_".$tracker_info['defaultOrderDir'];
} else {
		$sort_mode.= "_asc";
}

function needs_separator($string) {
	return empty($string) || substr($string, strlen($string) - 1, 1) == "\n";
}

// write out header
$str = '';
if ($showItemId) {
	$str .= $delimitorL.'itemId'.$delimitorL;
}
if ($showStatus) {
	$str .= needs_separator($str) ? '' : $separator;
	$str .= $delimitorL.'status'.$delimitorL;
}
if ($showCreated) {
	$str .= needs_separator($str) ? '' : $separator;
	$str .= $delimitorL.'created'.$delimitorL;
}
if ($showLastModif) {
	$str .= needs_separator($str) ? '' : $separator;
	$str .= $delimitorL.'lastModif'.$delimitorL;
}
if (count($listfields) > 0) {
	foreach ($listfields as $field) {
		$str .= needs_separator($str) ? '' : $separator;
		$str .= $delimitorL.$field['name'].' -- '.$field['fieldId'].$delimitorR;
	}
}
if (empty($_REQUEST['encoding']) || $_REQUEST['encoding'] == 'ISO-8859-1') {
	$str = utf8_decode($str);
}
$str .= "\n";
if (!empty($fp)) {
	fwrite($fp, $str);
} else {
	echo $str;
}
$str = '';

$logger->info('header done: ' . round($mem/1024, 4));
saveStatus(array('status' => 'header', 'msg' => ''));

include_once 'lib/filegals/max_upload_size.php';	// sets $memory_limit and $current_memory_usage
$mem = $mem2 = memory_get_usage(true);
// just to get the number of records...
$items = $trklib->list_items($_REQUEST['trackerId'], 0, 0, $sort_mode, $listfields, $filterFields, $values, $_REQUEST['status'], $_REQUEST['initial'], $exactValues);
$cant = $items['cant'];

if (!empty($_REQUEST['recordsOffset'])) {
	$offset = $recordsOffset = ((int) $_REQUEST['recordsOffset']) - 1;
} else {
	$offset = $recordsOffset = 0;
}
if (!empty($_REQUEST['recordsMax'])) {
	$recordsMax = $_REQUEST['recordsMax'];
} else {
	$recordsMax = $cant;	// all
}
if ($recordsMax + $offset > $cant) {
	$recordsMax = $cant - $offset;
}

function calc_chunkSize ($keepFree = 1) {
	global $memory_limit, $chunkSize, $recordsMax, $recordsOffset, $offset, $fields;
	if ($memory_limit < 0) {
		$chunkSize = 5000;	// unlimited memory?
	} else {
		$freeMem = $memory_limit - memory_get_usage(true); 
		if ($freeMem < $keepFree * 1048576) {
			$keepFree = $freeMem / (1048576 * 4);	// leave 25% free in low memory conditions
		}
		$chunkSize = (int)(($freeMem - $keepFree * 1048576 ) / ((count($fields['data']) * 4096 * 6) + (($recordsMax + $recordsOffset - $offset) * 2)));	// combination of possible record size and number of records
	}
	if ($chunkSize < 10) { $chunkSize = 10; }
	if ($chunkSize > 200) { $chunkSize = 200; }
	if ($chunkSize > $recordsMax) { $chunkSize = $recordsMax; } // limit to request or cant
}

//calc_chunkSize($memory_limit / (3 * 1048576));
calc_chunkSize(10);

$logger->info('$chunkSize: ' . $chunkSize . '    cant: '. $cant . '     fields: ' . count($fields['data']) . '      memlimit: ' . $memory_limit);
$starttime = microtime(true);
$lasttime = $starttime;

$export_cant = 0; $chunks = 0;
saveStatus(array('status' => 'waiting', 'total' => $recordsMax + $offset, 'current' => $export_cant, 'msg' => ''));

while (($items = $trklib->list_items($_REQUEST['trackerId'], $offset, $chunkSize, $sort_mode, $listfields, $filterFields, $values, $_REQUEST['status'], $_REQUEST['initial'], $exactValues)) && !empty($items['data'])) {
	
	// still need to filter the fields that are view only by the admin and the item creator
	if ($tracker_info['useRatings'] == 'y') {
		foreach ($items['data'] as $f=>$v) {
			$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$_REQUEST['trackerId'].'.'.$items['data'][$f]['itemId'],$user);
		}
	}
	
	//$str = '';
	foreach ($items['data'] as $item) {
		if ($showItemId) {
			$str .= $delimitorL.$item['itemId'].$delimitorL;
		}
		if ($showStatus) {
			$str .= needs_separator($str) ? '' : $separator;
			$str .= $delimitorL.$item['status'].$delimitorL;
		}
		if ($showCreated) {
			$str .= needs_separator($str) ? '' : $separator;
			$str .= $delimitorL.smarty_modifier_tiki_short_datetime($item['created'], '', 'n').$delimitorL;
		}
		if ($showLastModif) {
			$str .= needs_separator($str) ? '' : $separator;
			$str .= $delimitorL.smarty_modifier_tiki_short_datetime($item['lastModif'], '', 'n').$delimitorL;
		}
		if (count($item['field_values']) > 0) {
			foreach ($item['field_values'] as $field_value) {
				$data = '';
				if ($field_value['isHidden'] == 'n' || $field_value['isHidden'] == 'p' || ($field_value['isHidden'] == 'c' && ($item['itemUser'] == $user || $tiki_p_admin_trackers == 'y')) || ($field_value['isHidden'] == 'y' &&  $tiki_p_admin_trackers == 'y')) {
					
					// this way seems to be over 5 times slower... not sure why
//					$data = $trklib->get_item_value($item['trackerId'], $item['itemId'],$field_value['fieldId']);
//					if (is_array($data)) {			// TODO handle other types of field better here (preferably in a function in $trklib)
//						$data = implode('%%%', $data);
//					}
//					$data = str_replace(array("\r\n", "\n", '<br />', $delimitorL, $delimitorR), array($CR, $CR, $CR, $delimitorL.$delimitorL, $delimitorR.$delimitorR), $data);
					switch($field_value['type']) {
						case 'd': // text etc
							$data = $field_value['value'];
							if (is_array($data)) {			// TODO handle other types of field better here (preferably in a function in $trklib)
								$data = implode($CR, $data);
							}
							$data = str_replace(array("\r\n", "\n", '<br />', $delimitorL, $delimitorR), array($CR, $CR, $CR, $delimitorL.$delimitorL, $delimitorR.$delimitorR), $data);				
							break;
						default: 
							$data = $trklib->get_field_handler($field_value, $item)->renderOutput(array(
								'list_mode' => 'csv', 'CR'=>$CR, 'delimitorL'=>$delimitorL, 'delimitorR'=>$delimitorR
						));
					}
				}
				$str .= needs_separator($str) ? '' : $separator;
				$str .= $delimitorL.$data.$delimitorR;
			}
		}
		$str .= "\n";
		$export_cant++;
	}

	if (empty($_REQUEST['encoding']) || $_REQUEST['encoding'] == 'ISO-8859-1') {
		$str = utf8_decode($str);
	}
	if (!empty($fp)) {
		fwrite($fp, $str);
	} else {
		echo $str;
	}
	saveStatus(array('status' => 'export', 'current' => $export_cant, 'msg' => ''));
	$mem2 = memory_get_usage(true);
	
	if ($offset + $chunkSize < $recordsMax + $recordsOffset) {
		$offset += $chunkSize;
	} else {
		break;
	}
	$chunks++;
	$str = '';
	unset($items);
	unset($item);
	calc_chunkSize();		// recalculate chunkSize
	if ($offset + $chunkSize  > $recordsMax + $recordsOffset) {
		$chunkSize = $recordsMax + $recordsOffset - $offset;
	}

	$leak = $mem2 - $mem;
	$logger->info('done: '.$export_cant.' records - (current chunkSize='.$chunkSize.') - lost mem: '.round(($leak)/1024, 3).' kB - usage total: '.round($mem2/1048576, 3).' MB - time ' . round(microtime(true) - $lasttime,2).' secs');
	$mem = memory_get_usage(true);
	$lasttime = microtime(true);
	
	if ($leak + $mem > $memory_limit - 1024 && $chunks > 1) {	// unlikely to work, so fail safely if got past first chunk
		$str = 'Export incomplete, memory limit reached. Exported: '.$export_cant.' out of '.$cant.' records.';
		$logger->info('failed '.$export_cant.' records in ' . round(microtime(true) - $starttime,2).' secs');
		
		saveStatus(array('status' => 'failed', 'current' => $export_cant, 'msg' => 'Export incomplete (probably), memory limit reached.'));
		if (!empty($fp)) {
			fwrite($fp, $str);
		} else {
			echo $str;
		}
		//break;
	}

	//time_nanosleep(0, 300);	 // AJAX time!? please?
	if (0 && $chunks > 1) {	// debugging
		break;
	}
}

if (!empty($fp)) {
	fclose($fp);
	$trklib->write_export_header();
	header('Content-Length: ' . filesize($temp_filename));
	readfile($temp_filename);
}
saveStatus(array('status' => 'finish', 'current' => $export_cant));
$logger->info('done '.$export_cant.' records in ' . round(microtime(true) - $starttime,2).' secs');

flush();


// cannot send two outputs it seems :(
//if ($export_cant != $cant) {
//	$msg = tra('Problem with export: Only exported').' '.$export_cant.' '.tra('records.');
//} else {
//	$msg = tra('Exported').' '.$export_cant.' '.tra('records.');
//}
//$redir = $tikiroot.'tiki-view_tracker.php?trackerId='.$_REQUEST['trackerId'].'&cookietab=3&export_result='.urlencode($msg).'export_cant='.$export_cant;
//header('Content-type: text/html; charset: UTF-8');
//header('Content-Disposition: inline');
//header('Location:'.$redir);
die;
