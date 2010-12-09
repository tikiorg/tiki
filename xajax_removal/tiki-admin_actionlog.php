<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
if ($prefs['ajax_xajax'] == "y") {
	require_once ('lib/ajax/ajaxlib.php');
}
if (empty($prefs['feature_jpgraph'])) {
	$prefs['feature_jpgraph'] = 'n'; //optional package does not go througp prefs

}
include_once ('lib/comments/commentslib.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/contribution/contributionlib.php');
$commentslib = new Comments($dbTiki);
$access->check_user($user);
$access->check_feature('feature_actionlog');
$access->check_permission_either(array('tiki_p_view_actionlog', 'tiki_p_view_actionlog_owngroups'));
$auto_query_args = array(
		'actionId',
		'startDate_Day',
		'startDate_Month',
		'startDate_Year',
		'endDate_Day',
		'endDate_Month',
		'endDate_Year',
		'selectedUsers',
		'selectedGroups',
		'list',
		'sort_mode',
		'categId',
		'find'
		);
$categories = $categlib->list_categs();

if (!empty($_REQUEST["action_log_type"])) {
  $action_log_type = $_REQUEST["action_log_type"];
} else {
  $action_log_type = '%';
}

if (!empty($_REQUEST["action_log_action"])) {
  $action_log_action = $_REQUEST["action_log_action"];
} else {
  $action_log_action = '%';
}

$confs = $logslib->get_all_actionlog_conf();
$action_log_conf_selected = $logslib->get_actionlog_conf($action_log_type, $action_log_action);
$nbViewedConfs = 0;

if (isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];
} else {
  $find = '';
}
$smarty->assign('find', $find);
if ( !isset($_REQUEST['offset']) || !empty($_REQUEST['export']) ) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"];
}
if ( !empty($_REQUEST['export']) ) {
	$maxRecords = -1;
} elseif (isset($_REQUEST['max'])) {
	$maxRecords = $_REQUEST['max'];
} else {
	$maxRecords = $prefs['maxRecords'];
}

if ($tiki_p_admin == 'y') {
	if (isset($_REQUEST['save'])) {
		foreach($action_log_conf_selected as $index => $conf) {
			if (isset($_REQUEST['v_' . $conf['code']]) && $_REQUEST['v_' . $conf['code']] == 'on') { //viewed and reported
				$logslib->set_actionlog_conf($conf['action'], $conf['objectType'], 'v');
			} elseif (isset($_REQUEST[$conf['code']]) && $_REQUEST[$conf['code']] == 'on') {
				$logslib->set_actionlog_conf($conf['action'], $conf['objectType'], 'y');
			} else {
				$logslib->set_actionlog_conf($conf['action'], $conf['objectType'], 'n');
			}
		}
		global $actionlogConf; unset($actionlogConf);
		$confs = $logslib->get_all_actionlog_conf();
		$action_log_conf_selected = $logslib->get_actionlog_conf($action_log_type);
		$cookietab = 1;
	}
} else {
	if (isset($_REQUEST['save'])) {
		$_prefs = 'v';
		foreach($action_log_conf_selected as $index => $conf) {
			if ($conf['status'] == 'v' || $conf['status'] == 'y') { // can only change what is recorded
				if (isset($_REQUEST['v_' . $conf['code']]) && $_REQUEST['v_' . $conf['code']] == 'on') { //viewed
					$_prefs.= $conf['id'] . 'v';
				} else {
					$_prefs.= $conf['id'] . 'y';
				}
			}
		}
		global $actionlogConf; unset($actionlogConf);
		$confs = $logslib->get_all_actionlog_conf();
		$action_log_conf_selected = $logslib->get_actionlog_conf($action_log_type);
		$tikilib->set_user_preference($user, 'actionlog_conf', $_prefs);
		$cookietab = 1;
	} else {
		$_prefs = $tikilib->get_user_preference($user, 'actionlog_conf', '');
		if (!empty($_prefs)) {
			foreach($confs as $i => $conf) {
				if ($confs[$i]['status'] == 'v' || $confs[$i]['status'] == 'y') {
					if (preg_match('/[vy]' . $confs[$i]['id'] . '([vy])/', $_prefs, $matches)) $confs[$i]['status'] = $matches[1];
				}
			}
		}
	}
	global $actionlogConf;
	$actionlogConf = $confs;
}
foreach($confs as $conf) {
	if ($conf['status'] == 'v') {
		++$nbViewedConfs;
	}
}
$smarty->assign('nbViewedConfs', $nbViewedConfs);
if (empty($nbViewedConfs)) {
	$cookietab = 2;
}
if (!empty($cookietab)) {
	setcookie('tab', $cookietab);
	$smarty->assign('cookietab', $cookietab);
}
$smarty->assign_by_ref('actionlogConf', $confs);

