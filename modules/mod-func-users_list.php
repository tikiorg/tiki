<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_users_list_info()
{
	return array(
		'name' => tra('Users List'),
		'description' => tra('Display a list of users with optional extra information from each.'),
		'prefs' => array('feature_wiki'),
		'params' => array(
			'login' => array(
				'name' => tra('Login'),
				'description' => tra('Show the user name.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'y'
			),
			'realName' => array(
				'name' => tra('Real Name'),
				'description' => tra('Show the user real name.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'lastLogin' => array(
				'name' => tra('Last Login'),
				'description' => tra('Show the last login date.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'groups' => array(
				'name' => tra('Groups'),
				'description' => tra('Show the direct and included groups a user belongs to.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'avatar' => array(
				'name' => tra('Profile picture'),
				'description' => tra('Show the user profile picture.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'userPage' => array(
				'name' => tra('User Page'),
				'description' => tra('Show a link to the userPage.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'log' => array(
				'name' => tra('Log'),
				'description' => tra('Show a link to the user logs(feature actionlog must be activated).') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'group' => array(
				'name' => tra('Group'),
				'description' => tra('Show only the users of the group.') . ' ' . tra('Possible values:') . ' ' . tra('Groupname'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'includedGroups' => array(
				'name' => tra('Included Groups'),
				'description' => tra('Show only the users of the group group and of a group including group.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'initial' => array(
				'name' => tra('initial'),
				'description' => tra('Show only the users whose name begins with the letter.') . ' ' . tra('Possible values:') . ' ' . tra('a letter'),
				'filter' => 'word',
				'required' => false,
				'default' => 'n'
			),
			'heading' => array(
				'name' => tra('heading'),
				'description' => tra('Show the table heading.') . ' ' . tra('Possible values:') . ' ' . tra('y|n'),
				'filter' => 'word',
				'required' => false,
				'default' => 'y'
			),
			'sort_mode' => array(
				'name' => tra('Sort Mode'),
				'description' => tra('Sort users in ascending or descending order using these values: ') .
					'login_asc, login_desc, email_asc, email_desc.',
				'filter' => 'word',
				'required' => false,
				'default' => 'login_asc'
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $module_params
 */
function module_users_list($module_params)
{
	global $prefs;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	
	if (isset($module_params['params']['group'])) {
		$group = array($module_params['params']['group']);
		if (isset($module_params['params']['includedGroups']) && $module_params['params']['includedGroups'] == 'y') {
			$group = array_merge($group, $userlib->get_including_groups($group[0]));
		}
	 } else {
		$group = '';
	 }

	if (!isset($module_params['params']['sort_mode'])) {
		$sort_mode = 'login_asc';
	} else {
		$sort_mode = $module_params['params']['sort_mode'];
	}

	$users = $userlib->get_users(0, -1, $sort_mode, '',!empty($module_params['initial'])? $module_params['initial']:'', isset($module_params['groups'])?true: false, $group);
	if (isset($_REQUEST["realName"]) && ($prefs['auth_ldap_nameattr'] == '' || $prefs['auth_method'] != 'ldap')) {
		$tikilib->set_user_preference($userwatch, 'realName', $_REQUEST["realName"]);
		if ( $prefs['user_show_realnames'] == 'y' ) {
			$cachelib = TikiLib::lib('cache');
			$cachelib->invalidate('userlink.'.$user.'0');
		}
	}

	for ($i = 0; $i < $users['cant']; ++$i) {
		$my_user = $users['data'][$i]['user'];
		if (isset($module_params['params']['realName']) && $module_params['params']['realName'] == 'y') {
			$users['data'][$i]['realName'] = $tikilib->get_user_preference($my_user,'realName','');
		}
		if (isset($module_params['params']['avatar']) && $module_params['params']['avatar'] == 'y') {
			$users['data'][$i]['avatar'] = $tikilib->get_user_avatar($my_user);
		}
		if ((isset($module_params['params']['realName']) && $module_params['params']['realName'] == 'y')
			|| (isset($module_params['params']['login']) && $module_params['params']['login'] == 'y')) {
			$users['data'][$i]['info_public'] = $tikilib->get_user_preference($my_user, 'user_information', 'public')!= 'private'?'y':'n';
		}
		if (isset($module_params['params']['userPage']) && $module_params['params']['userPage'] == 'y') {
			global $feature_wiki_userpage;
			if ($prefs['feature_wiki_userpage'] == 'y' or $feature_wiki_userpage == 'y') {
				if (!isset($prefs['feature_wiki_userpage_prefix'])) {//trick compat 1.9, 1.10
					global $feature_wiki_userpage_prefix;
					$pre = $feature_wiki_userpage_prefix;
				} else {
					$pre = $prefs['feature_wiki_userpage_prefix'];
				}
				if ($tikilib->page_exists($pre.$my_user)) {
					$users['data'][$i]['userPage'] = $pre.$my_user;
				}
			}	
		}
	}
	if (isset($module_params['params']['log']) && $module_params['params']['log'] == 'y' && $prefs['feature_actionlog'] != 'y') {
		$module_params['params']['log'] = 'n';
	 }
	$smarty->assign_by_ref('users', $users['data']);
	$smarty->assign_by_ref('module_params_users_list', $module_params['params']);
}
