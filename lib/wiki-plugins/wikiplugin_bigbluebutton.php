<?php

function wikiplugin_bigbluebutton_info() {
	return array(
		'name' => tra('BigBlueButton'),
		'documentation' => 'PluginBigBlueButton',
		'description' => tra('Starts a video/audio/chat/presentation session using BigBlueButton'),
		'format' => 'html',
		'prefs' => array( 'wikiplugin_bigbluebutton', 'bigbluebutton_feature' ),
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Meeting'),
				'description' => tra('MeetingID provided by BigBlueButton.'),
				'filter' => 'text',
				'default' => ''
			),
			'prefix' => array(
				'required' => false,
				'name' => tra('Anonymous prefix'),
				'description' => tra('Unregistered users will get this token prepended to their name.'),
				'filter' => 'text',
				'default' => ''
			),
			'welcome' => array(
				'required' => false,
				'name' => tra('Welcome Message'),
				'description' => tra('A message to be provided when someone enters the room.'),
				'filter' => 'text',
				'default' => ''
			),
			'number' => array(
				'required' => false,
				'name' => tra('Dial Number'),
				'description' => tra('The phone-in support number to join from traditional phones.'),
				'filter' => 'text',
				'default' => ''
			),
			'voicebridge' => array(
				'required' => false,
				'name' => tra('Voice Bridge'),
				'description' => tra('Code to enter for phone attendees to join the room.'),
				'filter' => 'digits',
				'default' => ''
			),
			'logout' => array(
				'required' => false,
				'name' => tra('Logout URL'),
				'description' => tra('URL to which the user will be redirected when logging out from BigBlueButton.'),
				'filter' => 'url',
				'default' => ''
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Participants'),
				'description' => tra('Limit to the amount of simultaneous participants in the room. Support for this parameter depends on the BigBlueButton server.'),
				'filter' => 'int',
				'default' => ''
			),
		),
	);
}

function wikiplugin_bigbluebutton( $data, $params ) {
	global $smarty, $prefs, $user, $u_info;
	global $bigbluebuttonlib; require_once 'lib/bigbluebuttonlib.php';
	$name = $params['name'];

	$smarty->assign( 'bbb_name', $name );
	$smarty->assign( 'bbb_image', rtrim( $prefs['bigbluebutton_server_location'], '/' ) . '/images/bbb_logo.png' );

	$perms = Perms::get( 'bigbluebutton', $name );

	if( ! $bigbluebuttonlib->roomExists( $name ) ) {
		if( ! isset($_POST['bbb']) || $_POST['bbb'] != $name || ! $perms->bigbluebutton_create ) {
			return $smarty->fetch( 'wiki-plugins/wikiplugin_bigbluebutton_create.tpl' );
		}
	}

	$params = array_merge( array(
		'prefix' => '',
	), $params );

	if( $perms->bigbluebutton_join ) {
		if( isset($_POST['bbb']) && $_POST['bbb'] == $name ) {
			if( ! $user && isset($_POST['bbb_name']) && ! empty($_POST['bbb_name']) ) {
				$u_info['prefs']['realName'] = $params['prefix'] . $_POST['bbb_name'];
			}

			// Attempt to create room made before joining as the BBB server has no persistency.
			// Prior check ensures that the user has appropriate rights to create the room in the
			// first place or that the room was already officially created and this is only a
			// re-create if the BBB server restarted.
			//
			// This avoids the issue occuring when tiki cache thinks the room exist and it's gone
			// on the other hand. It does not solve the issue if the room is lost on the BBB server
			// and tiki cache gets flushed. To cover that one, create can be granted to everyone for
			// the specific object.
			$bigbluebuttonlib->createRoom( $name, $params );
			$bigbluebuttonlib->joinMeeting( $name );
		}

		$smarty->assign( 'bbb_attendees', $bigbluebuttonlib->getAttendees( $name ) );

		return $smarty->fetch( 'wiki-plugins/wikiplugin_bigbluebutton.tpl' );
	}
}

