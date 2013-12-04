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
		$lib = TikiLib::lib('unifiedsearch');
		$docs = $lib->getDocuments('activity', $params['id']);

		$activity = reset($docs);

		if (! $activity) {
			return tr('Not found.');
		}

		$activity = array_map(function ($entry) {
			if (is_object($entry)) {
				if (method_exists($entry, 'getRawValue')) {
					return $entry->getRawValue();
				} else {
					return $entry->getValue();
				}
			} else {
				return $entry;
			}
		}, $activity);
	}

	$smarty->assign('activity', $activity);
	return $smarty->fetch('activity/' . $activity['event_type'] . '.tpl');
}

