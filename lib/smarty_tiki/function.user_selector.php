<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	$defaults = [
			'user' => $user,
			'group' => 'all',
			'groupIds' => '',
			'contact' => 'false',
			'name' => 'user',
			'id' => 'user_selector_' . $iUserSelector,
			'multiple' => 'false',
			'mustmatch' => 'true',
			'style' => '' ,
			'editable' => $tiki_p_admin,
			'user_selector_threshold' => $prefs['user_selector_threshold'],
			'allowNone' => 'n',
			'noneLabel' => 'None',
			'realnames' => 'y',
			'class' => 'form-control',
	];

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
	if ($params['multiple'] === 'true') {
		$mt = ' multiple="multiple"';
	} else {
		$mt = '';
	}

	if (! empty($params['class'])) {
		$class = ' class="' . $params['class'] . '"';
	} else {
		$class = '';
	}

	$groupNames = [];
	if (is_array($params['groupIds'])) {
		foreach ($params['groupIds'] as $k => $groupId) {
			if ($groupId <= 0) {
				unset($params['groupIds'][$k]);
			}
		}
		if (! empty($params['groupIds'])) {
			$groupIds = $params['groupIds'];
		}
	} elseif (! empty($params['groupIds'])) {
		$groupIds = explode('|', $params['groupIds']);
	}
	if (! empty($groupIds)) {
		foreach ($groupIds as $groupId) {
			$group_info = $userlib->get_groupId_info($groupId);
			$groupNames[] = $group_info['groupName'];
		}
	}

	$users = [];
	$ret = '';
	if (! empty($groupNames)) {
		$ucant = $userlib->count_users_consolidated($groupNames);
	} else {
		$ucant = $userlib->count_users('');
	}

	if ($prefs['feature_jquery_autocomplete'] == 'y' && ($ucant > $prefs['user_selector_threshold'] or $ucant > $params['user_selector_threshold'])) {
		$ret .= '<input id="' . $params['id'] . '" type="text" name="' . $params['name'] . '" value="' . htmlspecialchars($params['user']) . '"' . $sz . $ed . ' style="' . $params['style'] . '"' . $class . ' />';
		if (($params['contact'] == 'true')) {
			$mode = ('usersandcontacts');
		} elseif ($prefs['user_show_realnames'] === 'y' && $params['realnames'] === 'y') {
			$mode = ('userrealname');
		} else {
			$mode = ('username');
		}
		$headerlib->add_jq_onready('$("#' . $params['id'] . '").tiki("autocomplete", "' . $mode . '", {mustMatch: ' . $params['mustmatch'] . ', multiple: ' . $params['multiple'] . ' });');
	} else {
		// get the user list
		if ($params['group'] !== 'all') {
			$groupNames[] = $params['group'];
		}

		// NOTE: if groupIds are present, the list of users is limited to those groups regardless of group == 'all'
		if (! empty($groupNames)) {
			$groupNames = array_unique($groupNames);
			$usrs = [];
			foreach ($groupNames as $groupName) {
				$group_users = $userlib->get_group_users($groupName);
				$usrs = array_merge($usrs, $group_users);
			}
			$usrs = array_unique($usrs);
			foreach ($usrs as $usr) {
				$users["$usr"] = $params['realnames'] === 'y' ? smarty_modifier_username($usr) : $usr;
			}
		}

		if ($params['group'] == 'all' && empty($params['groupIds'])) {
			$usrs = $tikilib->list_users(0, -1, 'login_asc');
			foreach ($usrs['data'] as $usr) {
				$users["{$usr['login']}"] = $params['realnames'] === 'y' ? smarty_modifier_username($usr['login']) : $usr['login'];
			}
		}

		asort($users, SORT_NATURAL | SORT_FLAG_CASE);

		$ret .= '<select name="' . $params['name'] . ( $params['multiple'] === 'true' ? '[]' : '' ) . '" id="' . $params['id'] . '"' . $sz . $ed . $mt . ' style="' . $params['style'] . '" class="form-control">';
		if ($params['allowNone'] === 'y') {
			$ret .= '<option value=""' . (empty($params['user']) ? ' selected="selected"' : '') . ' >' . tra($params['noneLabel']) . '</option>';
		}
		foreach ($users as $usr => $usersname) {
			$selected = isset($params['select']) && ( $params['select'] === $usr || (is_array($params['select']) && in_array($usr, $params['select'])) );
			if ($params['editable'] == 'y' || $usr == $params['user'] || $selected) {
				if (isset($params['select'])) {
					$ret .= '<option value="' . htmlspecialchars($usr) . '"' . ($selected ? ' selected="selected"' : '') . ' >' . $usersname . '</option>';
				} else {
					$ret .= '<option value="' . htmlspecialchars($usr) . '"' . ($usr == $params['user'] ? ' selected="selected"' : '') . ' >' . $usersname . '</option>';
				}
			}
		}
		$ret .= '</select>';
	}
	return $ret;
}
