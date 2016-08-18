<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_profile_info()
{
	return array(
		'name' => tra('Profile Application'),
		'description' => tra('Add a button for applying a profile.'),
		'documentation' => 'PluginProfile',
		'prefs' => array( 'wikiplugin_profile' ),
		'validate' => 'all',
		'inline' => true,
		'iconname' => 'cog',
		'introduced' => 3,
		'tags' => array( 'experimental' ),
		'params' => array(
			'domain' => array(
				'required' => false,
				'name' => tra('Domain'),
				'description' => tr('Profile repository domain. Default value is %0profiles.tiki.org%1', '<code>', '</code>'),
				'since' => '3.0',
				'filter' => 'url',
				'default' => 'profiles.tiki.org',
			),
			'name' => array(
				'required' => true,
				'name' => tra('Profile Name'),
				'description' => tra('Name of the profile to be applied.'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			)
		)
	);
}

function wikiplugin_profile( $data, $params )
{
	global $tiki_p_admin;

	if ( $tiki_p_admin != 'y' ) {
		return '__' . tra('Profile plugin only available to administrators') . '__';
	}

	$params = array_merge(array('domain' => 'profiles.tiki.org'), $params);

	if ( !isset( $params['name'] ) ) {
		return 'Missing parameter __name__';
	}

	$profile = Tiki_Profile::fromNames($params['domain'], $params['name']);

	if ( $profile ) {
		$installer = new Tiki_Profile_Installer;

		try {
			if ( $installer->isInstalled($profile) ) {
				if ( $_POST['reinstall'] == "{$params['domain']}/{$params['name']}" ) {
					$installer->forget($profile);
					$installer->install($profile);

					header('Location: ' . $_SERVER['REQUEST_URI']);
					exit;
				}
			} else {
				if ( $_POST['install'] == "{$params['domain']}/{$params['name']}" ) {
					$installer->install($profile);

					header('Location: ' . $_SERVER['REQUEST_URI']);
					exit;
				}
			}
		} catch( Exception $e ) {
			return '__' . $e->getMessage() . '__';
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('profile_is_installed', $installer->isInstalled($profile));
		$smarty->assign('profile_key', "{$params['domain']}/{$params['name']}");
		return '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_profile.tpl') . '~/np~';
	} else {
		return '__' . tr('Profile %0/%1 not found', $params['domain'], $params['name']) . '__';
	}
}
