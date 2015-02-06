<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class EngineSet
{
	private $engines = [];

	function register($name, Engine\EngineInterface $engine)
	{
		$this->registerWeighted($name, 1, $engine);
	}

	function registerWeighted($name, $weight, Engine\EngineInterface $engine)
	{
		$this->engines[$name] = [$weight, $engine];
	}

	function getBasicList()
	{
		foreach ($this->engines as $name => $entry) {
			list($weight, $engine) = $entry;
			yield [new RecommendationSet($name), $engine];
		}
	}

	function getGenerator()
	{
		$list = [];
		foreach ($this->engines as $name => $entry) {
			list($weight, $engine) = $entry;
			for ($i = 0; $i < $weight; ++$i) {
				$list[] = [$name, $engine];
			}
		}
		if (empty($list)) {
			return;
		}

		shuffle($list);

		while (true) {
			foreach ($list as $entry) {
				list($name, $engine) = $entry;
				yield [new RecommendationSet($name), $engine];
			}
		}
	}
}
