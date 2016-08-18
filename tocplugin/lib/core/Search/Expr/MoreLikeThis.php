<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Expr_MoreLikeThis implements Search_Expr_Interface
{
	private $type;
	private $object;
	private $field;
	private $weight;
	private $content;

	/**
	 * If a single argument is provided, it will be assumed to be the direct content.
	 */
	function __construct($type, $object = null)
	{
		if (is_null($object)) {
			$this->content = $type;
		} else {
			$this->type = $type;
			$this->object = $object;
		}
	}

	function setType($type)
	{
	}

	function getType()
	{
		return 'plaintext';
	}

	function getContent()
	{
		return $this->content;
	}

	function setField($field = 'contents')
	{
		$this->field = $field;
	}

	function setWeight($weight)
	{
	}

	function getWeight()
	{
		return 1;
	}

	function walk($callback)
	{
		return call_user_func($callback, $this, array());
	}

	function getValue(Search_Type_Factory_Interface $typeFactory)
	{
	}

	function getField()
	{
		return $this->field;
	}

	function traverse($callback)
	{
		return call_user_func($callback, $callback, $this, array());
	}

	function getObjectType()
	{
		return $this->type;
	}

	function getObjectId()
	{
		return $this->object;
	}
}

