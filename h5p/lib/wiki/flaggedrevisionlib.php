<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class FlaggedRevisionLib extends TikiDb_Bridge
{
	const ACTION = 'Flagged';

	function flag_revision($pageName, $version, $flag, $value)
	{
		global $prefs;
		$attributelib = TikiLib::lib('attribute');
		$histlib = TikiLib::lib('hist');

		if ($version_info = $histlib->get_version($pageName, $version)) {
			$tx = TikiDb::get()->begin();

			if ($prefs['feature_actionlog'] == 'y') {
				$logslib = TikiLib::lib('logs');
				$logslib->add_action(self::ACTION, $pageName, 'wiki page', "flag=$flag&version=$version&value=$value");
			}

			$attribute = $this->get_attribute_for_flag($flag);
			$attributelib->set_attribute('wiki history', $version_info['historyId'], $attribute, $value);

			require_once('lib/search/refresh-functions.php');
			refresh_index('pages', $pageName);
			refresh_index('pages', "$pageName~~latest");
			$tx->commit();

			return true;
		} else {
			return false;
		}
	}

	function get_version_with($pageName, $flag, $value)
	{
		$this->get_version_query($pageName, $flag, $value, $query, $bindvars);

		$result = $this->fetchAll($query, $bindvars, 1);

		$first = reset($result);
		return $first;
	}

	function get_versions_with($pageName, $flag, $value)
	{
		$this->get_version_query($pageName, $flag, $value, $query, $bindvars, 'version');
		$result = $this->fetchAll($query, $bindvars);

		$versions = array();
		foreach ($result as $row) {
			$versions[] = $row['version'];
		}

		return $versions;
	}

	private function get_version_query($pageName, $flag, $value, & $query, & $bindvars, $fields = 'th.*')
	{
		// NOTE : These are out variables
		$query = 'SELECT ' . $fields . ' FROM `tiki_history` th INNER JOIN `tiki_object_attributes` toa ON toa.`itemId` = `historyId` AND toa.`type` = ? WHERE toa.attribute = ? AND toa.value = ? AND th.pageName = ? ORDER BY `th`.`version` DESC';

		$bindvars = array(
			'wiki history',
			$this->get_attribute_for_flag($flag),
			$value,
			$pageName,
		);
	}

	function page_requires_approval($pageName)
	{
		global $prefs, $tikilib;

		if ($prefs['flaggedrev_approval'] != 'y') {
			return false;
		}

		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$approvalCategories = $tikilib->get_preference('flaggedrev_approval_categories', array(), true);

			$objectCategories = $categlib->get_object_categories('wiki page', $pageName);

			return count(array_intersect($approvalCategories, $objectCategories)) > 0;
		}

		return false;
	}

	function find_approval_information($page, $version)
	{
		global $prefs;

		if ($prefs['feature_actionlog'] == 'y') {
			$logs = $this->table('tiki_actionlog');
			return $logs->fetchRow(
				array('user', 'lastModif', 'ip'),
				array(
					'action' => self::ACTION,
					'object' => $page,
					'objectType' => 'wiki page',
					'comment' => "flag=moderation&version=$version&value=OK",
				)
			);
		}
	}

	private function get_attribute_for_flag($flag)
	{
		return 'tiki.history.' . $flag;
	}
}

