<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Handle actions of plugins (smarty plugins, wiki-plugins, modules, ...)

$plugins_actions = array();
$matches = array();

foreach ( $_REQUEST as $k => $v ) {
	if ( preg_match( '/^(\w_\w_)([a-zA-Z0-9_-]+)-(.*)$/', $k, $matches ) ) {
		$plugin_type =& $matches[1];
		$plugin_name =& $matches[2];
		$plugin_argument =& $matches[3];
		if ( ! isset( $plugins_actions[ $plugin_type ] ) ) {
			$plugins_actions[ $plugin_type ] = array();
		}
		if ( ! isset( $plugins_actions[ $plugin_type ][ $plugin_name ] ) ) {
			$plugins_actions[ $plugin_type ][ $plugin_name ] = array();
		}
		$plugins_actions[ $plugin_type ][ $plugin_name ][ $plugin_argument ] =& $_REQUEST[ $k ];
	}
}

foreach ( $plugins_actions as $plugin_type => $v ) {
	foreach ( $v as $plugin_name => $params ) {
		switch ( $plugin_type ) {
			case 's_f_': // Smarty Function
				@include_once( 'lib/smarty_tiki/function.' . $plugin_name . '.php' );
				$func = 's_f_' . $plugin_name . '_actionshandler';
				if ( ! function_exists( $func ) || ! call_user_func( $func, $params ) ) {
					global $smarty;
					$smarty->assign('msg', sprintf( tra('Handling actions of plugin "%s" failed.'), $plugin_name ) );
					$smarty->display('error.tpl');
					die;
				}
				break;
		}
	}
}

unset( $matches );
unset( $plugins_actions );