if (!empty($_REQUEST['actionId']) && $tiki_p_admin == 'y') {
	$action = $logslib->get_info_action($_REQUEST['actionId']);
	if (empty($action)) {
		$smarty->assign('msg', tra('Must specify actionId'));
		$smarty->display("error.tpl");
		die;
	}
	if (isset($_REQUEST['saveAction'])) {
		if ($prefs['feature_contribution'] == 'y') {
			if ($contributionlib->update($action, empty($_REQUEST['contributions']) ? '' : $_REQUEST['contributions'])) {
				$logslib->delete_params($_REQUEST['actionId'], 'contribution');
				if (isset($_REQUEST['contributions'])) {
					$logslib->insert_params($_REQUEST['actionId'], 'contribution', $_REQUEST['contributions']);
				}
				if (isset($_REQUEST['contributors'])) {
					$logslib->insert_params($_REQUEST['actionId'], 'contributor', $_REQUEST['contributors']);
				}
			} else {
				$smarty->assign('error', 'found more than one object that can correspond');
			}
		}
		if (empty($_REQUEST['cat_categories']) && !empty($action['categId'])) {
			$logslib->update_category($_REQUEST['actionId'], 0);
		} elseif (!empty($_REQUEST['cat_categories'])) {
			$old_categ = $action['categId'];
			if (!in_array($action['categId'], $_REQUEST['cat_categories'])) {
				$logslib->update_category($_REQUEST['actionId'], $_REQUEST['cat_categories'][0]);
			}
			if (count($_REQUEST['cat_categories']) > 1) {
				$action['params'] = $logslib->get_action_params($_REQUEST['actionId']);
				$action['contributions'] = $logslib->get_action_contributions($_REQUEST['actionId']);
				foreach($_REQUEST['cat_categories'] as $cat) {
					if ($cat != $old_categ) {
						$logslib->add_action($action['action'], $action['object'], $action['objectType'], $action['comment'], $action['user'], $action['ip'], '', $action['lastModif'], '', '');
					}
				}
			}
		}
	} elseif (isset($_REQUEST['remove'])) {
		$access->check_authenticity();
		$logslib->remove_action($_REQUEST['actionId']);
	} else {
		$smarty->assign_by_ref('action', $action);
		if ($action['objectType'] == 'wiki page') {
			$contributions = $logslib->get_action_contributions($action['actionId']);
			$info = $tikilib->get_page_info($action['object']);
			$contributors = $logslib->get_wiki_contributors($info);
			$tcontributors = array();
			foreach($contributors as $c) {
				$tcontributors[] = $c['userId'];
			}
			$smarty->assign_by_ref('contributors', $tcontributors);
		} elseif ($id = $logslib->get_comment_action($action)) {
			$contributions = $logslib->get_action_contributions($action['actionId']);
		} else {
			$contributions = $contributionlib->get_assigned_contributions($action['object'], $action['objectType']); // todo: do a left join

		}
		$cont = array();
		foreach($contributions as $contribution) {
			$cont[] = $contribution['contributionId'];
		}
		$_REQUEST['contributions'] = $cont;
		include ('contribution.php');
		$contributions['data'][] = array(
				'contributionId' => 0,
				'name' => ''
				);
		if (!empty($_REQUEST['startDate'])) $smarty->assign('startDate', $_REQUEST['startDate']);
		if (!empty($_REQUEST['endDate'])) $smarty->assign('endDate', $_REQUEST['endDate']);
		if (!empty($action['categId'])) {
			foreach($categories as $i => $cat) {
				if ($action['categId'] == $cat['categId']) {
					$categories[$i]['incat'] = 'y';
				}
			}
		}
	}
}
if ($tiki_p_admin == 'y') {
	$users = $userlib->list_all_users();
	$groups = $userlib->list_all_groups();
} else {
	$users = array(
			$userlib->get_user_id($user) => $user
			);
	$groups = $tikilib->get_user_groups($user);
	$groups = array_diff($groups, array(
				'Anonymous'
				));
	$_REQUEST['selectedUsers'] = array(
			$user
			);
}
$selectedGroups = array();
foreach($groups as $g) {
	$selectedGroups[$g] = 'y';
}
$smarty->assign_by_ref('users', $users);
$smarty->assign_by_ref('groups', $groups);
$smarty->assign_by_ref('categories', $categories);
foreach($categories as $categ) {
	$categNames[$categ['categId']] = $categ['name'];
}
$smarty->assign_by_ref('categNames', $categNames);
if (isset($_REQUEST['list']) || isset($_REQUEST['export']) || isset($_REQUEST['graph'])) {
	@ini_set('max_execution_time', 0); //will not work in safe_mode is on
	$url = '';
	$selectedUsers = array();
	if (isset($_REQUEST['selectedUsers'])) {
		foreach($users as $key => $u) {
			if (in_array($u, $_REQUEST['selectedUsers'])) {
				$url.= "&amp;selectedUsers[]=$u";
				$selectedUsers[$key] = 'y';
			} else $selectedUsers[$key] = 'n';
		}
	}
	$smarty->assign('selectedUsers', $selectedUsers);
	if (isset($_REQUEST['selectedGroups']) && !(count($_REQUEST['selectedGroups']) == 1 && $_REQUEST['selectedGroups'][0] == '')) {
		$selectedGroups = array();
		foreach($groups as $key => $g) {
			if (in_array($g, $_REQUEST['selectedGroups'])) {
				$url.= "&amp;selectedGroups[]=$g";
				$selectedGroups[$g] = 'y';
				if ($tiki_p_admin == 'y' || $tiki_p_view_actionlog_owngroups == 'y') {
					$members = $userlib->get_group_users($g);
					foreach($members as $m) {
						$_REQUEST['selectedUsers'][] = $m;
						$selectedUsers[$m] = 'y';
					}
				}
			} else {
				$selectedGroups[$g] = 'n';
			}
		}
		$smarty->assign_by_ref('selectedGroups', $selectedGroups);
	}
	if (!isset($_REQUEST['selectedUsers']) || (count($_REQUEST['selectedUsers']) == 1 && $_REQUEST['selectedUsers'][0] == '')) $_REQUEST['selectedUsers'] = '';
	if (!isset($_REQUEST['categId']) || $_REQUEST['categId'] == 0) $_REQUEST['categId'] = '';
	else {
		$url.= '&amp;categId=' . $_REQUEST['categId'];
		$smarty->assign('reportCateg', $categNames[$_REQUEST['categId']]);
	}
	$showCateg = $logslib->action_is_viewed('*', 'category');
	$smarty->assign('showCateg', $showCateg ? 'y' : 'n');
	$showLogin = $logslib->action_is_viewed('login', 'system');
	$smarty->assign('showLogin', $showLogin ? 'y' : 'n');
	if (isset($_REQUEST['startDate_Month'])) {
		$startDate = $tikilib->make_time(0, 0, 0, $_REQUEST['startDate_Month'], $_REQUEST['startDate_Day'], $_REQUEST['startDate_Year']);
		$url.= "&amp;start=$startDate";
	} elseif (isset($_REQUEST['startDate'])) {
		$startDate = $_REQUEST['startDate'];
	} else {
		$startDate = $tikilib->make_time(0, 0, 0, $tikilib->date_format('%m') , $tikilib->date_format('%d') , $tikilib->date_format('%Y'));
	}
	$smarty->assign('startDate', $startDate);
	if (isset($_REQUEST['endDate_Month'])) {
		$endDate = $tikilib->make_time(23, 59, 59, $_REQUEST['endDate_Month'], $_REQUEST['endDate_Day'], $_REQUEST['endDate_Year']);
		$url.= "&amp;end=$endDate";
	} elseif (isset($_REQUEST['endDate'])) {
		$endDate = $_REQUEST['endDate'];
	} else $endDate = $tikilib->make_time(23, 59, 59, $tikilib->date_format('%m') , $tikilib->date_format('%d') , $tikilib->date_format('%Y'));
	$smarty->assign('endDate', $endDate);
	$results = $logslib->list_actions('', '', $_REQUEST['selectedUsers'], $offset, $maxRecords, 'lastModif_desc', $find, $startDate, $endDate, $_REQUEST['categId']);
	$actions = $results['data'];
	$actions_cant = $results['cant'];
	$actions = $logslib->get_more_info($actions, $categNames);
	$contributorActions = $logslib->split_actions_per_contributors($actions, $_REQUEST['selectedUsers']);
	if (!empty($_REQUEST['selectedUsers'])) {
		$results = $logslib->list_actions('', '', '', $offset, $maxRecords, 'lastModif_desc', $find, $startDate, $endDate, $_REQUEST['categId']);
		$allActions = $results['data'];
		$allContributorsActions = $logslib->split_actions_per_contributors($actions, '');
	} else {
		$allActions = $actions;
		$allContributorsActions = $contributorActions;
	}
	$userActions = $logslib->get_stat_actions_per_user($contributorActions);
	$smarty->assign_by_ref('userActions', $userActions);
	$objectActions = $logslib->get_stat_actions_per_field($actions, 'object');
	$smarty->assign_by_ref('objectActions', $objectActions);
	$groupContributions = $logslib->get_stat_contributions_per_group($allContributorsActions, $selectedGroups);
	$smarty->assign_by_ref('groupContributions', $groupContributions);
	$userContributions = $logslib->get_stat_contributions_per_user($contributorActions);
	$smarty->assign_by_ref('userContributions', $userContributions['data']);
	if ($showCateg) {
		$statCateg = $logslib->get_action_stat_categ($actions, $categNames);
		$smarty->assign_by_ref('statCateg', $statCateg);
		$volCateg = $logslib->get_action_vol_categ($actions, $categNames);
		if (isset($_REQUEST['unit']) && $_REQUEST['unit'] == 'kb') $volCateg = $logslib->in_kb($volCateg);
		$smarty->assign_by_ref('volCateg', $volCateg);
		$volUserCateg = $logslib->get_action_vol_user_categ($contributorActions, $categNames);
		if (isset($_REQUEST['unit']) && $_REQUEST['unit'] == 'kb') $volUserCateg = $logslib->in_kb($volUserCateg);
		$smarty->assign_by_ref('volUserCateg', $volUserCateg);
		$typeVol = $logslib->get_action_vol_type($volCateg);
		$smarty->assign_by_ref('typeVol', $typeVol);
		$statUserCateg = $logslib->get_actions_per_user_categ($contributorActions, $categNames);
		$smarty->assign_by_ref('statUserCateg', $statUserCateg);
	}
	if ($showLogin) {
		$logins = $logslib->list_logs('login', $_REQUEST['selectedUsers'], 0, -1, 'lastModif_asc', '', $startDate, $endDate, $actions);
		$logTimes = $logslib->get_login_time($logins['data'], $startDate, $endDate, $actions);
		$smarty->assign_by_ref('logTimes', $logTimes);
	}
	if (isset($_REQUEST['unit']) && $_REQUEST['unit'] == 'kb') {
		for ($i = count($actions) - 1; $i >= 0; --$i) {
			if (isset($actions[$i]['add'])) $actions[$i]['add'] = round($actions[$i]['add'] / 1024);
			if (isset($actions[$i]['del'])) $actions[$i]['del'] = round($actions[$i]['del'] / 1024);
		}
	}
	if (isset($_REQUEST['sort_mode'])) {
		list($col, $order) = explode('_', $_REQUEST['sort_mode']);
		$sort = array();
		foreach($actions as $a) {
			$sort[] = isset($a[$col]) ? $a[$col] : '';
		}
		array_multisort($sort, ($order == 'desc') ? SORT_DESC : SORT_ASC, $actions);
		$smarty->assign('sort_mode', $_REQUEST['sort_mode']);
	}
	$smarty->assign_by_ref('actionlogs', $actions);
	if (isset($_REQUEST['unit'])) $url.= '&amp;unit=' . $_REQUEST['unit'];
	$smarty->assign('url', "&amp;list=y$url#Report");
	if (isset($_REQUEST['export'])) {
		$csv = $logslib->export($actions);
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="tiki-actionlogs_stats.csv"');
		if ( function_exists('mb_strlen') ) {
  	  header('Content-Length: '.mb_strlen($csv, '8bit'));
	  } else {
    	header('Content-Length: '.strlen($csv));
 	 }
		echo $csv;
		die();
	}
	if ($prefs['feature_contribution'] == 'y') {
		if (empty($_REQUEST['contribTime'])) $_REQUEST['contribTime'] = 'w';
		$contributionStat = $logslib->get_stat_contribution($actions, $startDate, $endDate, $_REQUEST['contribTime']);
		$smarty->assign_by_ref('contributionStat', $contributionStat['data']);
		$smarty->assign_by_ref('contributionNbCols', $contributionStat['nbCols']);
		$smarty->assign_by_ref('contribTime', $_REQUEST['contribTime']);
	}
}
if (isset($_REQUEST['graph'])) {
	$contributions = $contributionlib->list_contributions(0, -1);
	$legendWidth = 0;
	foreach($contributions['data'] as $contribution) {
		$legendWidth = max($legendWidth, strlen($contribution['name']));
	}
	$legendWidth = $legendWidth * 7 + 20;
	$xUserTickWidth = 0;
	foreach($userContributions['data'] as $u => $val) {
		$xUserTickWidth = max($xUserTickWidth, strlen($u));
	}
	$xUserTickWidth = $xUserTickWidth * 7;
	$xGroupTickWidth = 0;
	foreach($groupContributions as $g => $val) {
		$xGroupTickWidth = max($xGroupTickWidth, strlen($g));
	}
	$xGroupTickWidth = $xGroupTickWidth * 7;
	$yTickWidth = 0;
	foreach($contributionStat['data'] as $contribution) {
		$yTickWidth = max($yTickWidth, $contribution['add'], $contribution['del']);
	}
	$yTickWidth = "($yTickWidth).0";
	$yTickWidth = strlen($yTickWidth) * 7;
	$widthWeek = 70 * $contributionStat['nbCols'] + 100 + $legendWidth;
	$widthTotal = 70 + 100 + $legendWidth + $yTickWidth;
	$height = 250;
	$widthUser = 70 * $userContributions['nbCols'] + 100 + $legendWidth + $yTickWidth;
	$widthGroup = 70 * count($groupContributions) + 100 + $legendWidth + $yTickWidth;
	$space = 20;
	if ($prefs['feature_jpgraph'] == 'y') {
		require_once ('lib/jpgraph/src/jpgraph.php');
		require_once ('lib/jpgraph/src/jpgraph_bar.php');
		require_once ('lib/jpgraph/src/jpgraph_mgraph.php');
		global $imagegallib;
		include_once ('lib/imagegals/imagegallib.php');
		$ext = 'jpeg';
		$background = new MGraph();
		$background->SetImgFormat($ext);
		$background->SetFrame(true, 'black');
		$background->SetMargin(10, 10, 10, 10);
	} else {
		require_once ('lib/sheet/grid.php');
		require_once ('lib/graph-engine/gd.php');
		require_once ('lib/graph-engine/pdflib.php');
		require_once ('lib/graph-engine/ps.php');
		require_once ('lib/graph-engine/graph.pie.php');
		require_once ('lib/graph-engine/graph.bar.php');
		require_once ('lib/graph-engine/graph.multiline.php');
		$graphType = 'BarStackGraphic';
		$ext = 'jpg';
		$background = new GD_GRenderer(max($widthUser, $widthWeek) , 8 * $height, $ext);
		$legendWidth = 300;
	}
	include_once ('lib/smarty_tiki/modifier.tiki_short_date.php');
	$period = ' (' . smarty_modifier_tiki_short_date($startDate);
			$s = smarty_modifier_tiki_short_date($startDate);
			$e = smarty_modifier_tiki_short_date($endDate);
			$period = ($s != $e) ? " ($s-$e)" : " ($s)";
			$accumulated = (isset($_REQUEST['barPlot']) && $_REQUEST['barPlot'] == 'acc') ? true : false;
			$series = $logslib->draw_contribution_user($userContributions, 'add', $contributions);
			if ($series['totalVol']) {
				if ($tiki_p_admin == 'y') {
					$title = tra('Users Contributions: Addition');
				} else {
					$title = sprintf(tra('%s Contributions: Addition') , $user);
				}
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthUser, $height + $xUserTickWidth);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40 + $xUserTickWidth);
					$graph->title->Set($title);
					$graph->subtitle->Set($period);
					if ($tiki_p_admin == 'y') {
						$graph->xaxis->SetTitle(tra('Users') , 'center');
						$graph->xaxis->SetTitleMargin($xUserTickWidth);
					}
					$graph->xaxis->SetLabelAngle(90);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'darkgreen', 2);
					$background->Add($graph, 0, 0);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthUser, $height, $ext);
					$graph = new $graphType;
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 0, 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_contribution_user($userContributions, 'del', $contributions);
			if ($series['totalVol']) {
				if ($tiki_p_admin == 'y') {
					$title = tra('Users Contributions: Suppression');
				} else {
					$title = sprintf(tra('%s Contributions: Suppression') , $user);
				}
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthUser, $height + $xUserTickWidth);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40 + $xUserTickWidth);
					$graph->title->Set($title);
					$graph->subtitle->Set($period);
					if ($tiki_p_admin == 'y') {
						$graph->xaxis->SetTitle(tra('Users') , 'center');
						$graph->xaxis->SetTitleMargin($xUserTickWidth);
					}
					$graph->xaxis->SetLabelAngle(90);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'red', 2);
					$background->Add($graph, 0, ($height + $space + $xUserTickWidth));
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthUser, $height, $ext);
					$graph = new $graphType;
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_week_contribution_vol($contributionStat, 'add', $contributions);
			if ($series['totalVol']) {
				if ($_REQUEST['contribTime'] == 'd') {
					$title = tra('Total Contributions Addition per Day');
					$title2 = tra('Days');
				} else {
					$title = tra('Total Contributions Addition per Week');
					$title2 = tra('Weeks');
				}
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthWeek, $height);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40);
					$graph->title->Set($title);
					$graph->xaxis->SetTitle($title2, 'center');
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->subtitle->Set($period);
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'darkgreen', 2);
					$background->Add($graph, 0, 2 * ($height + $space) + 2 * $xUserTickWidth);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthWeek, $height, $ext);
					$graph = new $graphType;
					unset($series['totalVol']);
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 2 * ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_week_contribution_vol($contributionStat, 'del', $contributions);
			if ($series['totalVol']) {
				if ($_REQUEST['contribTime'] == 'd') {
					$title = tra('Total Contributions Suppression per Day');
					$title2 = tra('Days');
				} else {
					$title = tra('Total Contributions Suppression per Week');
					$title2 = tra('Weeks');
				}
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthWeek, $height);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40);
					$graph->title->Set($title);
					$graph->xaxis->SetTitle($title2, 'center');
					$graph->subtitle->Set($period);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'red', 2);
					$background->Add($graph, 0, 3 * ($height + $space) + 2 * $xUserTickWidth);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthWeek, $height, $ext);
					$graph = new $graphType;
					unset($series['totalVol']);
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 3 * ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_contribution_vol($contributionStat, 'add', $contributions);
			if ($series['totalVol']) {
				$title = tra('Total Contributions: Addition');
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthTotal, $height);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40);
					$graph->title->Set($title);
					$graph->subtitle->Set($period);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'darkgreen', 2);
					$background->Add($graph, 0, 4 * ($height + $space) + 2 * $xUserTickWidth);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthTotal, $height, $ext);
					$graph = new $graphType;
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 4 * ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_contribution_vol($contributionStat, 'del', $contributions);
			if ($series['totalVol']) {
				$title = tra('Total Contributions: Suppression');
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthTotal, $height);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40);
					$graph->title->Set($title);
					$graph->subtitle->Set($period);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'red', 2);
					$background->Add($graph, 0, 5 * ($height + $space) + 2 * $xUserTickWidth);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthTotal, $height, $ext);
					$graph = new $graphType;
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 5 * ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_contribution_group($groupContributions, 'add', $contributions);
			if ($series['totalVol']) {
				$title = tra('Groups Contributions: Addition');
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthGroup, $height + $xGroupTickWidth);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40 + $xGroupTickWidth);
					$graph->title->Set($title);
					$graph->subtitle->Set($period);
					$graph->xaxis->SetTitle(tra('Groups') , 'center');
					$graph->xaxis->SetLabelAngle(90);
					$graph->xaxis->SetTitleMargin($xGroupTickWidth);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'darkgreen', 2);
					$background->Add($graph, 0, 6 * ($height + $space) + 2 * $xUserTickWidth);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthGroup, $height, $ext);
					$graph = new $graphType;
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 6 * ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			$series = $logslib->draw_contribution_group($groupContributions, 'del', $contributions);
			if ($series['totalVol']) {
				$title = tra('Groups Contributions: Suppression');
				if ($prefs['feature_jpgraph'] == 'y') {
					$graph = new Graph($widthGroup, $height + $xGroupTickWidth);
					$graph->img->SetImgFormat($ext);
					$logslib->graph_to_jpgraph($graph, $series, $accumulated, $_REQUEST['bgcolor'], $_REQUEST['legendBgcolor']);
					$graph->img->SetMargin(40 + $yTickWidth, 40 + $legendWidth, 50, 40 + $xGroupTickWidth);
					$graph->title->Set($title);
					$graph->subtitle->Set($period);
					$graph->xaxis->SetLabelAngle(90);
					$graph->xaxis->SetTitle(tra('Groups') , 'center');
					$graph->xaxis->SetTitleMargin($xGroupTickWidth);
					$graph->yaxis->title->Set(tra($_REQUEST['unit']));
					$graph->yaxis->SetTitleMargin($yTickWidth);
					$graph->setFrame(true, 'red', 2);
					$background->Add($graph, 0, 7 * ($height + $space) + 2 * $xUserTickWidth + $xGroupTickWidth);
					if (!empty($_REQUEST['galleryId'])) {
						$logslib->insert_image($_REQUEST['galleryId'], $graph, $ext, $title, $period);
					}
				} else {
					$renderer = new GD_GRenderer($widthGroup, $height, $ext);
					$graph = new $graphType;
					$graph->setData($series);
					$graph->setTitle($title);
					$graph->draw($renderer);
					imagecopy($background->gd, $renderer->gd, 0, 7 * ($height + $space) , 0, 0, $renderer->width, $renderer->height);
				}
			}
			if ($prefs['feature_jpgraph'] == 'y') {
				$background->Stroke();
			} else {
				ob_start();
				$background->httpOutput("graph.$ext");
				$content = ob_get_contents();
				ob_end_flush();
			}
			die;
} elseif ($prefs['feature_jpgraph'] == 'y') {
	$smarty->assign('bgcolors', array(
				'white',
				'gray',
				'silver',
				'ivory',
				'whitesmoke',
				'beige',
				'darkgrey'
				));
	//get_strings tra('white'), tra('gray'), tra('silver'), tra('ivory'), tra('whitesmoke'), tra('beige'),tra('darkgrey')
	$smarty->assign('defaultBgcolor', 'whitesmoke');
	$smarty->assign('defaultLegendBgcolor', 'white');
	global $imagegallib; include_once ('lib/imagegals/imagegallib.php');
	$galleries = $imagegallib->list_galleries(0, -1, 'name_asc', $user, '');
	$smarty->assign('galleries', $galleries['data']);
}

$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('cant', $actions_cant);
$smarty->assign_by_ref('maxRecords', $maxRecords);
$action_log_types = $logslib->get_actionlog_types();
if (!empty($_REQUEST['action_log_type'])) $smarty->assign('action_log_type',$_REQUEST['action_log_type']);
if (!empty($_REQUEST['action_log_action'])) $smarty->assign('action_log_action',$_REQUEST['action_log_action']);
$smarty->assign('action_log_conf_selected',$action_log_conf_selected);
$smarty->assign('action_log_types',$action_log_types);
$smarty->assign('action_log_actions',$logslib->get_actionlog_actions());

if (isset($_REQUEST['time'])) $smarty->assign('time', $_REQUEST['time']);
if (isset($_REQUEST['unit'])) $smarty->assign('unit', $_REQUEST['unit']);
// Display the template
$smarty->assign('mid', 'tiki-admin_actionlog.tpl');
$smarty->display("tiki.tpl");
