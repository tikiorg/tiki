<?php
// $Id: /cvsroot/tikiwiki/tiki/contribution.php,v 1.9.2.1 2007-11-04 22:08:03 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// the script uses the var $_REQUEST['contributions'] = the list of selected contributions to preselect the contributions
//						$contributionItemId = the commentId if the object exists and you want to preselect  the comment contribuions
//						$section: to know where you are

// param: $contributionItemId: id of the comment if in coment/forum

if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== FALSE) {
  //smarty is not there - we need setup
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra('This script cannot be called directly'));
  $smarty->display('error.tpl');
  die;
}

require_once('tiki-setup.php'); 
global $prefs;

if ($prefs['feature_contribution'] == 'y') {
	global $contributionlib; include_once('lib/contribution/contributionlib.php');
	$contributions = $contributionlib->list_contributions();
	if (!empty($_REQUEST['contributions'])) {
		for ($i = $contributions['cant'] - 1; $i >= 0; -- $i) {
			if (in_array($contributions['data'][$i]['contributionId'], $_REQUEST['contributions'])) {
				$contributions['data'][$i]['selected'] = 'y';
				$oneSelected = 'y';
			}
		}
	}
	if (!empty($contributionItemId)) {
		$assignedContributions = $contributionlib->get_assigned_contributions($contributionItemId, 'comment');
		if (!empty($assignedContributions)) {
			foreach ($assignedContributions as $a) {
				for ($i = $contributions['cant'] - 1; $i >= 0; -- $i) {
					if ($a['contributionId'] == $contributions['data'][$i]['contributionId']) {
						$contributions['data'][$i]['selected'] = 'y';
						$oneSelected = 'y';
						break;
					}
				}
			}
		}
	}
	if (!empty($oneSelected)) {
		if ((isset($section) && $section == 'forum' && $prefs['feature_contribution_mandatory_forum'] != 'y') || ((!isset($section) || $section != 'forum') && $prefs['feature_contribution_mandatory_comment'] != 'y'))
		$contributions['data'][] = array('contributionId'=>0, 'name'=>'');
	}		
	$smarty->assign_by_ref('contributions', $contributions['data']);

	if ($prefs['feature_contributor_wiki'] == 'y' && !empty($section) && $section == 'wiki page') {
		$users = $userlib->list_all_users();
		$smarty->assign_by_ref('users', $users);
		if (!empty($_REQUEST['contributors'])) {
			$smarty->assign_by_ref('contributors', $_REQUEST['contributors']);
		}
	}
}
?>
