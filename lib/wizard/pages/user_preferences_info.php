<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


require_once('lib/wizard/wizard.php');
include_once('lib/userprefs/userprefslib.php');
/**
 * Set up the Basic User Information
 */
class UserWizardPreferencesInfo extends Wizard 
{
	function isEditable ()
	{
		return true;
	}

	function onSetupPage ($homepageUrl) 
	{

		global	$smarty, $userlib, $tikilib, $user;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		// Show if option is selected
		if ($prefs['feature_userPreferences'] === 'y') {
			$showPage = true;
		}
		
		if (isset($_REQUEST['userId']) || isset($_REQUEST['view_user'])) {
			if (empty($_REQUEST['view_user'])) $userwatch = $tikilib->get_user_login($_REQUEST['userId']);
			else $userwatch = $_REQUEST['view_user'];
			if ($userwatch != $user) {
				if ($userwatch === false) {
					/*
					$smarty->assign('msg', tra("Unknown user"));
					$smarty->display("error.tpl");
					*/
					//die;
					return false;
				} else {
					$access->check_permission('tiki_p_admin_users');
				}
			}
		} elseif (isset($_REQUEST["view_user"])) {
			if ($_REQUEST["view_user"] != $user) {
				$access->check_permission('tiki_p_admin_users');		// Permission should be checked differently. check_permission will terminate processing, I believe. Arild
				$userwatch = $_REQUEST["view_user"];
				if (!$userlib->user_exists($userwatch)) {
					/*
					$smarty->assign('msg', tra("Unknown user"));
					$smarty->display("error.tpl");
					*/
					//die;
					return false;
				}
			} else {
				$userwatch = $user;
			}
		} else {
			$userwatch = $user;
		}
		$userinfo = $userlib->get_user_info($userwatch);
		$smarty->assign_by_ref('userinfo', $userinfo);

		$smarty->assign('userwatch', $userwatch);
		$realName = $tikilib->get_user_preference($userwatch, 'realName', '');
		$smarty->assign('realName', $realName);
		if ($prefs['feature_community_gender'] == 'y') {
			$gender = $tikilib->get_user_preference($userwatch, 'gender', 'Hidden');
			$smarty->assign('gender', $gender);
		}
		$flags = $tikilib->get_flags();
		$smarty->assign_by_ref('flags', $flags);
		$country = $tikilib->get_user_preference($userwatch, 'country', 'Other');
		$smarty->assign('country', $country);
		$userbreadCrumb = $tikilib->get_user_preference($userwatch, 'userbreadCrumb', $prefs['site_userbreadCrumb']);
		$smarty->assign('userbreadCrumb', $userbreadCrumb);
		$homePage = $tikilib->get_user_preference($userwatch, 'homePage', '');
		$smarty->assign('homePage', $homePage);
		$avatar = $tikilib->get_user_avatar($userwatch);
		$smarty->assign_by_ref('avatar', $avatar);
		$smarty->assign_by_ref('user_prefs', $user_preferences[$userwatch]);
		$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
		$smarty->assign_by_ref('user_information', $user_information);
		$usertrackerId = false;
		$useritemId = false;
		if ($prefs['userTracker'] == 'y') {
			$re = $userlib->get_usertracker($userinfo["userId"]);
			if (isset($re['usersTrackerId']) and $re['usersTrackerId']) {
				include_once ('lib/trackers/trackerlib.php');
				$info = $trklib->get_item_id($re['usersTrackerId'], $trklib->get_field_id($re['usersTrackerId'], 'Login'), $userwatch);
				$usertrackerId = $re['usersTrackerId'];
				$useritemId = $info;
			}
		}
		$smarty->assign('usertrackerId', $usertrackerId);
		$smarty->assign('useritemId', $useritemId);

		// Assign the page template
		$wizardTemplate = 'wizard/user_preferences_info.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;		
	}

	function onContinue ($homepageUrl) 
	{
		global $tikilib, $user;
		
		$userwatch = $user;
		
		// Run the parent first
		parent::onContinue($homepageUrl);
		
		if (isset($_REQUEST["realName"]) && ($prefs['auth_ldap_nameattr'] == '' || $prefs['auth_method'] != 'ldap')) {
	     $tikilib->set_user_preference($userwatch, 'realName', $_REQUEST["realName"]);
	     if ( $prefs['user_show_realnames'] == 'y' ) {
	       global $cachelib;
	       $cachelib->invalidate('userlink.'.$user.'0');
	     }
		}
		if ($prefs['feature_community_gender'] == 'y') {
			if (isset($_REQUEST["gender"])) $tikilib->set_user_preference($userwatch, 'gender', $_REQUEST["gender"]);
		}
		$tikilib->set_user_preference($userwatch, 'country', $_REQUEST["country"]);
		if (isset($_REQUEST["homePage"])) $tikilib->set_user_preference($userwatch, 'homePage', $_REQUEST["homePage"]);
		$tikilib->set_user_preference($userwatch, 'user_information', $_REQUEST['user_information']);
	
	}
}
