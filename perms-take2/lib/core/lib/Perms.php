<?php

class Perms
{
	private static $instance;

	private $prefix = '';
	private $groups = array();
	private $factories = array();

	private $hashes = array();

	public static function get( array $context = array() ) {
		require_once 'lib/core/lib/Perms/Accessor.php';
		$accessor = new Perms_Accessor;

		if( self::$instance ) {
			$accessor->setPrefix( self::$instance->prefix );
			$accessor->setGroups( self::$instance->groups );

			if( $resolver = self::$instance->getResolver( $context ) ) {
				$accessor->setResolver( $resolver );
			}
		}

		return $accessor;
	}

	public static function set( self $perms ) {
		self::$instance = $perms;
	}

	function setGroups( array $groups ) {
		$this->groups = $groups;
	}

	function setPrefix( $prefix ) {
		$this->prefix = $prefix;
	}

	function setResolverFactories( array $factories ) {
		$this->factories = $factories;
	}

	private function getResolver( array $context ) {
		$toSet = array();
		$resolver = null;

		foreach( $this->factories as $factory ) {
			$hash = $factory->getHash( $context );

			if( isset( $this->hashes[$hash] ) ) {
				$resolver = $this->hashes[$hash];
				break;
			} else {
				$toSet[] = $hash;
			}

			if( $resolver = $factory->getResolver( $context ) ) {
				break;
			}
		}

		if( ! $resolver ) {
			$resolver = false;
		}

		foreach( $toSet as $hash ) {
			$this->hashes[$hash] = $resolver;
		}

		return $resolver;
	}
}

