<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Source;

class TrackerSource implements SourceInterface
{
	private $trackerId;

	function __construct(\Tracker\Tabular\Schema $schema)
	{
		$def = $schema->getDefinition();
		$this->trackerId = $def->getConfiguration('trackerId');
	}

	function getEntries()
	{
		$table = \TikiDb::get()->table('tiki_tracker_items');
		$ids = $table->fetchColumn('itemId', [
			'trackerId' => $this->trackerId,
		]);

		foreach ($ids as $id) {
			yield new TrackerSourceEntry($id);
		}
	}
}

