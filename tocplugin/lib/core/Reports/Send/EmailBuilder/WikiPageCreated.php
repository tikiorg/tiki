<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for wiki_page_created events
 */
class Reports_Send_EmailBuilder_WikiPageCreated extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Wiki pages created:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = tr(
			"%0 created the wikipage %1",
			"<u>{$change['data']['editUser']}</u>",
			"<a href=\"{$base_url}tiki-index.php?page={$change['data']['pageName']}\">{$change['data']['pageName']}</a>"
		);

		return $output;
	}
}
