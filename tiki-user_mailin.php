<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');

// Validate that the mail-in feature is activated
$access->check_feature('feature_mailin');

// Validate current user
$access->check_user($user);

// Validate current user's permissions
$access->check_permission(array('tiki_p_send_mailin'));

$usermailinlib = TikiLib::lib('usermailin');

$tikifeedback = array();

if ($prefs['feature_wiki_structure'] === 'y') {
	$structlib = TikiLib::lib('struct');
	
	// Add a new route
	if (!empty($_REQUEST['mailinSubjPattNew']) || !empty($_REQUEST['mailinBodyPattNew'])) {
		$mailinSubjPattNew = $_REQUEST['mailinSubjPattNew'];
		$mailinBodyPattNew = $_REQUEST['mailinBodyPattNew'];
		$mailinNewStruct = $_REQUEST['mailinNewStruct'];
		$mailinParNew = $_REQUEST['mailinParNew'];
		$mailinActNew = $_REQUEST['mailinActNew'];
		
		$subj_pattern = $mailinSubjPattNew;
		$body_pattern = $mailinBodyPattNew;
		$structure_id = (int)$mailinNewStruct;

		if ($structure_id > 0) {
			$retrieve_data = false;
			$info = $tikilib->get_page_info($mailinParNew, $retrieve_data);
			if ($info == false) {
				$page_id = null;
			} else {
				$page_id = $info['page_id'];
			}
			if ($mailinActNew == "on") {
				$is_active = 'y';
			} else {
				$is_active = 'n';
			}
			
			// Add new structure route
			$usermailinlib->add_user_mailin_struct($user, $subj_pattern, $body_pattern, $structure_id, $page_id, $is_active);
			$tikifeedback[] = array(
				'num' => 1,
				'mes' => tra('Mail-in added new structure route')
			);
		}
	}
	
	// Delete a route
	if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'y') {
		if (isset($_REQUEST['mailin_struct_id']) && (int)$_REQUEST['mailin_struct_id'] > 0) {
			$usermailinlib->delete_user_mailin_struct((int)$_REQUEST['mailin_struct_id']);
			$tikifeedback[] = array(
				'num' => 1,
				'mes' => tra('Deleted structure route')
			);
		}
	}
	
	// Update changed routes
	for ($i = 1; ; $i++) {
		if (!isset($_REQUEST['changed_'.$i])) {
			break;
		}
		if ($_REQUEST['changed_'.$i] !== 'y') {
			continue;
		}
		
		// Route updated
		$mailin_struct_id = $_REQUEST['mailin_struct_id_'.$i];
		if (empty($mailin_struct_id)) {
			continue;
		}
		$mailin_struct_id = (int)$mailin_struct_id;
		$username = $user;
		$subj_pattern = $_REQUEST['mailinSubjPatt'.$i];
		$body_pattern = $_REQUEST['mailinBodyPatt'.$i];
		$structure_id = (int) $_REQUEST['mailinStruct'.$i];

		$retrieve_data = false;
		$info = $tikilib->get_page_info($_REQUEST['mailinPar'.$i], $retrieve_data);
		if ($info === false) {
			$page_id = null;
		} else {
			$page_id = (int)$info['page_id'];
		}

		$is_active = ($_REQUEST['mailinAct'.$i] == "on") ? 'y' : 'n';
		
		$usermailinlib->update_user_mailin_struct($mailin_struct_id, $username, $subj_pattern, $body_pattern, $structure_id, $page_id, $is_active);
		$tikifeedback[] = array(
			'num' => 1,
			'mes' => tra('Updated structure route')
		);
	}
	
	// Prepare route display
	$userStructs = $usermailinlib->list_user_mailin_struct($user);
	
	$offset = 0;
	$maxRecords = -1;
	$sort_mode = 'pageName_asc';
	$structs = $structlib->list_structures($offset, $maxRecords, $sort_mode);

	$smarty->assign('structs', $structs['data']);
	$smarty->assign('userStructs', $userStructs['data']);
	$smarty->assign('addNewRoute', 'y');
} 

$smarty->assign_by_ref('tikifeedback', $tikifeedback);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-user_mailin.tpl');
$smarty->display("tiki.tpl");
