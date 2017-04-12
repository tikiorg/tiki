<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	/**
	 * @param $input JitFilter
	 *              sort_mode string   default desc_createdAt
	 *              find string        unusued
	 *              maxRecords int     entries per page
	 *              offset int         for paging
	 *              formId string      id of the form to add the media to
	 *              targetName string  name of the target hidden input
	 * 
	 * @return array
	 * @throws Exception
	 * @throws Services_Exception_Denied
	 */
	function action_list($input)
	{
		$perms = Perms::get();
		if (!$perms->upload_videos) {
			throw new Services_Exception_Denied('Not allowed to upload videos');
		}
		$sort_mode = $input->sort_mode->word() ?: 'desc_createdAt';
		$find = $input->find->text();	// TODO
		$page_size = $input->maxRecords->int() ?: -1;		// TODO paging $prefs['maxRecords'];
		$offset = max(0, $input->offset->int());
		$page = ($offset/$page_size) + 1;


		$kalturaadminlib = TikiLib::lib('kalturaadmin');
		$kmedialist = $kalturaadminlib->listMedia($sort_mode, $page, $page_size, $find);

		$out = array(
			'entries' => $kmedialist->objects,
			'totalCount' => $kmedialist->totalCount,
			'formId' => $input->formId->text(),
			'targetName' => $input->targetName->text(),

		);

		return $out;

	}
}

