<?php

require_once 'lib/core/lib/Perms/ResolverFactory.php';

class Perms_ResolverFactory_ObjectFactory implements Perms_ResolverFactory
{
	function getHash( array $context ) {
		if( isset( $context['type'], $context['object'] ) ) {
			return 'object:' . $context['type'] . ':' . strtolower( $context['object'] );
		} else {
			return '';
		}
	}

	function bulk( array $baseContext, $bulkKey, array $values ) {
		return array();
	}

	function getResolver( array $context ) {
		if( ! isset( $context['type'], $context['object'] ) ) {
			return null;
		}

		$objectId = md5( $context['type'] . strtolower( $context['object'] ) );

		$perms = array();
		$db = TikiDb::get();

		$result = $db->query( 'SELECT groupName, permName FROM users_objectpermissions WHERE objectType = ? AND objectId = ?', array( $context['type'], $objectId ) );

		while( $row = $result->fetchRow() ) {
			$group = $row['groupName'];
			$perm = $this->sanitize( $row['permName'] );

			if( ! isset( $perms[$group] ) ) {
				$perms[$group] = array();
			}

			$perms[$group][] = $perm;
		}

		if( count( $perms ) == 0 ) {
			return null;
		} else {
			require_once 'lib/core/lib/Perms/Resolver/Static.php';
			return new Perms_Resolver_Static( $perms );
		}
	}

	private function sanitize( $name ) {
		if( strpos( $name, 'tiki_p_' ) === 0 ) {
			return substr( $name, strlen( 'tiki_p_' ) );
		} else {
			return $name;
		}
	}
}

?>
