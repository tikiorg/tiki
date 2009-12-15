<?php

function post_20090416_plugin_security_tiki( $installer )
{
	$result = $installer->query( "SELECT value FROM tiki_preferences WHERE name = 'plugin_fingerprints'" );
	if( $row = $result->fetchRow() ) {
		$data = unserialize( $row['value'] );

		foreach( $data as $fingerprint => $string ) {
			list( $status, $timestamp, $user ) = explode( '/', $string );
			$installer->query( "INSERT INTO tiki_plugin_security (fingerprint, status, approval_by, last_update, last_objectType, last_objectId ) VALUES(?, ?, ?, ?, '', '')",
				array( $fingerprint, $status, $user, $timestamp ) );
		}

		$installer->query( "DELETE FROM tiki_preferences WHERE name = 'plugin_fingerprints'" );
	}
}

?>
