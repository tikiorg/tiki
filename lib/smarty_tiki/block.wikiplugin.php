<?php

function smarty_block_wikiplugin( $params, $content, $smarty ) {
	global $tikilib;
	if( ! isset( $params['_name'] ) ) {
		return '<div class="error">' . tra('Plugin name not specified.') . '</div>';
	}

	$name = $params['_name'];
	unset( $params['_name'] );

	return $tikilib->plugin_execute( $name, $content, $params, 0, false, array(
		'context_format' => 'html',
	) );
}

