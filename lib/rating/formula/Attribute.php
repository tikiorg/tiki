<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Formula_Function_Attribute extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$allowed = array( 'object', 'default', 'property' );

		if ( $extra = $element->getExtraValues($allowed) ) {
			$this->error(tr('Unexpected values: %0', implode(', ', $extra)));
		}

		$object = $element->object;

		if ( ! $object || count($object) != 2 ) {
			$this->error(tra('Object must be provided and contain two arguments: type and object'));
		}

		$type = $this->evaluateChild($object[0]);
		$object = $this->evaluateChild($object[1]);

		if ( ( $property = $element->property ) && count($property) == 1 ) {
			$property = $property[0];
		} else {
			$this->error(tra('Invalid property.'));
		}

		if ($property instanceof Math_Formula_Element) {
			$property = $this->evaluateChild($property);
		}

		if ( $type == 'wiki page' && is_numeric($object) ) {
			$tikilib = TikiLib::lib('tiki');
			$object = $tikilib->get_page_name_from_id($object);
		}

		$attributelib = TikiLib::lib('attribute');
		$values = $attributelib->get_attributes($type, $object);

		// Attributes are always lowercase
		$property = strtolower($property);

		if ( isset( $values[$property] ) ) {
			return $values[$property];
		} elseif ( ( $default = $element->default ) && count($default) == 1 ) {
			return $this->evaluateChild($default[0]);
		} else {
			return 0;
		}
	}
}

