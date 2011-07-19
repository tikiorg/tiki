<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_profile_info() {
	return array(
		'name' => tra('Profile Application'),
		'description' => tra('Add a button for applying a profile.'),
		'documentation' => 'PluginProfile',
		'prefs' => array( 'wikiplugin_profile' ),
		'validate' => 'all',
		'inline' => true,
		'icon' => 'pics/icons/database_lightning.png',
		'params' => array(
			'domain' => array(
				'required' => false,
				'name' => tra('Domain'),
				'description' => tra('Profile repository domain. Default value is profiles.tiki.org'),
				'default' => 'profiles.tiki.org',
			),
			'name' => array(
				'required' => true,
				'name' => tra('Profile Name'),
				'description' => tra('Name of the profile to be applied.'),
				'default' => '',
			)
		)
	);
}

function wikiplugin_profile( $data, $params ) {
	require_once 'lib/profilelib/profilelib.php';
	require_once 'lib/profilelib/installlib.php';

	global $tiki_p_admin;

	if( $tiki_p_admin != 'y' ) {
		return '__' . tra('Profile plugin only available to administrators') . '__';
	}

	$params = array_merge( array(
		'domain' => 'profiles.tiki.org',
	), $params );

	if( !isset( $params['name'] ) ) {
		return 'Missing parameter __name__';
	}

	$profile = Tiki_Profile::fromNames( $params['domain'], $params['name'] );

	if( $profile ) {
		$installer = new Tiki_Profile_Installer;

		try {
			if( $installer->isInstalled( $profile ) ) {
				if( $_POST['reinstall'] == "{$params['domain']}/{$params['name']}" ) {
					$installer->forget( $profile );
					$installer->install( $profile );

					header( 'Location: ' . $_SERVER['REQUEST_URI'] );
					exit;
				}
			} else {
				if( $_POST['install'] == "{$params['domain']}/{$params['name']}" ) {
					$installer->install( $profile );

					header( 'Location: ' . $_SERVER['REQUEST_URI'] );
					exit;
				}
			}
		} catch( Exception $e ) {
			return '__' . $e->getMessage() . '__';
		}

		global $smarty;
		$smarty->assign( 'profile_is_installed', $installer->isInstalled( $profile ) );
		$smarty->assign( 'profile_key', "{$params['domain']}/{$params['name']}" );
		return '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_profile.tpl') . '~/np~';
	} else {
		return '__' . tr('Profile %0/%1 not found', $params['domain'], $params['name']) . '__';
	}
}
