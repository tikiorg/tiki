<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class AttributeLib extends TikiDb_Bridge
{
	private $attributes;

	function __construct() {
		$this->attributes = $this->table('tiki_object_attributes');
	}

	function get_attributes( $type, $objectId ) {
		return $this->attributes->fetchMap('attribute', 'value', array(
			'type' => $type,
			'itemId' => $objectId,
		));
	}
	
	/**
	 * The attribute must contain at least two dots and only lowercase letters.
	 */

	/**
	 * NAMESPACE management and attribute naming.
	 * Please see http://dev.tiki.org/Object+Attributes+and+Relations for guidelines on 
	 * attribute naming, and document new tiki.*.* names that you add 
	 * (also grep "set_attribute" just in case there are undocumented names already used)
	 */
	function set_attribute( $type, $objectId, $attribute, $value ) {
		if( false === $name = $this->get_valid( $attribute ) ) {
			return false;
		}

		if( $value == '' ) {
			$this->attributes->delete(array(
				'type' => $type,
				'itemId' => $objectId,
				'attribute' => $name,
			));
		} else {
			$this->attributes->insertOrUpdate(array('value' => $value), array(
				'type' => $type,
				'itemId' => $objectId,
				'attribute' => $name,
			));
		}


		return true;
	}

	private function get_valid( $name ) {
		$filter = TikiFilter::get('attribute_type');
		return $filter->filter( $name );
	}
}

global $attributelib;
$attributelib = new AttributeLib;

