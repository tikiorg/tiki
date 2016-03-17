<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Formula_Function_TrackerField extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$default = 0;
		$allowed = array( 'object', 'field' );

		if ( $extra = $element->getExtraValues($allowed) ) {
			$this->error(tr('Unexpected values: %0', implode(', ', $extra)));
		}

		$object = $element->object;

		if ( ! $object || count($object) != 2 ) {
			$this->error(tra('Item must be provided and contain one argument: type, object-id'));
		}

		$type = $this->evaluateChild($object[0]);
		$object = $this->evaluateChild($object[1]);

		$field = $element->field;

		if ( ! $field || count($field) != 1 ) {
			$this->error(tra('Field must be provided and contain one argument: field permanent name'));
		}
		$field = $field[0];

		if ($type != 'trackeritem') {
			return $default;
		}

		return $this->fetchValue($object, $field, $default);
	}

	protected function fetchValue($object, $field, $default)
	{
		$trklib = TikiLib::lib('trk');
		$item = $trklib->get_tracker_item($object);

		if (! $item) {
			return $default;
		}

		if (! $definition = Tracker_Definition::get($item['trackerId'])) {
			return $default;
		}

		if (! $field = $definition->getFieldFromPermName($field)) {
			return $default;
		}

		$fieldId = $field['fieldId'];

		if (! isset($item[$fieldId])) {
			return $default;
		}

		return $item[$fieldId];
	}
}

