<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * Class FilegalCopyLib
 *
 * Container for functions involved files:copy console command
 *
 */
class FilegalCopyLib extends FileGalLib
{

	/**
	 * Processes a list of files to be copied to a directory in the filesystem
	 *
	 * @param array $files
	 * @param string $sourcePath
	 * @param string $destinationPath
	 * @return array					feedback messages
	 */

	function processCopy($files, $sourcePath = '', $destinationPath) {

		$feedback = [];

		// cycle through all files to copy
		foreach ($files as $file) {
			$result = $this->copyFile($file, $sourcePath, $destinationPath);
			if (isset($result['error'])) {
				$feedback[] = '<span class="text-danger">' . tr('Copy was not successful for "%0"', $file['filename']) . '<br>(' . $result['error'] . ')</span>';
			} else {
				$feedback[] = tra('Copy was successful') . ': ' . $file['filename'];
			}
		}
		return $feedback;
	}

	/**
	 *	Takes a file from a file gallery and copies it to a local path
	 *
	 * @param array $file
	 * @param string $sourcePath
	 * @param string $destinationPath
	 * @return array					[fileName[,error]]
	 */
	function copyFile($file, $sourcePath = '', $destinationPath)
	{

		$fileId = $file['fileId'];
		$filePath = $file['path'];
		$fileName = $file['filename'];

		if (! empty($filePath)) { // i.e., fgal_use_db !== 'y'
			if ($sourcePath == '') {
				return array('error' => tra('Source path empty'));
			}
			if (! copy($sourcePath . $filePath, $destinationPath . $fileName)) {
				if (! is_writable($destinationPath)) {
					return array('error' => tra('Cannot write to this path: ') . $destinationPath);
				} else {
					return array('error' => tra('Cannot read this file: ') . $sourcePath . $filePath);
				}
			}
		} else {
			$filesTable = $this->table('tiki_files');
			$fileData = $filesTable->fetchOne('data', array('fileId' => (int)$fileId));
			if (file_put_contents($destinationPath . $fileName, $fileData) === false) {
				if (! is_writable($destinationPath)) {
					return array('error' => tra('Cannot write to this path: ') . $destinationPath);
				} else {
					return array('error' => tra('Cannot get filedata from db'));
				}
			}
		}

		return array(
			'fileName' => $fileName,
		);
	}
}