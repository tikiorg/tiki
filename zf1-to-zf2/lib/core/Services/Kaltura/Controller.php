<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Kaltura_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_kaltura');
	}

	function action_upload($input)
	{
		$perms = Perms::get();
		if (! $perms->upload_videos) {
			throw new Services_Exception_Denied('Not allowed to upload videos');
		}

		global $user, $prefs;
		$kalturalib = TikiLib::lib('kalturauser');

		$identifier = uniqid();
		$cwflashVars = array(
			'uid' => $user ? $user : 'Anonymous',
			'partnerId' => $prefs['kaltura_partnerId'],
			'ks' => $kalturalib->getSessionKey(),
			'afterAddEntry' => 'afterAddEntry_' . $identifier,
			'close' => 'onContributionWizardClose',
			'showCloseButton' => false,
			'Permissions' => 1, // 1=public, 2=private, 3=group, 4=friends
		);

		$entries = $input->entryId->word();
		$message = null;
		if ($entries) {
			if (count($entries) > 1) {
				$message = tr('You have successfully added %0 new media items', count($entries));
			} else {
				$message = tr('You have successfully added one new media item');
			}
		}

		return array(
			'identifier' => $identifier,
			'flashVars' => json_encode($cwflashVars),
			'message' => $message,
			'entries' => $entries,
		);
	}
}

