<?php

function wikiplugin_datachannel_info()
{
	return array(
		'name' => tra('Data Channel'),
		'description' => tra('Displays a form to trigger data channels.'),
		'prefs' => array('wikiplugin_datachannel'),
		'body' => tra('List of fields to display. One field per line. Comma delimited: fieldname,label'),
		'extraparams' => true,
		'params' => array(
			'channel' => array(
				'required' => true,
				'name' => tra('Channel Name'),
				'description' => tra('Name of the channel as registered by the administrator.'),
			),
		),
	);
}

function wikiplugin_datachannel( $data, $params )
{
	static $execution = 0;
	global $prefs, $smarty;
	$executionId = 'datachannel-exec-' . ++$execution;

	$fields = array();
	$lines = explode( "\n", $data );
	$lines = array_map( 'trim', $lines );
	$lines = array_filter( $lines );

	foreach( $lines as $line ) {
		$parts = explode( ',', $line, 2 );

		if( count($parts) == 2 ) {
			$fields[ $parts[0] ] = $parts[1];
		}
	}

	require_once 'lib/profilelib/profilelib.php';
	require_once 'lib/profilelib/channellib.php';
	require_once 'lib/profilelib/installlib.php';

	$groups = Perms::get()->getGroups();

	$config = Tiki_Profile_ChannelList::fromConfiguration( $prefs['profile_channels'] );
	if( $config->canExecuteChannels( array( $params['channel'] ), $groups ) ) {
		if( $_SERVER['REQUEST_METHOD'] == 'POST' 
			&& isset( $_POST['datachannel_execution'] ) 
			&& $_POST['datachannel_execution'] == $executionId ) {

			$input = array_intersect_key( $_POST, $fields );
			$static = $params;
			unset( $static['channel'] );

			$userInput = array_merge( $input, $static );

			Tiki_Profile::useUnicityPrefix(uniqid());
			$installer = new Tiki_Profile_Installer;
			$installer->limitGlobalPreferences( array() );

			$profiles = $config->getProfiles( array( $params['channel'] ) );
			$profile = reset($profiles);

			$installer->setUserData( $userInput );
			$installer->install( $profile );

			header( 'Location: ' . $_SERVER['REQUEST_URI'] );
		} else {

			$smarty->assign( 'datachannel_fields', $fields );
			$smarty->assign( 'datachannel_execution', $executionId );

			return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_datachannel.tpl' ) . '~/np~';
		}
	}
}
