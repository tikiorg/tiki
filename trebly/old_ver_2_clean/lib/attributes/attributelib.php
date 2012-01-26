<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class AttributeLib extends TikiDb_Bridge
{
	function get_attributes( $type, $objectId ) {
		return $this->fetchMap( 'SELECT `attribute`, `value` FROM `tiki_object_attributes` WHERE `type` = ? AND `itemId` = ?', array( $type, $objectId ) );
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
			$this->query( 'DELETE FROM `tiki_object_attributes` WHERE `type` = ? AND `itemId` = ? AND `attribute` = ?',
				array( $type, $objectId, $name ) );
		} else {
			$this->query( 'INSERT INTO `tiki_object_attributes` (`type`, `itemId`, `attribute`, `value`) VALUES( ?, ?, ?, ? ) ON DUPLICATE KEY UPDATE `value` = ?',
				array( $type, $objectId, $name, $value, $value ) );
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

