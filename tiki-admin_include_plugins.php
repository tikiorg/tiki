<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$cacheToInvalidate = array( 'plugindesc' );

$headerlib->add_jsfile( 'tiki-jsplugin.php' );
$pluginsAlias = $tikilib->plugin_get_list( false, true );
$pluginsReal = $tikilib->plugin_get_list( true, false );

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if( isset( $_POST['save'] ) && ! in_array($_POST['plugin'], $pluginsReal) ) {
		$info = array(
			'implementation' => $_POST['implementation'],
			'description' => array(
				'name' => $_POST['name'],
				'description' => $_POST['description'],
				'prefs' => array(),
				'validate' => $_POST['validate'],
				'params' => array(),
			),
			'body' => array(
			),
			'params' => array(
			),
		);

		if( empty($_POST['prefs']) )
			$temp = array( "wikiplugin_{$_POST['plugin']}" );
		else
			$temp =explode( ',', $_POST['prefs'] );

		$info['description']['prefs'] = $temp;

		if( isset($_POST['input']) ) {
			foreach( $_POST['input'] as $param ) {
				if( !empty( $param['token'] ) && !empty($param['name']) ) {
					$info['description']['params'][ $param['token'] ] = array(
						'required' => isset($param['required']),
						'safe' => isset($param['safe']),
						'name' => $param['name'],
						'description' => $param['description'],
					);
				}
			}
		}

		echo "<pre>" . print_r( $info, true ) . "</pre>";
	}
}

if( isset($_REQUEST['plugin']) && $pluginInfo = $tikilib->plugin_alias_info($_REQUEST['plugin']) ) {
	// Add an extra empty parameter to create new ones
	$pluginInfo['description']['params'][''] = array(
		'token' => '',
		'name' => '',
		'description' => '',
		'required' => '',
		'safe' => '',
	);
	$smarty->assign( 'plugin', $pluginInfo );
}

$smarty->assign( 'plugins_alias', $pluginsAlias );
$smarty->assign( 'plugins_real', $pluginsReal );

?>
