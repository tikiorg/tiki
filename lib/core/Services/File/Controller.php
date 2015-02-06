<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_File_Controller
{
	private $defaultGalleryId = 1;
	private $utilities;

	function setUp()
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y') {
			throw new Services_Exception_Disabled('feature_file_galleries');
		}
		$this->defaultGalleryId = $prefs['fgal_root_id'];
		$this->utilities = new Services_File_Utilities;
	}

	function action_uploader($input)
	{
		$gal_info = $this->checkTargetGallery($input);

		return array(
			'title' => tr('File Upload'),
			'galleryId' => $gal_info['galleryId'],
			'limit' => abs($input->limit->int()),
			'typeFilter' => $input->type->text(),
			'files' => $this->getFilesInfo((array) $input->file->int()),
		);
	}

	function action_upload($input)
	{
		$gal_info = $this->checkTargetGallery($input);

		$fileId = $input->fileId->int();
		$asuser = $input->user->text();

		if (isset($_FILES['data'])) {
			if (is_uploaded_file($_FILES['data']['tmp_name'])) {
				$file = new JitFilter($_FILES['data']);
				$name = $file->name->text();
				$size = $file->size->int();
				$type = $file->type->text();

				$data = file_get_contents($_FILES['data']['tmp_name']);
			} else {
				throw new Services_Exception_NotAvailable(tr('File could not be uploaded.'));
			}
		} else {
			$name = $input->name->text();
			$size = $input->size->int();
			$type = $input->type->text();

			$data = $input->data->none();
			$data = base64_decode($data);
		}

		$mimelib = TikiLib::lib('mime');
		$type = $mimelib->from_content($name, $data);

		if ($fileId) {
			$this->utilities->updateFile($gal_info, $name, $size, $type, $data, $fileId, $asuser);
		} else {
			$fileId = $this->utilities->uploadFile($gal_info, $name, $size, $type, $data, $asuser);
		}

		if ($fileId === false) {
			throw new Services_Exception(tr('File could not be uploaded. Restrictions apply.'), 406);
		}

		return array(
			'size' => $size,
			'name' => $name,
			'type' => $type,
			'fileId' => $fileId,
			'galleryId' => $gal_info['galleryId'],
			'md5sum' => md5($data),
		);
	}

	function action_browse($input)
	{
		try {
			$gal_info = $this->checkTargetGallery($input);
		} catch (Services_Exception $e) {
			$gal_info = null;
		}
		$input->replaceFilter('file', 'int');
		$type = $input->type->text();

		return [
			'title' => tr('Browse'),
			'galleryId' => $input->galleryId->int(),
			'limit' => $input->limit->int(),
			'files' => $this->getFilesInfo($input->asArray('file', ',')),
			'typeFilter' => $type,
			'canUpload' => (bool) $gal_info,
			'list_view' => (substr($type, 0, 6) == 'image/') ? 'thumbnail_gallery' : 'list_gallery',
		];
	}

	function action_thumbnail_gallery($input)
	{
		// Same as list gallery, different template
		return $this->action_list_gallery($input);
	}

	function action_list_gallery($input)
	{
		$galleryId = $input->galleryId->int();

		$lib = TikiLib::lib('unifiedsearch');
		$query = $lib->buildQuery([
			'type' => 'file',
			'gallery_id' => (string) $galleryId,
		]);

		if ($search = $input->search->text()) {
			$query->filterContent($search);
		}

		if ($typeFilter = $input->type->text()) {
			$query->filterContent($typeFilter, 'filetype');
		}

		$query->setRange($input->offset->int());
		$query->setOrder('title_asc');
		$result = $query->search($lib->getIndex());

		return [
			'title' => tr('Gallery List'),
			'galleryId' => $galleryId,
			'results' => $result,
			'plain' => $input->plain->int(),
			'search' => $search,
			'typeFilter' => $typeFilter,
		];
	}

	function action_remote($input)
	{
		global $prefs;
		if ($prefs['fgal_upload_from_source'] != 'y') {
			throw new Services_Exception(tr('Upload from source disabled.'), 403);
		}

		$gal_info = $this->checkTargetGallery($input);
		$url = $input->url->url();

		if (! $url) {
			return array(
				'galleryId' => $gal_info['galleryId'],
			);
		}

		$filegallib = TikiLib::lib('filegal');

		if ($file = $filegallib->lookup_source($url)) {
			return $file;
		}

		$info = $filegallib->get_info_from_url($url);

		if (! $info) {
			throw new Services_Exception(tr('Data could not be obtained.'), 412);
		}

		if ($input->reference->int()) {
			$info['data'] = 'REFERENCE';
		}

		$fileId = $this->utilities->uploadFile($gal_info, $info['name'], $info['size'], $info['type'], $info['data']);

		if ($fileId === false) {
			throw new Services_Exception(tr('File could not be uploaded. Restrictions apply.'), 406);
		}

		$filegallib->attach_file_source($fileId, $url, $info, $input->reference->int());

		return array(
			'size' => $info['size'],
			'name' => $info['name'],
			'type' => $info['type'],
			'fileId' => $fileId,
			'galleryId' => $gal_info['galleryId'],
			'md5sum' => md5($info['data']),
		);
	}

	function action_refresh($input)
	{
		global $prefs;
		if ($prefs['fgal_upload_from_source'] != 'y') {
			throw new Services_Exception(tr('Upload from source disabled.'), 403);
		}

		if ($prefs['fgal_source_show_refresh'] != 'y') {
			throw new Services_Exception(tr('Manual refresh disabled.'), 403);
		}

		$filegallib = TikiLib::lib('filegal');
		$ret = $filegallib->refresh_file($input->fileId->int());

		return array(
			'success' => $ret,
		);
	}

	/**
	 * @param $input	string "name" for the filename to find
	 * @return array	file info for most recent file by that name
	 */
	function action_find($input)
	{

		$filegallib = TikiLib::lib('filegal');
		$gal_info = $this->checkTargetGallery($input);

		$name = $input->name->text();

		$pos = strpos($name, '?');		// strip off get params
		if ($pos !== false) {
			$name = substr($name, 0, $pos);
		}

		$info = $filegallib->get_file_by_name($gal_info['galleryId'], $name);

		if (empty($info)) {
			$info = $filegallib->get_file_by_name($gal_info['galleryId'], $name, 'filename');
		}
		unset($info['data']);

		return $info;
	}

	private function checkTargetGallery($input)
	{
		$galleryId = $input->galleryId->int() ?: $this->defaultGalleryId;

		// Patch for uninitialized utilities.
		//	The real problem is that setup is not called
		if ($this->utilities == null) {
			$this->utilities = new Services_File_Utilities;
		}
		
		return $this->utilities->checkTargetGallery($galleryId);
	}

	private function getFilesInfo($files)
	{
		return array_map(function ($fileId) {
			return TikiDb::get()->table('tiki_files')->fetchRow(['fileId', 'name' => 'filename', 'label' => 'name', 'type' => 'filetype'], ['fileId' => $fileId]);
		}, array_filter($files));
	}
}

