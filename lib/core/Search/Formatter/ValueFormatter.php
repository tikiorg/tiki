<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter
{
	private $valueSet;

	function __construct($valueSet)
	{
		$this->valueSet = $valueSet;
	}

	function getPlainValues()
	{
		return $this->valueSet;
	}

	function __call($format, $arguments)
	{
		$name = array_shift($arguments);

		if (empty($this->valueSet[$name])) {
			return tr("No value for '%0'", $name);
		}

		$class = 'Search_Formatter_ValueFormatter_' . ucfirst($format);
		if (class_exists($class)) {
			$formatter = new $class;
			return $formatter->render($this->valueSet[$name], $this->valueSet);
		} else {
			return tr("Unknown formatting rule '%0' for '%1'", $format, $name);
		}
	}
}

