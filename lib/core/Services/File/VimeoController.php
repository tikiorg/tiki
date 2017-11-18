<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
				[
					'controller' => 'oauth',
					'action' => 'request',
					'provider' => 'vimeo',
				]
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

		if (! empty($ticket['error'])) {
			$errMsg = tra($ticket['error']);
			if ($tiki_p_admin === 'y' && isset($ticket['developer_message'])) {
				$errMsg .= "\n" . tra($ticket['developer_message']);
			}
		} elseif (! empty($quota['error'])) {
			$errMsg = tra($quota['error']['msg']);
			if ($tiki_p_admin === 'y' && isset($quota['developer_message'])) {
				$errMsg .= "\n" . tra($quota['developer_message']);
			}
		} else {
			$availableMB = round($quota['space']['free'] / 1024 / 1024, 1);
			$availableSD = $quota['quota']['sd'];
			$availableHD = $quota['quota']['hd'];
		}

		return [
			'availableMB' => $availableMB,
			'availableSD' => $availableSD,
			'availableHD' => $availableHD,
			'ticket' => $ticket,
			'galleryId' => $galleryId,
			'fieldId' => $fieldId,
			'itemId' => $itemId,
			'errMsg' => $errMsg,
		];
	}

	function action_complete($input)
	{
		global $tiki_p_admin;

		$galleryId = $input->galleryId->int();
		$gal_info = $this->utilities->checkTargetGallery($galleryId);

		$completeUri = $input->completeUri->url();
		$title = $input->title->text();
		$filename = basename($input->file->text());

		$vimeolib = TikiLib::lib('vimeo');

		$completeInfo = $vimeolib->complete($completeUri);

		$video = '';
		$url = '';
		$fileId = 0;

		if (! empty($completeInfo['error'])) {
			$errMsg = tra($completeInfo['error']);
			if ($tiki_p_admin === 'y' && isset($completeInfo['developer_message'])) {
				$errMsg .= "\n" . tra($completeInfo['developer_message']);
			}
		} elseif (! empty($completeInfo['Location'])) {
			$errMsg = '';

			$location = $completeInfo['Location'];


			$urlparts = explode('/', $completeInfo['Location']);
			foreach ($urlparts as $urlpart) {
				if (ctype_digit($urlpart)) {
					$video = $urlpart;
				}
			}

			$vimeolib->setTitle($video, $title);
			$url = 'http://vimeo.com' . $location;

			$info = [
			'expires' => TikiLib::lib('tiki')->now,
			'etag' => null,
			];

			$size = $chunks['chunk']['size'];

			// Add placeholder directly without verification, URL will show 404 until processed
			$filegallib = TikiLib::lib('filegal');
			$fileId = $this->utilities->uploadFile($gal_info, $title ?: $filename, $size, 'video/vimeo', 'REFERENCE');
			$filegallib->attach_file_source($fileId, $url, $info, 1);
		} else {
			$errMsg = tra('Unknown error');
		}

		return [
			'ticket' => $ticket,
			'file' => $filename,
			'name' => $title ?: $filename,
			'video' => $video,
			'url' => $url,
			'fileId' => $fileId,
			'err' => $errMsg,
		];
	}

	/**
	 * View controller function. Best-used when called from a bootstrap_modal smarty function.
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_view($input)
	{
		$fileId = $input->file_id->text();
		$vimeoUrl = $input->vimeo_url->text();

		if ($fileId) {
			$filelib = TikiLib::lib("filegal");
			$file = $filelib->get_file_info($fileId);
		}

		if ($input->title->text()) {
			$title = $input->title->text();
		} elseif (isset($file)) {
			$title = $file["filename"];
		}

		return [
			"title" => $title,
			"file_id" => $fileId,
			"vimeo_url" => $vimeoUrl,
		];
	}
}
