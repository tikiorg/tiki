<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Files;

use FileGalLib;
use TikiLib;

/**
 * Allows to analyse the images from file gallery, to see if they exist and are stored in the right place
 */
class CheckFileGallery extends AbstractCheckGallery
{
	/**
	 * Process the analyses for File Galleries
	 *
	 * @return array
	 */
	public function analyse()
	{
		$usesDatabase = $this->areFilesStoredInDatabase();

		$galleryPath = $this->getPathOnDisk();
		$podcastPath = $this->getPathForPodcastOnDisk();

		$filesCountTotal = 0;
		$filesInDbCount = 0;
		$filesInDiskCount = 0;
		$podcastFilesInDiskCount = 0;

		$filesToCheckOnDisk = [];
		$podcastFilesToCheckOnDisk = [];

		$galleryList = $this->getGalleryList();

		foreach ($galleryList as $galleryInfo) {
			$gallery = $galleryInfo['id'];
			$isPodcast = $galleryInfo['isPodcast'];

			$fileList = $this->getFileList($gallery);
			$filesCount = count($fileList);
			$filesCountTotal += $filesCount;

			foreach ($fileList as $file) {
				if ($isPodcast) {
					$podcastFilesInDiskCount++;
					$podcastFilesToCheckOnDisk[] = [
						'id' => $file['fileId'],
						'name' => $file['path'],
						'path' => $podcastPath,
						'size' => $file['filesize'],
					];
				} elseif ($file['path']) {
					$filesInDiskCount++;
					$filesToCheckOnDisk[] = [
						'id' => $file['fileId'],
						'name' => $file['path'],
						'path' => $galleryPath,
						'size' => $file['filesize'],
					];
				} else {
					$filesInDbCount++;
				}
			}
		}

		$filesOnDisk = $this->listFilesInDirectory($galleryPath);

		$podcastFilesOnDisk = $this->listFilesInDirectory($podcastPath);

		$extraFilter = null;
		if ($galleryPath === $podcastPath) {
			$extraFilter = function ($files) use ($podcastFilesToCheckOnDisk) {
				return $this->filterFiles($files, $podcastFilesToCheckOnDisk);
			};
		}

		list($missing, $mismatch, $unknown) = $this->matchFileList($filesToCheckOnDisk, $filesOnDisk, [], $extraFilter);

		$extraFilterPodcast = null;
		if ($galleryPath === $podcastPath) {
			$extraFilterPodcast = function ($files) use ($filesToCheckOnDisk, $unknown) {
				$filesToBeRemoved = array_merge($filesToCheckOnDisk, $unknown);
				return $this->filterFiles($files, $filesToBeRemoved);
			};
		}

		list($podcastMissing, $podcastMismatch, $podcastUnknown) = $this->matchFileList($podcastFilesToCheckOnDisk, $podcastFilesOnDisk, [], $extraFilterPodcast);

		$missing = array_merge($missing, $podcastMissing);
		$mismatch = array_merge($mismatch, $podcastMismatch);
		$unknown = array_merge($unknown, $podcastUnknown); //todo

		return [
			'usesDatabase' => $usesDatabase,
			'path' => [$galleryPath, $podcastPath],
			'mixedLocation' => ($filesInDbCount !== 0 && $filesInDiskCount !== 0) ? true : false,
			'count' => $filesCountTotal,
			'countFilesDb' => $filesInDbCount,
			'countFilesDisk' => $filesInDiskCount + $podcastFilesInDiskCount,
			'issueCount' => count($missing) + count($mismatch) + count($unknown),
			'missing' => $missing,
			'mismatch' => $mismatch,
			'unknown' => $unknown,
		];
	}

	/**
	 * Helper Method to filter files from the results that are part of other result set
	 *
	 * @param array $files
	 * @param array $filesToRemove
	 * @return array
	 */
	protected function filterFiles($files, $filesToRemove)
	{
		$indexed = [];
		foreach ($filesToRemove as $item) {
			$indexed[$item['name']] = $item;
		}
		return array_filter($files, function ($item) use ($indexed) {
			return ! array_key_exists($item['name'], $indexed);
		});
	}

	/**
	 * The the list of galleries, and if they are of type podcast
	 *
	 * @return array
	 */
	protected function getGalleryList()
	{
		/** @var FileGalLib $fileGalleryLib */
		$fileGalleryLib = TikiLib::lib('filegal');
		$fileGalleryLib->getGalleryIds($galleryIdList, -1, 'list');

		$galleryList = [];
		foreach ($galleryIdList as $gallery) {
			$galleryList[] = ['id' => $gallery, 'isPodcast' => $fileGalleryLib->isPodCastGallery($gallery)];
		}
		return $galleryList;
	}

	/**
	 * Get the list of files for a given gallery
	 *
	 * @param int $gallery
	 * @return array
	 */
	protected function getFileList($gallery)
	{
		/** @var FileGalLib $fileGalleryLib */
		$fileGalleryLib = TikiLib::lib('filegal');
		$list = $fileGalleryLib->get_files_info($gallery, null, false, false, -1);

		return $list;
	}

	/**
	 * Checks if the configuration is to store files in DB or disk
	 *
	 * @return bool
	 */
	protected function areFilesStoredInDatabase()
	{
		global $prefs;
		$usesDatabase = true;
		if ($prefs['fgal_use_db'] != 'y') {
			$usesDatabase = false;
		}

		return $usesDatabase;
	}

	/**
	 * Returns where to store files on disk
	 *
	 * @return string
	 */
	protected function getPathOnDisk()
	{
		global $prefs;
		return $prefs['fgal_use_dir'];
	}

	/**
	 * Return where to store podcast files on disk
	 *
	 * @return string
	 */
	protected function getPathForPodcastOnDisk()
	{
		global $prefs;
		return $prefs['fgal_podcast_dir'];
	}
}
