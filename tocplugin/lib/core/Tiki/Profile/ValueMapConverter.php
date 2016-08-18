<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_ValueMapConverter
{
	private $map;
	private $implode;

	function __construct( $map, $implodeArray = false )
	{
		$this->map = $map;
		$this->implode = $implodeArray;
	}

	function convert( $value )
	{
		if ( is_array($value) ) {
			foreach ( $value as &$v ) {
				if ( isset( $this->map[$v] ) ) {
					$v = $this->map[$v]; 
				}
			}
			
			if ( $this->implode ) {
				return implode('', $value);
			} else {
				return $value;
			}
		} else {
			if ( isset( $this->map[$value] ) ) {
				return $this->map[$value];
			} else {
				return $value;
			}
		}
	}

	function reverse($key)
	{
		$tab = array_flip($this->map);

		if (isset($tab[$key])) {
			return $tab[$key];
		} else {
			return $key;
		}
	}

}
