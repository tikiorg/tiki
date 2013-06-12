<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_Facet_Term implements Search_Query_Facet_Interface
{
	private $field;

	function __construct($field)
	{
		$this->field = $field;
	}

	function getName()
	{
		return $this->field;
	}

	function getField()
	{
		return $this->field;
	}

	function getLabel()
	{
		return ucfirst($this->field);
	}
}

