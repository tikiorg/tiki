<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerfilter.php,v 1.14.2.3 2008-01-06 22:31:23 sylvieg Exp $
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
	$listfields = split(':',$filters);
	foreach ($listfields as $f) {
		if (strchr($f, '/')) {
			list($fieldId, $format) = split('/',$f);
			$formats[$fieldId] = $format;
		} else {
			$formats[$f] = 'r'; // radio as default
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
	if (!isset($filters)) {
		$smarty->assign('msg', tra("missing parameters"));
		return $dataRes.$smarty->fetch("error_simple.tpl");
	}
	$listfields = split(':',$filters);

	$filters = array();
	if (!isset($trackerId))
		$trackerId = 0;
	foreach ($listfields as $f) {
	if (strchr($f, '/')) {
		list($fieldId, $format) = split('/',$f);
 	} else {
 		$fieldId = $f;
		$format = 'r'; // radio as default
	}
	$field = $trklib->get_tracker_field($fieldId);
	if ($trackerId) {
		if ($field['trackerId'] != $trackerId) {
			$smarty->assign('msg', tra('All fields must be from the same tracker'));
			return $dataRes.$smarty->fetch('error_simple.tpl');
		}
	} else {
		$trackerId = $field['trackerId'];
	}

	$selected = false;
	$opts = array();
	switch ($field['type']){
	case 'e': // category
		global $categlib;
		include_once('lib/categories/categlib.php');
		$res = $categlib->get_child_categories($field['options_array'][0]);
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
	case 't': // text
	case 'a': // textarea
	case 'm': // email
	case 'y': // country
	case 'w': //dynamic item lists
		if ($format == 't') {
			if (!empty($_REQUEST['f_'.$fieldId])) {
				$selected = $_REQUEST['f_'.$fieldId];
			}
		} else {
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
		}
		break;		
		
	default:
		$smarty->assign('msg', tra("tracker field type not processed yet"));
		return $dataRes.$smarty->fetch("error_simple.tpl");
	}

	if (!isset($action)) {
		$action = 'Filter';// tra('Filter');
	}
	$smarty->assign('action', $action);

	$filters[] = array('name' => $field['name'], 'fieldId' => $field['fieldId'], 'format'=>$format, 'opts' => $opts, 'selected'=>$selected);
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
	$smarty->assign('open', $open);
	$dataF = $smarty->fetch('wiki-plugins/wikiplugin_trackerfilter.tpl');
	return $data.$dataF.$dataRes;
}
?>
