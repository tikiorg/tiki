<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class RecommendationSet implements \Countable, \Iterator
{
	private $engine;
	private $recommendations = [];
	private $debug = [];

	function __construct($engineName)
	{
		$this->engine = $engineName;
	}

	function add(EngineOutput $recommendation)
	{
		if ($recommendation instanceof Recommendation) {
			$this->recommendations[] = $recommendation;
		} else {
			$this->addDebug($recommendation);
		}
	}

	function addDebug($info)
	{
		$this->debug[] = $info;
	}

	function getEngine()
	{
		return $this->engine;
	}

	function getDebug()
	{
		return new \ArrayIterator($this->debug);
	}

	function count()
	{
		return count($this->recommendations);
	}

	function current()
	{
		return current($this->recommendations);
	}

	function next()
	{
		next($this->recommendations);
	}

	function key()
	{
		return key($this->recommendations);
	}

	function valid()
	{
		return current($this->recommendations) !== false;
	}

	function rewind()
	{
		reset($this->recommendations);
	}
}
