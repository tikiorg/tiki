<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_block_wikiplugin( $params, $content, &$smarty, $repeat = false ) {
 	global $tikilib;

	if ( $repeat ) return;

	if( ! isset( $params['_name'] ) ) {
		return '<div class="error">' . tra('Plugin name not specified.') . '</div>';
	}

	$name = $params['_name'];
	unset( $params['_name'] );

	return $tikilib->plugin_execute( $name, $content, $params, 0, false, array(
		'context_format' => 'html',
		'ck_editor' => false,
	) );
}

