<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_userlist_info()
{
	return [
		'name' => tra('User List'),
		'documentation' => 'PluginUserList',
		'description' => tra('Display a list of registered users'),
		'prefs' => [ 'wikiplugin_userlist' ],
		'body' => tra('Login Filter'),
		'iconname' => 'group',
		'introduced' => 3,
		'params' => [
			'sep' => [
				'required' => false,
				'name' => tra('Separator'),
				'description' => tra('Separator to use between users in the list. Default: comma'),
				'since' => '3.0',
				'default' => ', ',
				'advanced' => true,
			],
			'max' => [
				'required' => false,
				'name' => tra('Maximum'),
				'description' => tra('Result limit'),
				'since' => '3.0',
				'default' => '',
				'filter' => 'digits',
				'advanced' => true,
			],
			'sort' => [
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tra('Set to sort in ascending or descending order'),
				'since' => '3.0',
				'default' => '',
				'filter' => 'alpha',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Ascending'), 'value' => 'asc'],
					['text' => tra('Descending'), 'value' => 'desc'],
				],
			],
			'layout' => [
				'required' => false,
				'name' => tra('Layout'),
				'description' => tra('Set to table to display results in a table'),
				'since' => '3.0',
				'default' => '',
				'filter' => 'alpha',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('List'), 'value' => ''],
					['text' => tra('Table'), 'value' => 'table'],
				],
			],
			'link' => [
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('Make the user names listed links to different types of user information'),
				'since' => '3.0',
				'default' => '',
				'filter' => 'alpha',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('User info'), 'value' => 'userinfo'],
					['text' => tra('User page'), 'value' => 'userpage'],
					['text' => tra('User pref'), 'value' => 'userpref'],
				],
			],
			'realname' => [
				'required' => false,
				'filter' => 'alpha',
				'name' => tra('Real Name'),
				'description' => tra('Display the user\'s real name (when available) instead of login name'),
				'since' => '4.0',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => ''],
				],
			],
			'group' => [
				'required' => false,
				'name' => tra('Group'),
				'description' => tra('Filter on a group'),
				'since' => '5.0',
				'filter' => 'groupname',
				'default' => '',
			],
		],
	];
}

function wikiplugin_userlist($data, $params)
{
	global $prefs, $tiki_p_admin, $tiki_p_admin_users, $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');

	extract($params, EXTR_SKIP);

	if (! isset($sep)) {
		$sep = ', ';
	}
	if (! isset($max)) {
		$numRows = -1;
	} else {
		$numRows = (int) $max;
	}

	$from = '';
	if ($data) {
		$mid = '`login` like ?';
		$findesc = '%' . $data . '%';
		$bindvars = [$findesc];
	} else {
		$mid = '1';
		$bindvars = [];
	}
	$pre = '';
	$post = '';
	if (isset($layout)) {
		if ($layout == 'table') {
			$pre = '<table class=\'sortable\' id=\'' . $tikilib->now . '\'><tr><th>' . tra('users') . '</th></tr><tr><td>';
			$sep = '</td></tr><tr><td>';
			$post = '</td></tr></table>';
		}
	}
	if (isset($group)) {
		$from .= ", users_usergroups uug";
		$mid .= ' and uug.`groupName` = ? and uu.`userId` = uug.`userId`';
		$bindvars[] = $group;
	}
	if (isset($sort)) {
		$sort = strtolower($sort);
		if (($sort == 'asc') || ($sort == 'desc')) {
			$mid .= ' ORDER BY `login` ' . $sort;
		}
	}

		$query = "select `login`, uu.`userId` from `users_users` uu $from where $mid";
	$result = $tikilib->query($query, $bindvars, $numRows);
	$ret = [];

	while ($row = $result->fetchRow()) {
		$res = '';
		if (isset($link)) {
			if ($link == 'userpage') {
				if ($prefs['feature_wiki_userpage'] == 'y') {
					$wikilib = TikiLib::lib('wiki');
					$page = $prefs['feature_wiki_userpage_prefix'] . $row['login'];
					if ($tikilib->page_exists($page)) {
						$res = '<a href="' . $wikilib->sefurl($page) . '" title="' . tra('Page') . '">';
					}
				}
			} elseif (isset($link) && $link == 'userpref') {
				if ($prefs['feature_userPreferences'] == 'y' && ($tiki_p_admin_users == 'y' || $tiki_p_admin == 'y')) {
					$res = '<a href="tiki-user_preferences.php?userId=' . $row['userId'] . '" title="' . tra('Preferences') . '">';
				}
			} elseif (isset($link) && $link == 'userinfo') {
				if ($tiki_p_admin_users == 'y' || $tiki_p_admin == 'y') {
					$res = '<a href="tiki-user_information.php?userId=' . $row['userId'] . '" title="' . tra('User Information') . '">';
				} else {
					$user_information = $tikilib->get_user_preference($row['login'], 'user_information', 'public');
					if ($user_information != 'private' && $row['login'] != $user) {
						$res = '<a href="tiki-user_information.php?userId=' . $row['userId'] . '" title="' . tra('User Information') . '">';
					}
				}
			}
		}
		$displayName = $row['login'];
		if ($params['realname']) {
			$realName = $tikilib->get_user_preference($row['login'], 'realName');

			if ($realName) {
				$displayName = $realName;
			}
		}

		$ret[] = $res . $displayName . ($res ? '</a>' : '');
	}
	return $pre . implode($sep, $ret) . $post;
}
