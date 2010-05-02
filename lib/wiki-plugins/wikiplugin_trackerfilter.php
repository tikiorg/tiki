<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackerfilter_help() {
  $help = tra("Filters the items of a tracker, fields are indicated with numeric ids.").":\n";
  $help .= "~np~{TRACKERFILTER(filters=>2/d:4/r:5,action=>Name of submit button,displayList=y|n,line=y|n,TRACKERLIST_params )}Notice{TRACKERFILTER}~/np~";
  return $help;
}

function wikiplugin_trackerfilter_info() {
	require_once 'lib/wiki-plugins/wikiplugin_trackerlist.php';
	$list = wikiplugin_trackerlist_info();
	$params = array_merge( $list['params'], array(
		'filters' => array(
			'required' => true,
			'name' => tra('Filters'),
			'description' => tra('Example:') . '2/d:4/r:5:(6:7)/sqlsearch',
		),
		'action' => array(
			'required' => false,
			'name' => tra('Action'),
			'description' => tra('Label on the submit button'),
		),
		'displayList' => array(
			'required' => false,
			'name' => tra('Display List'),
			'description' => 'y|n',
		),
		'line' => array(
			'required' => false,
			'name' => tra('Line'),
			'description' => 'y|n - displays all the filter on the same line',
		),
		'noflipflop' => array(
			'required' => false,
			'name' => tra('Always displays the window without flip flop'),
			'description' => 'y|n',
		),
	) );

return array(
		'name' => tra('Tracker Filter'),
		'documentation' => 'PluginTrackerFilter',
		'description' => tra("Filters the items of a tracker, fields are indicated with numeric ids."),
		'prefs' => array( 'feature_trackers', 'wikiplugin_trackerfilter' ),
		'body' => tra('notice'),
		'params' => $params,
	);
}

