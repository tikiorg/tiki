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
	$name = $params['name'];
	$perms = Perms::get( 'bigbluebutton', $name );

	if( ! isset($params['prefix']) ) {
		$params['prefix'] = '';
	}

	if( $perms->bigbluebutton_join ) {
		if( isset($_POST['bbb']) && $_POST['bbb'] == $name ) {
			if( ! $user && isset($_POST['bbb_name']) && ! empty($_POST['bbb_name']) ) {
				$u_info['prefs']['realName'] = $params['prefix'] . $_POST['bbb_name'];
			}

			global $bigbluebuttonlib; require_once 'lib/bigbluebuttonlib.php';
			$bigbluebuttonlib->joinMeeting( $name );
		}

		if( isset( $u_info['prefs']['realName'] ) ) {
			$smarty->assign( 'bbb_username', $u_info['prefs']['realName'] );
		}

		$smarty->assign( 'bbb_name', $name );
		$smarty->assign( 'bbb_image', rtrim( $prefs['bigbluebutton_server_location'], '/' ) . '/images/bbb_logo.png' );

		return $smarty->fetch( 'wiki-plugins/wikiplugin_bigbluebutton.tpl' );
	}
}

