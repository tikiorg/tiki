<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');
include_once('lib/modules/modlib.php');
include_once('lib/userprefs/scrambleEmail.php');
include_once('lib/userprefs/userprefslib.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class UserWizardPreferencesParams extends Wizard 
{
	function isEditable ()
	{
		return true;
	}

	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $user;

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
					$smarty->assign('msg', tra("Unknown user"));
					$smarty->display("error.tpl");
					//die;
					return false;
				} else {
					$access->check_permission('tiki_p_admin_users');
				}
			}
		} elseif (isset($_REQUEST["view_user"])) {
			if ($_REQUEST["view_user"] != $user) {
				$access->check_permission('tiki_p_admin_users');
				$userwatch = $_REQUEST["view_user"];
				if (!$userlib->user_exists($userwatch)) {
					$smarty->assign('msg', tra("Unknown user"));
					$smarty->display("error.tpl");
					//die;
					return false;
				}
			} else {
				$userwatch = $user;
			}
		} else {
			$userwatch = $user;
		}

		// Custom fields
		include_once ('lib/registration/registrationlib.php');
		$customfields = $registrationlib->get_customfields();
		foreach ($customfields as $i => $c) {
			$customfields[$i]['value'] = $tikilib->get_user_preference($userwatch, $c['prefName']);
		}
		$smarty->assign_by_ref('customfields', $customfields);
		$smarty->assign('userwatch', $userwatch);

		// Assign the page template
		$wizardTemplate = 'wizard/user_preferences_params.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;		
	}

	function onContinue ($homepageUrl) 
	{
		global $tikilib, $user;
		
		// Run the parent first
		parent::onContinue($homepageUrl);
		
		if (isset($_REQUEST['userId']) || isset($_REQUEST['view_user'])) {
			if (empty($_REQUEST['view_user'])) $userwatch = $tikilib->get_user_login($_REQUEST['userId']);
			else $userwatch = $_REQUEST['view_user'];
			if ($userwatch != $user) {
				if ($userwatch === false) {
					$smarty->assign('msg', tra("Unknown user"));
					$smarty->display("error.tpl");
					//die;
					return false;
				} else {
					$access->check_permission('tiki_p_admin_users');
				}
			}
		} elseif (isset($_REQUEST["view_user"])) {
			if ($_REQUEST["view_user"] != $user) {
				$access->check_permission('tiki_p_admin_users');
				$userwatch = $_REQUEST["view_user"];
				if (!$userlib->user_exists($userwatch)) {
					$smarty->assign('msg', tra("Unknown user"));
					$smarty->display("error.tpl");
					//die;
					return false;
				}
			} else {
				$userwatch = $user;
			}
		} else {
			$userwatch = $user;
		}
		
		// Custom fields
		include_once ('lib/registration/registrationlib.php');
		$customfields = $registrationlib->get_customfields();
		foreach ($customfields as $i => $c) {
			$customfields[$i]['value'] = $tikilib->get_user_preference($userwatch, $c['prefName']);
		}
		
		//$foo = parse_url($_SERVER["REQUEST_URI"]);
		//$foo1 = str_replace("tiki-user_preferences", "tiki-editpage", $foo["path"]);
		//$foo2 = str_replace("tiki-user_preferences", "tiki-index", $foo["path"]);
		//$smarty->assign('url_edit', $tikilib->httpPrefix() . $foo1);
		//$smarty->assign('url_visit', $tikilib->httpPrefix() . $foo2);
		//$smarty->assign('show_mouseover_user_info', isset($prefs['show_mouseover_user_info']) ? $prefs['show_mouseover_user_info'] : $prefs['feature_community_mouseover']);
		if ($prefs['feature_userPreferences'] == 'y' && isset($_REQUEST["new_prefs"])) {
			check_ticket('user-prefs');
			// setting preferences
			if ($prefs['change_theme'] == 'y' && empty($group_style)) {
				if (isset($_REQUEST["mystyle"])) {
					if ($user == $userwatch) {
						$t = $tikidomain ? $tikidomain . '/' : '';
						if ($_REQUEST["mystyle"] == "") {
							//If mystyle is empty --> user has selected "Site Default" theme
							$sitestyle = $tikilib->getOne("select `value` from `tiki_preferences` where `name`=?", 'style');
							$headerlib->replace_cssfile('styles/' . $t . $prefs['style'], 'styles/' . $t . $sitestyle, 51);
						} else {
							$headerlib->replace_cssfile('styles/' . $t . $prefs['style'], 'styles/' . $t . $_REQUEST['mystyle'], 51);
						}
					}
					if ($_REQUEST["mystyle"] == "") {
						$tikilib->set_user_preference($userwatch, 'theme', "");
					} else {
						$tikilib->set_user_preference($userwatch, 'theme', $_REQUEST["mystyle"]);
					}
				}
			}
			if (isset($_REQUEST["userbreadCrumb"])) $tikilib->set_user_preference($userwatch, 'userbreadCrumb', $_REQUEST["userbreadCrumb"]);
			if (isset($_REQUEST["language"]) && $tikilib->is_valid_language($_REQUEST['language'])) {
				if ($tiki_p_admin || $prefs['change_language'] == 'y') {
					$tikilib->set_user_preference($userwatch, 'language', $_REQUEST["language"]);
				}
				if ($userwatch == $user) {
					include ('lang/' . $_REQUEST["language"] . '/language.php');
				}
			} else {
				$tikilib->set_user_preference($userwatch, 'language', '');
			}
			if (isset($_REQUEST['read_language'])) {
				$list = array();
				$tok = strtok($_REQUEST['read_language'], ' ');
				while (false !== $tok) {
					$list[] = $tok;
					$tok = strtok(' ');
				}
				$list = array_unique($list);
				$list = array_filter($list, array($tikilib, 'is_valid_language'));
				$list = implode(' ', $list);
				$tikilib->set_user_preference($userwatch, 'read_language', $list);
			}
			if (isset($_REQUEST['display_timezone'])) {
				$tikilib->set_user_preference($userwatch, 'display_timezone', $_REQUEST['display_timezone']);
			}
			$tikilib->set_user_preference($userwatch, 'user_information', $_REQUEST['user_information']);
			if (isset($_REQUEST['user_dbl']) && $_REQUEST['user_dbl'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'user_dbl', 'y');
				$smarty->assign('user_dbl', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'user_dbl', 'n');
				$smarty->assign('user_dbl', 'n');
			}
			if (isset($_REQUEST['display_12hr_clock']) && $_REQUEST['display_12hr_clock'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'display_12hr_clock', 'y');
				$smarty->assign('display_12hr_clock', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'display_12hr_clock', 'n');
				$smarty->assign('display_12hr_clock', 'n');
			}
			if (isset($_REQUEST['diff_versions']) && $_REQUEST['diff_versions'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'diff_versions', 'y');
				$smarty->assign('diff_versions', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'diff_versions', 'n');
				$smarty->assign('diff_versions', 'n');
			}
			if ($prefs['feature_community_mouseover'] == 'y') {
				if (isset($_REQUEST['show_mouseover_user_info']) && $_REQUEST['show_mouseover_user_info'] == 'on') {
					$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'y');
					$smarty->assign('show_mouseover_user_info', 'y');
				} else {
					$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'n');
					$smarty->assign('show_mouseover_user_info', 'n');
				}
			}
			$email_isPublic = isset($_REQUEST['email_isPublic']) ? $_REQUEST['email_isPublic'] : 'n';
			$tikilib->set_user_preference($userwatch, 'email is public', $email_isPublic);
			$tikilib->set_user_preference($userwatch, 'mailCharset', $_REQUEST['mailCharset']);
			// Custom fields
			foreach ($customfields as $custpref => $prefvalue) {
				if (isset($_REQUEST[$customfields[$custpref]['prefName']])) $tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
			}
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
			if (isset($_REQUEST["homePage"])) $tikilib->set_user_preference($userwatch, 'homePage', $_REQUEST["homePage"]);
		
			if (isset($_REQUEST['location'])) {
				if ($coords = TikiLib::lib('geo')->parse_coordinates($_REQUEST['location'])) {
					$tikilib->set_user_preference($userwatch, 'lat', $coords['lat']);
					$tikilib->set_user_preference($userwatch, 'lon', $coords['lon']);
					if (isset($coords['zoom'])) {
						$tikilib->set_user_preference($userwatch, 'zoom', $coords['zoom']);
					}
				}
			}
		
			// Custom fields
			foreach ($customfields as $custpref => $prefvalue) {
				// print $customfields[$custpref]['prefName'];
				// print $_REQUEST[$customfields[$custpref]['prefName']];
				$tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
			}
			$tikilib->set_user_preference($userwatch, 'country', $_REQUEST["country"]);
			if (isset($_REQUEST['mess_maxRecords'])) $tikilib->set_user_preference($userwatch, 'mess_maxRecords', $_REQUEST['mess_maxRecords']);
			if (isset($_REQUEST['mess_archiveAfter'])) $tikilib->set_user_preference($userwatch, 'mess_archiveAfter', $_REQUEST['mess_archiveAfter']);
			if (isset($_REQUEST['mess_sendReadStatus']) && $_REQUEST['mess_sendReadStatus'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'n');
			}
			if (isset($_REQUEST['minPrio'])) $tikilib->set_user_preference($userwatch, 'minPrio', $_REQUEST['minPrio']);
			if ($prefs['allowmsg_is_optional'] == 'y') {
				if (isset($_REQUEST['allowMsgs']) && $_REQUEST['allowMsgs'] == 'on') {
					$tikilib->set_user_preference($userwatch, 'allowMsgs', 'y');
				} else {
					$tikilib->set_user_preference($userwatch, 'allowMsgs', 'n');
				}
			}
			if (isset($_REQUEST['mytiki_pages']) && $_REQUEST['mytiki_pages'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_pages', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_pages', 'n');
			}
			if (isset($_REQUEST['mytiki_blogs']) && $_REQUEST['mytiki_blogs'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_blogs', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_blogs', 'n');
			}
			if (isset($_REQUEST['mytiki_gals']) && $_REQUEST['mytiki_gals'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_gals', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_gals', 'n');
			}
			if (isset($_REQUEST['mytiki_msgs']) && $_REQUEST['mytiki_msgs'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_msgs', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_msgs', 'n');
			}
			if (isset($_REQUEST['mytiki_tasks']) && $_REQUEST['mytiki_tasks'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_tasks', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_tasks', 'n');
			}
			if (isset($_REQUEST['mytiki_forum_topics']) && $_REQUEST['mytiki_forum_topics'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_forum_topics', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_forum_topics', 'n');
			}
			if (isset($_REQUEST['mytiki_forum_replies']) && $_REQUEST['mytiki_forum_replies'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_forum_replies', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_forum_replies', 'n');
			}
			if (isset($_REQUEST['mytiki_items']) && $_REQUEST['mytiki_items'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_items', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_items', 'n');
			}
			if (isset($_REQUEST['mytiki_articles']) && $_REQUEST['mytiki_articles'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'mytiki_articles', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'mytiki_articles', 'n');
			}
			if (isset($_REQUEST['tasks_maxRecords'])) $tikilib->set_user_preference($userwatch, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);
			if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']) && $prefs['feature_intertiki_import_preferences'] == 'y') { //send to the master
				$userlib->interSendUserInfo($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $userwatch);
			}
		
			TikiLib::events()->trigger(
				'tiki.user.update', array(
					'type' => 'user',
					'object' => $userwatch,
					'user' => $GLOBALS['user'],
				)
			);
		}
	}
}
