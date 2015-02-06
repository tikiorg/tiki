<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for structure_add events
 */
class Reports_Send_EmailBuilder_StructureAdd extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Wiki pages added to a structure:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		$output = tr(
			"%0 added %1 wiki page to a structure",
			"<u>{$change['user']}</u>",
			"<a href='{$base_url}tiki-index.php?page={$change['data']['name']}'>{$change['data']['name']}</a>"
		);

		return $output;
	}
}
