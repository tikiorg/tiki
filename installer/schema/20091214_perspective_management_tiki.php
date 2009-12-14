<?php

function post_20091214_perspective_management_tiki( $installer ) {
	$result = $installer->query( 'SELECT perspectiveId, pref, value FROM tiki_perspective_preferences' );
	while( $row = $result->fetchRow() ) {
		$installer->query( 'UPDATE tiki_perspective_preferences SET value = ? WHERE perspectiveId = ? and pref = ?',
			array( serialize( $row['value'] ), $row['perspectiveId'], $row['pref'] ) );
	}
}

