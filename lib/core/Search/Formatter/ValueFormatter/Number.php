<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Number extends Search_Formatter_ValueFormatter_Abstract
{
	private $decimals = null;
	private $dec_point = null;
	private $thousands_sep = null;

	function __construct($arguments)
	{
		if (isset($arguments['decimals'])) {
			$this->decimals = $arguments['decimals'];
		}
		if (isset($arguments['dec_point'])) {
			$this->dec_point = $arguments['dec_point'];
		}
		if (isset($arguments['thousands_sep'])) {
			$this->thousands_sep = $arguments['thousands_sep'];
		}
	}

	function render($name, $value, array $entry)
	{
		if ((string)floatval($value) !== (string)$value) {
			return $value;
		}
		if ($this->dec_point && $this->thousands_sep) {
			return number_format(floatval($value), $this->decimals, $this->dec_point, $this->thousands_sep);
		} elseif ($this->decimals) {
			return number_format(floatval($value), $this->decimals);
		} else {
			return number_format(floatval($value));
		}
	}
}
