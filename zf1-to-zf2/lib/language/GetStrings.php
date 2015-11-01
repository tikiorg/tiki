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

class Language_GetStrings
{
	/**
	 * Array of file types objects.
	 * 
	 * @var array
	 */
	protected $fileTypes = array();
	
	/**
	 * Array of valid extensions. Extracted
	 * from $this->fileTypes.
	 * 
	 * @var array
	 */
	protected $extensions = array();
	
	/**
	 * List of languages whose language.php
	 * files will be updated. If empty all
	 * language.php files are updated.
	 * 
	 * @var array
	 */
	protected $languages = array();
	
	/**
	 * Name of the file that contain the
	 * translations.
	 * @var string
	 */
	protected $fileName = 'language.php';
	
	/**
	 * @var Language_CollectFiles
	 */
	public $collectFiles;
	
	/**
	 * @var Language_WriteFile_Factory
	 */
	public $writeFileFactory;
	
	/**
	 * Whether file paths where the string was found
	 * is included or not in langauge.php files. Default
	 * is false.
	 * 
	 * @var bool
	 */
	protected $outputFiles = false;
	
	/**
	 * Directory used as base to search for strings
	 * and to construct paths to language.php files.
	 * @var string
	 */
	protected $baseDir;
	
	/**
	 * Class construct.
	 * 
	 * The following are valid $options:
	 *   - 'outputFiles' => true: will write to language.php file the path
	 *     to the files where the string was found. Default is false.
	 *   - 'lang' => 'langCode' or 'lang' => array(list of lang codes):
	 *     language code or list of language codes whose language.php will be
	 *     updated. If empty, all language.php files are updated.
	 * 
	 * @param Language_CollectFiles $collectFiles
	 * @param Language_WriteFile_Factory $writeFileFactory factory to create Language_WriteFile objects
	 * @param array $options list of options to control object behavior (see above)
	 * @return null
	 */
	public function __construct(Language_CollectFiles $collectFiles, Language_WriteFile_Factory $writeFileFactory, array $options = null)
	{
		$this->collectFiles = $collectFiles;
		$this->writeFileFactory = $writeFileFactory;
		
		if (isset($options['outputFiles'])) {
			$this->outputFiles = true;
		}
		
		if (isset($options['baseDir'])) {
			if (!is_dir($options['baseDir'])) {
				throw new Language_Exception("Invalid directory {$options['baseDir']}.");
			}
			
			$this->baseDir = $options['baseDir'];
		} else {
			$this->baseDir = getcwd();
		}
		
		if (isset($options['fileName'])) {
			$this->fileName = $options['fileName']; 
		}
		
		if (isset($options['lang'])) {
			$this->setLanguages($options['lang']);
		} else {
			$this->setLanguages();
		}
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
	 * Getter for $this->fileTypes
	 * @return array
	 */
	public function getFileTypes()
	{
		return $this->fileTypes;
	}
	
	/**
	 * Add a file type object to $this->fileTypes
	 * and update $this->extensions.
	 * 
	 * @param FileType $fileType
	 * @return null
	 * @throws Language_Exception if type being added already exists
	 */
	public function addFileType(Language_FileType $fileType)
	{	
		if (in_array($fileType, $this->fileTypes)) {
			$className = get_class($fileType);
			throw new Language_Exception("Type $className already added.");
		}
		
		$this->fileTypes[] = $fileType;
		$this->extensions = array_merge($this->extensions, $fileType->getExtensions());
	}
	
	/**
	 * Setter method $this->languages
	 * property.
	 *  
	 * @param array|string $languages
	 * @return null
	 */
	public function setLanguages($languages = null)
	{
		if (is_null($languages)) {
			$languages = $this->getAllLanguages();
		} else {
			if (is_string($languages)) {
				$languages = array($languages);
			}
			
			foreach ($languages as $lang) {
				if (!file_exists($this->baseDir . '/lang/' . $lang)) {
					throw new Language_Exception('Invalid language code.');
				}
			}
		}

		$this->languages = $languages;
	}
	
	/**
	 * Getter method for $this->languages.
	 * 
	 * @return array
	 */
	public function getLanguages()
	{
		return $this->languages;
	}
	
	/**
	 * Get English strings from a given file.
	 * 
	 * @param string $filePath path to file
	 * @return array collected strings
	 */
	public function collectStrings($filePath)
	{
		if (empty($this->fileTypes)) {
			throw new Language_Exception('No Language_FileType found.');
		}
		
		$strings = array();
		$fileExtension = strrchr($filePath, '.');
		
		if (!$fileExtension || $fileExtension == '.') {
			throw new Language_Exception('Could not determine file extension.');
		}

		foreach ($this->fileTypes as $fileType) {
			if (in_array($fileExtension, $fileType->getExtensions())) {
				$file = file_get_contents($filePath);
				
				foreach ($fileType->getCleanupRegexes() as $regex => $replacement) {
					$file = preg_replace($regex, $replacement, $file);
				}
				
				foreach ($fileType->getRegexes() as $postProcess => $regex) {
					$matches = array();
					preg_match_all($regex, $file, $matches);
					$newStrings = $matches[1];
					
					// $postProcess can be used to call a file type specific method for each regular expression
					// used for PHP file type to perform different clean up for single quoted and double quoted strings
					if (method_exists($fileType, $postProcess)) {
						$newStrings = $fileType->$postProcess($newStrings);
					}
					
					$strings = array_merge($strings, $newStrings);
				}
				
				break;
			}
		}
		
		return array_values(array_unique($strings));
	}

	/**
	 * Loop through a list of files and
	 * calls $this->collectStrings() for each
	 * file. Return a list of translatable strings
	 * found.
	 * 
	 * @param array $files
	 * @return array $strings translatable strings found in scanned files
	 */
	public function scanFiles($files)
	{
		$strings = array();
		
		// strings collected per file
		$filesStrings = array();
		
		if (!empty($files)) {
			foreach ($files as $file) {
				$filesStrings[$file] = $this->collectStrings($file);
			}
		}

		// join strings collected per file into a single array
		// and remove duplicated strings
		foreach ($filesStrings as $file => $fileStrings) {
			foreach ($fileStrings as $str) {
				if (!isset($strings[$str])) {
					$string = array('name' => $str);
					
					if ($this->outputFiles) {
						// $string['files'] is an array with all the files where the string was found
						$string['files'] = array($file);
					}
					
					$strings[$str] = $string;
				} else {
					if ($this->outputFiles) {
						$strings[$str]['files'][] = $file;
					}
				}
			}
		}
				
		return $strings;
	}
	
	public function writeToFiles($strings)
	{
		foreach ($this->languages as $lang) {
			$filePath = $this->baseDir . '/lang/' . $lang . '/' . $this->fileName;
			$writeFile = $this->writeFileFactory->factory($filePath);
			$writeFile->writeStringsToFile($strings, $this->outputFiles);
		}
	}
	
	/**
	 * Return all available languages (check for the
	 * existence of a language file).
	 * @return array all language codes
	 */
	protected function getAllLanguages()
	{
		$dirs = dir($this->baseDir . '/lang');

		while (false !== ($entry = $dirs->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			
			$path = $dirs->path . '/' . $entry;
			if (is_dir($path) && file_exists($path . '/' . $this->fileName)) {
				$languages[] = $entry;
			}
		}
		
		return $languages;
	}
	
	public function run()
	{
		if (empty($this->fileTypes)) {
			throw new Language_Exception('No Language_FileType found.');
		}
		
		$this->collectFiles->setExtensions($this->extensions);
		$files = $this->collectFiles->run($this->baseDir);
		$strings = $this->scanFiles($files);
		$this->writeToFiles($strings);
	}
}
