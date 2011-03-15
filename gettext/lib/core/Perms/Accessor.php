<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/Resolver.php';

/**
 * Interface providing convenient access to permissions in
 * a resolver for a set of groups. The permissions can be
 * accessed on the resolver as properties.
 *
 * The globalize() method also allows to deploy the permissions
 * in their global variables.
 */
class Perms_Accessor implements ArrayAccess
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

	function from() {
		return $this->resolver->from();
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
			
			return $this->checkPermission( $name );
		} else {
			return false;
		}
	}

	private function checkPermission( $name ) {
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
	}

	function globalize( $permissions, $smarty = null, $sanitize = true ) {
		foreach( $permissions as $perm ) {
			if( $sanitize ) {
				$perm = $this->sanitize( $perm );
			}
			$val = $this->checkPermission( $perm ) ? 'y' : 'n';
			$GLOBALS[ $this->prefix . $perm ] = $val;

			if( $smarty ) {
				$smarty->assign( 'tiki_p_' . $perm, $val );
			}
		}
	}

	private function sanitize( $name ) {
		if( $this->prefix && $name{0} == $this->prefix{0} && strpos( $name, $this->prefix ) === 0 ) {
			return substr( $name, strlen( $this->prefix ) );
		} else {
			return $name;
		}
	}

	public function offsetGet( $name ) {
		return $this->__get( $name );
	}

	public function offsetSet( $name, $value ) {
	}

	public function offsetUnset( $name ) {
	}

	public function offsetExists( $name ) {
		return true;
	}
}
