<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class TikiFilter_Lang
 *
 * Filters for valid language values
 */
class TikiFilter_Lang implements Zend\Filter\FilterInterface
{
	/**
	 * Based on is_valid_language() method in lib/language/Language.php. The Language class isn't used here because
	 * necessary classes/definitions are not available at the point the filter is used in the installer
	 *
	 * @param mixed $input
	 * @return mixed|string
	 */
	function filter($input)
	{
		$filtered = preg_filter('/^[a-zA-Z-_]*$/', '$0', $input);
		return $filtered && file_exists('lang/' . $filtered . '/language.php') ? $filtered : '';
	}
}
