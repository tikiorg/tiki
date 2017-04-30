<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class TikiFilter_PregFilter
 *
 * Use to create Tiki filters based on the PHP preg_filter() method
 */
class TikiFilter_PregFilter implements Zend_Filter_Interface
{

	/**
	 * @var
	 */
	private $pattern;
	private $replacement;
	private $limit;
	private $count;

	/**
	 * TikiFilter_PregFilter constructor.
	 *
	 * See PHP documentation for preg_filter() for parameter definitions
	 * @param $pattern
	 * @param $replacement
	 * @param int $limit
	 * @param null $count
	 */
	function __construct($pattern, $replacement, $limit = -1, $count = null)
	{
		$this->pattern = $pattern;
		$this->replacement = $replacement;
		$this->limit = $limit;
		$this->count = $count;
	}

	/**
	 * @param mixed $subject
	 * @return mixed
	 */
	function filter($subject)
	{
		if (is_null($this->count)) {
			$return = preg_filter($this->pattern, $this->replacement, $subject, $this->limit);
		} else {
			$return = preg_filter($this->pattern, $this->replacement, $subject, $this->limit, $this->count);
		}
		//return empty string rather than null
		return !empty($return) ? $subject : '';
	}
}
