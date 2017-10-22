<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Files;

use ImageGalsLib;
use TikiLib;

/**
 * Allows to analyse the images from image gallery, to see if they exist and are stored in the right place
 */
class CheckImageGallery extends AbstractCheckGallery
{
	/**
	 * Process the analyses for Image Galleries
	 *
	 * @return array
	 */
	public function analyse()
	{
		$imagesPerQuery = 100;
		$offset = 0;

		$usesDatabase = $this->areFilesStoredInDatabase();

		$galleryPath = $this->getPathOnDisk();

		$filesCountTotal = 0;
		$filesInDbCount = 0;
		$filesInDiskCount = 0;

		$filesToCheckOnDisk = [];

		$galleryList = $this->getGalleryIdList();

		foreach ($galleryList as $gallery) {
			list($imageList, $filesCount) = $this->getImageList($gallery, $offset, $imagesPerQuery);
			$filesCountTotal += $filesCount;

			do {
				foreach ($imageList as $image) {
					if ($image['path']) {
						$filesInDiskCount++;
						$filesToCheckOnDisk[] = [
							'id' => $image['imageId'],
							'name' => $image['path'],
							'path' => $galleryPath,
							'size' => $image['filesize'],
						];
					} else {
						$filesInDbCount++;
					}
				}
			} while (count($imageList) == $imagesPerQuery);
		}

		$filesOnDisk = $this->listFilesInDirectory($galleryPath);

		list($missing, $mismatch, $unknown) = $this->matchFileList($filesToCheckOnDisk, $filesOnDisk, ['.thumb']);

		return [
			'usesDatabase' => $usesDatabase,
			'path' => [$galleryPath],
			'mixedLocation' => ($filesInDbCount !== 0 && $filesInDiskCount !== 0) ? true : false,
			'count' => $filesCountTotal,
			'countFilesDb' => $filesInDbCount,
			'countFilesDisk' => $filesInDiskCount,
			'issueCount' => count($missing) + count($mismatch) + count($unknown),
			'missing' => $missing,
			'mismatch' => $mismatch,
			'unknown' => $unknown,
		];
	}

	/**
	 * The the list of Ids for galleries
	 *
	 * @return array
	 */
	protected function getGalleryIdList()
	{
		/** @var ImageGalsLib $imageGalleryLib */
		$imageGalleryLib = TikiLib::lib('imagegal');
		$galleryList = $imageGalleryLib->list_galleries(0, -1, 'name_desc', 'admin');

		$galleryIdList = array_map(
			function ($item) {
				return $item['galleryId'];
			},
			$galleryList['data']
		);

		return $galleryIdList;
	}

	/**
	 * Get the list of images for a given gallery
	 *
	 * @param int $gallery
	 * @param int $offset
	 * @param int $imagesPerQuery
	 * @return array
	 */
	protected function getImageList($gallery, $offset, $imagesPerQuery)
	{
		/** @var ImageGalsLib $imageGalleryLib */
		$imageGalleryLib = TikiLib::lib('imagegal');
		$imageList = $imageGalleryLib->list_images($offset, $imagesPerQuery, '', '', $gallery);

		return [$imageList['data'], $imageList['cant']];
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
		if ($prefs['gal_use_db'] != 'y') {
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
		return $prefs['gal_use_dir'];
	}
}
