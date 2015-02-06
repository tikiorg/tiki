<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Api_Group extends TikiAddons_Api
{
	protected static $trackers = array();
	protected static $public_catroots = array();
	protected static $private_catroots = array();
	protected static $managementpages = array();
	protected static $homepages = array();

	// overriding isInstalled in TikiAddons_Utilities
	function isInstalled($folder) {
		$installed1 = array_keys(self::$trackers);
		$installed2 = array_keys(self::$public_catroots);
		$installed3 = array_keys(self::$private_catroots);
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		if (parent::isInstalled($folder) && in_array($folder, $installed1) && in_array($folder, $installed2) && in_array($folder, $installed3) ) {
			return true;
		} else {
			return false;
		}
	}

	static function setTracker($folder, $ref) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$trackers[$folder] = $ref;
		return true;
	}

	static function setPublicCatroot($folder, $ref) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$public_catroots[$folder] = $ref;
		return true;
	}

	static function setPrivateCatroot($folder, $ref) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$private_catroots[$folder] = $ref;
		return true;
	}

	static function setManagementPage($folder, $ref) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$managementpages[$folder] = $ref;
		return true;
	}

	static function setHomePage($folder, $ref) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$homepages[$folder] = $ref;
		return true;
	}

	function isOrganicGroup($token) {
		$folder = $this->getFolderFromToken($token);
		$installed = array_keys(self::$trackers);
		if (in_array($folder, $installed)) {
			return true;
		} else {
			return false;
		}
	}

	function getOrganicGroupBaseName($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		$ret = $this->getItemTitleFromToken($token, 'tracker', self::$trackers[$folder]);
		return $ret;
	}

	function getOrganicGroupName($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		$ret = $this->getItemTitleFromToken($token, 'tracker', self::$trackers[$folder]);
		if ($ret && strpos($token, 'managers')) {
			$ret .= " Leaders";
		} elseif ($ret && strpos($token, 'pending')) {
			$ret .= " (Awaiting Approval)";
		}
		return $ret;
	}

	function getOrganicGroupLeaderToken($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		$id = $this->getItemIdFromToken($token);
		return $folder . '_managers_' . $id;
	}

	function getOrganicGroupPendingToken($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		$id = $this->getItemIdFromToken($token);
		return $folder . '_pending_' . $id;
	}

	function getOrganicGroupBaseToken($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		$id = $this->getItemIdFromToken($token);
		$ret = $folder . '_' . $id;
		return $ret;
	}

	function getOrganicGroupLeaders($token)
	{
		if ($gname = $this->getOrganicGroupLeaderToken($token)) {
			return TikiLib::lib('user')->get_group_users($gname);
		} else {
			return '';
		}
	}

	private function getPublicOrganicGroupCats($token)
	{
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return array();
		}
		if ($id = $this->getItemIdFromRef($token, array(self::$public_catroots[$folder]))) {
			return array_diff(TikiLib::lib('categ')->get_category_descendants($id), array(self::$public_catroots[$folder]));
		} else {
			return array();
		}
	}

	private function getPrivateOrganicGroupCats($token)
	{
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return array();
		}
		if ($id = $this->getItemIdFromRef($token, self::$private_catroots[$folder])) {
			return array_diff(TikiLib::lib('categ')->get_category_descendants($id), array(self::$private_catroots[$folder]));
		} else {
			return array();
		}
	}

	private function getOrganicGroupCats($token)
	{
		$pubcats = $this->getPublicOrganicGroupCats($token);
		$pricats = $this->getPrivateOrganicGroupCats($token);
		return array_merge($pubcats, $pricats);
	}

	private function getAllOrganicGroupCats()
	{
		$ret = array();
		foreach (self::$public_catroots as $folder => $ref) {
			$objects = $this->getObjects($folder);
			$ret = array_merge($ret, array_diff(TikiLib::lib('categ')->get_category_descendants($objects[$ref]['id']), array($objects[$ref]['id'])));
		}
		foreach (self::$private_catroots as $folder => $ref) {
			$objects = $this->getObjects($folder);
			$ret = array_merge($ret, array_diff(TikiLib::lib('categ')->get_category_descendants($objects[$ref]['id']), array($objects[$ref]['id'])));
		}
		return $ret;
	}

	function getOrganicGroupCatsForUser($usr)
	{
		$validcats = $this->getAllOrganicGroupCats();
		if (empty($validcats)) {
			return array();
		}

		$cats = array();
		$groups = TikiLib::lib('user')->get_user_groups($usr);

		foreach ($groups as $g) {
			$catId = TikiLib::lib('categ')->get_category_id($g);
			if (in_array($catId, $validcats)) {
				$cats[] = $catId;
			}
		}
		return $cats;
	}

	function organicGroupIsPrivate($token) {
		if ($id = $this->getItemIdFromToken($token)) {
			$status = TikiLib::lib('trk')->get_item_status($id);
			if ($status == 'p') {
				return true;
			}
		}
		return false;
	}

	function getOrganicGroupInfoForItem($type, $id) {
		$pubcats = array();
		$pricats = array();
		foreach (self::$public_catroots as $folder => $ref) {
			$objects = $this->getObjects($folder);
			$pubcats = TikiLib::lib('categ')->get_object_categories($type, $id, $objects[$ref]['id']);
		}
		foreach (self::$private_catroots as $folder => $ref) {
			$objects = $this->getObjects($folder);
			$pricats = TikiLib::lib('categ')->get_object_categories($type, $id, $objects[$ref]['id']);
		}
		$cats = array_merge($pubcats, $pricats);
		if (count($cats) == 1) {
			$cat = reset($cats);
			$catname = TikiLib::lib('categ')->get_category_name($cat);
			$ogid = $this->getItemIdFromToken($catname);
		} else {
			$ogid = '';
			$cat = '';
		}
		return array('organicgroup' => $ogid, 'cat' => $cat);
	}

	function getGroupHomePage($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		if (!empty(self::$homepages[$folder])) {
			return self::$homepages[$folder];
		} else {
			return '';
		}
	}

	function getGroupManagementPage($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		if (!empty(self::$managementpages[$folder])) {
			return self::$managementpages[$folder];
		} else {
			return '';
		}
	}
}
