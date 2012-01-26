<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: PlainText.php 34591 2011-05-24 17:38:12Z lphuberdeau $

class Search_Type_PlainText implements Search_Type_Interface
{
	private $value;

	function __construct($value)
	{
		$this->value = $value;
	}

	function getValue()
	{
		return $this->value;
	}

	function filter(array $filters)
	{
		$value = $this->value;

		foreach ($filters as $f) {
			$value = $f->filter($value);
		}

		return new self($value);
	}
}
