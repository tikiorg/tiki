<?php

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
	 * @param array $options		[string sourcePath, string destinationPath]
	 * @return array				feedback messages
	 */

	function processCopy($files, $options = []) {
		include_once ('lib/mime/mimetypes.php');
		global $mimetypes, $user, $prefs;

		$userlib = TikiLib::lib('user');

		$feedback = [];

		$options = array_merge(
			[
				'sourcePath' => '',
				'destinationPath' => '',
			],
			$options
		);

		$sourcePath = $options['sourcePath'];
		$destinationPath = $options['destinationPath'];

		// cycle through all files to copy
		foreach ($files as $file) {
			$result = $this->handle_copy(
					[
							'source' => $file,
							'sourcePath' => $sourcePath,
							'destinationPath' => $destinationPath,
					]
			);

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
	 * @param array $info		[source, sourcePath, destinationPath]
	 * @return array			[fhash[,error]]
	 */
	function handle_copy($info)
	{

		$sourcePath = $info['sourcePath'];
		$destinationPath = $info['destinationPath'];

		$fhash = $info['source']['filename'];
		if (! copy($sourcePath.$info['source']['path'], $destinationPath . $fhash)) {
			if (! is_writable($destinationPath)) {
				return array('error' => tra('Cannot write to this path: ') .  $destinationPath);
			} else {
				return array('error' => tra('Cannot read this file: ') . $sourcePath.$info['source']['path']);
			}
		}

		return array(
			'fhash' => $fhash,
		);
	}
}