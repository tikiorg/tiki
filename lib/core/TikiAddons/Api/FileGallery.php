<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Api_FileGallery extends TikiAddons_Api
{
	protected static $parents = array();
	protected static $trackers = array();

	// overriding isInstalled in TikiAddons_Utilities
	function isInstalled($folder) {
		$installed1 = array_keys(self::$parents);
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		if (parent::isInstalled($folder) && in_array($folder, $installed1) ) {
			return true;
		} else {
			return false;
		}
	}

	static function setParents($folder, $parent) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$parents[$folder] = $parent;
		return true;
	}

	static function setTrackers($folder, $tracker) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$trackers[$folder] = $tracker;
		return true;
	}

	private function getFolderFromGallery($galleryId) {
		foreach (self::$parents as $folder => $info) {
			$candidateId = $this->getObjectId($folder, $info->ref, $info->profile);
			if ($candidateId == $galleryId) {
				return $folder;
			}
		}
		return false;
	}

	function mapGalleryId($galleryId, $itemId = 0) {
		if ($folder = $this->getFolderFromGallery($galleryId)) {
			if (!empty($itemId)) {
				$trackerId = $this->getObjectId($folder, self::$trackers[$folder]->trackerref, self::$parents[$folder]->profile);
				$fieldId = $this->getObjectId($folder, self::$trackers[$folder]->itemlinkref, self::$trackers[$folder]->profile);
				$organicGroupId = TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId);
				$galleryName = $folder . "_" . $organicGroupId;
				$galleryId = TikiLib::lib('filegal')->getGalleryId($galleryName, $galleryId);
			} else if (!empty($_REQUEST['organicgroup'])) {
				$galleryName = $folder . "_" . $_REQUEST['organicgroup'];
				$galleryId = TikiLib::lib('filegal')->getGalleryId($galleryName, $galleryId);
			}
		}
		return $galleryId;
	}

	function getParentGalleryId($token) {
		$folder = $this->getFolderFromToken($token);
		if ($this->isInstalled($folder)) {
			$galleryId = $this->getItemIdFromRef($token, self::$parents[$folder]);
		} else {
			$galleryId = 0;
		}
		return $galleryId;
	}
}
