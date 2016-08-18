<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $

/**
 * This is a lock smarty function. It sets a lock button link that allows you to lock (and unlock) an object.
 *
 * @param $params
 *  - type       - sets the type of object being locked. Ex: tempmlate, structure (to come)
 *  - object     - sets the id of the object being liked. Can be left empty for objects being created (although this will not "lock" them)
 *  - lock_perm  - (optional) will be calculated from type if not supplied, e.g. lock_content_templates for templates
 *  - admin_perm - (optional) will be calculated from type if not supplied, e.g. admin_content_templates for templates
 *
 *
 * @param $smarty
 * @return string|void
 * @throws Exception
 */
function smarty_function_lock($params, $smarty)
{
	global $user, $smarty;

	static $instance = 0;
	$instance++;

	$attribute = "tiki.object.lock";

	// unregistered user, do nothing
	if (empty($user)) {
		return '';
	}

	if (empty($params['type'])) {
		return tra('Type not specified');
	}

	if (empty($params['lock_perm'])) {
		switch ($params['type']) {
			case 'template':
				$params['lock_perm'] = 'lock_content_templates';
				break;
			case 'wiki structure':
				$params['lock_perm'] = 'lock_structures';
				break;
			default:
				return tra('lock perm not found');
		}
	}

	if (empty($params['admin_perm'])) {
		switch ($params['type']) {
			case 'template':
				$params['admin_perm'] = 'admin_content_templates';
				break;
			case 'wiki structure':
				$params['admin_perm'] = 'admin_structures';
				break;
			default:
				return tra('admin perm not found');
		}
	}

	$attributelib = TikiLib::lib("attribute");

	if ($params['type'] === 'wiki structure') {
		$type = 'wiki page';	// ugly exception for wiki structures because they use the perms set on the top page
	} else {
		$type = $params['type'];
	}
	$perms = Perms::get([ 'type' => $type, 'object' => $params['object'] ]);

	if (! empty($params['object'])) {
		$value = $attributelib->get_attribute($params['type'], $params['object'], $attribute);
	} else {
		$value = '';
	}

	$params['instance'] = $instance;

	if ($value) {
		$params['is_locked'] = true;
		if ($value === $user || $perms->$params['admin_perm']) {
			$params['can_change'] = true;
		} else {
			$params['can_change'] = false;
		}
		$params['lockedby'] = $value;
	} else {
		$params['is_locked'] = false;
		$params['can_change'] = $perms->$params['lock_perm'];
	}

	$smarty->assign('data', $params);
	return $smarty->fetch('object/lock.tpl');
}