function wikiplugin_trackerfilter($data, $params) {
	global $smarty, $prefs;
	global $trklib;	include_once('lib/trackers/trackerlib.php');
	static $iTrackerFilter = 0;
	if ($prefs['feature_trackers'] != 'y') {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	$default = array('noflipflop'=>'n', 'action'=>'Filter');//tra('Filter')
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);
	$dataRes = '';
	if (isset($_REQUEST['msgTrackerFilter'])) {
		$smarty->assign('msgTrackerFilter', $_REQUEST['msgTrackerFilter']);
	}
	if (!isset($filters)) {
		return tra('missing parameters').' filters';
	}
	
	$listfields = wikiplugin_trackerFilter_split_filters($filters);
	foreach ($listfields as $i=>$f) {
		if (strchr($f, '/')) {
			list($fieldId, $format) = explode('/',$f);
			$listfields[$i] = $fieldId;
			$formats[$fieldId] = $format;
		} else {
			$formats[$f] = '';
		}
	}
	if (!isset($displayList)) {
		$displayList = 'n';
	}
	if (empty($trackerId) && !empty($_REQUEST['trackerId'])) {
		 $trackerId = $_REQUEST['trackerId'];
	}
	if (!isset($line)) {
		$line = 'n';
	}
	if (empty($_REQUEST['filter'])) { // look if not coming from an initial
		foreach ($_REQUEST as $key =>$val) {
			if (substr($key, 0, 2) == 'f_') {
				$_REQUEST['filter'] = 'y';
				break;
			}
		}
	}
	if (!isset($sortchoice)) {
		$sortchoice = '';
	} else {
		unset($params['sortchoice']);
		if (isset($_REQUEST["tr_sort_mode$iTrackerFilter"])) {
			$params['sort_mode'] = $_REQUEST["tr_sort_mode$iTrackerFilter"];
		}
		foreach ($sortchoice as $i=>$sc) {
			$sc = explode('|', $sc);
			$sortchoice[$i] = array('value'=>$sc[0], 'label'=>empty($sc[1])?$sc[0]: $sc[1]);
		}
	}
	if (empty($trackerId) || !($tracker = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	if ($displayList == 'y' || isset($_REQUEST['filter']) || isset($_REQUEST['tr_offset']) || isset($_REQUEST['tr_sort_mode'])) {
	  
		if (!isset($fields)) {
			$smarty->assign('msg', tra("missing parameters"));
			return $msg;
		}

		$ffs = array();
		$values = array();
		$exactValues = array();
		wikiplugin_trackerfilter_build_trackerlist_filter($_REQUEST, $formats, $ffs, $values, $exactValues);
		// echo '<pre>BUILD_FILTER'; print_r($ffs); print_r($exactValues); echo '</pre>';

		$params['fields'] = $fields;
		if (empty($params['trackerId'] )) {
			$params['trackerId'] = $trackerId;
		}
		unset($params['filterfield']); unset($params['filtervalue']); unset($params['exactvalue']);
		if (!empty($ffs)) {
			$params['filterfield'] = $ffs;
			$params['exactvalue'] = $exactValues;
			$params['filtervalue'] = $values;
		}
		$params['max'] = $prefs['maxRecords'];
		include_once('lib/wiki-plugins/wikiplugin_trackerlist.php');
		$dataRes .= wikiplugin_trackerlist($data, $params);
		$dataRes .= '<br />';
	} else {
		$data = '';
	}

	$smarty->assign_by_ref('sortchoice', $sortchoice);
	$filters = wikiplugin_trackerFilter_get_filters($trackerId, $listfields, $formats);
	if (!is_array($filters)) {
		return $filters;
	}
	$smarty->assign_by_ref('filters', $filters);
	//echo '<pre>';print_r($filters); echo '</pre>';
	$smarty->assign_by_ref('trackerId', $trackerId);
	$smarty->assign_by_ref('line', $line);
	$smarty->assign('iTrackerFilter', $iTrackerFilter++);
	if ($displayList == 'n' || !empty($_REQUEST['filter'])) {
		$open = 'y';
	} else {
		$open = 'n';
	}
	$smarty->assign_by_ref('open', $open);
	$smarty->assign_by_ref('action', $action);
	$smarty->assign_by_ref('noflipflop', $noflipflop);
	$dataF = $smarty->fetch('wiki-plugins/wikiplugin_trackerfilter.tpl');

	static $first = true;

	if( $first ) {
		$first = false;
		global $headerlib;
		$headerlib->add_js('$jq(".trackerfilter-result .prevnext").click( function( e ) {
			e.preventDefault();
			$jq(".trackerfilter-result form")
				.attr("action", $jq(this).attr("href"))
				.submit();
		} );' );
	}

	return '<div class="trackerfilter-result">' . $data.$dataF.$dataRes . '</div>';
}

function wikiplugin_trackerfilter_build_trackerlist_filter($input, $formats, &$ffs, &$values, &$exactValues) {
	global $trklib;
	foreach ($input as $key =>$val) {
		if (substr($key, 0, 2) == 'f_' && !empty($val) && (!is_array($val) || !empty($val[0]))) {
			$val = urldecode($val);
			$fieldId = substr($key, 2);
			if (preg_match('/([0-9]+)(Month|Day|Year|Hour|Minute|Second)/', $fieldId, $matches)) { // a date
				if (!in_array($matches[1], $ffs)) {
					$fieldId = $matches[1];
					$ffs[] = $matches[1];
					// TO do optimize get options of the field
					$date = $trklib->build_date($_REQUEST, $trklib->get_tracker_field($fieldId) , 'f_'.$fieldId);	
					if (empty($formats[$fieldId])) { // = date
						$exactValues[] = $date;
					} else { // > or < data
						$exactValues[] = array($formats[$fieldId]=>$date);
					}
				}
			} else {
				if (!is_numeric($fieldId)) { // composite filter
					$ffs[] = array('sqlsearch'=>explode(':', str_replace(array('(', ')'), '', $fieldId)));
				} else {
					$ffs[] = $fieldId;
				}
				if (isset($formats[$fieldId]) && ($formats[$fieldId] == 't' || $formats[$fieldId] == 'i')) {
					$exactValues[] = '';
					$values[] = ($formats[$fieldId] == 'i')? "$val%": $val;
				} else {
					$exactValues[] = $val;
					$values[] = '';
				}
			}
		}
	}
}

function wikiplugin_trackerFilter_split_filters($filters) {
	$in = false;
	for ($i=0, $max=strlen($filters); $i < $max; ++$i) {
		if ($filters[$i] == '(') {
			$in = true;
		} elseif ($filters[$i] == ')') {
			$in = false;
		} elseif ($in && $filters[$i] == ':') {
			$filters[$i] = ',';
		}
	}
	$list = explode(':', $filters);
	foreach ($list as $i=> $filter) {
		$list[$i] = str_replace(',', ':', $filter);
	}
	return $list;
}

function wikiplugin_trackerFilter_get_filters($trackerId=0, $listfields='', $formats='') {
	global $tiki_p_admin_trackers, $smarty, $tikilib;
	global $trklib;	include_once('lib/trackers/trackerlib.php');
	$filters = array();
	if (empty($trackerId) && !empty($listfields[0])) {
		$field = $trklib->get_tracker_field($listfields[0]);
		$trackerId = $field['trackerId'];
	}

	$fields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '', true, empty($listfields)?'': array('fieldId'=>$listfields));
	if (empty($listfields)) {
		foreach ($fields['data'] as $field) {
			$listfields[] = $field['fieldId'];
		}
	}

	$iField = 0;
	foreach ($listfields as $fieldId) {
		if (!is_numeric($fieldId)) { // composite field
			$filter = array('name'=> 'Text', 'fieldId'=> $fieldId, 'format'=>'sqlsearch');
			If (!empty($_REQUEST['f_'.$fieldId])) {
				$filter['selected'] = $_REQUEST['f_'.$fieldId];
			}
			$filters[] = $filter;
			continue;
		}
		$field = &$fields['data'][$iField];
		++$iField;
		if (($field['isHidden'] == 'y' || $field['isHidden'] == 'c') && $tiki_p_admin_trackers != 'y') {
			continue;
		}
		if ($field['type'] == 'i' || $field['type'] == 'h' || $field['type'] == 'G' || $field['type'] == 'x') {
			continue;
		}
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
			case 'y': // country
				$formats[$fieldId] = 'd';
				break;
			case 'R': // radio
				$formats[$fieldId] = 'r';
				break;
			case '*': //rating
				$formats[$fieldId] = '*';
				break;
			case 'f':
			case 'j':
				$formats[$fieldId] = $field['type'];
				break;
			default:
				$formats[$fieldId] = 't';
				break;
			}
		}
		if ($field['type'] == 'e' && ($formats[$fieldId] == 't' || $formats[$fieldId] == 'T' || $formats[$fieldId] == 'i')) { // do not accept a format text for a categ for the moment
			if (empty($res)) {
				global $categlib; include_once('lib/categories/categlib.php');
				$res = $categlib->get_child_categories($field['options_array'][0]);
			}
			$formats[$fieldId] = (count($res) >= 6)? 'd': 'r';
		}
		$opts = array();
		if ($formats[$fieldId] == 't' || $formats[$fieldId] == 'T' || $formats[$fieldId] == 'i') {
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
					if (!empty($_REQUEST['f_'.$fieldId]) && ((is_array($_REQUEST['f_'.$fieldId]) && in_array($opt['id'], $_REQUEST['f_'.$fieldId])) || (!is_array($_REQUEST['f_'.$fieldId]) && $opt['id'] == $_REQUEST['f_'.$fieldId]))) {
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
			case '*': // stars
				$cumul = '';
				foreach ($field['options_array'] as $val) {
					$sval = strip_tags($tikilib->parse_data($val));
					$opt['id'] = $val;
					if ($field['type'] == '*') {
						$cumul = $opt['name'] = "$cumul*";
					} else {
						$opt['name'] = $sval;
					}
					if (!empty($_REQUEST['f_'.$fieldId]) && ((!is_array($_REQUEST['f_'.$fieldId]) && $_REQUEST['f_'.$fieldId] == $val) || (is_array($_REQUEST['f_'.$fieldId]) && in_array($val, $_REQUEST['f_'.$fieldId])))) {
						$opt['selected'] = 'y';
						$selected = true;
					} else {
						$opt['selected'] = 'n';
					}
					$opts[] = $opt;
				}
				break;
			case 'c': // checkbox
				$opt['id'] = 'y';
				$opt['name'] = 'Yes';
				if (!empty($_REQUEST['f_'.$fieldId]) && $_REQUEST['f_'.$fieldId] == 'y') {
					$opt['selected'] = 'y';
					$selected = true;
				} else {
					$opt['selected'] = 'n';
				}
				$opts[] = $opt;
				$opt['id'] = 'n';
				$opt['name'] = 'No';
				if (!empty($_REQUEST['f_'.$fieldId]) && $_REQUEST['f_'.$fieldId] == 'n') {
					$opt['selected'] = 'y';
					$selected = true;
				} else {
					$opt['selected'] = 'n';
				}
				$opts[] = $opt;
				$formats[$fieldId] = 'r';
				break;
			case 'n': // numeric
			case 'D': // drop down + other
			case 't': // text
			case 'i': // text with initial
			case 'a': // textarea
			case 'm': // email
			case 'y': // country
			case 'w': //dynamic item lists
			case 'r': //item link
			case 'k': //page selector
			case 'u': // user
			case 'g': // group
				if (isset($status)) {
					$res = $trklib->list_tracker_field_values($trackerId, $fieldId, $status);
				} else {
					$res = $trklib->list_tracker_field_values($trackerId, $fieldId);
				}
				foreach ($res as $val) {
					$sval = strip_tags($tikilib->parse_data($val));
					$opt['id'] = $val;
					$opt['name'] = $sval;
					if ($field['type'] == 'y') { // country
						$opt['name'] = str_replace('_', ' ', $opt['name']);
					}
					if (!empty($_REQUEST['f_'.$fieldId]) && ((!is_array($_REQUEST['f_'.$fieldId]) && urldecode($_REQUEST['f_'.$fieldId]) == $val) || (is_array($_REQUEST['f_'.$fieldId]) && in_array($val, $_REQUEST['f_'.$fieldId])))) {
						$opt['selected'] = 'y';
						$selected = true;
					} else {
						$opt['selected'] = 'n';
					}
					$opts[] = $opt;
				}
				break;
		
			case 'f':
			case 'j':
				$field['ins_id'] = 'f_'.$field['fieldId'];
				break;
			default:
				return tra('tracker field type not processed yet').' '.$field['type'];
			}
		}
		$filters[] = array('name' => $field['name'], 'fieldId' => $fieldId, 'format'=>$formats[$fieldId], 'opts' => $opts, 'selected'=>$selected, 'field' => $field);
		// echo '<pre>'; print_r($filters); echo '</pre>';
	}
	return $filters;
}
