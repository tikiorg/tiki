<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Api_NavBar extends TikiAddons_Api
{
	protected static $templates = array();

	// overriding isInstalled in TikiAddons_Utilities
	function isInstalled($folder) {
		$installed1 = array_keys(self::$templates);
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		if (parent::isInstalled($folder) && in_array($folder, $installed1) ) {
			return true;
		} else {
			return false;
		}
	}

	static function setNavBar($folder, $tpl) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$templates[$folder] = $tpl;
		return true;
	}


	function getNavBar($token, $from = '') {
		$smarty = TikiLib::lib('smarty');

		$folder = $this->getFolderFromToken($token);

		if (!$this->isInstalled($folder)) {
			return '';
		}

		if ($id = $this->getItemIdFromToken($token)) {
			$smarty->assign('groupTrackerItemId', $id);
			$_REQUEST['organicgroup'] = $id;
		} elseif (isset($_REQUEST['organicgroup'])) {
			$smarty->assign('groupTrackerItemId', $_REQUEST['organicgroup']);
		}

		if (!isset($_REQUEST['organicgroup']) && !empty($_REQUEST['page'])) {
			$info = $this->getOrganicGroupInfoForItem('wiki page', $_REQUEST['page']);
			$cat = $info['cat'];
			$ogid = $info['organicgroup'];
			$smarty->assign('groupTrackerItemId', $ogid);
			$_REQUEST['organicgroup'] = $ogid;
			if (!isset($_REQUEST['cat'])) {
				$_REQUEST['cat'] = $cat;
			}
		}

		if (!isset($_REQUEST['organicgroup']) && !empty($_REQUEST['itemId'])) {
			$info = $this->getOrganicGroupInfoForItem('trackeritem', $_REQUEST['itemId']);
			$cat = $info['cat'];
			$ogid = $info['organicgroup'];
			$smarty->assign('groupTrackerItemId', $ogid);
			$_REQUEST['organicgroup'] = $ogid;
			if (!isset($_REQUEST['cat'])) {
				$_REQUEST['cat'] = $cat;
			}
		}

		if (!empty($_REQUEST['organicgroup']) && empty($_REQUEST['cat'])) {
			$ogname = 'syn_organicgrp_' . $_REQUEST['organicgroup'];
			$cat = \TikiLib::lib('categ')->get_category_id($ogname);
			$_REQUEST['cat'] = $cat;
		}

		$approvalCount = TikiLib::lib('user')->nb_users_in_group($folder . "_" . $_REQUEST['organicgroup']);
		$smarty->assign('groupapprovalcount', $approvalCount);

		$smarty->assign('groupnavfrom', $from);
		return $smarty->fetch(self::$templates[$folder]);
	}

}