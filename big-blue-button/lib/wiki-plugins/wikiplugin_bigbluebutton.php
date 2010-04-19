<?php

function wikiplugin_bigbluebutton_info() {
	return array(
		'name' => tra('BigBlueButton'),
		'description' => tra('Allows to join a BigBlueButton meeting.'),
		'format' => 'html',
		'prefs' => array( 'wikiplugin_bigbluebutton', 'bigbluebutton_feature' ),
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Meeting'),
				'description' => tra('MeetingID provided by BigBlueButton.'),
				'filter' => 'text',
			),
			'prefix' => array(
				'required' => false,
				'name' => tra('Anonymous prefix'),
				'description' => tra('Unregistered users will get this token prepended to their name.'),
				'filter' => 'text',
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

	if( ! $bigbluebuttonlib->roomExists( $name ) ) {
		if( isset($_POST['bbb']) && $_POST['bbb'] == $name && Perms::get()->bigbluebutton_create ) {
			$bigbluebuttonlib->createRoom( $name );
		} else {
			return $smarty->fetch( 'wiki-plugins/wikiplugin_bigbluebutton_create.tpl' );
		}
	}

	if( ! isset($params['prefix']) ) {
		$params['prefix'] = '';
	}

	$perms = Perms::get( 'bigbluebutton', $name );
	if( $perms->bigbluebutton_join ) {
		if( isset($_POST['bbb']) && $_POST['bbb'] == $name ) {
			if( ! $user && isset($_POST['bbb_name']) && ! empty($_POST['bbb_name']) ) {
				$u_info['prefs']['realName'] = $params['prefix'] . $_POST['bbb_name'];
			}

			$bigbluebuttonlib->joinMeeting( $name );
		}

		$smarty->assign( 'bbb_attendees', $bigbluebuttonlib->getAttendees( $name ) );

		return $smarty->fetch( 'wiki-plugins/wikiplugin_bigbluebutton.tpl' );
	}
}

