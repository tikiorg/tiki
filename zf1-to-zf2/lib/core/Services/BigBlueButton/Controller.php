<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_BigBlueButton_Controller
{
	function setUp()
	{
		global $prefs;

		Services_Exception_Disabled::check('bigbluebutton_feature');
	}

	function action_join($input)
	{
		if (! $params = Tiki_Security::get()->decode($input->params->none())) {
			throw new Services_Exception_Denied;
		}

		$meetingName = $params['name'];

		$bigbluebuttonlib = TikiLib::lib('bigbluebutton');
		$perms = Perms::get('bigbluebutton', $meetingName);

		if (! $bigbluebuttonlib->roomExists($meetingName)) {
			if (! $perms->bigbluebutton_create) {
				throw new Services_Exception_NotFound;
			}
		}

		if (! $perms->bigbluebutton_join) {
			throw new Services_Exception_Denied;
		}

		global $user;
		if ( ! $user && $input->bbb_name->text() ) {
			$_SESSION['bbb_name'] = $params['prefix'] . $input->bbb_name->text();
		}

		$configuration = null;
		if (! empty($params['configuration'])) {
			$configuration = $params['configuration'];
			unset($params['configuration']);
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
		$bigbluebuttonlib->createRoom($meetingName, $params);
		$token = null;

		if ($configuration) {
			$token = $bigbluebuttonlib->configureRoom($meetingName, $configuration);
		}

		$bigbluebuttonlib->joinMeeting($meetingName, $token);
	}

	function action_delete_recording($input)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception_Denied;
		}

		$bigbluebuttonlib = TikiLib::lib('bigbluebutton');
		$bigbluebuttonlib->removeRecording($input->recording_id->text());

		return array(
		);
	}
}

