<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerstat.php,v 1.14.2.5 2007-12-18 23:03:15 sylvieg Exp $
/* to have some statistiques about a tracker
 * will returns a table with for each tracker field, the list of values and the number of times the values occurs
 * trackerId = the id of the tracker
 * fields = the iof of the fields you wnat the stat - the fields must be public
 * show_percent : optionnal - to show a percent
 * show_bar : optionnal to show a bar(length 100 pixels)
 * status : optionnal to filter on the status ( a combinaison of letters c:close, o:open, p:pending)
 */
function wikiplugin_trackerstat_help() {
	$help = tra("Displays some stat of a tracker content, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{TRACKERSTAT(trackerId=>1,fields=>2:4:5,show_percent=>y,show_bar=>n,status=>o|c|p|op|oc|pc|opc)}Title{TRACKERSTAT}~/np~";
	return $help;
}

function wikiplugin_trackerstat($data, $params) {
	global $smarty, $prefs, $tiki_p_admin_trackers, $trklib;
	include_once('lib/trackers/trackerlib.php');
	extract ($params,EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y' || !isset($trackerId) || !($tracker_info = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}

	if (!isset($status)) {
		$status = 'o';
	} elseif (!$trklib->valid_status($status)) {
		return "invalid status";
	}
	if (isset($show_percent) && $show_percent == 'y') {
		$average = 'y';
		$smarty->assign('show_percent', 'y');
	} else {
		$smarty->assign('show_percent', 'n');
	}
	if (isset($show_bar) && $show_bar == 'y') {
		$average = 'y';
		$smarty->assign('show_bar', 'y');
	} else {
		$smarty->assign('show_bar', 'n');
	}
	
	$allFields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
	for ($iUser = count($allFields['data']) - 1; $iUser >= 0; $iUser--) {
		if ($allFields['data'][$iUser]['type'] == 'u') { // this tracker has a user field - can look for the value the user sets
			break;
		}
	}
	if ($iUser <= -1) {
		for ($iIp = count($allFields['data']) - 1; $iIp >= 0; $iIp--) {
			if ($allFields['data'][$iIp]['type'] == 'I') { // this tracker has a IP field - can look for the value the user sets
				break;
			}
		}
	}
	if (!empty($fields)) {
		$listFields = split(':',$fields);
	} else {
		foreach($allFields['data'] as $f) {
			$listFields[] = $f['fieldId'];
		}
	}

	if ($t = $trklib->get_tracker_options($trackerId)) {
		$tracker_info = array_merge($tracker_info, $t);
	}

	$status_types = $trklib->status_types();

	foreach ($listFields as $fieldId) {
		for ($i = count($allFields['data']) - 1; $i >= 0; $i--) {
			if ($allFields['data'][$i]['fieldId'] == $fieldId) {
				break;
			}
		}
		if ($i < 0 ) {
			return tra("incorrect fieldId")." ".$fieldId;
		}
		if ($allFields["data"][$i]['type'] == 'u' || $allFields["data"][$i]['type'] == 'I' || $allFields["data"][$i]['type'] == 'g' || $allFields["data"][$i]['type'] == 's') {
			continue;
		}
		if (!($allFields["data"][$i]['isHidden'] == 'n' || $allFields["data"][$i]['isHidden'] == 'p' || ($allFields["data"][$i]['isHidden'] == 'y' && $tiki_p_admin_trackers == 'y'))) {
			continue;
		}
		if ($allFields["data"][$i]['type'] == 'e') {
			global $categlib; include_once('lib/categories/categlib.php');
			$listCategs = $categlib->get_child_categories($allFields["data"][$i]['options']);
			$itemId = $trklib->get_user_item($trackerId, $tracker_info);
			for ($j = 0; $j < count($listCategs); ++$j) {
				$objects = $categlib->get_category_objects($listCategs[$j]['categId'], "tracker $trackerId");
				$v[$j]['count'] = count($objects);
				$v[$j]['value'] = $listCategs[$j]['name'];
				foreach($objects as $o) {
					if ($o['itemId'] == $itemId) {
						$v[$j]['me'] = 'y';
						break;
					}
				}
			}
		} else	if ($allFields["data"][$i]['type'] == 'h') {
			$stat['name'] = $allFields["data"][$i]['name'];
			$stat['values'] = array();
			$stats[] = $stat;
			continue;
		} else {
			if ($iUser >= 0) {
				global $user;
				$userValues = $trklib->get_filtered_item_values($allFields["data"][$iUser]['fieldId'], $user, $allFields["data"][$i]['fieldId']);
			} else if ($iIp >= 0 && isset($_SERVER['REMOTE_ADDR'])) {
				$userValues = $trklib->get_filtered_item_values($allFields["data"][$iIp]['fieldId'],  $_SERVER['REMOTE_ADDR'], $allFields["data"][$i]['fieldId']);
			}
			
			$allValues = $trklib->get_all_items($trackerId, $fieldId, $status);
			$j = -1;
			foreach ($allValues as $value) {
				if ($j < 0 || $value != $v[$j]['value']) {
					++$j;
					$v[$j]['value'] = $value;
					$v[$j]['count'] = 1;
					if (isset($userValues) && in_array($value, $userValues)) {
						$v[$j]['me'] = 'y';
					}
				} else {
					++$v[$j]['count'];
				}
			}
		}
		if (isset($average)) {
			$total = $trklib->get_nb_items($trackerId);
			for (; $j >= 0; --$j) {
				$v[$j]['average'] = 100*$v[$j]['count']/$total;
			}
		}
		if (!empty($v)) {
			$stat['name'] = $allFields["data"][$i]['name'];
			$stat['values'] = $v;
			$stats[] = $stat;
		}
		unset($v);
	}
	$smarty->assign_by_ref('stats', $stats);
	return "~np~".$smarty->fetch('wiki-plugins/wikiplugin_trackerstat.tpl')."~/np~";
}

?>
