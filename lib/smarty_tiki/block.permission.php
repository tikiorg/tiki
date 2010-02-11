<?php
/* $Id$ */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_permission($params, $content, &$smarty, $repeat) {
	global $prefs;

	$context = array();

	if( isset( $params['type'], $params['object'] ) ) {
		$context['type'] = $params['type'];
		$context['object'] = $params['object'];
	}

	$perms = Perms::get( $context );
	$name = $params['name'];

	if( $perms->$name ) {
		return $content;
	} else {
		return '';
	}
}
