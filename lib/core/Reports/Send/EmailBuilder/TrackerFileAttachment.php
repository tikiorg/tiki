<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for tracker_file_attachment events
 */
class Reports_Send_EmailBuilder_TrackerFileAttachment extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('File attached to tracker:');
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
				'%0 attached a file (%1) to tracker item %2',
				"<u>{$change['user']}</u>",
				"<a href=\"{$base_url}tiki-download_item_attachment.php?attId={$change['data']['attachment']['attId']}\">{$change['data']['attachment']['filename']}</a>",
				"<a href='{$base_url}tiki-view_tracker_item.php?itemId=$itemId'>$mainFieldValue</a>"
			);
		} else {
			$output = tr(
				'%0 attached a file (%1) to tracker item %2',
				"<u>{$change['user']}</u>",
				"<a href=\"{$base_url}tiki-download_item_attachment.php?attId={$change['data']['attachment']['attId']}\">{$change['data']['attachment']['filename']}</a>",
				"<a href='{$base_url}tiki-view_tracker_item.php?itemId=$itemId'>$itemId</a>"
			);
		}

		return $output;
	}
}
