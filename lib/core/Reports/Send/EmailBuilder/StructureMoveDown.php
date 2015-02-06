<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for structure_move_down events
 */
class Reports_Send_EmailBuilder_StructureMoveDown extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Wiki pages moved down in a structure tree:');
	}

	public function getOutput(array $change)
	{
		$output = tr(
			"%0 moved a wiki page down in a structure tree",
			"<u>{$change['user']}</u>"
		);

		return $output;
	}
}
