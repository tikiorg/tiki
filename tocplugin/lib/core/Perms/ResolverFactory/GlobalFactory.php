<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * The global ResolverFactory is used as the fallback factory. It provides
 * a constant hash (so it will be queries only once) and obtains the global
 * permissions for all groups.
 *
 * Bulk does not apply to this factory.
 */
class Perms_ResolverFactory_GlobalFactory implements Perms_ResolverFactory
{
	function getHash( array $context )
	{
		return 'global';
	}

	function getResolver( array $context )
	{
		$perms = array();
		$db = TikiDb::get();

		$result = $db->fetchAll('SELECT `groupName`,`permName` FROM users_grouppermissions');
		foreach ( $result as $row ) {
			$group = $row['groupName'];
			$perm = $this->sanitize($row['permName']);

			if ( ! isset($perms[$group]) ) {
				$perms[$group] = array();
			}

			$perms[$group][] = $perm;
		}

		return new Perms_Resolver_Static($perms);
	}

	function bulk( array $baseContext, $bulkKey, array $values )
	{
		return array();
	}

	private function sanitize( $name )
	{
		if ( strpos($name, 'tiki_p_') === 0 ) {
			return substr($name, strlen('tiki_p_'));
		} else {
			return $name;
		}
	}
}
