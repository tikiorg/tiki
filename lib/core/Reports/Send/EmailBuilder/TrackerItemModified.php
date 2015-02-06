<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for tracker_iem_modified events
 */
class Reports_Send_EmailBuilder_TrackerItemModified extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Tracker items modified:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$trackerId = $change['data']['trackerId'];
		$itemId = $change['data']['itemId'];

		$trklib = TikiLib::lib('trk');
		$tracker = $trklib->get_tracker($trackerId);
		$mainFieldValue = $trklib->get_isMain_value($trackerId, $itemId);

		if ($mainFieldValue) {
			$output = tr(
				'%0 added or updated tracker item %1 on tracker %2',
				"<u>{$change['data']['user']}</u>",
				"<a href='{$base_url}tiki-view_tracker_item.php?itemId=$itemId'>$mainFieldValue</a>",
				"<a href='{$base_url}tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
			);
		} else {
			$output = tr(
				'%0 added or updated tracker item id %1 on tracker %2',
				"<u>{$change['data']['user']}</u>",
				"<a href='{$base_url}tiki-view_tracker_item.php?itemId=$itemId'>$itemId</a>",
				"<a href='{$base_url}tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
			);
		}

		return $output;
	}
}
