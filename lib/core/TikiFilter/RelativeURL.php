<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: RelativeUrl.php 63143 2017-07-02 16:37:28Z lindonb $

/**
 * Class TikiFilter_RelativeURL
 *
 * Filters for valid relative URL's, and strips any tags.
 */
class TikiFilter_RelativeURL implements Zend_Filter_Interface
{
	/**
	 *
	 * @param string $input		Absolute or relative URL.
	 * @return string			Absolute URL components stripped out.
	 */


	function filter($input)
	{

		$filter = new Zend_Filter_StripTags();
		$url =  $filter->filter($input);

		$url = parse_url($url);

		$path = isset($url['path']) ? $url['path'] : '';
		$query = isset($url['query']) ? '?' . $url['query'] : '';
		$fragment = isset($url['fragment']) ? '#' . $url['fragment'] : '';

		return $path . $query . $fragment;
	}
}