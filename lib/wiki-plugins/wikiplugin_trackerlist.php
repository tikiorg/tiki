<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackerlist_info() {
	return array(
		'name' => tra('Tracker List'),
		'documentation' => 'PluginTrackerList',
		'description' => tra('Displays the output of a tracker content, fields are indicated with numeric ids.'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_trackerlist' ),
		'body' => tra('Notice'),
		'icon' => 'pics/icons/database_table.png',
		'filter' => 'text',
		'params' => array(
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Tracker ID'),
				'filter' => 'digits'
			),
			'fields' => array(
				'required' => false,
				'name' => tra('Fields'),
				'description' => tra('Colon-separated list of field IDs to be displayed. Example: 2:4:5'),
				'filter' => 'digits',
				'separator' => ':',
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort'),
				'description' => 'y|n'.' '.tra('If y, sort fields'),
				'filter' => 'alpha'
			),
			'popup' => array(
				'required' => false,
				'name' => tra('Popup'),
				'description' => tra('Colon-separated list of fields to display in a tooltip on mouse over. Example: 6:7'),
				'filter' => 'digits',
				'separator' => ':',
			),
			'stickypopup' => array(
				'required' => false,
				'name' => tra('Sticky Popup'),
				'description' => 'y|n'.' '.tra('If y, the tooltip will stay displayed on mouse out'),
				'filter' => 'alpha'
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showlinks' => array(
				'required' => false,
				'name' => tra('Show Links'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'shownbitems' => array(
				'required' => false,
				'name' => tra('Show Number of Items'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showinitials' => array(
				'required' => false,
				'name' => tra('Show Initials'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showstatus' => array(
				'required' => false,
				'name' => tra('Show Status'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showcreated' => array(
				'required' => false,
				'name' => tra('Show Creation Date'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showlastmodif' => array(
				'required' => false,
				'name' => tra('Show Last Modification'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showfieldname' => array(
				'required' => false,
				'name' => tra('Show Field Name'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showitemrank' => array(
				'required' => false,
				'name' => tra('Show Item Rank'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'status' => array(
				'required' => false,
				'name' => tra('Status Filter'),
				'description' => 'o|p|c|op|oc|pc|opc'.' '.tra('Which item status to list. o = open, p = pending, c = closed.'),
				'filter' => 'alpha'
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort mode. Example: lastModif_desc, created_asc, fieldname_desc, etc.'),
				'filter' => 'word'
			),
			'sortchoice' => array(
				'required' => false,
				'name' => tra('Sort Choice'),
				'description' => tra('Sort choice'),
				'filter' => 'text',
				'separator' => ':'
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum number of items'),
				'description' => tra('Maximum number of items'),
				'filter' => 'int'
			),
			'offset' => array(
			),
			'showpagination' => array(
				'required' => false,
				'name' => tra('Show pagination'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'filterfield' => array(
				'required' => false,
				'name' => tra('Filter Field'),
				'description' => tra('Colon separated list of fields to allow filtering on.'),
				'filter' => 'digits',
				'separator' => ':',
			),
			'filtervalue' => array(
				'required' => false,
				'name' => tra('Filter Value'),
				'description' => tra('Filter value of the filterfield. For better performance, use exact value instead'),
				'filter' => 'text',
				'separator' => ':',
			),
			'exactvalue' => array(
				'required' => false,
				'name' => tra('Exact Value'),
				'description' => tra('Exact value of the filter'),
				'filter' => 'text',
				'separator' => ':',
			),
			'checkbox' => array(
				'required' => false,
				'name' => tra('Checkbox'),
				'description' => tra('Adds a checkbox on each line to be able to do an action.'),
			),
			'goIfOne' => array(
				'required' => false,
				'name' => tra('goIfOne'),
				'description' => 'Go directly to tiki-view_tracker_item.php if only one item is found',
				'filter' => 'alpha'
			),
			'more' => array(
				'required' => false,
				'name' => tra('More'),
				'description' => 'y|n'.' '.tra('If y, show a more link'),
				'filter' => 'alpha'
			),
			'moreurl' => array(
				'required' => false,
				'name' => tra('More URL'),
				'description' => tra('More link pointing to specified URL instead of default tracker item link'),
				'filter' => 'url'
			),
			'view' => array(
				'required' => false,
				'name' => tra('View'),
				'description' => 'user|page '.tra('Display automatically the item of the current user or the current page name'),
				'filter' => 'alpha'
			),
			'tpl' => array(
				'required' => false,
				'name' => tra('Template File'),
				'description' => tra('Use content of the tpl file as template to display the item'),
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Page'),
				'description' => tra('Use content of the wiki page as template to display the item'),
				'filter' => 'pagename'
			),
			'view_user' => array(
				'required' => false,
				'name' => tra('View User'),
				'description' => tra('Will display the items of the specified user'),
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID separated with :'),
				'description' => tra('List of items Ids'),
				'filter' => 'digits',
				'separator' => ':',
			),
			'ignoreRequestItemId' => array(
				'required' => false,
				'name' => tra('Do not filter on the param itemId if in the url'),
				'description' => 'y|n',
				'filter' => 'alpha',
				'default' => 'n',
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('The link that will be on each main field'),
				'filter' => 'url'
			),
			'ldelim' => array(
				'required' => false,
				'name' => tra('Left Deliminator'),
				'description' => tra('Smarty left delimiter'),
			),
			'rdelim' => array(
				'required' => false,
				'name' => tra('Right Deliminator'),
				'description' => tra('Smarty right delimiter'),
			),
			'list_mode' => array(
				'required' => false,
				'name' => tra('Mode'),
				'description' => 'y|n'.' '.tra('If y, value will be truncated'),
				'filter' => 'alpha'
			),
			'export' => array(
				'required' => false,
				'name' => tra('Export Button'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'compute' => array(
				'required' => false,
				'name' => tra('Compute'),
				'description' => tra('Sum or average all the values of a field  and displays it at the bottom of the table.').' '.tra('fieldId').'/sum:'.tra('fieldId').'/avg',
				'filter' => 'text'
			),
			'silent' => array(
				'required' => false,
				'name' => tra('Show nothing if no items'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showdelete' => array(
				'required' => false,
				'name' => tra('Delete'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showwatch' => array(
				'required' => false,
				'name' => tra('Show Watch Button'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'showrss' => array(
				'required' => false,
				'name' => tra('Show Feed Button'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
		),
	);
}

function wikiplugin_trackerlist($data, $params) {
	global $smarty, $tikilib, $dbTiki, $userlib, $tiki_p_admin_trackers, $prefs, $_REQUEST, $tiki_p_view_trackers, $user, $page, $tiki_p_tracker_vote_ratings, $tiki_p_tracker_view_ratings, $trklib, $tiki_p_traker_vote_rating, $tiki_p_export_tracker, $tiki_p_watch_trackers;
	require_once("lib/trackers/trackerlib.php");
	global $notificationlib;  include_once('lib/notifications/notificationlib.php');//needed if plugin tracker after plugin trackerlist
	static $iTRACKERLIST = 0;
	++$iTRACKERLIST;
	$smarty->assign('iTRACKERLIST', $iTRACKERLIST);
	extract ($params,EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y' || !isset($trackerId) || !($tracker_info = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	} else {

		global $auto_query_args;
		$auto_query_args = array_merge($auto_query_args, array('itemId','tr_initial',"tr_sort_mode$iTRACKERLIST",'tr_user'));
		$smarty->assign('trackerId', $trackerId);
		$tracker_info = $trklib->get_tracker($trackerId);
		if ($t = $trklib->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info, $t);
		}

		if (!isset($sort)) {
			$sort = 'n';
		}

		if ($tiki_p_admin_trackers != 'y') {
			$perms = $tikilib->get_perm_object($trackerId, 'tracker', $tracker_info, false);
			if ($perms['tiki_p_view_trackers'] != 'y' && !$user) {
				return;
			}
			$userCreatorFieldId = $trklib->get_field_id_from_type($trackerId, 'u', '1%');
			$groupCreatorFieldId = $trklib->get_field_id_from_type($trackerId, 'g', '1%');
			if ($perms['tiki_p_view_trackers'] != 'y' && $tracker_info['writerCanModify'] != 'y' && empty($userCreatorFieldId) && empty($groupCreatorFieldId)) {
				return;
			}
			$smarty->assign_by_ref('perms', $perms);
		}

		global $trklib; require_once("lib/trackers/trackerlib.php");
		if (!empty($fields)) {
			$listfields = $fields;
			if ($sort == 'y') {
				$allfields = $trklib->sort_fields($allfields, $listfields);
			}
		} elseif (!empty($wiki) || !empty($tpl)) {
				if (!empty($wiki)) {
					$listfields = $trklib->get_pretty_fieldIds($wiki, 'wiki');
				} else {
					$listfields = $trklib->get_pretty_fieldIds($tpl, 'tpl');
				}
		} else {
			$listfields = '';
		}
		if (!empty($compute) && !empty($listfields)) {
			if (preg_match_all('/[0-9.]+/', $compute, $matches)) {
				foreach ($matches[0] as $f) {
					if (!in_array($f, $listfields))
						$listfields[] = $f;
				}
			}
		}
		$limit = $listfields;
		if (!empty($filterfield) && !empty($limit)) {
			$limit = array_unique(array_merge($limit, $filterfield));
		}
		if (!empty($limit) && $trklib->test_field_type($limit, array('C'))) {
			$limit = '';
		}
		$allfields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '', true, '', $limit);

		if (!empty($filterfield)) {
			if (is_array($filterfield)) {
				foreach ($filterfield as $ff) {
					unset($filterfieldok);
					if (is_array($ff)) {// already checked in trackerfilter
						$filterfieldok=true;
						break;
					} else {
						foreach ($allfields['data'] as $f) {
							if ($f['fieldId'] == $ff) {
								$filterfieldok=true;
								break;
							}
						}
					}
					if (!isset($filterfieldok))
						break;
				}
			} else {
				foreach ($allfields['data'] as $f) {
					if ($f['fieldId'] == $filterfield) {
						$filterfieldok=true;
						break;
					}
				}
			}
			if (!isset($filterfieldok)) {
				return tra('incorrect filterfield');
			}
		}

		if (!empty($_REQUEST['itemId']) && $tiki_p_tracker_vote_ratings == 'y' && $user) {
			$hasVoted = false;
			foreach ($allfields['data'] as $f) {
				if ($f['type'] == 's' && isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' && ($f['name'] == 'Rating' || $f['name'] = tra('Rating'))) {
					$i = $f['fieldId'];
					if (isset($_REQUEST["ins_$i"]) && ($_REQUEST["ins_$i"] == 'NULL' || in_array($_REQUEST["ins_$i"], split(',',$tracker_info['ratingOptions'])))) {
						$trklib->replace_rating($trackerId, $_REQUEST['itemId'], $i, $user, $_REQUEST["ins_$i"]);
						$hasVoted = true; 
					}
				} elseif ($f['type'] == '*') {
					$i = $f['fieldId'];
					if (isset($_REQUEST["ins_$i"])) {
						$trklib->replace_star($_REQUEST["ins_$i"], $trackerId, $_REQUEST['itemId'], $f, $user);
						$hasVoted = true;
					}
				}
			}
			if ($hasVoted) {
				$url = preg_replace('/[(\?)|&]vote=y/', '$1', preg_replace('/[(\?)|&]itemId=[0-9]+/', '$1', preg_replace('/[(\?)|&]ins_[0-9]+=-?[0-9]*/', '$1', $_SERVER['REQUEST_URI'])));
				header("Location: $url");
				die;
			}
		}

		if (!empty($showwatch) && $showwatch == 'y' && $prefs['feature_user_watches'] == 'y' && $tiki_p_watch_trackers == 'y' && !empty($user)) {
			if (isset($_REQUEST['watch']) && isset($_REQUEST['trackerId']) && $_REQUEST['trackerId'] == $trackerId) {
				if ($_REQUEST['watch'] == 'add') { 
					$tikilib->add_user_watch($user, 'tracker_modified', $trackerId, 'tracker', $tracker_info['name'], "tiki-view_tracker.php?trackerId=" . $trackerId);
				} elseif ($_REQUEST['watch'] == 'stop') {
					$tikilib->remove_user_watch($user, 'tracker_modified', $trackerId, 'tracker');
				}
			}
			if ($tikilib->user_watches($user, 'tracker_modified', $trackerId, 'tracker')) {
				$smarty->assign('user_watching_tracker', 'y');
			} else {
				$smarty->assign('user_watching_tracker', 'n');
			}
		} else {
			$smarty->clear_assign('user_watching_tracker');
		}
		if (empty($showrss) || $showrss == 'n') {
			$smarty->assign('showrss', 'n');
		} else {
			$smarty->assign('showrss', 'y');
		}

		if (empty($listfields)) {
			foreach($allfields['data'] as $f) {
				$listfields[] = $f['fieldId'];
			}
		}
		if (!empty($popup)) {
			$popupfields = $popup;
		} else {
			$popupfields = array();
		}
		if ($t = $trklib->get_tracker_options($trackerId))
			$tracker_info = array_merge($tracker_info, $t);
		$smarty->assign_by_ref('tracker_info', $tracker_info);
		
		//$query_array = array();
		//$quarray = array();
		//TikiLib::parse_str($_SERVER['QUERY_STRING'],$query_array);

		if (isset($stickypopup) && $stickypopup == 'y') {
			$stickypopup = true;
		} else {
			$stickypopup = false;
		}
		$smarty->assign_by_ref('stickypopup', $stickypopup);

		if (!isset($showtitle)) {
			$showtitle = "n";
		}
		$smarty->assign_by_ref('showtitle', $showtitle);
		
		if (!isset($showlinks)) {
			$showlinks = "n";
		}
		$smarty->assign_by_ref('showlinks', $showlinks);
		
		if (!isset($showdesc)) {
			$showdesc = "n";
		}
		$smarty->assign_by_ref('showdesc', $showdesc);
		
		if (!isset($showinitials)) {
			$showinitials = "n";
		}
		$smarty->assign_by_ref('showinitials', $showinitials);

		if (!isset($shownbitems)) {
			$shownbitems = "n";
		}
		$smarty->assign_by_ref('shownbitems', $shownbitems);
		
		if (!isset($showstatus)) {
			$showstatus = "n";
		}
		$smarty->assign_by_ref('showstatus', $showstatus);

		if (!isset($showfieldname)) {
			$showfieldname = "y";
		}
		$smarty->assign_by_ref('showfieldname', $showfieldname);

		if (!isset($showitemrank)) {
			$showitemrank = 'n';
		}
		$smarty->assign_by_ref('showitemrank', $showitemrank);

		if (!isset($showdelete)) {
			$showdelete = 'n';
		}
		$smarty->assign_by_ref('showdelete', $showdelete);
		if (!isset($showpagination)) {
			$showpagination = 'y';
		}
		$smarty->assign_by_ref('showpagination', $showpagination);
		if (!isset($sortchoice)) {
			$sortchoice = '';
		} else {
			foreach ($sortchoice as $i=>$sc) {
				$sc = explode('|', $sc);
				$sortchoice[$i] = array('value'=>$sc[0], 'label'=>empty($sc[1])?$sc[0]: $sc[1]);
			}
		}
		$smarty->assign_by_ref('sortchoice', $sortchoice);

		if (!isset($status)) {
			$status = "o";
		}
		$tr_status = $status;
		$smarty->assign_by_ref('tr_status', $tr_status);
		if (!isset($list_mode)) {
			$list_mode = 'y';
		}
		$smarty->assign_by_ref('list_mode', $list_mode);

		if (!isset($showcreated)) {
			$showcreated = $tracker_info['showCreated'];
		}
		$smarty->assign_by_ref('showcreated', $showcreated);
		if (!isset($showlastmodif)) {
			$showlastmodif = $tracker_info['showLastModif'];
		}
		$smarty->assign_by_ref('showlastmodif', $showlastmodif);
		if (!isset($more))
			$more = 'n';
		$smarty->assign_by_ref('more', $more);
		if (!isset($moreurl))
			$moreurl = 'tiki-view_tracker.php';
		$smarty->assign_by_ref('moreurl', $moreurl);
		if (!isset($url))
			$url = '';
		$smarty->assign_by_ref('url', $url);
		if (!isset($export))
			$export = 'n';
		$smarty->assign_by_ref('export', $export);

		if (!empty($ldelim))
			$smarty->left_delimiter = $ldelim;
		if (!empty($rdelim))
			$smarty->right_delimiter = $rdelim;

		if (isset($checkbox)) {
			$cb = split('/', $checkbox);
			if (isset($cb[0]))
				$check['fieldId'] = $cb[0];
			if (isset($cb[1]))
				$check['name'] = $cb[1];
			if (isset($cb[2]))
				$check['title'] = $cb[2];
			if (isset($cb[3]))
				$check['submit'] = $cb[3];
			if (isset($cb[4]))
				$check['action'] = $cb[4];
			if (isset($cb[5]))
				$check['tpl'] = $cb[5];
			$smarty->assign_by_ref('checkbox', $check);
		}	

		if (isset($_REQUEST["tr_sort_mode$iTRACKERLIST"])) {
			$sort_mode = $_REQUEST["tr_sort_mode$iTRACKERLIST"];
		} elseif (!isset($sort_mode)) {
			if (!empty($tracker_info['defaultOrderKey'])) {
				if ($tracker_info['defaultOrderKey'] == -1)
					$sort_mode = 'lastModif';
				elseif ($tracker_info['defaultOrderKey'] == -2)
					$sort_mode = 'created';
				elseif ($tracker_info['defaultOrderKey'] == -3)
					$sort_mode = 'itemId';
				else
					$sort_mode = 'f_'.$tracker_info['defaultOrderKey'];
				if (isset($tracker_info['defaultOrderDir'])) {
					$sort_mode.= "_".$tracker_info['defaultOrderDir'];
				} else {
					$sort_mode.= "_asc";
				}
			} else {
				$sort_mode = '';
			}
		} elseif ($sort_mode != 'created_asc' && $sort_mode != 'lastModif_asc' && $sort_mode != 'created_desc' && $sort_mode != 'lastModif_desc' && !preg_match('/f_[0-9]+_(asc|desc)/', $sort_mode)) {
			return tra('Incorrect param').' sort_mode';
		}

		$tr_sort_mode = $sort_mode;
		$smarty->assign_by_ref('tr_sort_mode', $tr_sort_mode);
		
		if (isset($compute)) {
			$max = -1; // to avoid confusion compute is on what you see or all the items
		} elseif (!isset($max)) {
			$max = $prefs['maxRecords'];
		}

		if (isset($_REQUEST['tr_offset'])) {
			$tr_offset = $_REQUEST['tr_offset'];
		} else {
			$tr_offset = 0;
		}
		$smarty->assign_by_ref('tr_offset',$tr_offset);

			
		$tr_initial = '';
		if ($showinitials == 'y') {
			if (isset($_REQUEST['tr_initial'])) {
			  //$query_array['tr_initial'] = $_REQUEST['tr_initial'];
				$tr_initial = $_REQUEST['tr_initial'];
			}
			$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));
		}
		$smarty->assign_by_ref('tr_initial', $tr_initial);

		if ((isset($view) && $view == 'user') || isset($view_user) || isset($_REQUEST['tr_user'])) {
			if ($f = $trklib->get_field_id_from_type($trackerId, 'u', '1%')) {
				$filterfield[] = $f;
				$filtervalue[] = '';
				if (!isset($_REQUEST['tr_user'])) {
					$exactvalue[] = isset($view)? (empty($user)?'Anonymous':$user): $view_user;
				} else {
					$exactvalue[] = $_REQUEST['tr_user'];
					$smarty->assign_by_ref('tr_user', $exactvalue);
				}
			}
		}
		if (isset($view) && $view == 'page' && isset($_REQUEST['page'])) {
			if (($f = $trklib->get_field_id_from_type($trackerId, 'k', '1%')) || ($f = $trklib->get_field_id_from_type($trackerId, 'k', '%,1%')) || ($f =  $trklib->get_field_id_from_type($trackerId, 'k'))) {
				$filterfield[] = $f;
				$filtervalue[] = '';
				$exactvalue[] = $_REQUEST['page'];
			}
		}
			
		if (!isset($filtervalue)) {
			$filtervalue = '';
		} else {
			foreach ($filtervalue as $i=>$f) {
				if ($f == '#user') {
					$filtervalue[$i] = $user;
				}
			}
		}
		
		if (!isset($exactvalue)) {
			$exactvalue = '';
		} else {
			foreach ($exactvalue as $i=>$f) {
				if ($f == '#user') {
					$exactvalue[$i] = $user;
				}
			}
		}
		if (!empty($_REQUEST['itemId']) && (empty($ignoreRequestItemId) || $ignoreRequestItemId != 'y') ) {
			$itemId = $_REQUEST['itemId'];
		}

		if (isset($itemId)) {
			if (strstr($itemId, ':'))
				$itemId = explode(':', $itemId);
			$filter = array('tti.`itemId`'=> $itemId);
		} else {
			$filter = '';
		}
		
		$newItemRateField = false;
		$status_types = $trklib->status_types();
		$smarty->assign('status_types', $status_types);

		if (!isset($filterfield)) {
			$filterfield = '';
		} else {
			if (!empty($filterfield)) {
				if (!empty($filtervalue)) {
					$fvs = $filtervalue;
					unset($filtervalue);
					for ($i = 0, $count_ff = count($filterfield); $i < $count_ff; ++$i) {
						$filtervalue[] = isset($fvs[$i])? $fvs[$i]:'';
					}
				}
				if (!empty($exactvalue)) {
					$evs = $exactvalue;
					unset($exactvalue);
					for ($i = 0, $count_ff2 = count($filterfield); $i < $count_ff2; ++$i) {
						if (isset($evs[$i])) {
							if (preg_match('/(not)?categories\(([0-9]+)\)/', $evs[$i], $matches)) {
								global $categlib; include_once('lib/categories/categlib.php');
								$categs = $categlib->list_categs($matches[2]);
								$l = array($matches[2]);
								foreach ($categs as $cat) {
									$l[] = $cat['categId'];
								}
								if (empty($matches[1])) {
									$exactvalue[] = $l;
								} else {
									$exactvalue[] = array('not'=>$l);
								}
							} elseif (preg_match('/(not)?preference\((.*)\)/', $evs[$i], $matches)) {
								if (empty($matches[1])) {
									$exactvalue[] = $prefs[$matches[2]];
								} else {
									$exactvalue[] = array('not'=>$prefs[$matches[2]]);
								}
							} elseif (preg_match('/(not)?field\(([0-9]+)(,([0-9]+|user)(,([0-9]+))?)?\)/', $evs[$i], $matches)) { // syntax field(fieldId, user, trackerId) or field(fieldId)(need the REQUEST['itemId'] or field(fieldId, itemId) or field(fieldId, user)
								if (empty($matches[4]) && !empty($_REQUEST['itemId'])) { // user the itemId of the url
									$matches[4] = $_REQUEST['itemId'];
								}
								if (!empty($matches[4]) && $matches[4] == 'user') {
									if (!empty($matches[6])) { // pick the user item of this tracker
										$t_i = $trklib->get_tracker($matches[6]);
										$matches[4] = $trklib->get_user_item($matches[6], $t_i, $user);
									} elseif ($prefs['userTracker'] == 'y') { //pick the generic user tracker
										global $userlib;
										$utid = $userlib->get_tracker_usergroup($user);
										$matches[4] = $trklib->get_item_id($utid['usersTrackerId'], $utid['usersFieldId'], $user);
									}
								}
								if (!empty($matches[4])) {
									$l = $trklib->get_item_value(0, $matches[4], $matches[2]);
									$field = $trklib->get_tracker_field($matches[2]);
									if ($field['type'] == 'r') {
										$refItemId = $trklib->get_item_id($field['options_array'][0], $field['options_array'][1], $l);
										$l = $trklib->get_item_value($field['options_array'][0], $refItemId, $field['options_array'][3]);
									}
								}
								if (empty($matches[1])) {
									$exactvalue[] = $l;
								} else {
									$exactvalue[] = array('not'=>$l);
								}
							} else {
								$exactvalue[] = $evs[$i];
							}
						} else {
							$exactvalue[] = '';
						}
					}
				}
			}
		}
		if ($tiki_p_admin_trackers != 'y' && $perms['tiki_p_view_trackers'] != 'y' && $tracker_info['writerCanModify'] == 'y' && $user && $userCreatorFieldId) { //patch this should be in list_items
			if ($filterfield != $userCreatorFieldId || (is_array($filterfield) && !in_array($$userCreatorFieldId, $filterfield))) {
				if (is_array($filterfield))
					$filterfield[] = $userCreatorFieldId;
				elseif (empty($filterfield))
					$filterfield = $userCreatorFieldId;
				else
					$filterfield = array($filterfield, $fieldId);
				if (is_array($exactvalue))
					$exactvalue[] = $user;
				elseif (empty($exactvalue))
					$exactvalue = $user;
				else
					$exactvalue = array($exactvalue, $user);
			}
		}
		if ($tiki_p_admin_trackers != 'y' && $perms['tiki_p_view_trackers'] != 'y' && $user && $groupCreatorFieldId) {
			if ($filterfield != $groupCreatorFieldId || (is_array($filterfield) && !in_array($groupCreatorFieldId, $filterfield))) {
				$groups = $userlib->get_user_groups($user);
				if (is_array($filterfield))
					$filterfield[] = $groupCreatorFieldId;
				elseif (empty($filterfield))
					$filterfield = $groupCreatorFieldId;
				else
					$filterfield = array($filterfield, $fieldId);
				if (is_array($exactvalue))
					$exactvalue[] = array_merge($exactvalue, $groups);
				elseif (empty($exactvalue))
					$exactvalue = $groups;
				else
					$exactvalue = array_merge(array($exactvalue), $groups);
				global $group;// awful trick - but the filter garantee that the group is ok
				$smarty->assign_by_ref('ours', $group);
				$perms = array_merge($perms, $trklib->get_special_group_tracker_perm($tracker_info));
			}
		}

		for ($i = 0, $count_allf = count($allfields['data']); $i < $count_allf; $i++) {
			if ((in_array($allfields["data"][$i]['fieldId'],$listfields) or in_array($allfields["data"][$i]['fieldId'],$popupfields))and $allfields["data"][$i]['isPublic'] == 'y') {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
			}
			if (isset($check['fieldId']) && $allfields["data"][$i]['fieldId'] == $check['fieldId']) {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
				if (!in_array($allfields["data"][$i]['fieldId'], $listfields))
					$allfields["data"][$i]['isPublic'] == 'n'; //don't show it
				$check['ix'] = count($passfields) -1;
			}
			if ($allfields["data"][$i]['name'] == 'page' && empty($filterfield) && empty($displayList)) {
				$filterfield = $allfields["data"][$i]['fieldId'];
				$filtervalue = $_REQUEST['page'];
			}
			if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' 
					and $allfields["data"][$i]['type'] == 's' and $allfields["data"][$i]['name'] == 'Rating') {
				$newItemRateField = $allfields["data"][$i]['fieldId'];
			}
		}
		$smarty->assign_by_ref('filterfield',$filterfield);
		$smarty->assign_by_ref('filterfield',$filtervalue);
		$smarty->assign_by_ref('fields', $passfields);
		$smarty->assign_by_ref('filterfield',$exactvalue);
		$smarty->assign_by_ref('listfields', $listfields);
		$smarty->assign_by_ref('popupfields', $popupfields);
		if (!empty($export) && $export != 'n' && $tiki_p_export_tracker == 'y') {
			$exportUrl = "tiki-view_tracker.php?trackerId=$trackerId&amp;cookietab=3";
			if (!empty($fields)) 
				$exportUrl .= "&amp;displayedFields=$fields";
			if (is_array($filterfield)) {
				foreach ($filterfield as $i=>$fieldId) {
					$exportUrl .= "&amp;f_$fieldId=";
					if (empty($filtervalue[$i])) {
						$exportUrl .= $exactvalue[$i];
					} else {
						$exportUrl .= $filtervalue[$i];
					}
				}
			} elseif(!empty($filterfield)) {
				$exportUrl .= "&amp;f_$filterfield=";
				if (empty($filtervalue))
					$exportUrl .= $exactvalue;
				else
					$exportUrl .= $filtervalue;
			}
			$smarty->assign('exportUrl', $exportUrl);
		}

		if (!empty($_REQUEST['delete'])) {
			if (($item_info = $trklib->get_item_info($_REQUEST['delete'])) && $trackerId == $item_info['trackerId']) {
				if ($tiki_p_admin_trackers == 'y'
					|| ($perms['tiki_p_modify_tracker_items'] == 'y' && $item_info['status'] != 'p' && $item_info['status'] != 'c')
					|| ($perms['tiki_p_modify_tracker_items_pending'] == 'y' && $item_info['status'] == 'p')
					|| ($perms['tiki_p_modify_tracker_items_closed'] == 'y' && $item_info['status'] == 'c')	) {
					$trklib->remove_tracker_item($_REQUEST['delete']);
				}
			}
		}

		if (count($passfields)) {
			$items = $trklib->list_items($trackerId, $tr_offset, $max, $tr_sort_mode, $passfields, $filterfield, $filtervalue, $tr_status, $tr_initial, $exactvalue, $filter, $allfields);
			if (isset($silent) && $silent == 'y' && empty($items['cant'])) {
				return;
			}

			if ($items['cant'] == 1 && isset($goIfOne) && ($goIfOne == 'y' || $goIfOne == 1)) {
				header('Location: tiki-view_tracker_item.php?itemId='.$items['data'][0]['itemId'].'&amp;trackerId='.$items['data'][0]['trackerId']);
				die;
			}
			
			if ($newItemRateField && !empty($items['data'])) {
				foreach ($items['data'] as $f=>$v) {
					$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$trackerId.'.'.$items['data'][$f]['itemId'],$user);
				}
			}
			if ($tracker_info['useComments'] == 'y' && $tracker_info['showComments'] == 'y') {
				foreach ($items['data'] as $itkey=>$oneitem) {
					$items['data'][$itkey]['comments'] = $trklib->get_item_nb_comments($items['data'][$itkey]['itemId']);
				}
			}
			if ($tracker_info['useAttachments'] == 'y' && $tracker_info['showAttachments'] == 'y') {
				foreach ($items["data"] as $itkey=>$oneitem) {
					$res = $trklib->get_item_nb_attachments($items["data"][$itkey]['itemId']);
					$items["data"][$itkey]['attachments']  = $res['attachments'];
				}
			}
			if (!empty($compute)) {
				$fs = preg_split('/ *: */', $compute);
				foreach ($fs as $fieldId) {
					if (strstr($fieldId, "/")) {
						list($fieldId, $oper) = preg_split('/ *\/ */', $fieldId);
						$oper = strtolower($oper);
						if ($oper == 'average') {
							$oper = 'avg';
						} elseif ($oper != 'sum' && $oper != 'avg') {
							$oper = 'sum';
						}
					} else {
						$oper = 'sum';
					}
					foreach ($items['data'] as $i=>$item) {
						foreach ($item['field_values'] as $field) {
							if ($field['fieldId'] == $fieldId) {
								if (preg_match('/^ *$/', $field['value']) || !is_numeric($field['value']))
									$l[$i] = '0';
								else
									$l[$i] = $field['value'];
								break;
							}
						}
					}
					eval('$value='.implode('+', $l).';');
					if ($oper == 'avg')
						$value = round($value / count($l));
					$computedFields[$fieldId][] = array_merge(array('computedtype' => 'n', 'operator'=>$oper, 'value'=>$value), $passfields[$fieldId]);
				}
				$smarty->assign_by_ref('computedFields', $computedFields);
			} else {
				$smarty->assign('computedFields', '');
			}
			if (!isset($tpl) && !empty($wiki)) {
				$tpl = "wiki:$wiki";
			} elseif (empty($tpl)) {
				$tpl = '';
			}
			if (!empty($tpl))
				$smarty->security = true;
			$smarty->assign('tpl', $tpl);
			
			$smarty->assign_by_ref('max', $max);
			$smarty->assign_by_ref('count_item', $items['cant']);
			$smarty->assign_by_ref('items', $items["data"]);
			$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M"); 
			
			$tracker = $tikilib->get_tracker($trackerId,0,-1);
			/*foreach ($query_array as $k=>$v) {
				if (!is_array($v)) { //only to avoid an error: eliminate the params that are not simple (ex: if you have in the same page a tracker list plugin and a tracker plugin, filling the tracker plugin interfers with the tracker list. In any case this is buggy if two tracker list plugins in the same page and if one needs the query value....
					$quarray[] = urlencode($k) ."=". urlencode($v);
				}
			}
			if (is_array($quarray)) {
				$query_string = implode("&amp;",$quarray);
			} else {
				$quering_string = '';
			}
			$smarty->assign('query_string', $query_string);
			*/
			if (!$tracker) {
				$smarty->assign('msg', tra("Error in tracker ID"));
				return "~np~".$smarty->fetch("error_simple.tpl")."~/np~";
			} else {
				return "~np~".$smarty->fetch('tiki-plugin_trackerlist.tpl')."~/np~";
			}
		} else {
			$smarty->assign('msg', tra("No field indicated"));
			return "~np~".$smarty->fetch("error_simple.tpl")."~/np~";
		}
	}
	return $back;
}
