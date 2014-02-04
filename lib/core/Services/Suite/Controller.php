<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Suite_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('suite_jitsi_provision');
	}

	public static function getJitsiUrl()
	{
		$url = TikiLib::lib('service')->getUrl([
			'controller' => 'suite',
			'action' => 'jitsi',
			'username' => '${username}',
		]);
		return TikiLib::tikiUrl($url);
	}

	function action_jitsi($input)
	{
		global $prefs;
		$config = $prefs['suite_jitsi_configuration'];
		$config = str_replace(['${username}'], [
			$input->username->none(),
		], $config);
		return array(
			'configuration' => $config,
		);
	}

	private function infobox_trackeritem($input)
	{
		$itemId = $input->object->int();
		$trklib = TikiLib::lib('trk');

		if (! $item = $trklib->get_tracker_item($itemId)) {
			throw new Services_Exception_NotFound;
		}

		if (! $definition = Tracker_Definition::get($item['trackerId'])) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($item);

		if (! $itemObject->canView()) {
			throw new Services_Exception('Permission denied', 403);
		}

		$fields = array();
		foreach ($definition->getPopupFields() as $fieldId) {
			if ($itemObject->canViewField($fieldId) && $field = $definition->getField($fieldId)) {
				$fields[] = $field;
			}
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('fields', $fields);
		$smarty->assign('item', $item);
		$smarty->assign('can_modify', $itemObject->canModify());
		$smarty->assign('can_remove', $itemObject->canRemove());
		$smarty->assign('mode', $input->mode->text() ? $input->mode->text() : '');	// default divs mode
		return $smarty->fetch('object/infobox/trackeritem.tpl');
	}

	private function infobox_activity($input)
	{
		$itemId = $input->object->int();
		$lib = TikiLib::lib('activity');
		$info = $lib->getActivity($itemId);

		if (! $info) {
			throw new Services_Exception_NotFound;
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('activity', $itemId);
		return $smarty->fetch('object/infobox/activity.tpl');
	}

	/**
	 * Generic function to allow consistently formatted errors from javascript using ErrorReportLib
	 *
	 * @param $input jit filtered input object
	 */
	function action_report_error($input)
	{
		TikiLib::lib('errorreport')->report($input->message->text());
		TikiLib::lib('errorreport')->send_headers();
	}
}

