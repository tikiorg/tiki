<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for wiki_page_changed events
 */
class Reports_Send_EmailBuilder_WikiPageChanged extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Wiki pages updated:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$newVersion = $change['data']['oldVer'] + 1;

		$output = tr(
			"%0 edited the wikipage %1 (<a href=%2>this history</a>, <a href=%3>all history</a>)",
			"<u>{$change['data']['editUser']}</u>",
			"<a href=\"{$base_url}tiki-index.php?page={$change['data']['pageName']}\">{$change['data']['pageName']}</a>",
			"\"{$base_url}tiki-pagehistory.php?page={$change['data']['pageName']}&diff_style=sidediff&compare=Compare&newver=$newVersion&oldver={$change['data']['oldVer']}\"",
			"\"{$base_url}tiki-pagehistory.php?page={$change['data']['pageName']}&diff_style=sidediff&compare=Compare&newver=0&oldver={$change['data']['oldVer']}\""
		);

		return $output;
	}
}
