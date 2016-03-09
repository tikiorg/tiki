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
			'uploadInModal' => $input->uploadInModal->int(),
			'files' => $this->getFilesInfo((array) $input->file->int()),
		);
	}

	function action_upload($input)
	{
		if ($input->files->array()) {
			return;
		}

		$gal_info = $this->checkTargetGallery($input);

		$fileId = $input->fileId->int();
		$asuser = $input->user->text();

		if (isset($_FILES['data'])) {
			// used by $this->action_upload_multiple and file gallery Files fields (possibly others)
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

		if (empty($name) || $size == 0 || empty($data)) {
			throw new Services_Exception(tr('File could not be uploaded. File empty.'), 406);
		}

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

	/**
	 * Uploads several files at once, currently from jquery_upload when file_galleries_use_jquery_upload pref is enabled
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_NotAvailable
	 */
	function action_upload_multiple($input)
	{
		global $user;
		$filegallib = TikiLib::lib('filegal');
		$errorreportlib = TikiLib::lib('errorreport');
		$output = ['files' => []];

		if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {

			// a few other params that are still arrays but shouldn't be (mostly)
			if (is_array($input->galleryId->asArray())) {
				$input->offsetSet('galleryId', $input->galleryId->asArray()[0]);
			}
			if (is_array($input->hit_limit->asArray())) {
				$input->offsetSet('hit_limit', $input->hit_limit->asArray()[0]);
			}
			if (is_array($input->isbatch->asArray())) {
				$input->offsetSet('isbatch', $input->isbatch->asArray()[0]);
			}
			if (is_array($input->deleteAfter->asArray())) {
				$input->offsetSet('deleteAfter', $input->deleteAfter->asArray()[0]);
			}
			if (is_array($input->deleteAfter_unit->asArray())) {
				$input->offsetSet('deleteAfter_unit', $input->deleteAfter_unit->asArray()[0]);
			}
			if (is_array($input->author->asArray())) {
				$input->offsetSet('author', $input->author->asArray()[0]);
			}
			if (is_array($input->user->asArray())) {
				$input->offsetSet('user', $input->user->asArray()[0]);
			}
			if (is_array($input->listtoalert->asArray())) {
				$input->offsetSet('listtoalert', $input->listtoalert->asArray()[0]);
			}

			for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
				if (is_uploaded_file($_FILES['files']['tmp_name'][$i])) {
					$_FILES['data']['name'] = $_FILES['files']['name'][$i];
					$_FILES['data']['size'] = $_FILES['files']['size'][$i];
					$_FILES['data']['type'] = $_FILES['files']['type'][$i];
					$_FILES['data']['tmp_name'] = $_FILES['files']['tmp_name'][$i];

					// do the actual upload
					$file = $this->action_upload($input);

					if (!empty($file['fileId'])) {
						$file['info'] =  $filegallib->get_file_info($file['fileId']);
						// when stored in the database the file contents is here and should not be sent back to the client
						$file['info']['data'] = null;
						$file['syntax'] = $filegallib->getWikiSyntax($file['galleryId'], $file['info'], $input->asArray());
					}

					if ($input->isbatch->word() && stripos($input->type->text(), 'zip') !== false) {
						$errors = [];
						$perms = Perms::get(['type' => 'file', 'object' => $file['fileId']]);
						if ($perms->batch_upload_files) {
							try {
								$filegallib->process_batch_file_upload(
									$file['galleryId'],
									$_FILES['files']['tmp_name'][$i],
									$user,
									'',
									$errors
								);
							} catch (Exception $e) {
								$errorreportlib->report($e->getMessage());
							}
							if ($errors) {
								foreach ($errors as $error) {
									$errorreportlib->report($error);
								}
							} else {
								$file['syntax'] = tr('Batch file processed: "%0"', $file['name']);	// cheeky?
							}
						} else {
							$errorreportlib->report(tra('You don\'t have permission to upload zipped file packages'));
						}
					}


					$output['files'][] = $file;
				} else {
					throw new Services_Exception_NotAvailable(tr('File could not be uploaded.'));
				}
			}

			if ($input->autoupload->word()) {
				TikiLib::lib('user')->set_user_preference($user, 'filegals_autoupload', 'y');
			} else {
				TikiLib::lib('user')->set_user_preference($user, 'filegals_autoupload', 'n');
			}
		} else {
			throw new Services_Exception_NotAvailable(tr('File could not be uploaded.'));
		}

		return $output;

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

