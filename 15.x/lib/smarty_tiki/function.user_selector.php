<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* {user_selector
 *     user = $user
 *     select = 'user_tobe_selected'
 *     group = 'all'
 *     groupIds = ''
 *     name = 'user'
 *     id = user_selector_XX
 *     size = ''
 *     contact = 'false'
 *     multiple = 'false'
 *     editable = $tiki_p_admin
 * 	   allowNone = 'n'
 *     realnames = 'y'
 *  }
 *
 * Display a drop down menu of all users or
 * an input box with autocomplete if there are more users
 * than $prefs['user_selector_threshold']
 */
function smarty_function_user_selector($params, $smarty)
{
	global $prefs, $user, $tiki_p_admin;
	$tikilib = TikiLib::lib('tiki');
	$headerlib = TikiLib::lib('header');
	$userlib = TikiLib::lib('user');
	$smarty->loadPlugin('smarty_modifier_username');
	
	static $iUserSelector = 0;
	$iUserSelector++;
	
	$defaults = array( 
			'user' => $user,
			'group' => 'all',
			'groupIds' => '',
			'contact'=> 'false',
			'name' => 'user',
			'id' => 'user_selector_' . $iUserSelector,
			'multiple'=> 'false',
			'mustmatch' => 'true',
			'style'=> '' ,
			'editable' => $tiki_p_admin,
			'user_selector_threshold' => $prefs['user_selector_threshold'],
			'allowNone' => 'n',
			'realnames' => 'y',
			'class' => '',
	);
	
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
	
	if(isset($params['class'])) {
		$class = ' class="'. $params['class'] . '"';
	}

	$groupNames = array();
	if (is_array($params['groupIds'])) {
		foreach ($params['groupIds'] as $k => $groupId) {
			if ($groupId <= 0) {
				unset($params['groupIds'][$k]);
			}
		}
		if (!empty($params['groupIds'])) {
			$groupIds = $params['groupIds'];
		}	
	} elseif (!empty($params['groupIds'])) {
		$groupIds = explode('|', $params['groupIds']);
	}
	if (!empty($groupIds)) {
		foreach ($groupIds as $groupId) {
			$group_info = $userlib->get_groupId_info($groupId);
			$groupNames[] = $group_info['groupName'];
		}
	}

	$ucant = 0;
	$users = array();

	if ($params['group'] == 'all') {
		$ucant = $tikilib->list_users(0, 0, 'login_asc');
		$ucant = $ucant['cant'];
	} else {
		$groupNames[] = $params['group'];
	}

	// NOTE: if groupIds are present, the list of users is limited to those groups regardless of group == 'all'
	if (!empty($groupNames)) {
		$groupNames = array_unique($groupNames);
		foreach ($groupNames as $groupName) {
			$group_users = $userlib->get_group_users($groupName);
			$users = array_merge($users, $group_users);
		}
		$users = array_unique($users);
		$ucant = count($users);
	}

	$ret = '';
	
	if ($prefs['feature_jquery_autocomplete'] == 'y' && ($ucant > $prefs['user_selector_threshold'] or $ucant > $params['user_selector_threshold'])) {
		$ret .= '<input id="' . $params['id'] . '" type="text" name="' . $params['name'] . '" value="' . htmlspecialchars($params['user']) . '"' . $sz . $ed . ' style="'.$params['style'].'"'.$class.' />';
		if (($params['contact'] == 'true')) {
			$mode = ('usersandcontacts');
		} else if ($prefs['user_show_realnames'] === 'y' && $params['realnames'] === 'y') {
			$mode = ('userrealname');
		} else {
			$mode = ('username');
		}
		$headerlib->add_jq_onready('$("#' . $params['id'] . '").tiki("autocomplete", "'. $mode .'", {mustMatch: '.$params['mustmatch'].', multiple: '.$params['multiple'].' });');
	} else {
		if ($params['group'] == 'all' && empty($params['groupIds'])) {
			$usrs = $tikilib->list_users(0, -1, 'login_asc');
			foreach ($usrs['data'] as $usr) {
				$users[] = $usr['login'];
			}
		}
		$ret .= '<select name="' . $params['name'] . '" id="' . $params['id'] . '"' . $sz . $ed . ' style="'.$params['style'].'" class="form-control">';
		if ($params['allowNone'] === 'y') {
			$ret .= '<option value=""' . (empty($params['user']) ? ' selected="selected"' : '') . ' >' . tra('None') .'</option>';
		}
		foreach ($users as $usr) {
			if ($params['editable'] == 'y' || $usr == $params['user'] || (isset($params['select']) && $params['select'] === $usr)) {
				$usersname = $params['realnames'] === 'y' ? smarty_modifier_username($usr) : $usr;
				if (isset($params['select'])) {
					$ret .= '<option value="' . htmlspecialchars($usr) . '"' . ($usr == $params['select'] ? ' selected="selected"' : '') . ' >' . $usersname .'</option>';
				} else {
					$ret .= '<option value="' . htmlspecialchars($usr) . '"' . ($usr == $params['user'] ? ' selected="selected"' : '') . ' >' . $usersname .'</option>';
				}
			}
		}
		$ret .= '</select>';
	}
	return $ret;
		
}
