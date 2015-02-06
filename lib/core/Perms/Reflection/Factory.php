<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Reflection_Factory
{
	private $fallback;
	private $registry = array();

	function register($type, $class)
	{
		$this->registry[$type] = $class;
	}

	function registerFallback( $class )
	{
		$this->fallback = $class;
	}

	function get( $type, $object )
	{
		if ( ! $class = $this->getRegistered($type) ) {
			$class = $this->fallback;
		}

		if ( $class ) {
			return new $class($this, $type, $object);
		}
	}

	private function getRegistered( $type )
	{
		if ( isset($this->registry[$type]) ) {
			return $this->registry[$type ];
		}
	}

	public static function getDefaultFactory()
	{
		$factory = new self;
		$factory->register('global', 'Perms_Reflection_Global');
		$factory->register('category', 'Perms_Reflection_Category');
		$factory->registerFallback('Perms_Reflection_Object');

		return $factory;
	}
}
