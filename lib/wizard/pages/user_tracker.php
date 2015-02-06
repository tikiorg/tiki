<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class UserWizardUserTracker extends Wizard 
{
	function pageTitle ()
    {
        return tra('User Details');
    }
    
   	function isEditable ()
	{
		return true;
	}

	function isVisible ()
	{
		global	$prefs;
		return $prefs['userTracker'] === 'y';
	}

	function onSetupPage ($homepageUrl) 
	{
		global $user, $prefs;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		$registrationlib = TikiLib::lib('registration');

		$trklib = TikiLib::lib('trk');

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		$showPage = false;
		
		// Show if option is selected
		if ($prefs['userTracker'] === 'y') {
			$showPage = true;
		}
		
		if (empty($user)) {
			TikiLib::lib('access')->redirect($prefs['tikiIndex'], tr('You cannot access this page directly'));
			return;
		}
		
		$smarty->assignByRef('merged_prefs', $registrationlib->merged_prefs);
		
		
		//get custom fields
		$customfields = $registrationlib->get_customfields();
		$smarty->assignByRef('customfields', $customfields);
		
		if ($registrationlib->merged_prefs['userTracker'] == 'y') {
			$smarty->assign('trackerEditFormId', 1);	// switch on to make mandatory_star *'s appear even though the tracker form is loaded by ajax
		}
		
		$needs_validation_js = true;
		if ($registrationlib->merged_prefs['userTracker'] == 'y') {
			$chosenGroup = 'Registered';
			$re = $userlib->get_group_info($chosenGroup);

			if (!empty($re['usersTrackerId']) && ( (!empty($re['registrationUsersFieldIds']) && $prefs['feature_userWizardDifferentUsersFieldIds'] != 'y') or ($prefs['feature_userWizardDifferentUsersFieldIds'] == 'y' && !empty($prefs['feature_userWizardUsersFieldIds'])) ) ) {
				$needs_validation_js = false;
				include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
				if (isset($_REQUEST['name'])) {
					$user = $_REQUEST['name'];	// so that one can set user preferences at registration time
					$_REQUEST['iTRACKER'] = 1;	// only one tracker plugin on registration
				}
				$userWizardDetailsFieldIds = '';
				if ($prefs['feature_userWizardDifferentUsersFieldIds'] != 'y' or empty($prefs['feature_userWizardUsersFieldIds'])) {
					$userWizardDetailsFieldIds = $re['registrationUsersFieldIds'];
				} elseif ($prefs['feature_userWizardDifferentUsersFieldIds'] == 'y' and !empty($prefs['feature_userWizardUsersFieldIds'])) {
					$userWizardDetailsFieldIds = $prefs['feature_userWizardUsersFieldIds'];
				} 
				
				if (!is_array($userWizardDetailsFieldIds)) {
					$userWizardDetailsFieldIds = explode(':', $userWizardDetailsFieldIds);
				}
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $userWizardDetailsFieldIds, 'showdesc' => 'n', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => 'Save_User_Details', 'registration' => 'n', 'userField' => $re['usersFieldId']));
				$tr = TikiLib::lib('trk')->get_tracker($re['usersTrackerId']);
		
		
				$utid = $userlib->get_tracker_usergroup($user);
		
				if (isset($utid['usersTrackerId'])) {
					$_REQUEST['trackerId'] = $utid['usersTrackerId'];
					$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'], $utid['usersFieldId'], $user);
				}
		
				$definition = Tracker_Definition::get($_REQUEST['trackerId']);
				$xfields = array('data' => $definition->getFields());
				$smarty->assign('tracker_is_multilingual', $prefs['feature_multilingual'] == 'y' && $definition->getLanguageField());
				
				$smarty->assign('itemId', $_REQUEST["itemId"]);
			
				if ($prefs['feature_actionlog'] == 'y') {
					$logslib = TikiLib::lib('logs');
					$logslib->add_action('Viewed', $_REQUEST['itemId'], 'trackeritem');
				}
	
				if (!empty($tr['description'])) {
					$smarty->assign('userTrackerHasDescription', true);
				}
				if (isset($_REQUEST['error']) && $_REQUEST['error'] === 'y') {
					$result = null;
					$smarty->assign('msg', '');
					$smarty->assign('showmsg', 'n');
		
				} else if (isset($_REQUEST['name'])) {		// user tracker saved ok
					# Provide some info to the user about 'success' (to be done)
				}
				$smarty->assign('userTrackerData', $userTrackerData);
			}
			
		}
		
		return $showPage;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/user_tracker.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		global $user, $prefs;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$registrationlib = TikiLib::lib('registration');
		$trklib = TikiLib::lib('trk');

		// Run the parent first
		parent::onContinue($homepageUrl);
		
		//get custom fields
		$customfields = $registrationlib->get_customfields();
		
		$needs_validation_js = true;
		if ($registrationlib->merged_prefs['userTracker'] == 'y') {
			$chosenGroup = 'Registered';
			$re = $userlib->get_group_info($chosenGroup);
			if (!empty($re['usersTrackerId']) && ( (!empty($re['registrationUsersFieldIds']) && $prefs['feature_userWizardDifferentUsersFieldIds'] != 'y') or ($prefs['feature_userWizardDifferentUsersFieldIds'] == 'y' && !empty($prefs['feature_userWizardUsersFieldIds'])) ) ) {
				$needs_validation_js = false;
				include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
				if (isset($_REQUEST['name'])) {
					$user = $_REQUEST['name'];	// so that one can set user preferences at registration time
					$_REQUEST['iTRACKER'] = 1;	// only one tracker plugin on registration
				}
				$userWizardDetailsFieldIds = '';
				if ($prefs['feature_userWizardDifferentUsersFieldIds'] != 'y' or empty($prefs['feature_userWizardUsersFieldIds'])) {
					$userWizardDetailsFieldIds = $re['registrationUsersFieldIds'];
				} elseif ($prefs['feature_userWizardDifferentUsersFieldIds'] == 'y' and !empty($prefs['feature_userWizardUsersFieldIds'])) {
					$userWizardDetailsFieldIds = $prefs['feature_userWizardUsersFieldIds'];
				} 
				
				if (!is_array($userWizardDetailsFieldIds)) {
					$userWizardDetailsFieldIds = explode(':', $userWizardDetailsFieldIds);
				}
				$userTrackerData = wikiplugin_tracker('', array('trackerId' => $re['usersTrackerId'], 'fields' => $userWizardDetailsFieldIds, 'showdesc' => 'n', 'showmandatory' => 'y', 'embedded' => 'n', 'action' => 'Save_User_Details', 'registration' => 'n', 'userField' => $re['usersFieldId']));
				$tr = TikiLib::lib('trk')->get_tracker($re['usersTrackerId']);
		
				$utid = $userlib->get_tracker_usergroup($user);

				if (isset($utid['usersTrackerId'])) {
					$_REQUEST['trackerId'] = $utid['usersTrackerId'];
					$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'], $utid['usersFieldId'], $user);
				}
		
				$definition = Tracker_Definition::get($_REQUEST['trackerId']);
				$xfields = array('data' => $definition->getFields());
				
			}
		}
	}

}
