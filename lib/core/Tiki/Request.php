<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Creates a request object populated with data
 * from either http request or cli arguments.
 */
class Tiki_Request
{
	protected $properties = array();
	
	/**
	 * Populates $this->properties with params passed to PHP
	 * via http request or cli arguments.
	 * 
	 * @return null
	 */
	public function __construct()
	{
		if (isset($_SERVER['REQUEST_METHOD'])) {
			// http
			$this->properties = $_REQUEST;
		} else if (isset($_SERVER['argc'], $_SERVER['argv']) && $_SERVER['argc'] >= 2) {
			// cli
			foreach ($_SERVER['argv'] as $arg) {
				if (strpos($arg, '=')) {
					list($key, $value) = explode('=', $arg);
					$this->setProperty($key, $value);
				}
			}
		}
	}
	
	/**
	 * Set property a new property
	 * 
	 * @param string $key property key
	 * @param string $value property value
	 * @return null
	 */
	public function setProperty($key, $value)
	{
		$this->properties[$key] = $value;
	}
	
	/**
	 * Return property value
	 * 
	 * @param string $key property key
	 * @return string|null property value or null
	 */
	public function getProperty($key)
	{
		if (isset($this->properties[$key])) {
			return $this->properties[$key];
		}
	}
	
	/**
	 * Return true or false depending whether the
	 * property exist or not.
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function hasProperty($key)
	{
		if (isset($this->properties[$key])) {
			return true;
		}
		
		return false;
	}
}
