<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Files extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		global $prefs;

		$options = array(
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
						'legacy_index' => 0,
						'profile_reference' => 'file_gallery',
					),
					'filter' => array(
						'name' => tr('MIME Type Filter'),
						'description' => tr('Mask for accepted MIME types in the field'),
						'filter' => 'text',
						'legacy_index' => 1,
					),
					'count' => array(
						'name' => tr('File Count'),
						'description' => tr('Maximum number of files to be attached on the field.'),
						'filter' => 'int',
						'legacy_index' => 2,
					),
					'displayMode' => array(
						'name' => tr('Display Mode'),
						'description' => tr('Show files as object links or via a wiki plugin (img so far)'),
						'filter' => 'word',
						'options' => array(
							'' => tr('Links'),
							'barelink' => tr('Bare Links'),
							'img' => tr('Images'),
							'vimeo' => tr('Vimeo'),
							'googleviewer' => tr('Google Viewer'),
							'moodlescorm' => tr('Moodle Scorm Viewer'),
						),
						'legacy_index' => 3,
					),
					'displayParams' => array(
						'name' => tr('Display parameters'),
						'description' => tr('URL-encoded parameters used such as in the {img} plugin, for example,.') . ' "max=400&desc=namedesc&stylebox=block"',
						'filter' => 'text',
						'legacy_index' => 4,
					),
					'displayParamsForLists' => array(
						'name' => tr('Display parameters for lists'),
						'description' => tr('URL-encoded parameters used such as in the {img} plugin, for example,.') . ' "thumb=box&max=60"',
						'filter' => 'text',
						'legacy_index' => 5,
					),
					'deepGallerySearch' => array(
						'name' => tr('Include Child Galleries'),
						'description' => tr('Use files from child galleries as well.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 6,
					),
					'replace' => array(
						'name' => tr('Replace Existing File'),
						'description' => tr('Replace the existing file, if any, instead of uploading a new one.'),
						'filter' => 'alpha',
						'default' => 'n',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
						'legacy_index' => 7,
					),
					'browseGalleryId' => array(
						'name' => tr('Browse Gallery ID'),
						'description' => tr('File gallery browse files. Use 0 for root file gallery. (requires elFinder feature - experimental)') . '. ' . tr('Restrict permissions to view the file gallery to hide the button.') ,
						'filter' => 'int',
						'legacy_index' => 8,
						'profile_reference' => 'file_gallery',
					),
					'duplicateGalleryId' => array(
						'name' => tr('Duplicate Gallery ID'),
						'description' => tr('File gallery to duplicate files into when copying the tracker item. 0 or empty means do not duplicate (default).'),
						'filter' => 'int',
						'legacy_index' => 9,
						'profile_reference' => 'file_gallery',
					),
					'indexGeometry' => array(
						'name' => tr('Index As Map Layer'),
						'description' => tr('Index the files in a specific format for use in map searchlayers to display trails and features.'),
						'filter' => 'text',
						'default' => '',
						'options' => array(
							'' => tr('No'),
							'geojson' => tr('GeoJSON'),
							'gpx' => tr('GPX'),
						),
						'legacy_index' => 10,
					),
					'uploadInModal' => array(
						'name' => tr('Upload In Modal'),
						'description' => tr('Upload files in a new modal window.'),
						'filter' => 'alpha',
						'default' => 'y',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
						'legacy_index' => 11,
					)
				),
			),
		);
		if (isset($prefs['vimeo_upload']) && $prefs['vimeo_upload'] === 'y') {
			$options['FG']['params']['displayMode']['description'] = tr('Show files as object links or via a wiki plugin (img, Vimeo)');
			$options['FG']['params']['displayMode']['options']['vimeo'] = tr('Vimeo');
		}
		return $options;
	}

	function getFieldData(array $requestData = array())
	{
		global $prefs;
		$filegallib = TikiLib::lib('filegal');

		$galleryId = (int) $this->getOption('galleryId');
		$count = (int) $this->getOption('count');
		$deepGallerySearch = (boolean) $this->getOption('deepGallerySearch');

		// Support Addon File Gallery API switching
		$api = new TikiAddons_Api_FileGallery;
		$itemId = $this->getItemId();
		$galleryId = $api->mapGalleryId($galleryId, $itemId);

		// to use the user's userfiles gallery enter the fgal_root_user_id which is often (but not always) 2
		$galleryId = $filegallib->check_user_file_gallery($galleryId);

		$value = '';
		$ins_id = $this->getInsertId();
		if (isset($requestData[$ins_id])) {
			// Incoming data from form

			// Get the list of selected file IDs from the text field
			$value = $requestData[$ins_id];
			$fileIds = explode(',', $value);

			// Remove missed uploads
			$fileIds = array_filter($fileIds);

			// Obtain the info for display and filter by type if specified
			$fileInfo = $this->getFileInfo($fileIds);
			$fileInfo = array_filter($fileInfo, array($this, 'filterFile'));

			// Rebuild the database value, but preserve the order the files have been attached to the item
			foreach ($fileIds as & $fileId) {
				if (!isset($fileInfo[$fileId])) {
					$fileId = 0;
				}
			}

			// Keep only the last files if a limit is applied
			if ($count) {
				$fileIds = array_filter($fileIds);
				$fileIds = array_slice($fileIds, -$count);
				$value = implode(',', $fileIds);
			} else {
				$value = implode(',', array_filter($fileIds));
			}
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

		if ($this->getOption('displayMode') == 'img' && $fileIds) {
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
		global $prefs;

		if ($prefs['fgal_tracker_existing_search']) {
			if ($this->getOption('browseGalleryId')) {
				$defaultGalleryId = $this->getOption('browseGalleryId');
			} else if ($this->getOption('galleryId')) {
				$defaultGalleryId = $this->getOption('galleryId');
			} else {
				$defaultGalleryId = 0;
			}
			$deepGallerySearch = $this->getOption('galleryId');

			$context['onclick'] = 'return openElFinderDialog(this, {
	defaultGalleryId:' . $defaultGalleryId . ',
	deepGallerySearch: ' . $deepGallerySearch . ',
	getFileCallback: function(file,elfinder){ window.handleFinderFile(file,elfinder); },
	eventOrigin:this
});';
		}

		return $this->renderTemplate('trackerinput/files.tpl', $context, array(
			'replaceFile' => 'y' == $this->getOption('replace', 'n'),
		));
	}

	function renderOutput($context = array())
	{
		global $prefs;
		global $mimetypes; include ('lib/mime/mimetypes.php');
		$galleryId = (int)$this->getOption('galleryId');

		// Support Addon File Gallery API switching
		$api = new TikiAddons_Api_FileGallery;
		$itemId = $this->getItemId();
		$galleryId = $api->mapGalleryId($galleryId, $itemId);

		if (!isset($context['list_mode'])) {
			$context['list_mode'] = 'n';
		}
		$value = $this->getValue();

		if ($context['list_mode'] === 'csv') {
			return $value;
		}

		$ret = '';
		if (!empty($value)) {
			if ($this->getOption('displayMode')) { // images etc
				$params = array(
					'fileId' => $value,
				);
				if ($context['list_mode'] === 'y') {
					$otherParams = $this->getOption('displayParamsForLists');
				} else {
					$otherParams = $this->getOption('displayParams');
				}
				if ($otherParams) {
					parse_str($otherParams, $otherParams);
					$params = array_merge($params, $otherParams);
				}
				$params['fromFieldId'] = $this->getConfiguration('fieldId');
				$params['fromItemId'] = $this->getItemId();
				$item = Tracker_Item::fromInfo($this->getItemData());
				$params['checkItemPerms'] = $item->canModify() ? 'n' : 'y';

				if ($this->getOption('displayMode') == 'img') { // img

					if ($context['list_mode'] === 'y') {
						$params['thumb'] = $context['list_mode'];
						$params['rel'] = 'box[' . $this->getInsertId() . ']';
					}
					include_once('lib/wiki-plugins/wikiplugin_img.php');
					$ret = wikiplugin_img('', $params);

				} else if ($this->getOption('displayMode') == 'vimeo') {	// Vimeo videos stored as filegal REMOTEs

					include_once('lib/wiki-plugins/wikiplugin_vimeo.php');
					$ret = wikiplugin_vimeo('', $params);

				} else if ($this->getOption('displayMode') == 'moodlescorm') {

					include_once('lib/wiki-plugins/wikiplugin_playscorm.php');
					foreach ($this->getConfiguration('files') as $fileId => $file) {
						$params['fileId'] = $fileId;
						$ret .= wikiplugin_playscorm('', $params);
					}

				} else if ($this->getOption('displayMode') == 'googleviewer') {
					if ($prefs['auth_token_access'] != 'y') {
						$ret = tra('Token access needs to be enabled for Google viewer to be used');
					} else {
						$files = array();
						foreach ($this->getConfiguration('files') as $fileId => $file) {
							global $base_url, $tikiroot, $https_mode;
							if ($https_mode) {
								$scheme = 'https';
							} else {
								$scheme = 'http';
							}
							$googleurl = $scheme . "://docs.google.com/viewer?url=";
							$fileurl = urlencode($base_url . "tiki-download_file.php?fileId=" . $fileId);
							require_once 'lib/auth/tokens.php';
							$tokenlib = AuthTokens::build($prefs);
							$token = $tokenlib->createToken($tikiroot . "tiki-download_file.php",
								array('fileId' => $fileId), array('Registered'), array('timeout' => 300, 'hits' => 3));
							$fileurl .= urlencode("&TOKEN=" . $token);
							$url = $googleurl . $fileurl . '&embedded=true';
							$title = $file['name'];
							$files[] = array('url' => $url, 'title' => $title, 'id' => $fileId);
						}
						$smarty = TikiLib::lib('smarty');
						$smarty->assign('files', $files);
						$ret = $smarty->fetch('trackeroutput/files_googleviewer.tpl');
					}
				} else if ($this->getOption('displayMode') == 'barelink') {					
						$smarty = TikiLib::lib('smarty');
						$smarty->loadPlugin('smarty_function_object_link');
						$smarty->loadPlugin('smarty_modifier_sefurl');
						foreach ($this->getConfiguration('files') as $fileId => $file) {
							$ret .= smarty_modifier_sefurl($file['fileId'], 'file');
						}
				}
				$ret = preg_replace('/~\/?np~/', '', $ret);
			} else {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');
				$ret = '<ol class="tracker-item-files">';

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
						$smarty->loadPlugin('smarty_function_icon');
						$editicon = smarty_function_icon(['name' => 'edit'], $smarty);
						$ret .= " <a href='tiki-edit_draw.php?fileId=" . $file['fileId']
							. "' onclick='return $(this).ajaxEditDraw();' class='tips' title='Edit: ".$file['name']
							."' data-fileid='".$file['fileId']."' data-galleryid='".$galleryId."'>
							$editicon
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

	/**
	 * called from action_clone_item and duplicates the related files if option duplicateGalleryID is set
	 */
	function handleClone()
	{
		global $prefs;

		$oldValue = $this->getValue();
		if ($galleryId = $this->getOption('duplicateGalleryId')) {

			$filegallib = TikiLib::lib('filegal');

			// to use the user's userfiles gallery enter the fgal_root_user_id which is often (but not always) 2
			$galleryId = $filegallib->check_user_file_gallery($galleryId);

			$newIds = array();

			foreach (array_filter(explode(',', $oldValue)) as $fileId) {
				$newIds[] = $filegallib->duplicate_file($fileId, $galleryId);
			}

			return $this->handleSave(implode(',', $newIds), $oldValue);
		}
		return array(
			'value' => $oldValue,
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
			TikiLib::lib('errorreport')->report(tr('You don\'t have permission to upload a file to gallery "%0"', $gal_info['name']));
			return false;
		}

		$fileIds = $this->getConfiguration('files');

		if ($this->getOption('displayMode') == 'img' && is_array($fileIds) && count($fileIds) > 0) {
			return $filegallib->update_single_file($gal_info, $file['name'], $file['size'], $file['type'], file_get_contents($file['tmp_name']), $fileIds[0]);
		} else {
			return $filegallib->upload_single_file($gal_info, $file['name'], $file['size'], $file['type'], file_get_contents($file['tmp_name']));
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		if ($this->getOption('indexGeometry') && $this->getValue()) {
			TikiLib::lib('smarty')->loadPlugin('smarty_modifier_sefurl');
			$urls = array();

			foreach(explode(',', $this->getValue()) as $value) {
				$urls[] = smarty_modifier_sefurl($value, 'file');
			}
			return array(
				'geo_located' => $typeFactory->identifier('y'),
				'geo_file' => $typeFactory->identifier(implode(',', $urls)),
				'geo_file_format' => $typeFactory->identifier($this->getOption('indexGeometry')),
			);
		} else {
			return parent::getDocumentPart($typeFactory);
		}
	}

	function getProvidedFields()
	{
		if ($this->getOption('indexGeometry') && $this->getValue()) {
			return array('geo_located', 'geo_file', 'geo_file_format');
		} else {
			return parent::getProvidedFields();
		}
	}

	function getGlobalFields()
	{
		if ($this->getOption('indexGeometry') && $this->getValue()) {
			return array();
		} else {
			return parent::getGlobalFields();
		}
	}
}

