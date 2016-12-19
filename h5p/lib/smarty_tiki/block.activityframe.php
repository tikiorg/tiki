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

function smarty_block_activityframe($params, $content, $smarty, &$repeat)
{
	if ( $repeat ) return;

	$commentMode = 'default';
	if (isset($params['comment']) && in_array($params['comment'], ['object', 'activity', 'disabled'])) {
		$commentMode = $params['comment'];
	}

	$likeMode = 'default';
	if (isset($params['like']) && $params['like'] == 'disabled') {
		$likeMode = 'disabled';
	}

	$likes = isset($params['activity']['like_list']) ? $params['activity']['like_list'] : array();
	if (! is_array($likes)) {
		$params['activity']['like_list'] = $likes = array();
	}

	if (isset($params['activity']['user_groups'])) {
		$userGroups = TikiLib::lib('user')->get_user_groups($GLOBALS['user']);
		$choiceGroups = TikiLib::lib('user')->get_groups_userchoice();
		$sharedGroups = array_intersect($params['activity']['user_groups'], $userGroups, $choiceGroups);
	} else {
		$sharedGroups = array();
	}

	if (isset($params['activity']['object_type'], $params['activity']['object_id'])) {
		// Use the activity
		$object = array(
			'type' => $params['activity']['object_type'],
			'id' => $params['activity']['object_id'],
		);
	} elseif (isset($params['activity']['type'], $params['activity']['object'])) {
		// Not a registered activity, use parent object
		$object = array(
			'type' => $params['activity']['type'],
			'id' => $params['activity']['object'],
		);
	} else {
		$object = [];
	}

	/*
	Comment modes.
	By default the activity is picked, with a fallback to the object if not a registered
	activity.

	* disabled - completely remove comments
	* activity - prevent fallback to object
	* object - comments use object's comments
	*/
	if (empty($object) || $commentMode == 'disabled') {
		$comment = null;
	} elseif ($object['type'] == 'activity' && $commentMode == 'object') {
		if (isset($params['activity']['type'], $params['activity']['object'])) {
			// Not a registered activity, use parent object
			$comment = array(
				'type' => $params['activity']['type'],
				'id' => $params['activity']['object'],
			);
		} else {
			$comment = null;
		}
	} elseif ($object['type'] != 'activity' && $commentMode == 'activity') {
		$comment = null;
	} else {
		$comment = $object;
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign(
		'activityframe', array(
			'content' => $content,
			'activity' => $params['activity'],
			'object' => $object,
			'comment' => $comment,
			'heading' => $params['heading'],
			'like' => in_array($GLOBALS['user'], $likes),
			'likeactive' => $likeMode != 'disabled',
			'sharedgroups' => $sharedGroups,
			'summary' => isset($params['summary']) ? $params['summary'] : null,
			'params' => $params,
		)
	);
	$out = $smarty->fetch('activity/activityframe.tpl');

	return $out;
}
