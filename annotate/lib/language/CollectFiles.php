<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('Exception.php');

/**
 * Scan directories collecting files that should be parsed
 * when searching for translatable strings. Provide methods
 * to manually exclude directories from the search and
 * manually include files inside excluded directories.  
 */
class Language_CollectFiles
{
	/**
	 * List of excluded directories.
	 * @var array
	 */
	protected $excludeDirs = array();
	
	/**
	 * List of files manually included files.
	 * @var array
	 */
	protected $includeFiles = array();
	
	/**
	 * List of valid file extensions
	 * @var array
	 */
	protected $extensions = array('.tpl', '.php');
	
	/**
	 * Set $this->excludeDirs
	 * @param array $excludeDirs 
	 * @return null
	 */
	public function setExcludeDirs(array $excludeDirs)
	{
		foreach ($excludeDirs as $dir) {
			if (!is_dir($dir)) {
				throw new Language_Exception("Dir $dir does not exist");
			}
		}
		
		$this->excludeDirs = $excludeDirs;
	}
	
	/**
	 * Set $this->includeFiles
	 * @param array $includeFiles 
	 * @return null
	 */
	public function setIncludeFiles(array $includeFiles)
	{
		foreach ($includeFiles as $file) {
			if (!file_exists($file)) {
				throw new Language_Exception("File $file does not exist");
			}
		}
		
		$this->includeFiles = $includeFiles;
	}
	
	/**
	 * Set $this->extensions
	 * @param array $extensions
	 * @return null
	 */
	public function setExtensions(array $extensions)
	{
		$this->extensions = $extensions;
	}
	
	/**
	 * Return $this->excludeDirs
	 * @return array
	 */
	public function getExcludeDirs()
	{
		return $this->excludeDirs;
	}
	
	/**
	 * Return $this->includeFiles
	 * @return array
	 */
	public function getIncludeFiles()
	{
		return $this->includeFiles;
	}
	
	/**
	 * Return all files that can contain 
	 * "localizable" strings.
	 * @param $dir
	 * @return array all files that can contain "localizable" strings
	 */
	public function run($dir)
	{
		$files = array_merge($this->scanDir($dir), $this->getIncludeFiles());
		$files = array_values(array_unique($files));
		
		return $files;
	}
	
	/**
	 * Recursively scan directory and return all its
	 * files (excluding $this->excludeDirs)
	 * 
	 * @param $dir base dir
	 * @return array collected files
	 */
	public function scanDir($dir)
	{
		if (!file_exists($dir)) {
			throw new Language_Exception("Dir $dir does not exist.");
		}
		
		$files = array();
		$pattern = $this->buildExtensionsPattern();
		$handle = opendir($dir);

		while (false !== ($file = readdir($handle))) {
			// Skip current, parent and hidden directories
			if ('.'  === $file || '..' === $file
				|| ((is_dir($file)) && strpos($file, '.') === 0)) {
				continue;
			}
	
			$path = $dir . '/' . $file;
			
			if (in_array($path, $this->getExcludeDirs())) {
				continue;
			}
			
			if (preg_match($pattern, $file)) {
				$files[] = $path;
			} else if (is_dir($path)) {
				$files = array_merge($files, $this->scanDir($path));
			}
		}
		
		closedir($handle);
		
		foreach ($this->getIncludeFiles() as $file) {
			if (preg_match($pattern, $file) && !in_array($file, $files)) {
				$files[] = $file;
			}
		}
		
		return $files;
	}
	
	/**
	 * Auxiliary method to build a pattern with
	 * the valid extensions to match against file names
	 * 
	 * @return string
	 */
	protected function buildExtensionsPattern()
	{
		$extensionsString = '';
		
		foreach ($this->extensions as $extension) {
			$extensionsString .= substr($extension, 1) . '|';
		}
		
		$extensionsString = substr($extensionsString, 0, strlen($extensionsString) - 1);
		
		return "/.*\.($extensionsString)$/";
	}
}