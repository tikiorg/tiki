<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class Services_Object_Controller
{
	public static function supported()
	{
		global $prefs;
		$supported = array();
		
		if ($prefs['feature_trackers'] == 'y') {
			$supported[] = 'trackeritem';
		}

		return $supported;
	}

	function action_infobox($input)
	{
		$type = $input->type->none();
		if (! in_array($type, self::supported())) {
			throw new Services_Exception_NotAvailable(tr('No box available for %0', $type));
		}

		return array(
			'type' => $type,
			'object' => $input->object->none(),
			'content' => $this->{'infobox_' . $type}($input),
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
		return $smarty->fetch('object/infobox/trackeritem.tpl');
	}
}

