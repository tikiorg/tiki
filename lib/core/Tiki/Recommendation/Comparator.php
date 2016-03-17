<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class Comparator
{
	private $engines;

	function __construct(EngineSet $engines)
	{
		$this->engines = $engines;
	}

	function generate($input)
	{
		$out = [];

		$list = $this->engines->getBasicList();
		foreach ($list as $entry) {
			list($set, $engine) = $entry;
			$generated = $engine->generate($input);
			foreach ($generated as $recommendation) {
				$set->add($recommendation);
			}

			$out[] = $set;
		}

		return $out;
	}
}
