<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_CalendarItem extends Tracker_Field_JsCalendar
{
	/**
	 * @return array field definition
	 */
	public static function getTypes()
	{
		$def = [
			'CAL' => [
				'name' => tr('Date and Time (Calendar Item)'),
				'description' => tr('Associates calendar items to tracker items.'),
				'prefs' => ['trackerfield_calendaritem'],
				'tags' => ['advanced'],
				'default' => 'n',
				'supported_changes' => ['f', 'j', 'CAL'],
				'params' => [
					'calendarId' => [
						'name' => tr('Calendar Id'),
						'description' => tr('Calendar to use for associated events'),
						'filter' => 'int',
						'profile_reference' => 'calendar',

					],
				],
			],
		];

		$parentDef = parent::getTypes();

		$def['CAL']['params'] = array_merge($def['CAL']['params'], $parentDef['j']['params']);

		return $def;
	}

	/**
	 * Tracker_Field_CalendarItem constructor.
	 * @param array $fieldInfo
	 * @param array $itemData
	 * @param Tracker_Definition $trackerDefinition
	 */
	function __construct($fieldInfo, $itemData, $trackerDefinition)
	{
		if ($fieldInfo['options_map']['calendarId']) {
			TikiLib::lib('relation')->add_relation(
				'tiki.calendar.attach',
				'tracker',
				$trackerDefinition->getConfiguration('trackerId'),
				'calendar',
				$fieldInfo['options_map']['calendarId'],
				true
			);
		}

		parent::__construct($fieldInfo, $itemData, $trackerDefinition);
	}

	function handleSave($value, $oldValue)
	{
		$calendarId = $this->getOption('calendarId');

		if ($calendarId) {
			global $user, $language;

			/** @var CalendarLib $calendarlib */
			$calendarlib = TikiLib::lib('calendar');
			/** @var AttributeLib $attributelib */
			$attributelib = TikiLib::lib('attribute');
			/** @var TrackerLib $trklib */
			$trklib = TikiLib::lib('trk');

			$itemId = $this->getItemId();
			$trackerId = $this->getConfiguration('trackerId');
			$name = $trklib->get_isMain_value($trackerId, $itemId);;

			$calitemId = $attributelib->get_attribute('tracker_item', $itemId, 'tiki.calendar.item');
			$new = ! $calitemId;

			// save the event whether new or not as start time or the title/name might have changed
			$calitemId = $calendarlib->set_item($user, $calitemId, [
				'calendarId' => $calendarId,
				'start' => $value,
//					'end',
//					'locationId',
//					'categoryId',
//					'nlId',
//					'priority',
//					'status',
//					'url',
				'lang' => $language,
				'name' => $name,
//					'description',
//					'user',
//					'created',
//					'lastmodif',
//					'allday',
//					'recurrenceId',
//					'changed'
			]);

			if ($new) {	// added a new one?
				$attributelib->set_attribute('tracker_item', $itemId, 'tiki.calendar.item', $calitemId);
			}
			//$itemInfo = $calendarlib->get_item($calitemId);
		}

		return array(
			'value' => $value,
		);
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();

		$calitemId = TikiLib::lib('attribute')->get_attribute('tracker_item', $this->getItemId(), 'tiki.calendar.item');

		return array(
			$baseKey => $typeFactory->timestamp($this->getValue(), $this->getOption('datetime') == 'd'),
			"{$baseKey}_calitemid" => $typeFactory->numeric($calitemId),
		);
	}

	function getFieldData(array $requestData = array())
	{
		return parent::getFieldData($requestData);
	}

	function renderInput($context = array())
	{
		return parent::renderInput($context);
	}

	function isValid($ins_fields_data)
	{
		return parent::isValid($ins_fields_data);
	}
}

