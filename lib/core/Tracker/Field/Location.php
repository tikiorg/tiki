<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for location/map/gmap
 * 
 * Letter key: ~G~
 *
 */
class Tracker_Field_Location extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'G' => array(
				'name' => tr('Location'),
				'description' => tr('Allows to select a geolocation for the item and displays it on a map.'),
				'params' => array(
					'use_as_item_location' => array(
						'name' => tr('Use as item location'),
						'description' => tr('When enabled, records the field\'s value as the item\'s geolocation to be displayed on locator maps.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		if (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue();
		}
		
		$parts = explode(',', $value);
		$parts = array_map('floatval', $parts);

		if (count($parts) >= 2) {
			return array(
				'value' => implode(',', $parts),
				'x' => $parts[0],
				'y' => $parts[1],
				'z' => isset($parts[2]) ? $parts[2] : 0,
			);
		} else {
			return array(
				'value' => '',
				'x' => null,
				'y' => null,
				'z' => null,
			);
		}
	}

	function renderInput($context = array())
	{
		TikiLib::lib('header')->add_map();
		return $this->renderTemplate('trackerinput/location.tpl', $context);
	}

	function renderOutput($context = array())
	{
		if ($context['list_mode'] === 'csv') {
			return $this->getConfiguration('value');
		} else {
			TikiLib::lib('header')->add_map();
			return $this->renderTemplate('trackeroutput/location.tpl', $context);
		}
	}

	function import($value)
	{
		return $value;
	}

	function export($value)
	{
		return $value;
	}

	function importField(array $info)
	{
		return $info;
	}
}

