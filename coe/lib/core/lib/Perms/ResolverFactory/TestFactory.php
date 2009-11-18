<?php

require_once 'lib/core/lib/Perms/ResolverFactory.php';

/**
 * Factory used in test cases to test fallbacks.
 */
class Perms_ResolverFactory_TestFactory implements Perms_ResolverFactory
{
	private $known;
	private $resolvers;

	function __construct( array $known, array $resolvers ) {
		$this->known = $known;
		$this->resolvers = $resolvers;
	}

	function bulk( array $baseContext, $bulkKey, array $values ) {
		return array();
	}

	function getHash( array $context ) {
		$parts = array();
		
		foreach( $this->known as $key ) {
			if( isset( $context[$key] ) ) {
				$parts[] = $context[$key];
			}
		}

		return implode( ':', $parts );
	}

	function getResolver( array $context ) {
		$hash = $this->getHash( $context );

		if( isset( $this->resolvers[$hash] ) ) {
			return $this->resolvers[$hash];
		}
	}
}
