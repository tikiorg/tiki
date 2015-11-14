<?php

// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class FilegalBatchLib
 *
 * Container for functions involved in tiki-batch_upload_files.php and files:batchupload console command
 *
 */
class FilegalBatchLib extends FileGalLib
{
	// list of filetypes you DO NOT want to show
	private $disallowed_types = [
		'php',
		'php3',
		'php4',
		'phtml',
		'phps',
		'py',
		'pl',
		'sh',
		'php~'
	];


	/**
	 * Processes a list of file to be "uploaded" as a batch
	 *
	 * @param array $files
	 * @param int $galleryId
	 * @param array $options		[bool subToDesc, bool subdirToSubgal, bool createSubgals, string fileUser, string fileGroup, string fileMode]
	 * @return array				feedback messages
	 */

	function processBatchUpload($files, $galleryId = null, $options = [
			'subToDesc' => false,
			'subdirToSubgal' => false,
			'createSubgals' => false,
			'deleteAfter' => null,
			'fileUser' => '',
			'fileGroup' => '',
			'fileMode' => '',
	]) {
		include_once ('lib/mime/mimetypes.php');
		global $mimetypes, $user, $prefs;

		$feedback = [];

		if ($galleryId === null) {
			$galleryId = $prefs['fgal_root_id'];
		}

		// for subdirToSubgal we need all existing dir galleries for the current gallery
		$subgals = [];
		if ($options['subdirToSubgal']) {
			$subgals = $this->getSubGalleries($galleryId, true, 'batch_upload_file_dir');
		}

		// cycle through all files to upload
		foreach ($files as $file) {

			//add meadata
			$metadata = $this->extractMetadataJson($file);

			$path_parts = pathinfo($file);
			$ext = strtolower($path_parts["extension"]);

			$type = $mimetypes["$ext"];
			$filesize = @filesize($file);

			$destinationGalleryId = $galleryId;

			if ($options['fileUser']) {
				if (!chown($file, $options['fileUser'])) {
					$feedback[] = '<span class="text-danger">' . tr('Problem setting user for "%0"', $path_parts['basename']);
				}
			}
			if ($options['fileGroup']) {
				if (!chgrp($file, $options['fileGroup'])) {
					$feedback[] = '<span class="text-danger">' . tr('Problem setting group for "%0"', $path_parts['basename']);
				}
			}
			if ($options['fileMode']) {
				if (!chmod($file, octdec($options['fileMode']))) {
					$feedback[] = '<span class="text-danger">' . tr('Problem setting mode for "%0"', $path_parts['basename']);
				}
			}

			if ($options['subdirToSubgal']) {

				$foundDir = true;

				$dirs = array_filter(
						explode(DIRECTORY_SEPARATOR,
								substr($path_parts['dirname'], strlen($prefs['fgal_batch_dir']))
						)
				);

				foreach($dirs as $dir) {
					$foundDir = false;

					foreach($subgals['data'] as $subgal) {
						if ($subgal['parentId'] == $destinationGalleryId && $subgal['name'] == $dir) {
							$destinationGalleryId = (int) $subgal['id'];
							$foundDir = true;
							break;
						}
					}
					if (! $foundDir) {
						if ($options['createSubgals']) {
							$perms = $this->get_perm_object($destinationGalleryId, 'file gallery', $this->get_file_gallery_info($destinationGalleryId), false);
							if ($perms['tiki_p_create_file_galleries'] === 'y') {

								$new_info = $this->default_file_gallery();
								$new_info['name'] = $dir;
								$new_info['description'] = tr('Created by batch upload by user "%0" on %1', $user, $this->get_short_datetime($this->now, $user));
								$new_info['parentId'] = $destinationGalleryId;
								$new_info['user'] = $user;

								$newGalleryId = $this->replace_file_gallery($new_info);
								if ($newGalleryId) {
									$destinationGalleryId = $newGalleryId;
									$subgals = $this->getSubGalleries($galleryId, true, 'batch_upload_file_dir');
									$foundDir = true;
								} else {
									$feedback[] = '<span class="text-danger">' .
											tr('Upload was not successful for "%0"', $path_parts['basename']) .
											'<br>' . tr('Create gallery "%0" failed</span>', $dir);
									break;
								}
							} else {
								$feedback[] = '<span class="text-danger">' .
										tr('Upload was not successful for "%0"', $path_parts['basename']) .
										'<br>' . tr('No permsission to create gallery "%0"</span>', $dir);
								break;
							}
						} else {
							$feedback[] = '<span class="text-danger">' .
									tr('Upload was not successful for "%0"', $path_parts['basename']) .
									'<br>' . tr('Gallery "%0" not found</span>', $dir);
							break;
						}
					}
				}
				if (! $foundDir) {
					continue;
				}
			}

			$result = $this->handle_batch_upload(
					$destinationGalleryId,
					[
							'source' => $file,
							'size' => $filesize,
							'type' => $type,
							'name' => $path_parts['basename'],
					],
					$ext
			);

			if (isset($result['error'])) {
				$feedback[] = '<span class="text-danger">' . tr('Upload was not successful for "%0"', $path_parts['basename']) . '<br>(' . $result['error'] . ')</span>';
			} else {
				// if subToDesc is set, set description:
				if ($options['subToDesc']) {
					// get last subdir 'last' from 'some/path/last'
					$tmpDesc = preg_replace('/.*([^\/]*)\/([^\/]+)$/U', '$1', substr($file, strlen($prefs['fgal_batch_dir'])));
				} else {
					$tmpDesc = '';
				}
				// get filename
				$name = $path_parts['basename'];

				$fileId = $this->insert_file(
						$destinationGalleryId, $name, $tmpDesc, $name, $result['data'], $filesize, $type,
						$user, $result['fhash'], null, null, null, null, $options['deleteAfter'], null, $metadata
				);
				if ($fileId) {
					$feedback[] = tra('Upload was successful') . ': ' . $name;
					@unlink($file);    // seems to return false sometimes even if the file was deleted
					if (!file_exists($file)) {
						$feedback[] = sprintf(tra('File %s removed from Batch directory.'), $file);
					} else {
						$feedback[] = '<span class="text-danger">' . sprintf(tra('Impossible to remove file %s from Batch directory.'), $file) . '</span>';
					}
				}
			}
		}
		return $feedback;
	}

