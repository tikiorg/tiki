<?php

interface Perms_ResolverFactory
{
	function getHash( array $context );
	function getResolver( array $context );
}

