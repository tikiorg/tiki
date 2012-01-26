<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* {user_selector
 *     user = $user
 *     select = 'user_tobe_selected'
 *     group = 'all'
 *     name = 'user'
 *     id = user_selector_XX
 *     size = ''
 *     contact = 'false'
 *     multiple = 'false'
 *     editable = $tiki_p_admin
 *  }
 * 
 * Display a drop down menu of all users or
 * an input box with autocomplete if there are more users
 * than $prefs['user_selector_threshold']
 */
function smarty_function_user_selector($params, &$smarty) {
	global $prefs, $user, $userlib, $headerlib, $tikilib, $tiki_p_admin;
	require_once 'lib/userslib.php';
	
	static $iUserSelector = 0;
	$iUserSelector++;
	
	$defaults = array( 'user' => $user, 'group' => 'all', 'contact'=> 'false','name' => 'user', 'id' => 'user_selector_' . $iUserSelector, 'multiple'=> 'false', 'mustmatch' => 'true', 'style'=> '' ,'editable' => $tiki_p_admin);
	$params = array_merge($defaults, $params);
	if (isset($params['size'])) {
		$sz = ' size="' . $params['size'] . '"';
	} else {
		$sz = '';
	}
	if ($params['editable'] != 'y') {
		$ed = ' disabled="disabled"';
	} else {
		$ed = '';
	}
	
	if ($params['group'] == 'all') {
		$ucant = $tikilib->list_users(0, 0, 'login_asc');
		$ucant = $ucant['cant'];
	} else {
		$ucant = $userlib->count_users($params['group']);
	}
	$ret = '';
	
	if ($prefs['feature_jquery_autocomplete'] == 'y' && ($ucant > $prefs['user_selector_threshold'] or $ucant> $params['user_selector_threshold'])) {
		$ret .= '<input id="' . $params['id'] . '" type="text" name="' . $params['name'] . '" value="' . $params['user'] . '"' . $sz . $ed . ' style="'.$params['style'].'" />';
		$headerlib->add_jq_onready('$("#' . $params['id'] . '").tiki("autocomplete", "'.(($params['contact'] == 'true')?('usersandcontacts'):('username')).'", {mustMatch: '.$params['mustmatch'].', multiple: '.$params['multiple'].' });');
	} else {
		if ($params['group'] == 'all') {
			$usrs = $tikilib->list_users(0, -1, 'login_asc');
			$users = array();
			foreach ($usrs['data'] as $usr) {
				$users[] = $usr['login'];
			}
		} else {
			$users = $userlib->get_group_users($params['group']);
		}
		$ret .= '<select name="' . $params['name'] . '" id="' . $params['id'] . '"' . $sz . $ed . ' style="'.$params['style'].'">';
		foreach($users as $usr) {
			if ($params['editable'] == 'y' || $usr == $params['user']) {
			    if (isset($params['select'])) {
				$ret .= '<option value="' . $usr . '"' . ($usr == $params['select'] ? ' selected="selected"' : '') . ' >' . $usr .'</option>';
				} else {
				$ret .= '<option value="' . $usr . '"' . ($usr == $params['user'] ? ' selected="selected"' : '') . ' >' . $usr .'</option>';
				}
			}
		}
		$ret .= '</select>';
	}
	return $ret;
		
}
