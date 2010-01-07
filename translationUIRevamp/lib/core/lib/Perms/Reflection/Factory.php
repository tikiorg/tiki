<?php

require_once 'lib/core/lib/Perms/Reflection/Object.php';
require_once 'lib/core/lib/Perms/Reflection/Global.php';
require_once 'lib/core/lib/Perms/Reflection/Category.php';

class Perms_Reflection_Factory
{
	private $fallback;
	private $registry = array();

	function register( $type, $class ) {
		$this->registry[ $type ] = $class;
	}

	function registerFallback( $class ) {
		$this->fallback = $class;
	}

	function get( $type, $object ) {
		if( ! $class = $this->getRegistered( $type ) ) {
			$class = $this->fallback;
		}

		if( $class ) {
			return new $class( $this, $type, $object );
		}
	}

	private function getRegistered( $type ) {
		if( isset( $this->registry[ $type ] ) ) {
			return $this->registry[ $type ];
		}
	}

	public static function getDefaultFactory() {
		$factory = new self;
		$factory->register( 'global', 'Perms_Reflection_Global' );
		$factory->register( 'category', 'Perms_Reflection_Category' );
		$factory->registerFallback( 'Perms_Reflection_Object' );

		return $factory;
	}
}
