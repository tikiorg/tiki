<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Categoryname.php 57970 2016-12-21 15:41:22Z kroky6 $

class Search_Formatter_ValueFormatter_Categoryname extends Search_Formatter_ValueFormatter_Abstract
{
	private $separator = ', ';

	function __construct($arguments)
	{
		if (isset($arguments['separator'])) {
			$this->separator = $arguments['separator'];
		}
	}

	function render($name, $value, array $entry)
	{
		$categlib = TikiLib::lib('categ');
		$categories = $categlib->get_names((array)$value);
		return implode($this->separator, $categories);
	}
}

