<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerfilter.php,v 1.14.2.4 2008-01-14 15:57:00 sylvieg Exp $
function wikiplugin_trackerfilter_help() {
  $help = tra("Filters the items of a tracker, fields are indicated with numeric ids.").":\n";
  $help .= "~np~{TRACKERFILTER(filters=>2/d:4/r:5,action=>Name of submit button,displayList=y|n,line=y|n,TRACKERLIST_params )}Notice{TRACKERFILTER}~/np~";
  return $help;
}
function wikiplugin_trackerfilter($data, $params) {
	global $smarty, $prefs, $trklib;
	include_once('lib/trackers/trackerlib.php');
	extract($params, EXTR_SKIP);
	$dataRes = '';
	if (isset($_REQUEST['msgTrackerFilter'])) {
		$smarty->assign('msgTrackerFilter', $_REQUEST['msgTrackerFilter']);
	}
	if (!isset($filters)) {
		$smarty->assign('msg', tra("missing parameters"));
		return $dataRes.$smarty->fetch("error_simple.tpl");
	}
	$listfields = split(':',$filters);
	foreach ($listfields as $i=>$f) {
		if (strchr($f, '/')) {
			list($fieldId, $format) = split('/',$f);
			$listfields[$i] = $fieldId;
			$formats[$fieldId] = $format;
		} else {
			$formats[$f] = '';
		}
	}
	if (!isset($displayList)) {
		$displayList = 'n';
	} elseif ($displayList == 'y' && isset($trackerId)) {
		$_REQUEST['trackerId'] = $trackerId;
	}
	if (!isset($line)) {
		$line = 'n';
	}
	if ($displayList == 'y' || isset($_REQUEST['filter']) || isset($_REQUEST['tr_offset']) || isset($_REQUEST['tr_sort_mode'])) {
	  
		if ($prefs['feature_trackers'] != 'y' || empty($_REQUEST['trackerId']) || !isset($trackerId) || !($tracker = $trklib->get_tracker($trackerId))) {
			return $smarty->fetch("wiki-plugins/error_tracker.tpl");
		}
		if (!isset($fields)) {
			$smarty->assign('msg', tra("missing parameters"));
			return $msg;
		}
		foreach ($_REQUEST as $key =>$val) {
			if (substr($key, 0, 2) == 'f_' && $val[0] != '') {
				$fieldId = substr($key, 2);
				$ffs[] = $fieldId;
				if ($formats[$fieldId] == 't') {
					$exactValues[] = '';
					$values[] = $val;
				} else {
					$exactValues[] = $val;
					$values[] = '';
				}
			}
		}
		$params['fields'] = $fields;
		if (empty($params['trackerId'] ))
			$params['trackerId'] = $_REQUEST['trackerId'];
		unset($params['filterfield']); unset($params['filtervalue']);
		if (!empty($ffs)) {
			$params['filterfield'] = $ffs;
			$params['exactvalue'] = $exactValues;
			$params['filtervalue'] = $values;
		}
		include_once('lib/wiki-plugins/wikiplugin_trackerlist.php');
		$dataRes .= wikiplugin_trackerlist($data, $params);
		$dataRes .= '<br />';
	} else {
		$data = '';
	}

	$filters = array();
	if (!isset($trackerId) && !empty($listfields[0])) {
		$field = $trklib->get_tracker_field($listfields[0]);
		$trackerId = $field['trackerId'];
	}

	$fields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '', true, array('fieldId'=>$listfields));

	foreach ($fields['data'] as $field) {
		$fieldId = $field['fieldId'];
		$res = array();
		if (empty($formats[$fieldId])) { // default format depends on field type
			switch ($field['type']){
			case 'e':// category
				global $categlib; include_once('lib/categories/categlib.php');
				$res = $categlib->get_child_categories($field['options_array'][0]);
				$formats[$fieldId] = (count($res) >= 6)? 'd': 'r';
				break;
			case 'd': // drop down list
				$formats[$fieldId] = 'd';
				break;
			case 'R': // radio
				$formats[$fieldId] = 'r';
				break;
			default:
				$formats[$fieldId] = 't';
				break;
			}
		}
		if ($field['type'] == 'e' && ($formats[$fieldId] == 't' || $formats[$fieldId] == 'T')) { // do not accept a format text for a categ for the moment
			if (empty($res)) {
				global $categlib; include_once('lib/categories/categlib.php');
				$res = $categlib->get_child_categories($field['options_array'][0]);
			}
			$formats[$fieldId] = (count($res) >= 6)? 'd': 'r';
		}
		$opts = array();
		if ($formats[$fieldId] == 't' || $formats[$fieldId] == 'T') {
			$selected = empty($_REQUEST['f_'.$fieldId])? '': $_REQUEST['f_'.$fieldId];
		} else {
			$selected = false;
			switch ($field['type']){
			case 'e': // category
				if (empty($res)) {
					global $categlib; include_once('lib/categories/categlib.php');
					$res = $categlib->get_child_categories($field['options_array'][0]);
				}
				foreach ($res as $opt) {
					$opt['id'] = $opt['categId'];
					if (!empty($_REQUEST['f_'.$fieldId]) && in_array($opt['id'], $_REQUEST['f_'.$fieldId])) {
						$opt['selected'] = 'y';
						$selected = true;
					} else {
						$opt['selected'] = 'n';
					}
					$opts[] = $opt;
				}
				break;
			case 'd': // drop down list
			case 'R': // radio buttons
				foreach ($field['options_array'] as $val) {
					$opt['id'] = $val;
					$opt['name'] = $val;
					if (!empty($_REQUEST['f_'.$fieldId]) && $_REQUEST['f_'.$fieldId][0] == $val) {
						$opt['selected'] = 'y';
						$selected = true;
					} else {
						$opt['selected'] = 'n';
					}
					$opts[] = $opt;
				}
				break;
			case 'n': // numeric
			case 'D': // drop down + other
			case 't': // text
			case 'a': // textarea
			case 'm': // email
			case 'y': // country
			case 'w': //dynamic item lists
				if (isset($status)) {
					$res = $trklib->list_tracker_field_values($fieldId, $status);
				} else {
					$res = $trklib->list_tracker_field_values($fieldId);
				}
				foreach ($res as $val) {
					$opt['id'] = $val;
					$opt['name'] = $val;
					if (!empty($_REQUEST['f_'.$fieldId]) && ($_REQUEST['f_'.$fieldId][0] == $val || in_array($val, $_REQUEST['f_'.$fieldId]))) {
						$opt['selected'] = 'y';
						$selected = true;
					} else {
						$opt['selected'] = 'n';
					}
					$opts[] = $opt;
				}
				break;		
		
			default:
				$smarty->assign('msg', tra("tracker field type not processed yet"));
				return $dataRes.$smarty->fetch("error_simple.tpl");
			}
		}
		$filters[] = array('name' => $field['name'], 'fieldId' => $fieldId, 'format'=>$formats[$fieldId], 'opts' => $opts, 'selected'=>$selected);
	}
	$smarty->assign_by_ref('filters', $filters);
	$smarty->assign_by_ref('trackerId', $trackerId);
	$smarty->assign_by_ref('line', $line);
	static $iTrackerFilter = 0;
	$smarty->assign('iTrackerFilter', $iTrackerFilter++);
	if ($displayList == 'n' || !empty($_REQUEST['filter'])) {
		$open = 'y';
	} else {
		$open = 'n';
	}
	$smarty->assign_by_ref('open', $open);
	if (!isset($action)) {
		$action = 'Filter';// tra('Filter');
	}
	$smarty->assign_by_ref('action', $action);

	$dataF = $smarty->fetch('wiki-plugins/wikiplugin_trackerfilter.tpl');

	return $data.$dataF.$dataRes;
}
?>
