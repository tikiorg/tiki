<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Definition
{
	static $definitions = array();

	private $trackerInfo;
	private $fields;

	public static function get($trackerId)
	{
		$trackerId = (int) $trackerId;

		if (isset(self::$definitions[$trackerId])) {
			return self::$definitions[$trackerId];
		}

		$trklib = TikiLib::lib('trk');
		$tracker_info = $trklib->get_tracker($trackerId);

		$definition = false;

		if ($tracker_info) {
			if ($t = $trklib->get_tracker_options($trackerId)) {
				$tracker_info = array_merge($tracker_info, $t);
			}

			$definition = new self($tracker_info);
		}

		return self::$definitions[$trackerId] = $definition;
	}

	private function __construct($trackerInfo)
	{
		$this->trackerInfo = $trackerInfo;
	}

	function getInformation()
	{
		return $this->trackerInfo;
	}

	function getConfiguration($key, $default = false)
	{
		return isset($this->trackerInfo[$key]) ? $this->trackerInfo[$key] : $default;
	}

	function getFields()
	{
		if ($this->fields) {
			return $this->fields;
		}

		$trklib = TikiLib::lib('trk');
		$trackerId = $this->trackerInfo['trackerId'];
		$fields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc');

		return $this->fields = $fields['data'];
	}

	function getField($id)
	{
		foreach ($this->getFields() as $f) {
			if ($f['fieldId'] == $id) {
				return $f;
			}
		}
	}

	function getFieldFromName($name)
	{
		foreach ($this->getFields() as $f) {
			if ($f['name'] == $name) {
				return $f;
			}
		}
	}

	function getPopupFields()
	{
		if (!empty($this->trackerInfo['showPopup'])) {
			return explode(',', $this->trackerInfo['showPopup']);
		} else {
			return array();
		}
	}

	function getAuthorField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'u'
				&& isset($field['options'][0]) && $field['options'][0] == 1
				&& isset($this->trackerInfo["writerCanModify"]) && $this->trackerInfo["writerCanModify"] == 'y') {

				return $field['fieldId'];
			}
		}
	}

	function getWriterField()
	{
		foreach ($this->getFields() as $field) {
			if (in_array($field['type'], array('u', 'I'))
				&& isset($field['options'][0]) && $field['options'][0] == 1) {
				return $field['fieldId'];
			}
		}
	}

	function getUserField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'u'
				&& isset($field['options'][0]) && $field['options'][0] == 1) {

				return $field['fieldId'];
			}
		}
	}

	function getGeolocationField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'G' && isset($field['options_array'][0]) && $field['options_array'][0] == 'y') {
				return $field['fieldId'];
			}
		}
	}

	function getWriterGroupField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'g'
				&& isset($field['options'][0]) && $field['options'][0] == 1) {
				return $field['fieldId'];
			}
		}
	}

	function getRateField()
	{
		foreach ($this->getFields() as $field) {
			if ($field['type'] == 's' && $field['name'] == 'Rating') {
				return $field['fieldId'];
			}
		}
	}

	function getCategorizedFields()
	{
		$out = array();

		foreach ($this->getFields() as $field) {
			if ($field['type'] == 'e') {
				$out[] = $field['fieldId'];
			}
		}

		return $out;
	}
	
	/**
	 * Get the name of the item user if any.
	 * A item user is defined if a 'user selector' field
	 * exist for this tracker and it has one user selected.
	 * 
	 * @param int $itemId
	 * @return string item user name
	 */
	function getItemUser($itemId)
	{
		global $trklib;
		return $trklib->get_item_creator($this->trackerInfo['trackerId'], $itemId);
	}
}

