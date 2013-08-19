<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_activity($params)
{
	$smarty = TikiLib::lib('smarty');
	
	if (isset($params['info'])) {
		$activity = $params['info'];
	} else {
		$lib = TikiLib::lib('activity');
		$info = $lib->getActivity($params['id']);
		if (! $info) {
			return tr('Not found.');
		}

		$activity = $info['arguments'];
		$activity['object_type'] = 'activity';
		$activity['object_id'] = $params['id'];
		$activity['event_type'] = $info['eventType'];
		$activity['comment_count'] = TikiLib::lib('comments')->count_comments("activity:{$params['id']}");
		$activity['like_list'] = TikiLib::lib('social')->getLikes('activity', $params['id']);
	}

	$smarty->assign('activity', $activity);
	return $smarty->fetch('activity/' . $activity['event_type'] . '.tpl');
}