	/**
	 *	Takes a local file and prepares it for addition to a file gallery
	 *
	 * @param int $galleryId
	 * @param array $info		[source, size, type, name]
	 * @param string $ext		file extension
	 *
	 * @return array			[data,fhash[,error]]
	 */
	function handle_batch_upload($galleryId, $info, $ext = '')
	{
		$savedir = $this->get_gallery_save_dir($galleryId);

		$fhash = null;
		$data = null;

		if ($savedir) {
			$fhash = $this->find_unique_name($savedir, $info['name']);

			if (in_array($ext, array("m4a", "mp3", "mov", "mp4", "m4v", "pdf"))) {
				$fhash.= "." . $ext;
			}

			if (! rename($info['source'], $savedir . $fhash)) {
				if (is_writable(dirname($info['source']))) {
					return array('error' => tra('Cannot write to this file:') . $savedir . $fhash);
				} else {
					return array('error' => tra('Cannot move this file:') . $info['source']);
				}
			}
		} else {
			$data = file_get_contents($info['source']);

			if (false === $data) {
				return array('error' => tra('Cannot read file on disk.'));
			}
		}

		return array(
			'data' => $data,
			'fhash' => $fhash,
		);
	}

	/**
	 * build a complete list of all files in $prefs['fgal_batch_dir'] including all necessary file info
	 *
	 * @throws Exception
	 */
	function batchUploadFileList()
	{
		global $prefs;

		// get root dir
		$filedir = $prefs['fgal_batch_dir'];
		$filedir = rtrim($filedir, '/');

		$filelist = [];

		$files = $this->batchUploadDirContent($filedir);

		sort($files, SORT_NATURAL);

		// build file data array
		foreach ($files as $file) {

			// get file information
			$filesize = @filesize($file);

			$filelist[] = [
				'file' => $file,
				'size' => $filesize,
				'ext' => strtolower(substr($file, -(strlen($file) - 1 - strrpos($file, ".")))),
				'writable' => is_writable($file) && is_writable(dirname($file)),	// it's the parent dir perms needed to move the file
			];
		}

		return $filelist;
	}

	/**
	 * recursively get all files from all subdirectories
	 *
	 * @param $dir
	 * @return array
	 * @throws Exception
	 */
	private function batchUploadDirContent($dir)
	{
		$files = [];

		if (false === $allfile = scandir($dir)) {
			throw new Exception(tra("Invalid directory name"));
		}

		foreach ($allfile as $filefile) {
			if ('.' === $filefile{0}) {
				continue;
			}

			if (is_dir($dir . "/" . $filefile)) {
				$files = array_merge($this->batchUploadDirContent($dir . DIRECTORY_SEPARATOR . $filefile), $files);

			} elseif (!in_array(strtolower(substr($filefile, -(strlen($filefile) - strrpos($filefile, ".") - 1))), $this->disallowed_types)) {
				$files[] =  $dir . DIRECTORY_SEPARATOR . $filefile;
			}
		}
		return $files;
	}

}