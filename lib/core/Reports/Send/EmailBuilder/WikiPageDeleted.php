<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for wiki_page_deleted events
 */
class Reports_Send_EmailBuilder_WikiPageDeleted extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Wiki pages deleted:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = tr(
			"%0 deleted the wikipage %1",
			"<u>{$change['data']['editUser']}</u>",
			"{$change['data']['pageName']}"
		);

		return $output;
	}
}
