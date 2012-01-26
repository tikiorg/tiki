<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Trackerrender.php 36281 2011-08-17 15:53:24Z nkoth $

class Search_Formatter_ValueFormatter_Trackerrender implements Search_Formatter_ValueFormatter_Interface
{
	function render($name, $value, array $entry)
	{
		if (substr($name, 0, 14) !== 'tracker_field_') {
			return $value;
		}

		$tracker = Tracker_Definition::get($entry['tracker_id']);
		if (!is_object($tracker)) {
			return $value;
		}
		$field = $tracker->getField(substr($name, 14));
		$field['value'] = $value;

		$item = array();
		if ($entry['object_type'] == 'trackeritem') {
			$item['itemId'] = $entry['object_id'];
		}

		$trklib = TikiLib::lib('trk');
		return '~np~' . $trklib->field_render_value(array(
			'item' => $item,
			'field' => $field,
			'process' => 'y',
			'search_render' => 'y',
		)) . '~/np~';
	}
}

