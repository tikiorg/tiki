<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class RecommendationSet implements \Countable, \Iterator
{
	private $engine;
	private $recommendations = [];

	function __construct($engineName)
	{
		$this->engine = $engineName;
	}

	function add(Recommendation $recommendation)
	{
		$this->recommendations[] = $recommendation;
	}

	function getEngine()
	{
		return $this->engine;
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
