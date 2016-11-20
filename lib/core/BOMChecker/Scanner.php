<?php

class BOMChecker_Scanner
{

	// Tiki source folder
	protected $sourceDir = __DIR__ . '/../../../';
	
	protected $scanExtensions = array(
		'php',
		'tpl'
	);

	// The number of files scanned.
	public $scannedFiles = 0;

	// The list of files detected with BOM
	public $bomFiles = array();

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
		self::checkDir($this->sourceDir);
		return $this->bomFiles;
	}

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
				self::checkDir($sourcefilePath);
			}

			if (!is_file($sourcefilePath)
				|| !in_array(self::getFileExtension($sourcefilePath), $this->scanExtensions)
				|| !self::checkUtf8Bom($sourcefilePath)
			) {
				continue;
			}
			$this->bomFiles[] = str_replace($this->sourceDir, '',$sourcefilePath);
		}
	}

	private function fixDirSlash($dirPath)
	{
		$dirPath = str_replace('\\', '/', $dirPath);

		if (substr($dirPath, -1, 1) != '/')
			$dirPath .= '/';

		return $dirPath;
	}

	private function getFileExtension($filePath)
	{
		$info = pathinfo($filePath);
		return isset($info['extension'])?$info['extension']:'';
	}

	public function checkUtf8Bom($filePath)
	{
		$file = fopen($filePath, 'r');
		$data = fgets($file, 10);
		fclose($file);

		$this->scannedFiles++;

		return (substr($data, 0, 3) == "\xEF\xBB\xBF");
	}

}
