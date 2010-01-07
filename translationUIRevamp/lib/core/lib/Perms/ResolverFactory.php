<?php

interface Perms_ResolverFactory
{
	function getHash( array $context );
	function getResolver( array $context );
	function bulk( array $baseContext, $bulkKey, array $values );
}

