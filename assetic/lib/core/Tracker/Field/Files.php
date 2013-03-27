<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Files extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		global $prefs;

		return array(
			'FG' => array(
				'name' => tr('Files'),
				'description' => tr('Attached and upload files stored in the file galleries to the tracker item.'),
				'prefs' => array('trackerfield_files', 'feature_file_galleries'),
				'tags' => array('advanced'),
				'help' => 'Files Tracker Field',
				'default' => 'y',
				'params' => array(
					'galleryId' => array(
						'name' => tr('Gallery ID'),
						'description' => tr('File gallery to upload new files into.'),
						'filter' => 'int',
					),
					'filter' => array(
						'name' => tr('MIME Type Filter'),
						'description' => tr('Mask for accepted MIME types in the field'),
						'filter' => 'text',
					),
					'count' => array(
						'name' => tr('File Count'),
						'description' => tr('Maximum number of files to be attached on the field.'),
						'filter' => 'int',
					),
					'displayImages' => array(
						'name' => tr('Display Images'),
						'description' => tr('Show files as images or object links'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Links'),
							1 => tr('Images'),
						),
					),
					'imageParams' => array(
						'name' => tr('Image parameters'),
						'description' => tr('URL encoded params used as in the {img} plugin. e.g.') . ' "max=400&desc=namedesc&stylebox=block"',
						'filter' => 'text',
					),
					'imageParamsForLists' => array(
						'name' => tr('Image parameters for lists'),
						'description' => tr('URL encoded params used as in the {img} plugin. e.g.') . ' "thumb=mouseover&rel="',
						'filter' => 'text',
					),
					'deepGallerySearch' => array(
						'name' => tr('Include Child Galleries'),
						'description' => tr('Use files from child galleries as well.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'replace' => array(
						'name' => tr('Replace Existing File'),
						'description' => tr('Replace existing file if any, instead of uploading new one.'),
						'filter' => 'alpha',
						'default' => 'n',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
					),
					'browseGalleryId' => array(
						'name' => tr('Browse Gallery ID'),
						'description' => tr('File gallery browse files. Use 0 for root file gallery. (requires elFinder feature - experimental)'),
						'filter' => 'int',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $prefs;
		$filegallib = TikiLib::lib('filegal');

		$galleryId = (int) $this->getOption('galleryId');
		$count = (int) $this->getOption('count');
		$deepGallerySearch = (boolean) $this->getOption('deepGallerySearch');

		// to use the user's userfiles gallery enter the fgal_root_user_id which is often (but not always) 2
		if ($prefs['feature_use_fgal_for_user_files'] === 'y' && $galleryId == $prefs['fgal_root_user_id']) {
			$galleryId = (int) $filegallib->get_user_file_gallery();
		}

		$value = '';
		$ins_id = $this->getInsertId();
		if (isset($requestData[$ins_id])) {
			// Incoming data from form

			// Get the list of selected file IDs from the text field
			$value = $requestData[$ins_id];
			$fileIds = explode(',', $value);

			// Add manually uploaded files (non-HTML5 browsers only)
			if (isset($_FILES[$ins_id]['name']) && is_array($_FILES[$ins_id]['name'])) {
				foreach (array_keys($_FILES[$ins_id]['name']) as $index) {
					$fileIds[] = $this->handleUpload(
						$galleryId,
						array(
							'name' => $_FILES[$ins_id]['name'][$index],
							'type' => $_FILES[$ins_id]['type'][$index],
							'size' => $_FILES[$ins_id]['size'][$index],
							'tmp_name' => $_FILES[$ins_id]['tmp_name'][$index],
						)
					);
				}
			}

			// Remove missed uploads
			$fileIds = array_filter($fileIds);

			// Keep only the last files if a limit is applied
			if ($count) {
				$fileIds = array_slice($fileIds, -$count);
			}

			// Obtain the info for display and filter by type if specified
			$fileInfo = $this->getFileInfo($fileIds);
			$fileInfo = array_filter($fileInfo, array($this, 'filterFile'));

			// Rebuild the database value
			$value = implode(',', array_keys($fileInfo));
		} else {
			$value = $this->getValue();

			// Obtain the information from the database for display
			$fileIds = array_filter(explode(',', $value));
			$fileInfo = $this->getFileInfo($fileIds);

		}

		if ($deepGallerySearch) {
			$gallery_list = null;
			$filegallib->getGalleryIds($gallery_list, $galleryId, 'list');
			$gallery_list = implode(' or ', $gallery_list);
		} else {
			$gallery_list = $galleryId;
		}

		if ($this->getOption('displayImages') == 'y' && $fileIds) {
			$firstfile = $fileIds[0];
		} else {
			$firstfile = 0;
		}

		$galinfo = $filegallib->get_file_gallery($galleryId);
		if ($prefs['feature_use_fgal_for_user_files'] !== 'y' || $galinfo['type'] !== 'user') {
			$perms = Perms::get('file gallery', $galleryId);
			$canUpload = $perms->upload_files;
		} else {
			global $user;
			$perms = TikiLib::lib('tiki')->get_local_perms($user, $galleryId, 'file gallery', $galinfo, false);		//get_perm_object($galleryId, 'file gallery', $galinfo);
			$canUpload = $perms['tiki_p_upload_files'] === 'y';
		}


		return array(
			'galleryId' => $galleryId,
			'canUpload' => $canUpload,
			'limit' => $count,
			'files' => $fileInfo,
			'firstfile' => $firstfile,
			'value' => $value,
			'filter' => $this->getOption('filter'),
			'gallerySearch' => $gallery_list,
		);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/files.tpl', $context);
	}

	function renderOutput($context = array())
	{
		global $prefs;
		global $mimetypes; include ('lib/mime/mimetypes.php');
		$galleryId = (int)$this->getOption('galleryId');

		if (!isset($context['list_mode'])) {
			$context['list_mode'] = 'n';
		}
		$value = $this->getValue();

		if ($context['list_mode'] === 'csv') {
			return $value;
		}

		$ret = '';
		if (!empty($value)) {
			if ($this->getOption('displayImages')) { // images
				$params = array(
					'fileId' => $value,
				);
				if ($context['list_mode'] === 'y') {
					$params['thumb'] = $context['list_mode'];
					$params['rel'] = 'box[' . $this->getInsertId() . ']';
					$otherParams = $this->getOption('imageParamsForLists');
				} else {
					$otherParams = $this->getOption('imageParams');
				}
				if ($otherParams) {
					parse_str($otherParams, $otherParams);
					$params = array_merge($params, $otherParams);
				}

				include_once('lib/wiki-plugins/wikiplugin_img.php');
				$params['fromFieldId'] = $this->getConfiguration('fieldId');
				$params['fromItemId'] = $this->getItemId();
				$ret = wikiplugin_img('', $params, 0);
				$ret = preg_replace('/~\/?np~/', '', $ret);
			} else {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');
				$ret = '<ol>';

				foreach ($this->getConfiguration('files') as $fileId => $file) {
					$ret .= '<li>';
					$ret .= smarty_function_object_link(array('type' => 'file', 'id' => $fileId, 'title' => $file['name']), $smarty);

					$globalperms = Perms::get(array( 'type' => 'file gallery', 'object' => $galleryId ));

					if (
						$prefs['feature_draw'] == 'y' &&
						$globalperms->upload_files == 'y' &&
						($file['filetype'] == $mimetypes["svg"] ||
						$file['filetype'] == $mimetypes["gif"] ||
						$file['filetype'] == $mimetypes["jpg"] ||
						$file['filetype'] == $mimetypes["png"] ||
						$file['filetype'] == $mimetypes["tiff"])
					) {
						$ret .= " <a href='tiki-edit_draw.php?fileId=" . $file['fileId'] . "' onclick='return $(this).ajaxEditDraw();'  title='Edit: ".$file['name']."' data-fileid='".$file['fileId']."' data-galleryid='".$file['galleryId']."'>
							<img width='16' height='16' class='icon' alt='Edit' src='img/icons/page_edit.png' />
						</a>";
					}

					$ret .= '</li>';
				}
				$ret .= '</ol>';
			}
		}
		return $ret;
	}

	function handleSave($value, $oldValue)
	{
		$new = array_diff(explode(',', $value), explode(',', $oldValue));
		$remove = array_diff(explode(',', $oldValue), explode(',', $value));

		$itemId = $this->getItemId();

		$relationlib = TikiLib::lib('relation');
		$relations = $relationlib->get_relations_from('trackeritem', $itemId, 'tiki.file.attach');
		foreach ($relations as $existing) {
			if ($existing['type'] != 'file') {
				continue;
			}

			if (in_array($existing['itemId'], $remove)) {
				$relationlib->remove_relation($existing['relationId']);
			}
		}

		foreach ($new as $fileId) {
			$relationlib->add_relation('tiki.file.attach', 'trackeritem', $itemId, 'file', $fileId);
		}

		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
	}

	function filterFile($info)
	{
		$filter = $this->getOption('filter');

		if (! $filter) {
			return true;
		}

		$parts = explode('*', $filter);
		$parts = array_map('preg_quote', $parts, array_fill(0, count($parts), '/'));

		$body = implode('[\w-]*', $parts);

		// Force begin, ignore end which may contain charsets or other attributes
		return preg_match("/^$body/", $info['filetype']);
	}

	private function getFileInfo($ids)
	{
		$db = TikiDb::get();
		$table = $db->table('tiki_files');

		$data = $table->fetchAll(
			array(
				'fileId',
				'name',
				'filetype',
				'archiveId'
			),
			array(
				'fileId' => $table->in($ids),
			)
		);

		$out = array();
		foreach ($data as $info) {
			$out[$info['fileId']] = $info;
		}

		return $out;
	}

	private function handleUpload($galleryId, $file)
	{
		if (empty($file['tmp_name'])) {
			// Not an actual file upload attempt, just skip
			return false;
		}

		if (! is_uploaded_file($file['tmp_name'])) {
			TikiLib::lib('errorreport')->report(tr('Problem with uploaded file: "%0"', $file['name']));
			return false;
		}

		$filegallib = TikiLib::lib('filegal');
		$gal_info = $filegallib->get_file_gallery_info($galleryId);

		if (! $gal_info) {
			TikiLib::lib('errorreport')->report(tr('No gallery for uploaded file, galleryId=%0', $galleryId));
			return false;
		}

		$perms = Perms::get('file gallery', $galleryId);
		if (! $perms->upload_files) {
			TikiLib::lib('errorreport')->report(tr('No permissions to upload file to gallery "%0"', $gal_info['name']));
			return false;
		}

		$fileIds = $this->getConfiguration('files');

		if ($this->getOption('displayImages') == 'y' && is_array($fileIds) && count($fileIds) > 0) {
			return $filegallib->update_single_file($gal_info, $file['name'], $file['size'], $file['type'], file_get_contents($file['tmp_name']), $fileIds[0]);
		} else {
			return $filegallib->upload_single_file($gal_info, $file['name'], $file['size'], $file['type'], file_get_contents($file['tmp_name']));
		}
	}
}

