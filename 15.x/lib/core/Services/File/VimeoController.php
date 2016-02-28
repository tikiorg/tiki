<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		$servicelib = TikiLib::lib('service');
		TikiLib::lib('access')->redirect(
			$servicelib->getUrl(
				array(
					'controller' => 'oauth',
					'action' => 'request',
					'provider' => 'vimeo',
				)
			)
		);
	}

	function action_upload($input)
	{
		global $prefs, $tiki_p_admin;

		$galleryId = $input->galleryId->int() ?: $prefs['vimeo_default_gallery'];
		$fieldId = $input->fromFieldId->int();
		$itemId = $input->fromItemId->int();

		$this->utilities->checkTargetGallery($galleryId);

		$vimeolib = TikiLib::lib('vimeo');

		if (! $vimeolib->isAuthorized()) {
			throw new Services_Exception_NotAvailable(tr('Vimeo not authenticated.'));
		}

		$quota = $vimeolib->getQuota();
		$ticket = $vimeolib->getTicket();

		$errMsg = '';
		$availableMB = 0;
		$availableSD = 0;
		$availableHD = 0;

		if ($ticket['stat'] !== 'ok') {
			$errMsg = tra($ticket['err']['msg']);				// get_strings tra('The upload limit was exceeded')
			if ($tiki_p_admin === 'y') {
				$errMsg .= '<br>' . tra($ticket['err']['expl']); // get_strings tra('The user has exceeded the daily number of uploads allowed.')
			}
		} else if ($quota['stat'] !== 'ok') {
			$errMsg = tra($quota['err']['msg']);				// get_strings tra('Permission Denied') tra('Invalid signature')
																// get_strings tra('Invalid consumer key')
			if ($tiki_p_admin === 'y') {
				$errMsg .= '<br>' . tra($quota['err']['expl']);	// get_strings tra('The OAuth token that was passed has either expired or was not valid.')
																// get_strings tra('The oauth_signature passed was not valid.')
																// get_strings tra('The consumer key passed was not valid.')
			}
		} else {
			$availableMB = round($quota['user']['upload_space']['free'] / 1024 / 1024, 1);
			$availableSD = $quota['user']['sd_quota'];
			$availableHD = $quota['user']['hd_quota'];
		}

		return array(
			'availableMB' => $availableMB,
			'availableSD' => $availableSD,
			'availableHD' => $availableHD,
			'ticket' => $ticket['ticket'],
			'galleryId' => $galleryId,
			'fieldId' => $fieldId,
			'itemId' => $itemId,
			'errMsg' => $errMsg,
		);
	}

	function action_complete($input)
	{
		global $tiki_p_admin;

		$galleryId = $input->galleryId->int();
		$gal_info = $this->utilities->checkTargetGallery($galleryId);

		$ticket = $input->ticket->word();
		$title = $input->title->text();
		$filename = basename($input->file->text());

		$vimeolib = TikiLib::lib('vimeo');
		$chunks = $vimeolib->verifyChunks($ticket);
		$completeInfo = $vimeolib->complete($ticket, $filename);

		$video = '';
		$url = '';
		$fileId = 0;

		if ($completeInfo['stat'] !== 'ok') {
			$errMsg = tra($completeInfo['err']['msg']);				// get_strings TODO?
			if ($tiki_p_admin === 'y') {
				$errMsg .= "\n" . tra($completeInfo['err']['expl']); // get_strings TODO?
			}
		} else {
			$errMsg = '';

			$video = $completeInfo['ticket']['video_id'];
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
		}

		return array(
			'ticket' => $ticket,
			'file' => $filename,
			'video' => $video,
			'url' => $url,
			'fileId' => $fileId,
			'err' => $errMsg,
		);
	}

	/**
	 * View controller function. Best-used when called from a bootstrap_modal smarty function.
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_view($input){
		$fileId = $input->file_id->text();

		$filelib = TikiLib::lib("filegal");
		$file = $filelib->get_file_info($fileId);
		return array(
			"title" => $file["filename"],
			"file_id" => $fileId,
		);
	}
}

