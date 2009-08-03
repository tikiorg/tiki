<?php

class PerspectiveLib
{
	function get_preferences( $perspectiveId ) {
		$result = TikiDb::get()->query( "SELECT pref, value FROM tiki_perspective_preferences WHERE perspectiveId = ?", array( $perspectiveId ) );

		$out = array();

		while( $row = $result->fetchRow() ) {
			$out[ $row['pref'] ] = $row['value'];
		}

		return $out;
	}

	function replace_perspective( $perspectiveId, $name ) {
		$db = TikiDb::get();

		if( $perspectiveId ) {
			$db->query( 'UPDATE tiki_perspectives SET name = ? WHERE perspectiveId = ?', 
				array( $name, $perspectiveId ) );

			return $perspectiveId;
		} else {
			$db->query( 'INSERT INTO tiki_perspectives ( name ) VALUES( ? )',
				array( $name ) );

			$max = $db->getOne( 'SELECT MAX(perspectiveId) FROM tiki_perspectives' );
			return $max;
		}
	}

	function replace_preferences( $perspectiveId, $preferences ) {
		$db = TikiDb::get();
		$db->query( 'DELETE FROM tiki_perspective_preferences WHERE perspectiveId = ?',
			array( $perspectiveId ) );

		foreach( $preferences as $pref => $value ) {
			$this->set_preference( $perspectiveId, $pref, $value );
		}
	}

	function set_preference( $perspectiveId, $preference, $value ) {
		$db = TikiDb::get();

		$db->query( 'DELETE FROM tiki_perspective_preferences WHERE perspectiveId = ? AND pref = ?',
			array( $perspectiveId, $preference ) );
		$db->query( 'INSERT INTO tiki_perspective_preferences ( perspectiveId, pref, value ) VALUES( ?, ?, ? )',
			array( $perspectiveId, $preference, $value ) );
	}

	function perspective_exists( $perspectiveId ) {
		$db = TikiDb::get();

		$id = $db->getOne( 'SELECT perspectiveId FROM tiki_perspectives WHERE perspectiveId = ?',
			array( $perspectiveId ) );
		
		return ! empty( $id );
	}

	function list_perspectives() {
		$db = TikiDb::get();

		$list = $db->fetchAll( "SELECT perspectiveId, name FROM tiki_perspectives" );

		$list = Perms::filter( array( 'type' => 'perspective' ), 'object', $list, array( 'object' => 'perspectiveId' ), 'perspective_view' );
		return $list;
	}
}

global $perspectivelib;
$perspectivelib = new PerspectiveLib;

?>
