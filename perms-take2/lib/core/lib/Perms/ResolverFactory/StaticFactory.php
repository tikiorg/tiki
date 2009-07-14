<?php

require_once 'lib/core/lib/Perms/ResolverFactory.php';

class Perms_ResolverFactory_StaticFactory implements Perms_ResolverFactory
{
	private $key;
	private $resolver;

	function __construct( $key, $resolver ) {
		$this->key = $key;
		$this->resolver = $resolver;
	}

	function getHash( array $context ) {
		return $this->key;
	}

	function getResolver( array $context ) {
		return $this->resolver;
	}
}

?>
