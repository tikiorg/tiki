<?php

require_once 'lib/core/lib/Perms/Resolver.php';

/**
 * Interface providing convenient access to permissions in
 * a resolver for a set of groups. The permissions can be
 * accessed on the resolver as properties.
 *
 * The globalize() method also allows to deploy the permissions
 * in their global variables.
 */
class Perms_Accessor
{
	private $resolver;
	private $prefix = '';
	private $context = array();
	private $groups = array();
	private $checkSequence = null;

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

	function setContext( array $context ) {
		$this->context = $context;
	}

	function getContext() {
		return $this->context;
	}

	function setCheckSequence( array $sequence ) {
		$this->checkSequence = $sequence;
	}

	function __get( $name ) {

		if( $this->resolver ) {
			$name = $this->sanitize( $name );
			
			if( $this->checkSequence ) {
				foreach( $this->checkSequence as $check ) {
					if( $check->check( $this->resolver, $this->context, $name, $this->groups ) ) {
						return true;
					}
				}

				return false;
			} else {
				return $this->resolver->check( $name, $this->groups );
			}
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

	function smartify( $smarty, $permissions ) {
		foreach( $permissions as $perm ) {
			$perm = $this->sanitize( $perm );
			$smarty->assign( 'tiki_p_' . $perm, $this->$perm ? 'y' : 'n' );
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
