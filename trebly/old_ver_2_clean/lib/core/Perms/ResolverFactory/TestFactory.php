<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/ResolverFactory.php';

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
