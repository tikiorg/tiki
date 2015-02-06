<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for calendar_changed events
 */
class Reports_Send_EmailBuilder_CalendarChanged extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('New calendar events:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$calendarlib = TikiLib::lib('calendar');

		$item = $calendarlib->get_item($change['data']['calitemId']);
		$output = tr(
			'%0 added or updated event %1',
			"<u>{$change['data']['user']}</u>",
			"<a href='{$base_url}tiki-calendar_edit_item.php?viewcalitemId={$change['data']['calitemId']}'>{$item['name']}</a>"
		);
		return $output;
	}
}
