<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for tracker_item_comment events
 */
class Reports_Send_EmailBuilder_TrackerItemComment extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Comments in tracker items:');
	}

	public function getOutput(array $change)
	{
		global $prefs;
        $base_url = $change['data']['base_url'];

		$trackerId = $change['data']['trackerId'];
		$itemId = $change['data']['itemId'];

		$trklib = TikiLib::lib('trk');
		$tracker = $trklib->get_tracker($trackerId);
		$mainFieldValue = $trklib->get_isMain_value($trackerId, $itemId);
        if ( $prefs['tracker_show_comments_below'] == 'y' ) {
            $locationComments = 'cookietab=1#Comments';
        } else {
            $locationComments = 'cookietab=2';
        }
		if ($mainFieldValue) {
			$output = tr(
				'%0 added a new comment to %1 on tracker %2',
				"<u>{$change['data']['user']}</u>",
				"<a href='{$base_url}tiki-view_tracker_item.php?itemId=$itemId&$locationComments'>$mainFieldValue</a>",
				"<a href='{$base_url}tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
			);
		} else {
			$output = tr(
				'%0 added a new comment to item id %1 on tracker %2',
				"<u>{$change['data']['user']}</u>",
				"<a href='{$base_url}tiki-view_tracker_item.php?itemId=$itemId&$locationComments'>$itemId</a>",
				"<a href='{$base_url}tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
			);
		}

		return $output;
	}
}
