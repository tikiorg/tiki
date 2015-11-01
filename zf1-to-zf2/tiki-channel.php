<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
				array(
					'staticKeyFiltersForArrays' => array('channels' => 'rawhtml_unsafe',),
				)
);

require_once 'tiki-setup.php';

// This file will handle a second mode of authentication, don't limit it to permissions.
// Only channels registered through the admin panel can be executed.
// Each channel execution validates access rights.

if ( ! isset($_REQUEST['channels']) || ! is_array($_REQUEST['channels']) ) {
	$access->display_error('tiki-channel.php', tra('Invalid request. Expecting channels array.'));
}

$calls = array();
$channels = array();

foreach ( $_REQUEST['channels'] as $info ) {
	if ( ! isset( $info['channel_name'] ) ) {
		$access->display_error('tiki-channel.php', tra('Missing channel name.'));
	}

	$channel = $info['channel_name'];
	$channels[] = $channel;
	unset($info['channel_name']);
	$calls[] = array( $channel, $info );
}

$config = Tiki_Profile_ChannelList::fromConfiguration($prefs['profile_channels']);

$channels = array_unique($channels);
$groups = $tikilib->get_user_groups($user);

if ( ! $user && ! $config->canExecuteChannels($channels, $groups) ) {
	// User not defined and some groups missing, likely to be a machine
	if ( ! $access->http_auth() ) {
		$access->display_error('tiki-channel.php', tra('Authentication required.'));
	}

	// Get the new ones
	$groups = $tikilib->get_user_groups($user);
}

if ( ! $config->canExecuteChannels($channels, $groups) ) {
	$access->display_error(
		'tiki-channel.php',
		tra('One of the requested channels cannot be requested. It does not exist or permission is denied.')
	);
}

$profiles = $config->getProfiles($channels);

if ( count($profiles) != count($channels) ) {
	$access->display_error('tiki-channel.php', tra('One of the install profiles could not be obtained.'));
}

Tiki_Profile::useUnicityPrefix(uniqid());
$installer = new Tiki_Profile_Installer;
$installer->limitGlobalPreferences(array());

foreach ( $calls as $call ) {
	list($channel, $userInput) = $call;

	// Profile can be installed multiple times
	// Only last values preserved
	$profile = $profiles[$channel];
	$installer->forget($profile);

	$installer->setUserData($userInput);
	$installer->install($profile);
}

if ( isset($_REQUEST['return_uri']) ) {
	header("Location: {$_REQUEST['return_uri']}");
}
