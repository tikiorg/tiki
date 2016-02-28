<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Expr_ImplicitPhrase implements Search_Expr_Interface
{
	private $parts;
	private $weight = 1.0;

	function __construct(array $parts)
	{
		$this->parts = $parts;
	}

	function __clone()
	{
		$this->parts = array_map(function ($part) {
			return clone $part;
		}, $this->parts);
	}

	function addPart(Search_Expr_Interface $part)
	{
		$this->parts[] = $part;
	}

	function setType($type)
	{
		foreach ($this->parts as $part) {
			$part->setType($type);
		}
	}

	function setField($field = 'global')
	{
		foreach ($this->parts as $part) {
			$part->setField($field);
		}
	}

	function setWeight($weight)
	{
		$this->weight = (float) $weight;
	}

	function getWeight()
	{
		return $this->weight;
	}

	function getBasicOperator()
	{
		global $prefs;
		if ($prefs['unified_lucene_default_operator'] == 1) {
			return new Search_Expr_And($this->parts);
		} else {
			return new Search_Expr_Or($this->parts);
		}
	}

	function walk($callback)
	{
		$results = array();
		foreach ($this->parts as $part) {
			$results[] = $part->walk($callback);
		}

		return call_user_func($callback, $this, $results);
	}

	function traverse($callback)
	{
		return call_user_func($callback, $callback, $this, $this->parts);
	}
}

