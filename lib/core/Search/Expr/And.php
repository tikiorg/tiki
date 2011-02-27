<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Expr_And implements Search_Expr_Interface
{
	private $parts;

	function __construct(array $parts)
	{
		$this->parts = $parts;
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

	function walk($callback)
	{
		$results = array();
		foreach ($this->parts as $part) {
			$results[] = $part->walk($callback);
		}

		return call_user_func($callback, $this, $results);
	}
}

