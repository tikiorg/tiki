<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @package   Tiki
 * @subpackage    Language
 */

abstract class Language_FileType
{
	/**
	 * List of regexes used to extract
	 * translatable strings from a file.
	 * @var array
	 */
	protected $regexes = array();
	
	/**
	 * List of valid file extensions for a
	 * specific file type.
	 * @var array
	 */
	protected $extensions = array();
	
	/**
	 * List of regexes used to clean a file
	 * before searching for translatable strings.
	 * @var array
	 */
	protected $cleanupRegexes = array();
	
	/**
	 * Getter for $this->regexes
	 * @return array
	 */
	public function getRegexes()
	{
		return $this->regexes;
	}
	
	/**
	 * Getter for $this->extensions
	 * @return array
	 */
	public function getExtensions()
	{
		return $this->extensions;
	}

	/**
	 * Getter for $this->cleanupRegexes
	 * @return array
	 */
	public function getCleanupRegexes()
	{
		return $this->cleanupRegexes;
	}
}
