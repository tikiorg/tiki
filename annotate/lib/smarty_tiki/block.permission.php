<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
