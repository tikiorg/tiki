<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation\Engine;
use Tiki\Recommendation\Recommendation;

class FakeEngine implements EngineInterface
{
	private $list;

	function __construct($list)
	{
		$this->list = $list;
	}

	function generate($input)
	{
		foreach ($this->list as $entry) {
			if (is_array($entry)) {
				yield new Recommendation($entry['type'], $entry['object']);
			} else {
				yield $entry;
			}
		}
	}
}
