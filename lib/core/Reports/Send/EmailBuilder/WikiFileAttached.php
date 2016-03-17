<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for wiki_file_attached events
 */
class Reports_Send_EmailBuilder_WikiFileAttached extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('New attachments in wiki pages:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = tr(
			'%0 uploaded the file %1 onto %2',
			"<u>{$change['user']}</u>",
			"<a href=\"{$base_url}tiki-download_wiki_attachment.php?attId={$change['data']['attId']}\">{$change['data']['filename']}</a>",
			"<a href=\"{$base_url}tiki-index.php?page={$change['data']['pageName']}\">{$change['data']['pageName']}</a>."
		);

		return $output;
	}
}
