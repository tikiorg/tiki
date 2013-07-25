<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_File_VimeoController
{
	function setUp()
	{
		global $prefs;

		Services_Exception_Disabled::check('vimeo_upload');
	}

	function action_authorize()
	{
		TikiLib::lib('access')->redirect('tiki-ajax_services.php?oauth_request=vimeo');
	}

	function action_upload($input)
	{
		$vimeolib = TikiLib::lib('vimeo');

		if (! $vimeolib->isAuthorized()) {
			throw new Services_Exception_NotAvailable(tr('Vimeo not authenticated.'));
		}

		$quota = $vimeolib->getQuota();
		$ticket = $vimeolib->getTicket();

		return array(
			'available' => $quota['free'],
			'ticket' => $ticket,
		);
	}

	function action_complete($input)
	{
		$ticket = $input->ticket->word();
		$filename = basename($input->file->text());

		$vimeolib = TikiLib::lib('vimeo');
		$video = $vimeolib->complete($ticket, $filename);

		return array(
			'ticket' => $ticket,
			'file' => $filename,
			'video' => $video,
		);
	}
}

