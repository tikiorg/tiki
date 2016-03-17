<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_memberlist_info()
{
	return array(
		'name' => tra('Member List'),
		'documentation' => 'PluginMemberList',
		'description' => tra('List and allow editing of group members'),
		'prefs' => array( 'wikiplugin_memberlist' ),
		'filter' => 'wikicontent',
		'iconname' => 'group',
		'introduced' => 4,
		'params' => array(
			'groups' => array(
				'required' => true,
				'name' => tra('Groups'),
				'description' => tr('List of groups to handle through the interface (use %0*%1 for all). Semi-colon
					separated.', '<code>', '</code>'),
				'since' => '4.0',
				'separator' => ':',
				'filter' => 'groupname',
				'default' => '',
			),
			'showDescriptions' => array(
				'required' => false,
				'name' => tra('Descriptions'),
				'description' => tra('Display group descriptions below list name.'),
				'since' => '8.0',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
				'default' => 'n',
			),
			'displayMode' => array(
				'required' => false,
				'name' => tra('Display Mode'),
				'description' => tra('How to show the member lists.'),
				'since' => '8.0',
				'filter' => 'word',
				'options' => array(
					array('text' => 'Default (plain)', 'value' => ''),
					array('text' => tra('Tabs'), 'value' => 'tabs'),
					// more soon...
				),
				'default' => '',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum'),
				'description' => tr('Maximum number of users to list in each group (default %0100%1).', '<code>', '</code>'),
				'since' => '8.0',
				'default' => 100,
				'filter' => 'digits',
			),
			'membersOnly' => array(
				'required' => false,
				'name' => tra('Members Only'),
				'description' => tr('Show only groups containing a certain user. Enter %0%user%%1 to show groups for
					the current logged-in user.', '<code>', '</code>'),
				'since' => '8.0',
				'default' => '',
				'filter' => 'username',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort mode'),
				'description' => tra('Sort mode for member listing.'),
				'since' => '8.0',
				'default' => 'login_asc',
				'filter' => 'text',
			),
			'readOnly' => array(
				'required' => false,
				'name' => tra('Read only'),
				'description' => tra('Read only mode. All ability to modify membership is hidden.'),
				'since' => '8.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'defaultGroup' => array(
				'required' => false,
				'name' => tra('Set as Default Group'),
				'description' => tra('Adds possibility to set group as default group. This automatically adds the user
					to the group. "Required" option will not propose simple addition in group.'),
				'since' => '9.2',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Not suggested'), 'value' => 'n'),
					array('text' => tra('Suggested'), 'value' => 'both'),
					array('text' => tra('Required'), 'value' => 'y')
				),
			),
			'including' => array(
				'required' => false,
				'name' => tra('Including Group'),
				'description' => tra('Only groups including the group that you specify will be listed'),
				'since' => '8.0',
				'filter' => 'groupname',
				'default' => '',
			),
			'email_to_added_user' => array(
				'required' => false,
				'name' => tra('Notify Added User'),
				'description' => tra(''),
				'since' => '14.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'email_to_removed_user' => array(
				'required' => false,
				'name' => tra('Notify Removed User'),
				'description' => tra('Email notification to removed user'),
				'since' => '14.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'addon_groups_approval_buttons' => array(
				'required' => false,
				'name' => tra('Need Approval'),
				'description' => tra('Add approve/reject user buttons for private addon groups'),
				'since' => '14.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
		),
	);
}

function wikiplugin_memberlist( $data, $params )
{
	global $prefs, $user, $page;
	$mail = false;
	$tikilib = TikiLib::lib('tiki');
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	static $execution = 0;
	$exec_key = 'memberlist-execution-' . ++ $execution;

	if ( ! isset( $params['groups'] ) ) {
		return "^Missing group list^";
	}

	$groups = $params['groups'];

	$defaults = array();
	$plugininfo = wikiplugin_memberlist_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	if ($prefs['feature_user_watches'] == 'y') {
		if (!empty($user)) {
			if ( isset($_REQUEST['watch'] ) ) {
				$tikilib->add_user_watch($user, 'user_joins_group', $_REQUEST['watch'], 'group');
			} else if ( isset($_REQUEST['unwatch'] ) ) {
				$tikilib->remove_user_watch($user, 'user_joins_group', $_REQUEST['unwatch'], 'group');
			}
		}
	}

	if (count($groups) === 1 && $groups[0] === '*') {	// all available
		$groups = $userlib->list_all_groups();
	}

	if (!empty($params['membersOnly'])) {
		if ($params['membersOnly'] === '%user%') {
			$params['membersOnly'] = $GLOBALS['user'];
		}
		$usergroups = $userlib->get_user_groups($params['membersOnly']);
		$in_group = array();
		foreach ($groups as $group) {
			if (in_array($group, $usergroups) && $group != 'Anonymous') {
				$in_group[] = $group;
			}
		}
		$groups = $in_group;
		unset($in_group);
	}

	if (!empty($params['including'])) {
		$includinggroups = $userlib->get_including_groups($params['including']);
		$in_group = array();
		foreach ($groups as $group) {
			if (in_array($group, $includinggroups)) {
				$in_group[] = $group;
			}
		}
		$groups = $in_group;
		unset($in_group);
	}

	if ($params['addon_groups_approval_buttons'] == 'y') {
		$pageInfo = $tikilib->get_page_info($page);
		$pageLang = $pageInfo['lang'];
		$api = new TikiAddons_Api_Group;
		$group_base = $api->getOrganicGroupBaseName($params['groups'][0]);
		$smarty->assign('mail_group', $group_base);
		$itemId = $api->getItemIdFromToken($params['groups'][0]);
		$smarty->assign('mail_organicgroup_id', $itemId);
		$userid = "user" . $userlib->get_user_id($user);
		$smarty->assign('mail_userid', $userid);
		$smarty->assign('mail_url', $api->getGroupHomePage($params['groups'][0]) . '?itemId=' . $itemId);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix(true) . dirname($foo["path"]);
		if (substr($machine, -1) == '/' ) { $machine = substr($machine, 0, -1);}
		$smarty->assign('mail_machine', $machine);
		$file_wel = $smarty->fetchLang($pageLang, "mail/admin_approval_user_joins_group_notification.tpl");
		$file_rej = $smarty->fetchLang($pageLang, "mail/admin_rejection_user_group_notification.tpl");
		$smarty->assign('welcome_content', $file_wel);
		$smarty->assign('reject_content', $file_rej);
		$smarty->assign('Need_app', $exec_key);
	} else {
		$smarty->assign('Need_app', '');
	}

	Perms::bulk(array( 'type' => 'group' ), 'object', $groups);

	if ($params['readOnly'] == 'y') {
		$readOnly = true;
	} else {
		$readOnly = false;
	}
	$validGroups = wikiplugin_memberlist_get_group_details($groups, $params['max'], $params['sort_mode'], $readOnly);

	if ( isset($_POST[$exec_key]) ) {
		if ( isset( $_POST['join'] ) ) {
			wikiplugin_memberlist_join($validGroups, $_POST['join']);
		}
		if ( isset( $_POST['leave'] ) ) {
			wikiplugin_memberlist_leave($validGroups, $_POST['leave']);
		}
		if ( isset( $_POST['remove'] ) ) {
			if (isset($params['email_to_removed_user']) && $params['email_to_removed_user'] == 'y' || isset($_POST['text_area'])) {
				$mail = 'true';
			}
			wikiplugin_memberlist_remove($validGroups, $_POST['remove'], $mail, $params);
		}
		if ( isset( $_POST['add'] ) ) {
			$addit = array();
			foreach ($_POST['add'] as $key => $value) {
				if ($params['addon_groups_approval_buttons'] == 'y') {
					$basegroup = $api->getOrganicGroupBaseToken($key);
					$valgroup[] = $basegroup;
					$addit['add'][$basegroup] = $value;
					$removeit['add'][$api->getOrganicGroupPendingToken($key)][] = $value;
				} else {
					$valgroup[] = $key;
					$addit['add'][$key] = $value;
				}
			}
			if (isset($params['email_to_added_user']) && $params['email_to_added_user'] == 'y' || isset($_POST['text_area'])) {
				$mail = 'true';
			}
			$validrem = wikiplugin_memberlist_get_group_details($valgroup, $params['max'], $params['sort_mode'], $readOnly);
			if ($params['addon_groups_approval_buttons'] == 'y' && isset($removeit['add'])) {
				wikiplugin_memberlist_remove($validGroups, $removeit['add'], 'false', $params);
			}
			wikiplugin_memberlist_add($validrem, $addit['add'], '', $mail, $params);
		}
		if ( isset( $_POST['defgroup'] ) ) {
			wikiplugin_memberlist_add($validGroups, $_POST['defgroup'], true);
		}
		header('Location: ' . $_SERVER['REQUEST_URI']);
		exit;
	}

	if ( isset( $_REQUEST['transition'], $_REQUEST['member'] ) ) {
		if ( $prefs['feature_group_transition'] == 'y' ) {
			require_once 'lib/transitionlib.php';
			$transitionlib = new TransitionLib('group');
			$transitionlib->triggerTransition($_REQUEST['transition'], $_REQUEST['member']);

			$url = $_SERVER['REQUEST_URI'];
			$url = str_replace('transition=', 'x=', $url);
			$url = str_replace('member=', 'x=', $url);
			header('Location: ' . $url);
			exit;
		}
	}

	$canApply = false;
	foreach ( $validGroups as $group ) {
		if ( $group['can_add'] || $group['can_remove'] || $group['can_join'] || $group['can_leave'] ) {
			$canApply = true;
			break;
		}
	}

	if ($params['showDescriptions'] === 'y') {
		foreach ( $validGroups as $name => &$group ) {
			$group['info'] = $userlib->get_group_info($name);
		}
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('execution_key', $exec_key);
	$smarty->assign('can_apply', $canApply);
	$smarty->assign('defaultGroup', $params['defaultGroup']);
	$smarty->assign('memberlist_groups', $validGroups);
	$smarty->assign('displayMode', $params['displayMode']);

	// seems conditionally adding tabs in the tpl doesn't work (unclosed {tabset} errors etc) - a Smarty 3 change?
	if (empty($params['displayMode']) && $prefs['feature_tabs'] === 'y') {
		$oldTabs = $prefs['feature_tabs'];
		$prefs['feature_tabs'] = 'n';
		// css workarounds for when in non tabs mode
		TikiLib::lib('header')->add_css(
			'.memberlist > fieldset { border: none; margin:  0; padding:  0; }
			.memberlist > fieldset > legend { display: none; }'
		);
	}
	$out = '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_memberlist.tpl') . '~/np~';

	if (empty($params['displayMode']) && !empty($oldTabs)) {
		$prefs['feature_tabs'] = $oldTabs;
	}
	return $out;
}

function wikiplugin_memberlist_get_members( $groupName, $maxRecords = -1, $sort_mode = 'login_asc')
{
	$userlib = TikiLib::lib('user');

	$raw = $userlib->get_users(0, $maxRecords, $sort_mode, '', '', false, $groupName);
	$users = array();

	if (isset($raw['data'])) {
		foreach ( $raw['data'] as $user ) {
			$users[] = $user['login'];
		}
	}

	return $users;
}

function wikiplugin_memberlist_get_group_details( $groups, $maxRecords = -1, $sort_mode = 'login_asc', $readOnly = false )
{
	global $user, $prefs;
	$userlib = TikiLib::lib('user');
	$validGroups = array();
	foreach ( $groups as $groupName ) {
		if ( ! $userlib->group_exists($groupName) ) {
			continue;
		}

		$perms = Perms::get(array( 'type' => 'group', 'object' => $groupName ));

		if ( $perms->group_view ) {
			$isMember = in_array($groupName, $perms->getGroups());

			$validGroups[$groupName] = array(
				'can_join' => $perms->group_join && ! $isMember && $user && ! $readOnly,
				'can_leave' => $perms->group_join && $isMember && $user && ! $readOnly,
				'can_add' => $perms->group_add_member && ! $readOnly,
				'can_remove' => $perms->group_remove_member && ! $readOnly,
				'is_member' => $isMember,
			);

			if ( $perms->group_view_members ) {
				$validGroups[$groupName]['members'] = wikiplugin_memberlist_get_members($groupName, $maxRecords, $sort_mode);

				if ( $prefs['feature_group_transition'] == 'y' ) {
					require_once 'lib/transitionlib.php';
					$transitionlib = new TransitionLib('group');
					$validGroups[$groupName]['transitions'] = array();
					foreach ( $validGroups[$groupName]['members'] as $username ) {
						$validGroups[$groupName]['transitions'][$username] = $transitionlib->getAvailableTransitionsFromState($groupName, $username);
					}
				}

				if (!empty($user)) {
					 $validGroups[$groupName]['isWatching'] = TikiLib::lib('tiki')->user_watches($user, 'user_joins_group', $groupName, 'group') > 0;
				} else {
					 $validGroups[$groupName]['isWatching'] = false;
				}
			}
		}
	}

	return $validGroups;
}

function wikiplugin_memberlist_join( $groups, $joins )
{
	global $user;
	$userlib = TikiLib::lib('user');
	foreach ( $joins as $group ) {
		if ( isset( $groups[$group] ) ) {
			if ( $groups[$group]['can_join'] ) {
				$userlib->assign_user_to_group($user, $group);
			}
		}
	}
}

function wikiplugin_memberlist_leave( $groups, $leaves )
{
	global $user;
	$userlib = TikiLib::lib('user');
	foreach ( $leaves as $group ) {
		if ( isset( $groups[$group] ) ) {
			if ( $groups[$group]['can_leave'] ) {
				$userlib->remove_user_from_group($user, $group);
			}
		}
	}
}

function wikiplugin_memberlist_add( $groups, $adds, $asdefault=false, $mail=false, $params=array())
{
	$userlib = TikiLib::lib('user');

	foreach ( $adds as $group => $members ) {
		if ( isset( $groups[$group] ) ) {
			if ( $groups[$group]['can_add'] ) {
				$members = explode(',', $members);
				$members = array_map('trim', $members);
				$members = array_filter($members);

				foreach ( $members as $name ) {
					if ( $userlib->user_exists($name) ) {
						if ( $asdefault == true ) {
							$userlib->set_default_group($name, $group);
						} else {
							$userlib->assign_user_to_group($name, $group);
							if ($mail == 'true') {
								$added_user[$name] = $_SESSION['u_info']['login'];
								$par_data['gname'] = $group;
								$par_data['app_data'] = isset($_POST['text_area']) ? $_POST['text_area'] : '';
							}
						}
					}
				}
				if ($params['addon_groups_approval_buttons'] == 'y') {
					$subject = "admin_approval_user_joins_group_notification_subject.tpl";
					$body = "admin_approval_user_joins_group_notification.tpl";
				} else {
					$subject = "admin_add_user_joins_group_notification_subject.tpl";
					$body = "admin_add_user_joins_group_notification.tpl";
				}
				if(!empty($added_user) && isset($par_data)) {
					require_once ("lib/notifications/notificationemaillib.php");
					sendEmailNotification($added_user, 'add_rem_mail', $subject, $par_data, $body);
				}
			}
		}
	}
}

function wikiplugin_memberlist_remove( $groups, $removes, $mail=false, $params=array())
{
	$userlib = TikiLib::lib('user');

	foreach ( $removes as $group=> $members ) {
		if ( isset( $groups[$group] ) ) {
			if ( $groups[$group]['can_remove'] ) {
				foreach ( $members as $name ) {
					$userlib->remove_user_from_group($name, $group);
					if($mail == 'true') {
						$removed_user[$name] = $_SESSION['u_info']['login'];
						$par_data['gname'] = $group;
						$par_data['app_data'] = isset($_POST['text_area']) ? $_POST['text_area'] : '';
					}
				}
				if ($params['addon_groups_approval_buttons'] == 'y') {
					$subject = "admin_rejection_user_group_notification_subject.tpl";
					$body = "admin_rejection_user_group_notification.tpl";
				} else {
					$subject = "admin_remove_user_group_notification_subject.tpl";
					$body = "admin_remove_user_group_notification.tpl";
				}
				if (!empty($removed_user) && isset($par_data)) {
					require_once ("lib/notifications/notificationemaillib.php");
					sendEmailNotification($removed_user, 'add_rem_mail', $subject, $par_data, $body);
				}
			}
		}
	}
}
