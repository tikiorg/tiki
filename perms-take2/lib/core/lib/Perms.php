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

	public static function bulk( array $baseContext, $bulkKey, array $data, $dataKey = null ) {
		$remaining = array();

		foreach( $data as $entry ) {
			if( $dataKey ) {
				$value = $entry[$dataKey];
			} else {
				$value = $entry;
			}

			$remaining[] = $value;
		}

		self::$instance->loadBulk( $baseContext, $bulkKey, $remaining );
	}

	public static function filter( array $baseContext, $bulkKey, array $data, $dataKey, $permission ) {
		self::bulk( $baseContext, $bulkKey, $data, $dataKey );

		$valid = array();

		foreach( $data as $entry ) {
			$context = $baseContext;
			$context[$bulkKey] = $entry[$dataKey];

			$accessor = self::get( $context );
			if( $accessor->$permission ) {
				$valid[] = $entry;
			}
		}

		return $valid;
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

	private function loadBulk( $baseContext, $bulkKey, $data ) {
		foreach( $this->factories as $factory ) {
			$data = $factory->bulk( $baseContext, $bulkKey, $data );
		}
	}
}

