<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Trackerrender extends Search_Formatter_ValueFormatter_Abstract
{
	private $list_mode = 'n';
	private $cancache = null;
	private $editable = false;
	private $group = false;

	function __construct($arguments)
	{
		if (isset($arguments['list_mode']) && $arguments['list_mode'] !== 'n') {
			if ($arguments['list_mode'] == 'csv') {
				$this->list_mode = 'csv';
			} else {
				$this->list_mode = 'y';
			}
		}

		if (isset($arguments['editable'])) {
			$parts = explode(' ', $arguments['editable']);
			$editable = array_shift($parts);
			$group = array_shift($parts);

			if (in_array($editable, array('block', 'inline', 'dialog'))) {
				$this->editable = $editable;
				$this->group = $group;
			}
		}
	}

	function render($name, $value, array $entry)
	{
		if ($name === 'tracker_status') {
			switch ($value) {
			case 'o':
				$status = 'open';
				break;
			case 'p':
				$status = 'pending';
				break;
			default:
			case 'c':
				$status = 'closed';
				break;
			}

			$smarty = TikiLib::lib('smarty');
			$smarty->loadPlugin('smarty_function_icon');
			return smarty_function_icon(['name' => 'status-' . $status, 'iclass' => 'tips', 'ititle' => ':'
				. ucfirst($status) ], $smarty);
		} elseif (substr($name, 0, 14) !== 'tracker_field_') {
			return $value;
		}

		$tracker = Tracker_Definition::get($entry['tracker_id']);
		if (!is_object($tracker)) {
			return $value;
		}
		$field = $tracker->getField(substr($name, 14));

		// check translations of multilingual fields
		global $prefs;
		if ($field['isMultilingual'] === 'y' && isset($entry[$name . '_' . $prefs['language']])) {
			$name = $name . '_' . $prefs['language'];
			$value = $entry[$name];
		}
		// TextArea fields need the raw wiki syntax here for it to get wiki parsed if necessary
		if ($field['type'] === 'a' && isset($entry[$name . '_raw'])) {
			$value = $entry[$name . '_raw'];
		} elseif( in_array($field['type'], array('f', 'j')) ) {
			$formatter = new Search_Formatter_ValueFormatter_Datetime();
			$value = $formatter->timestamp($value);
		}
		$field['value'] = $value;

		$this->cancache = ! in_array($field['type'], array('STARS', 's'));	// don't cache ratings fields

		if ($this->editable) {
			// Caching breaks inline editing
			$this->cancache = false;
		}

		$item = array();
		if ($entry['object_type'] == 'trackeritem') {
			$item['itemId'] = $entry['object_id'];
		}

		$trklib = TikiLib::lib('trk');
		$rendered = $trklib->field_render_value(
			array(
				'item' => $item,
				'field' => $field,
				'process' => 'y',
				'search_render' => 'y',
				'list_mode' => $this->list_mode,
				'editable' => $this->editable,
				'editgroup' => $this->group,
				'showpopup' => $field['isMain'],
			)
		);
		return '~np~' . $rendered . '~/np~';
	}

	function canCache()
	{
		if ($this->cancache === null) {
			trigger_error('Search_Formatter_ValueFormatter_Trackerrender->canCache() called before field rendered, assuming "true"');
			$this->cancache = true;
		}
		return $this->cancache;
	}
}

