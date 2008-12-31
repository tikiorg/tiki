<?php

interface AccessControl_Resolver
{
	const UNDEFINED = -1;
	const ALLOWED = 1;
	const DENIED = 0;

	/**
	 * Indicates if the permission is available based on the
	 * arguments provided with the query.
	 * 
	 * @param string Permission name begining with tiki_p_
	 * @param array List of arguments to the query. Argument keys
	 *              may be strings like 'user', 'object', etc.
	 * @return int One of the constants UNDEFINED, ALLOWED, DENIED
	 */
	function hasPermission( $permission, array $arguments );
}

?>
