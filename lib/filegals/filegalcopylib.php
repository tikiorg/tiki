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
 * Container for functions involved in files:copy and files:move console commands
 *
 */
class FilegalCopyLib extends FileGalLib
{

	/**
	 * Processes a list of files to be copied/moved to a directory in the filesystem
	 *
	 * @param array $files
	 * @param string $destinationPath
	 * @param string $sourcePath
	 * @param bool $move
	 * @return array					feedback messages
	 */

	function processCopy($files, $destinationPath, $sourcePath = '', $move = false) {

		$feedback = [];
		$operation = ($move) ? "Move" : "Copy";

		// cycle through all files to copy
		foreach ($files as $file) {
			$result = $this->copyFile($file, $destinationPath, $sourcePath, $move);
			if (isset($result['error'])) {
				$feedback[] = '<span class="text-danger">' . tr('%0 was not successful for "%1"', $operation, $file['filename']) . '<br>(' . $result['error'] . ')</span>';
			} else {
				$feedback[] = tr('%0 was successful', $operation) . ': ' . $file['filename'];
			}
		}
		return $feedback;
	}

	/**
	 *	Takes a file from a file gallery and copies/moves it to a local path
	 *
	 * @param array $file
	 * @param string $destinationPath
	 * @param string $sourcePath
	 * @param bool $move
	 * @return array					[fileName[,error]]
	 */
	function copyFile($file, $destinationPath, $sourcePath = '', $move = false)
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

		if ($move) {
			// This is a hack to bypass inconsistency in filegallib that would cause a Notice
			// message to the user.
			// remove_file() needs $file['data'], despite it being an optional field.
			// In the end, no Handlers in FileGallery implement any usage of $file['data']
			$file['data'] = null;
			if ($this->remove_file($file, '', true) === false) {
				return array('error' => tra('Cannot remove file from gallery'));
			}
		}

		return array(
			'fileName' => $fileName,
		);
	}
}