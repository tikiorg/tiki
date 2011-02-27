<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Expr_Not implements Search_Expr_Interface
{
	private $expression;

	function __construct($expression)
	{
		$this->expression = $expression;
	}

	function setType($type)
	{
		$this->expression->setType($type);
	}

	function setField($field = 'global')
	{
		$this->expression->setField($field);
	}

	function walk($callback)
	{
		$result = $this->expression->walk($callback);

		return call_user_func($callback, $this, array($result));
	}
}

