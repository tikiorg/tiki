<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiFilter_IsoDate implements Zend\Filter\FilterInterface
{
	function __construct($format = 'Y-m-d')
	{
		$this->format = $format;
	}

	function filter($value)
	{
		$format = $this->format;
		if ($date = date_create_from_format($format, $value)) {
			$formatted = $date->format($format);

			if ($value == $formatted) {
				return $formatted;
			}
		}
	}
}
