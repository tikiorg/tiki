<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ParseFile.php 37798 2011-09-29 19:59:41Z changi67 $

require_once('Exception.php');

/**
 * Parse a Tiki language file and create
 * a data structure that represents it.
 */
class Language_ParseFile
{
	
	public $filePath;
	
	public function __construct($filePath)
	{
		if (!file_exists($filePath)) {
			throw new Language_Exception("Path $filePath does not exist.");
		}
				
		$this->filePath = $filePath;
	}
	
	/**
	 * Read a language file and return an array with
	 * the data collected from it. Each entry has the
	 * following format:
	 * 
	 * 'String key in English' => array('key' => 'String key in English', 'translation' => 'Translation to some language', 'translated' => true)
	 * 
	 * @return array
	 */
	public function parse()
	{
		$lines = file($this->filePath);
		$content = array(); 
		
		foreach ($lines as $line) {
			$matches = array();
			$entry = array();
			
			// build an array with all the translations from the source file
			if (preg_match('|^(//)?\s*\"(.*)\"\s*\=\>\s*\"(.*)\"\s*\,\s*$|', $line, $matches)) {
				$entry['key'] = $matches[2];

				if (empty($matches[1])) {
					$entry['translation'] = $matches[3];
					$entry['translated'] = true;
				} else {
					$entry['translated'] = false;
				}
				
				$content[$matches[2]] = $entry;
			}
		}
		
		return $content;
	}
}