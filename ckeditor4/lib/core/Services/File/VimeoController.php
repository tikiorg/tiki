<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_File_VimeoController
{
	private $utilities;

	function setUp()
	{
		Services_Exception_Disabled::check('vimeo_upload');
		$this->utilities = new Services_File_Utilities;
	}

	function action_authorize()
	{
		TikiLib::lib('access')->redirect('tiki-ajax_services.php?oauth_request=vimeo');
	}

	function action_upload($input)
	{
		global $prefs;

		$galleryId = $input->galleryId->int() ?: $prefs['vimeo_default_gallery'];

		$this->utilities->checkTargetGallery($galleryId);

		$vimeolib = TikiLib::lib('vimeo');

		if (! $vimeolib->isAuthorized()) {
			throw new Services_Exception_NotAvailable(tr('Vimeo not authenticated.'));
		}

		$quota = $vimeolib->getQuota();
		$ticket = $vimeolib->getTicket();

		return array(
			'available' => $quota['free'],
			'ticket' => $ticket,
			'galleryId' => $galleryId,
		);
	}

	function action_complete($input)
	{
		$galleryId = $input->galleryId->int();
		$gal_info = $this->utilities->checkTargetGallery($galleryId);

		$ticket = $input->ticket->word();
		$title = $input->title->text();
		$filename = basename($input->file->text());

		$vimeolib = TikiLib::lib('vimeo');
		$chunks = $vimeolib->verifyChunks($ticket);
		$video = $vimeolib->complete($ticket, $filename);
		$vimeolib->setTitle($video, $title);
		$url = 'http://vimeo.com/' . $video;

		$info = array(
			'expires' => TikiLib::lib('tiki')->now,
			'etag' => null,
		);

		$size = $chunks['chunk']['size'];

		// Add placeholder directly without verification, URL will show 404 until processed
		$filegallib = TikiLib::lib('filegal');
		$fileId = $this->utilities->uploadFile($gal_info, $title ?: $filename, $size, 'video/vimeo', 'REFERENCE');
		$filegallib->attach_file_source($fileId, $url, $info, 1);

		return array(
			'ticket' => $ticket,
			'file' => $filename,
			'video' => $video,
			'url' => $url,
			'fileId' => $fileId,
		);
	}
}

