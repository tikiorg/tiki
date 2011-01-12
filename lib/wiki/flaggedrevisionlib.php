<?php

class FlaggedRevisionLib extends TikiDb_Bridge
{
	function flag_revision($pageName, $version, $flag, $value)
	{
		global $user;
		global $histlib; require_once 'lib/wiki/histlib.php';

		if ($version_info = $histlib->get_version($pageName, $version)) {
			global $attributelib; require_once 'lib/attributes/attributelib.php';

			if ($prefs['feature_actionlog'] == 'y') {
				global $logslib; include_once('lib/logs/logslib.php');
				$logslib->add_action('Flagged', $pageName, 'wiki page', "flag=$flag&version=$version&value=$value");
			}

			$attribute = $this->get_attribute_for_flag($flag);
			$attributelib->set_attribute('wiki history', $version_info['historyId'], $attribute, $value);

			return true;
		} else {
			return false;
		}
	}

	function get_version_with($pageName, $flag, $value)
	{
		$result = $this->fetchAll('SELECT th.* FROM `tiki_history` th INNER JOIN `tiki_object_attributes` toa ON toa.`itemId` = `historyId` AND toa.`type` = ? WHERE toa.attribute = ? AND toa.value = ? AND th.pageName = ? ORDER BY `th`.`version` DESC', array(
			'wiki history',
			$this->get_attribute_for_flag($flag),
			$value,
			$pageName,
		), 1);

		$first = reset($result);
		return $first;
	}

	function page_requires_approval($pageName)
	{
		global $prefs, $tikilib;

		if ($prefs['flaggedrev_approval'] != 'y') {
			return false;
		}

		if ($prefs['feature_categories'] == 'y') {
			global $categlib; require_once 'lib/categories/categlib.php';
			$approvalCategories = $tikilib->get_preference('flaggedrev_approval_categories', array(), true);

			$objectCategories = $categlib->get_object_categories('wiki page', $pageName);

			return count(array_intersect($approvalCategories, $objectCategories)) > 0;
		}

		return false;
	}

	private function get_attribute_for_flag($flag)
	{
		return 'tiki.history.' . $flag;
	}
}

global $flaggedrevisionlib; $flaggedrevisionlib = new FlaggedRevisionLib;
