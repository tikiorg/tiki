<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class BOMChecker_Scanner
{

	// Tiki source folder
	protected $sourceDir = __DIR__ . '/../../../';
	
	protected $scanExtensions = array(
		'php',
		'tpl'
	);

	// The number of files scanned.
	protected $scannedFiles = 0;

	// The list of files detected with BOM
	protected $bomFiles = array();

	/**
	 * @param string $scanDir
	 *    The file directory to scan.
	 * @param array $scanExtensions
	 *    An array with the file extensions to scan for BOM.
	 */
	public function __construct($scanDir = null, $scanExtensions = array())
	{
		if (!empty($scanDir) && is_dir($scanDir)) {
			$this->sourceDir = $scanDir;
		}

		if (is_array($scanExtensions)&&count($scanExtensions)) {
			$this->scanExtensions = $scanExtensions;
		}
	}

	/**
	 * Scan the folder for BOM files
	 * @return array
	 *  An array with the path to the BOM detected files.
	 */
	public function scan()
	{
		$this->checkDir($this->sourceDir);
		return $this->bomFiles;
	}

	/**
	 * Check directory path
	 *
	 * @param string $sourceDir
	 * @return void
	 */
	protected function checkDir($sourceDir)
	{
		$sourceDir = $this->fixDirSlash($sourceDir);

		// Copy files and directories.
		$sourceDirHandler = opendir($sourceDir);

		while ($file = readdir($sourceDirHandler)) {
			// Skip ".", ".." and hidden fields (Unix).
			if (substr($file, 0, 1) == '.')
				continue;

			$sourcefilePath = $sourceDir . $file;

			if (is_dir($sourcefilePath)) {
				$this->checkDir($sourcefilePath);
			}

			if (!is_file($sourcefilePath)
				|| !in_array($this->getFileExtension($sourcefilePath), $this->scanExtensions)
				|| !$this->checkUtf8Bom($sourcefilePath)
			) {
				continue;
			}
			$this->bomFiles[] = str_replace($this->sourceDir, '',$sourcefilePath);
		}
	}

	/**
	 * Check and change slash directory path
	 *
	 * @param string $dirPath
	 * @return string
	 */
	protected function fixDirSlash($dirPath)
	{
		$dirPath = str_replace('\\', '/', $dirPath);

		if (substr($dirPath, -1, 1) != '/')
			$dirPath .= '/';

		return $dirPath;
	}

	/**
	 * Get file extension
	 *
	 * @param string $filePath
	 * @return string
	 */
	protected function getFileExtension($filePath)
	{
		$info = pathinfo($filePath);
		return isset($info['extension'])?$info['extension']:'';
	}

	/**
	 * Check if UTF-8 BOM codification file
	 *
	 * @param string $filePath
	 * @return bool
	 */
	protected function checkUtf8Bom($filePath)
	{
		$file = fopen($filePath, 'r');
		$data = fgets($file, 10);
		fclose($file);

		$this->scannedFiles++;

		return (substr($data, 0, 3) == "\xEF\xBB\xBF");
	}

	/**
	 * Get the number of files scanned.
	 * 
	 * @return int
	 */
	public function getScannedFiles() {
		return $this->scannedFiles;
	}

	/**
	 * Get the list of files detected with BOM.
	 * 
	 * @return array
	 */
	public function getBomFiles() {
		return $this->bomFiles;
	}
}
