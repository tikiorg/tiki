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

	function set_attribute( $type, $objectId, $attribute, $value ) {
		if( false === $name = $this->get_valid( $attribute ) ) {
			return false;
		}

		$this->query( 'INSERT INTO `tiki_object_attributes` (`type`, `itemId`, `attribute`, `value`) VALUES( ?, ?, ?, ? ) ON DUPLICATE KEY UPDATE `value` = ?',
			array( $type, $objectId, $name, $value, $value ) );


		return true;
	}

	private function get_valid( $name ) {
		// Force to have at least two dots to scope the attribute name
		if( substr_count( $name, '.' ) < 2 ) {
			return false;
		}

		$name = strtolower( $name );
		$name = preg_replace( '/[^a-z\.]/', '', $name );

		return $name;
	}
}

global $attributelib;
$attributelib = new AttributeLib;

