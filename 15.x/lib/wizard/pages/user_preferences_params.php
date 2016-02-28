<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');
$userprefslib = TikiLib::lib('userprefs');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class UserWizardPreferencesParams extends Wizard 
{
	function pageTitle ()
    {
        return tra('User Preferences:') . ' ' . tra('Settings');
    }
    
	function isEditable ()
	{
		return true;
	}

	function isVisible ()
	{
		global	$prefs;
		return $prefs['feature_userPreferences'] === 'y';
	}

	function onSetupPage ($homepageUrl) 
	{
		global	$user, $prefs, $tiki_p_messages;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = false;
		
		// Show if option is selected
		if ($prefs['feature_userPreferences'] === 'y') {
			$showPage = true;
		}
		
		if (!$showPage) {
			return false;
		}

		$userwatch = $user;

		$smarty->assign('userwatch', $userwatch);
		$smarty->assign('show_mouseover_user_info', isset($prefs['show_mouseover_user_info']) ? $prefs['show_mouseover_user_info'] : $prefs['feature_community_mouseover']);

		$mailCharsets = array('utf-8', 'iso-8859-1');
		$smarty->assign_by_ref('mailCharsets', $mailCharsets);

		$mytiki_pages = $tikilib->get_user_preference($userwatch, 'mytiki_pages', 'y');
		$smarty->assign('mytiki_pages', $mytiki_pages);
		$mytiki_blogs = $tikilib->get_user_preference($userwatch, 'mytiki_blogs', 'y');
		$smarty->assign('mytiki_blogs', $mytiki_blogs);
		$mytiki_gals = $tikilib->get_user_preference($userwatch, 'mytiki_gals', 'y');
		$smarty->assign('mytiki_gals', $mytiki_gals);
		$mytiki_items = $tikilib->get_user_preference($userwatch, 'mytiki_items', 'y');
		$smarty->assign('mytiki_items', $mytiki_items);
		$mytiki_msgs = $tikilib->get_user_preference($userwatch, 'mytiki_msgs', 'y');
		$smarty->assign('mytiki_msgs', $mytiki_msgs);
		$mytiki_tasks = $tikilib->get_user_preference($userwatch, 'mytiki_tasks', 'y');
		$smarty->assign('mytiki_tasks', $mytiki_tasks);
		$mylevel = $tikilib->get_user_preference($userwatch, 'mylevel', '1');
		$smarty->assign('mylevel', $mylevel);
		$allowMsgs = $tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
		$smarty->assign('allowMsgs', $allowMsgs);
		$minPrio = $tikilib->get_user_preference($userwatch, 'minPrio', 3);
		$smarty->assign('minPrio', $minPrio);
		$theme = $tikilib->get_user_preference($userwatch, 'theme', '');
		$smarty->assign('theme', $theme);
		$language = $tikilib->get_user_preference($userwatch, 'language', $prefs['language']);
		$smarty->assign('language', $language);
			

		$email_isPublic = $tikilib->get_user_preference($userwatch, 'email is public', 'n');
		if (isset($user_preferences[$userwatch]['email is public'])) {
			$user_preferences[$userwatch]['email_isPublic'] = $user_preferences[$userwatch]['email is public'];
			$email_isPublic = $user_preferences[$userwatch]['email is public'];
		}
		$smarty->assign('email_isPublic', $email_isPublic);
		
		$mailCharset = $tikilib->get_user_preference($userwatch, 'mailCharset', $prefs['default_mail_charset']);
		$smarty->assign('mailCharset', $mailCharset);
		$user_dbl = $tikilib->get_user_preference($userwatch, 'user_dbl', 'n');
		$userbreadCrumb = $tikilib->get_user_preference($userwatch, 'userbreadCrumb', $prefs['site_userbreadCrumb']);
		$smarty->assign('userbreadCrumb', $userbreadCrumb);
		$smarty->assign('user_dbl', $user_dbl);
		$display_12hr_clock = $tikilib->get_user_preference($userwatch, 'display_12hr_clock', 'n');
		$smarty->assign('display_12hr_clock', $display_12hr_clock);
		$userinfo = $userlib->get_user_info($userwatch);
		$smarty->assign_by_ref('userinfo', $userinfo);
		$llist = array();
		$llist = $tikilib->list_styles();
		$smarty->assign_by_ref('styles', $llist);
		$languages = array();
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages();
		$smarty->assign_by_ref('languages', $languages);
		$user_pages = $tikilib->get_user_pages($userwatch, -1);
		$smarty->assign_by_ref('user_pages', $user_pages);
		$bloglib = TikiLib::lib('blog');
		$user_blogs = $bloglib->list_user_blogs($userwatch, false);
		$smarty->assign_by_ref('user_blogs', $user_blogs);
		$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
		$smarty->assign_by_ref('user_galleries', $user_galleries);
		$user_items = TikiLib::lib('trk')->get_user_items($userwatch);
		$smarty->assign_by_ref('user_items', $user_items);
		$scramblingMethods = array("n", "strtr", "unicode", "x", 'y'); // email_isPublic utilizes 'n'
		$smarty->assign_by_ref('scramblingMethods', $scramblingMethods);
		$scramblingEmails = array(
				tra("no"),
				TikiMail::scrambleEmail($userinfo['email'], 'strtr'),
				TikiMail::scrambleEmail($userinfo['email'], 'unicode') . "-" . tra("unicode"),
				TikiMail::scrambleEmail($userinfo['email'], 'x'), $userinfo['email'],
			);
		$smarty->assign_by_ref('scramblingEmails', $scramblingEmails);
		$mailCharsets = array('utf-8', 'iso-8859-1');
		$smarty->assign_by_ref('mailCharsets', $mailCharsets);
		$smarty->assign_by_ref('user_prefs', $user_preferences[$userwatch]);
		$tikilib->get_user_preference($userwatch, 'diff_versions', 'n');
		$usertrackerId = false;
		$useritemId = false;
		if ($prefs['userTracker'] == 'y') {
			$re = $userlib->get_usertracker($userinfo["userId"]);
			if (isset($re['usersTrackerId']) and $re['usersTrackerId']) {
				$trklib = TikiLib::lib('trk');
				$info = $trklib->get_item_id($re['usersTrackerId'], $trklib->get_field_id($re['usersTrackerId'], 'Login'), $userwatch);
				$usertrackerId = $re['usersTrackerId'];
				$useritemId = $info;
			}
		}
		$smarty->assign('usertrackerId', $usertrackerId);
		$smarty->assign('useritemId', $useritemId);
		//// Custom fields
		//foreach ($customfields as $custpref => $prefvalue) {
			//$customfields[$custpref]['value'] = $tikilib->get_user_preference($userwatch, $customfields[$custpref]['prefName'], $customfields[$custpref]['value']);
			//$smarty->assign($customfields[$custpref]['prefName'], $customfields[$custpref]['value']);
		//}
		if ($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
			$unread = $tikilib->user_unread_messages($userwatch);
			$smarty->assign('unread', $unread);
		}
		$smarty->assign('timezones', TikiDate::getTimeZoneList());
		
		// Time zone data for the user
		if ($prefs['users_prefs_display_timezone'] == 'Site') {
			$smarty->assign('warning_site_timezone_set', 'y');
		}

		if (isset($prefs['display_timezone'])) {
			$smarty->assign('display_timezone', $prefs['display_timezone']);
		}
		$smarty->assign('userPageExists', 'n');
		if ($prefs['feature_wiki'] == 'y' and $prefs['feature_wiki_userpage'] == 'y') {
			if ($tikilib->page_exists($prefs['feature_wiki_userpage_prefix'] . $user)) $smarty->assign('userPageExists', 'y');
		}
		$smarty->assign_by_ref('tikifeedback', $tikifeedback);
		
		return true;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/user_preferences_params.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		global $tikilib, $user, $prefs, $tiki_p_admin, $tikidomain;

		$userwatch = $user;
		$headerlib = TikiLib::lib('header');

		// Run the parent first
		parent::onContinue($homepageUrl);
		
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
		$langLib = TikiLib::lib('language');
		if (isset($_REQUEST["language"]) && $langLib->is_valid_language($_REQUEST['language'])) {
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
			$langLib = TikiLib::lib('language');
			$list = array_filter($list, array($langLib, 'is_valid_language'));
			$list = implode(' ', $list);
			$tikilib->set_user_preference($userwatch, 'read_language', $list);
		}
		if (isset($_REQUEST['display_timezone'])) {
			$tikilib->set_user_preference($userwatch, 'display_timezone', $_REQUEST['display_timezone']);
		}

		if (isset($_REQUEST['user_dbl']) && $_REQUEST['user_dbl'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'user_dbl', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'user_dbl', 'n');
		}
		if (isset($_REQUEST['display_12hr_clock']) && $_REQUEST['display_12hr_clock'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'display_12hr_clock', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'display_12hr_clock', 'n');
		}
		if (isset($_REQUEST['diff_versions']) && $_REQUEST['diff_versions'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'diff_versions', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'diff_versions', 'n');
		}
		if ($prefs['feature_community_mouseover'] == 'y') {
			if (isset($_REQUEST['show_mouseover_user_info']) && $_REQUEST['show_mouseover_user_info'] == 'on') {
				$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'y');
			} else {
				$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'n');
			}
		}
		$email_isPublic = isset($_REQUEST['email_isPublic']) ? $_REQUEST['email_isPublic'] : 'n';
		$tikilib->set_user_preference($userwatch, 'email is public', $email_isPublic);
		$tikilib->set_user_preference($userwatch, 'mailCharset', $_REQUEST['mailCharset']);
		//// Custom fields
		//foreach ($customfields as $custpref => $prefvalue) {
			//if (isset($_REQUEST[$customfields[$custpref]['prefName']])) $tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
		//}
	
		if (isset($_REQUEST['location'])) {
			if ($coords = TikiLib::lib('geo')->parse_coordinates($_REQUEST['location'])) {
				$tikilib->set_user_preference($userwatch, 'lat', $coords['lat']);
				$tikilib->set_user_preference($userwatch, 'lon', $coords['lon']);
				if (isset($coords['zoom'])) {
					$tikilib->set_user_preference($userwatch, 'zoom', $coords['zoom']);
				}
			}
		}
	
		//// Custom fields
		//foreach ($customfields as $custpref => $prefvalue) {
			//// print $customfields[$custpref]['prefName'];
			//// print $_REQUEST[$customfields[$custpref]['prefName']];
			//$tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
		//}
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
			TikiLib::lib('user')->interSendUserInfo($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $userwatch);
		}
		


	}
}
