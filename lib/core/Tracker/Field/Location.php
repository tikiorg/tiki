<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
				'description' => tr('Enable a geographic location to be selected for the item and displayed on a map.'),
				'help' => 'Location Tracker Field',				
				'prefs' => array('trackerfield_location'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'use_as_item_location' => array(
						'name' => tr('Use as item location'),
						'description' => tr("When enabled, the field's value is recorded as the item's geolocation to be displayed on locator maps."),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 0,
					),
					'list_width' => array(
						'name' => tr('List View Width'),
						'description' => tr('Width of map in pixels when tracker items are shown in list view'),
						'filter' => 'int',
						'default' => 200,
						'legacy_index' => 1,
					),
					'list_height' => array(
						'name' => tr('List View Height'),
						'description' => tr('Height of map in pixels when tracker items are shown in list view'),
						'filter' => 'int',
						'default' => 200,
						'legacy_index' => 2,
					),
					'item_width' => array(
						'name' => tr('Item View Width'),
						'description' => tr('Width of map in pixels when a single tracker item is shown'),
						'filter' => 'int',
						'default' => 500,
						'legacy_index' => 3,
					),
					'item_height' => array(
						'name' => tr('Item View Height'),
						'description' => tr('Height of map in pixels when a single tracker item is shown'),
						'filter' => 'int',
						'default' => 400,
						'legacy_index' => 4,
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
			// Always use . as the decimal point in the value, and not comma as used some places
			$value = '';
			$value .= str_replace(',', '.', $parts[0]).',';
			$value .= str_replace(',', '.', $parts[1]).',';
			$value .= str_replace(',', '.', $parts[2]);

			return array(
				'value' => $value,
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

			$attributes = TikiLib::lib('attribute')->get_attributes('trackeritem', $this->getItemId());

			if (isset($attributes['tiki.icon.src'])) {
				TikiLib::lib('smarty')->loadPlugin('smarty_modifier_escape');
				$context['icon_data'] = ' data-icon-src="'. smarty_modifier_escape($attributes['tiki.icon.src']) .'"';
			} else {
				$context['icon_data'] = '';
			}

			return $this->renderTemplate('trackeroutput/location.tpl', $context);
		}
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}
}

