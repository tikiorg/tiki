<?php

require_once 'lib/core/lib/Perms/Resolver.php';

class Perms_Accessor
{
	private $resolver;
	private $prefix = '';
	private $groups = array();

	function setPrefix( $prefix ) {
		$this->prefix = $prefix;
	}

	function getPrefix() {
		return $this->prefix;
	}

	function setGroups( array $groups ) {
		$this->groups = $groups;
	}

	function getGroups() {
		return $this->groups;
	}

	function setResolver( Perms_Resolver $resolver ) {
		$this->resolver = $resolver;
	}

	function getResolver() {
		return $this->resolver;
	}

	function __get( $name ) {

		if( $this->resolver ) {
			$name = $this->sanitize( $name );
			
			return $this->resolver->check( $name, $this->groups );
		} else {
			return false;
		}
	}

	function globalize( $permissions ) {
		foreach( $permissions as $perm ) {
			$perm = $this->sanitize( $perm );
			$GLOBALS[ $this->prefix . $perm ] = $this->$perm ? 'y' : 'n';
		}
	}

	private function sanitize( $name ) {
		if( $this->prefix && strpos( $name, $this->prefix ) === 0 ) {
			return substr( $name, strlen( $this->prefix ) );
		} else {
			return $name;
		}
	}
}

?>
