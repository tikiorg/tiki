<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('Exception.php');

/**
 * Represents the data of a Tiki language file.
 */
class Language_File
{
	public $filePath;
	
	/**
	 * Language file parsed content.
	 * 
	 * @var array
	 */
	protected $content = array();
	
	/**
	 * Wheter $this->paser() has been called and
	 * the content for the file is already loaded.
	 * 
	 * @var bool
	 */
	protected $contentLoaded = false;
	
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
		
		$this->content = $content;
		$this->contentLoaded = true;
		
		return $content;
	}

	/**
	 * Return statistics about the language file.
	 * Information available is total number of strings,
	 * number of untranslated strings, number of translated strings
	 * and translation percentage.
	 * 
	 * @return array
	 */
	public function getStats()
	{
		if (!$this->contentLoaded) {
			$this->parse();
		}
		
		$stats = array(
			'total' => 0,
			'translated' => 0,
			'untranslated' => 0,
			'percentage' =>  0,
		);
		
		foreach ($this->content as $entry) {
			if ($entry['translated']) {
				$stats['translated']++;
			} else {
				$stats['untranslated']++;
			}
			
			$stats['total']++;
		}
		
		if (!empty($stats['total'])) {
			$stats['percentage'] = round($stats['translated'] / $stats['total'], 4) * 100;
		}
		
		return $stats;
	}
	
	/**
	 * Return an array with translations extracted from
	 * language file.
	 * 
	 * @return array translations
	 */
	public function getTranslations()
	{
		require($this->filePath);

		if (isset($lang) && !empty($lang)) {
			return $lang;
		} else {
			return array();
		}
	}
}