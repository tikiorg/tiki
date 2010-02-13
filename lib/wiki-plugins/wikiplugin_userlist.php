<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Displays a list of users
// Use:
// {USERLIST(sep=>", ",max=>10,sort=>asc|desc,layout=>table)}substring{USERLIST}
//
// If no pattern is given returns all users or all that contain 'substring'

function wikiplugin_userlist_help() {
        return tra("Displays a list of registered users").":<br />~np~{USERLIST(sep=>\"SEPARATOR\",max=>MAXROWS,sort=>asc|desc,layout=>table)}substring{USERLIST}~/np~";
}

function wikiplugin_userlist_info() {
	return array(
		'name' => tra('User List'),
		'documentation' => 'PluginUserList',
		'description' => tra('Displays a list of registered users'),
		'prefs' => array( 'wikiplugin_userlist' ),
		'body' => tra('Login Filter'),
		'icon' => 'pics/icons/group_go.png',
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
			'realname' => array(
				'required' => false,
				'filter' => 'alpha',
				'name' => tra('Real Name'),
				'description' => tra('Display the user\'s real name instead of login name.'),
			),
			'group' => array(
				'required' => false,
				'name' => tra('Group'),
				'description' => tra('Filter on the group'),
				'filter' => 'groupname',
			),
		),
	);
}

function wikiplugin_userlist($data, $params) {
    global $tikilib, $userlib, $prefs, $tiki_p_admin, $tiki_p_admin_users;

    extract ($params,EXTR_SKIP);

    if (!isset($sep)) $sep=', ';
    if (!isset($max)) { $numRows = -1; } else { $numRows = (int) $max; }

	$from = '';
    if ($data) {
        $mid = '`login` like ?';
        $findesc = '%' . $data . '%';
	 $bindvars=array($findesc);
    } else {
        $mid = '1';
        $bindvars=array();
    }
    if (isset($sort)) {
    	$sort=strtolower($sort);
        if (($sort=='asc') || ($sort=='desc')) {
            $mid .= ' ORDER BY `login` '.$sort;
        }
    }
    $pre=''; $post='';
    if (isset($layout)) {
        if ($layout=='table') {
        	$pre='<table class=\'sortable\' id=\''.$tikilib->now.'\'><tr><th>'.tra('users').'</th></tr><tr><td>';
        	$sep = '</td></tr><tr><td>';
        	$post='</td></tr></table>';
        }
    }
	if (isset($group)) {
		$from .= ", users_usergroups uug";
		$mid .= ' and uug.`groupName` = ? and uu.`userId` = uug.`userId`';
		$bindvars[] = $group;
	}
    
    $query = "select `login`, uu.`userId` from `users_users` uu $from where $mid";
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
		$displayName = $row['login'];
		if( $params['realname'] ) {
			$realName = $tikilib->get_user_preference( $row['login'], 'realName' );
			
			if( $realName ) {
				$displayName = $realName;
			}
		}

        $ret[] = $res.$displayName.($res?'</a>':'');
    }
    return $pre.implode ( $sep, $ret ).$post;
}
