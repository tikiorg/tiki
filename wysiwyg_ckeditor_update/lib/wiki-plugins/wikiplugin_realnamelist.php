<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Displays a list of users by their realName
// Use:
// {REALNAMELIST(sep=>", ",max=>10,sort=>asc|desc,layout=>table)}groupname{REALNAMELIST}
//
// If no pattern is given returns all users, else all that belong to 'groupname' or groupnames
// 2009-06-27: omstefanov: written on base model of plugin USERLIST with group selection possibilities from USERCOUNT
//                         For all users selected who have no realName login names are returned, but in italics.
//                         All these sort before users who have a realName.
// 2009-06-30: omstefanov: added option to surpress users with login values 'admin' and/or containing 'test'
//                         using 'exclude=test|admin|test-admin|admin-test'.

function wikiplugin_realnamelist_help() {
	return tra("Displays a list of users showing their real name").":<br />~np~{REALNAMELIST(sep=>\"SEPARATOR\",max=>MAXROWS,sort=>asc|desc,layout=>table)}groupname{REALNAMELIST}~/np~";
}

function wikiplugin_realnamelist_info() {
	return array(
		'name' => tra('User List (showing Real Name)'),
		'documentation' => 'PluginRealNameList',
		'description' => tra("Displays a list of registered users showing their Real Names").tra(' (experimental, should be merged with UserList in Tiki5)'),		
		'prefs' => array( 'wikiplugin_realnamelist' ),
		'body' => tra('Login Filter'),
		'params' => array(
			'sep' => array(
				'required' => false,
				'name' => tra('Separator'),
				'description' => tra('String to use between elements of the list.'),
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum'),
				'description' => tra('Result limit.'),
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => 'asc|desc',
			),
			'layout' => array(
				'required' => false,
				'name' => tra('Layout'),
				'description' => 'table',
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => 'userpage|userinfo|userpref',
			),
			'exclude' => array(
				'required' => false,
				'name' => tra('Exclude'),
				'description' => 'test|admin|test-admin|admin-test',
			),
		),
	);
}

function wikiplugin_realnamelist($data, $params) {
	global $tikilib, $userlib, $prefs, $tiki_p_admin, $tiki_p_admin_users;

	extract ($params,EXTR_SKIP);

	if (!isset($sep)) $sep=', ';
	if (!isset($max)) { $numRows = -1; } else { $numRows = (int) $max; }

	if ($data) {
		$mid = 'g.`groupName` like ?';
		$groupjoin = ' LEFT JOIN `users_usergroups` g ON u.`userId` = g.`userId`';
		$findesc = '%' . $data . '%';
		$bindvars=array($findesc);
		$tableheader='users in group(s) containing ';
		$tableheader .= '*'.$data.'*:';
	} else {
		$mid = '1';
		$groupjoin = '';
		$bindvars=array();
		$tableheader='all users';
	}
	if (isset($sort)) {
		$sort=strtolower($sort);
		if (($sort=='asc') || ($sort=='desc')) {
			$mid .= ' ORDER BY `value`, `login` '.$sort;
		}
	}
	if (isset($exclude)) {
		$exclude=strtolower($exclude);
		$exclude_clause='';
		if (($exclude=='test') || ($exclude=='admin')) {
			$exclude_clause= ' u.`login` NOT LIKE \'%'.$exclude.'%\' AND ' ;
			//$exclude_clause= ' `users_users`.`login` NOT LIKE \'%'.$exclude.'%\' AND ' ;
		}
		if (($exclude=='test-admin') || ($exclude=='admin-test')) {
			$exclude_clause= ' u.`login` NOT LIKE \'%admin%\' AND u.`login` NOT LIKE \'%test%\' AND ';
			//$exclude_clause= ' `users_users`.`login` NOT LIKE \'%admin%\' AND `users_users`.`login` NOT LIKE \'%test%\' AND ';
		}
	}
	$pre=''; $post='';
	if (isset($layout)) {
		if ($layout=='table') {
			$pre='<table class=\'sortable\' id=\''.$tikilib->now.'\'><tr><th>'.tra($tableheader).'</th></tr><tr><td>';
			$sep = '</td></tr><tr><td>';
			$post='</td></tr></table>';
		}
	}

	$query = 'SELECT `login` , u.`userId` , `value` FROM `users_users` u'.$groupjoin.' LEFT JOIN `tiki_user_preferences` p ON p.`user` = u.`login` WHERE p.`prefName` = "realName" AND '.$exclude_clause.$mid; 

	$result = $tikilib->query($query, $bindvars, $numRows);
	$ret = array();

	while ($row = $result->fetchRow()) {
		$res = '';
		if (isset($link)) {
			if ($link == 'userpage') {
				if ($prefs['feature_wiki_userpage'] == 'y') {
					global $wikilib; include_once('lib/wiki/wikilib.php');
					$page = $prefs['feature_wiki_userpage_prefix'].$row['login'];
					if ($tikilib->page_exists($page)) {
						$res = '<a href="'.$wikilib->sefurl($page).'" title="'.tra('Page').'">';
					}
				}
			} elseif (isset($link) && $link == 'userpref') {
				if ($prefs['feature_userPreferences'] == 'y' && ($tiki_p_admin_users == 'y' || $tiki_p_admin == 'y')) {
					$res = '<a href="tiki-user_preferences.php?userId='.$row['userId'].'" title="'.tra('Preferences').'">';
				}
			} elseif (isset($link) && $link == 'userinfo') {
				if ($tiki_p_admin_users == 'y' || $tiki_p_admin == 'y') {
					$res = '<a href="tiki-user_information.php?userId='.$row['userId'].'" title="'.tra('User Information').'">';
				} else {
					$user_information = $tikilib->get_user_preference($row['login'], 'user_information', 'public');
					if ($user_information == 'private' && $row['login'] != $user) {
						$res = '<a href="tiki-user_information.php?userId='.$row['userId'].'" title="'.tra('User Information').'">';
					}
				}
			}
		}
		if( $row['value'] != '' ) {
			$row['login'] = $row['value'];
		} else {
			$temp = $row['login'];
			$row['login']= '<i>'.$temp.'</i>';
		}
		$ret[] = $res.$row['login'].($res?'</a>':'');
	}
	return $pre.implode ( $sep, $ret ).$post;
}
