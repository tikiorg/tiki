<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Expr_Not implements Search_Expr_Interface
{
	private $expression;
	private $weight = 1.0;

	function __construct($expression)
	{
		$this->expression = $expression;
	}

	function __clone()
	{
		$this->expression = clone $this->expression;
	}

	function setType($type)
	{
		$this->expression->setType($type);
	}

	function setField($field = 'global')
	{
		$this->expression->setField($field);
	}

	function setWeight($weight)
	{
		$this->weight = (float) $weight;
	}

	function getWeight()
	{
		return $this->weight;
	}

	function walk($callback)
	{
		$result = $this->expression->walk($callback);

		return call_user_func($callback, $this, array($result));
	}

	function traverse($callback)
	{
		return call_user_func($callback, $callback, $this, array($this->expression));
	}
}

