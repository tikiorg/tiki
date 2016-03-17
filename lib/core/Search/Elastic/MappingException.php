<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_MappingException extends Search_Elastic_Exception
{
	private $type;
	private $field;

	function __construct($type, $field)
	{
		$this->type = $type;
		$this->field = $field;

		parent::__construct(tr('Unknown mapping type "%0" for field "%1"', $type, $field));
	}

	function getType()
	{
		return $this->type;
	}
}

