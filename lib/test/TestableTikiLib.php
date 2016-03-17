<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class used to make possible to override libs in
 * TikiLib::libraries and thus make code that rely on
 * TikiLib::lib() easier to test.
 */
class TestableTikiLib extends TikiLib
{
	/**
	 * List of original objects as they are
	 * defined by TikiLib::lib()
	 * @var array
	 */
	protected $originalObjects = array();
	
	/**
	 * Override libs defined in TikiLib::lib().
	 * For each entry in $libs, the key should
	 * match the key used in TikiLib::lib() and the
	 * value should be the mock object used as replacement.
	 *  
	 * @param array $libs
	 * @return 
	 */
	public function overrideLibs(array $libs)
	{
		foreach ($libs as $key => $obj) {
			$this->originalObjects[$key] = TikiLib::lib($key);
		}
		
		self::$libraries = array_merge(self::$libraries, $libs);
	}
	
	/**
	 * Restore TikiLib::libraries to its original
	 * state.
	 * @return null
	 */
	public function __destruct()
	{
		self::$libraries = array_merge(self::$libraries, $this->originalObjects);
	}
}