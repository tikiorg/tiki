<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once "vendor_extra/elfinder/php/elFinderConnector.class.php";
include_once "vendor_extra/elfinder/php/elFinder.class.php";
include_once "vendor_extra/elfinder/php/elFinderVolumeDriver.class.php";

include_once 'lib/jquery_tiki/elfinder/elFinderVolumeTikiFiles.class.php';
include_once 'lib/jquery_tiki/elfinder/tikiElFinder.php';

class Services_File_FinderController
{
	private $fileController;

	private $parentIds;

	function setUp()
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y') {
			throw new Services_Exception_Disabled('feature_file_galleries');
		}
		if ($prefs['fgal_elfinder_feature'] != 'y') {
			throw new Services_Exception_Disabled('fgal_elfinder_feature');
		}
		$this->fileController = new Services_File_Controller();
		$this->fileController->setUp();

		$this->parentIds = null;
	}

	/**********************
	 * elFinder functions *
	 *********************/

	/***
	 * Main "connector" to handle all requests from elfinder
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception_Disabled
	 */

	public function action_finder($input)
	{
		global $prefs, $user;


		if ($this->parentIds === null) {
			$ids = TikiLib::lib('filegal')->getGalleriesParentIds();
			$this->parentIds = array( 'galleries' => array(), 'files' => array() );
			foreach ($ids as $id) {
				if ($id['parentId'] > 0) {
					$this->parentIds['galleries'][(int) $id['galleryId']] = (int) $id['parentId'];
				}
			}
			$tiki_files = TikiDb::get()->table('tiki_files');
			$this->parentIds['files'] = $tiki_files->fetchMap('fileId', 'galleryId', array());

		}

		// turn off some elfinder commands here too (stops the back-end methods being accessible)
		$disabled = array('mkfile', 'edit', 'archive', 'resize');
		// done so far: 'rename', 'rm', 'duplicate', 'upload', 'copy', 'cut', 'paste', 'mkdir', 'extract',

		// check for a "userfiles" gallery - currently although elFinder can support more than one root, it always starts in the first one
		$opts = array(
			'debug' => true,
			'roots' => array(),
		);

		$rootDefaults = array(
			'driver' => 'TikiFiles', // driver for accessing file system (REQUIRED)
//			'path' => $rootId, // tiki root filegal - path to files (REQUIRED) - to be filled in later
			'disabled' => $disabled,
//			'URL'           => 									// URL to files (seems not to be REQUIRED)
			'accessControl' => array($this, 'elFinderAccess'), // obey tiki perms
			'uploadMaxSize' => ini_get('upload_max_filesize'),
			'accessControlData' => array(
				'deepGallerySearch' => $input->deepGallerySearch->int(),
				'parentIds' => $this->parentIds,
			),
		);

		// gallery to start in
		$startGallery = $input->defaultGalleryId->int();

		if ($startGallery) {
			$gal_info = TikiLib::lib('filegal')->get_file_gallery_info($startGallery);
			if (!$gal_info) {
				TikiLib::lib('errorreport')->report(tr('Gallery ID %0 not found', $startGallery));
				$startGallery = $prefs['fgal_root_id'];
			}
		}

		// 'startPath' not functioning with multiple roots as yet (https://github.com/Studio-42/elFinder/issues/351)
		// so work around it for now with startRoot

		$opts['roots'][] = array_merge(
			// normal file gals
			array(
				'path' => $prefs['fgal_root_id'],		// should be a function?
			),
			$rootDefaults
		);
		$startRoot = 0;

		if (!empty($user) && $prefs['feature_userfiles'] == 'y' && $prefs['feature_use_fgal_for_user_files'] == 'y') {

			if ($startGallery && $startGallery == $prefs['fgal_root_user_id'] && ! Perms::get('file gallery', $startGallery)->admin_file_galleries) {
				$startGallery = (int) TikiLib::lib('filegal')->get_user_file_gallery();
			}
			$userRootId = $prefs['fgal_root_user_id'];

			if ($startGallery != $userRootId) {

				$gal_info = TikiLib::lib('filegal')->get_file_gallery_info($startGallery);
				if ($gal_info['type'] == 'user') {
					$startRoot = count($opts['roots']);
				}
			} else {
				$startRoot = count($opts['roots']);
			}
			$opts['roots'][] = array_merge(
				array(
					'path' => $userRootId,		// should be $prefs['fgal_root_id']?
				),
				$rootDefaults
			);

		}

		if ($prefs['feature_wiki_attachments'] == 'y' && $prefs['feature_use_fgal_for_wiki_attachments'] === 'y') {
			if ($startGallery && $startGallery == $prefs['fgal_root_wiki_attachments_id']) {
				$startRoot = count($opts['roots']);
			}
			$opts['roots'][] = array_merge(
				array(
					'path' => $prefs['fgal_root_wiki_attachments_id'],		// should be $prefs['fgal_root_id']?
				),
				$rootDefaults
			);
		}

		if ($startGallery) {
			$opts['startRoot'] = $startRoot;
			$d = $opts['roots'][$startRoot]['path'] == $startGallery ? '' : 'd_';	// needs to be the cached name in elfinder (with 'd_' in front) unless it's the root id
			$opts['roots'][$startRoot]['startPath'] = $d . $startGallery;
		}

/* thumb size not working due to css issues - tried this in setup/javascript.php but needs extensive css overhaul to get looking right
		if ($prefs['fgal_elfinder_feature'] === 'y') {
			$tmbSize = (int) $prefs['fgal_thumb_max_size'] / 2;
			TikiLib::lib('header')->add_css(".elfinder-cwd-icon {width:{$tmbSize}px; height:{$tmbSize}px;}");	// def 48
			$tmbSize += 4;	// def 52
			TikiLib::lib('header')->add_css(".elfinder-cwd-view-icons .elfinder-cwd-file-wrapper {width:{$tmbSize}px; height:{$tmbSize}px;}");
			$tmbSize += 28; $tmbSizeW = $tmbSize + 40;	// def 120 x 80
			TikiLib::lib('header')->add_css(".elfinder-cwd-view-icons .elfinder-cwd-file {width: {$tmbSizeW}px;height: {$tmbSize}px;}");
		}
*/
		// run elFinder
		$elFinder = new tikiElFinder($opts);
		$connector = new elFinderConnector($elFinder);

		$filegallib = TikiLib::lib('filegal');
		if ($input->cmd->text() === 'tikiFileFromHash') {	// intercept tiki only commands
			$fileId = $elFinder->realpath($input->hash->text());
			if (strpos($fileId, 'f_') !== false) {
				$info = $filegallib->get_file(str_replace('f_', '', $fileId));
			} else {
				$info = $filegallib->get_file_gallery(str_replace('d_', '', $fileId));
			}
			$params = array();
			if ($input->filegals_manager->text()) {
				$params['filegals_manager'] = $input->filegals_manager->text();
			}
			if ($input->insertion_syntax->text()) {
				$params['insertion_syntax'] = $input->insertion_syntax->text();
			}
			$info['wiki_syntax'] = $filegallib->getWikiSyntax($info['galleryId'], $info, $params);
			$info['data'] = '';	// binary data makes JSON fall over
			return $info;
		} else if ($input->cmd->text() === 'file') {

			// intercept download command and use tiki-download_file so the mime type and extension is correct
			$fileId = $elFinder->realpath($input->target->text());
			if (strpos($fileId, 'f_') !== false) {
				global $base_url;

				$fileId = str_replace('f_', '', $fileId);
				$display = '';

				$url = $base_url . 'tiki-download_file.php?fileId=' . $fileId;

				if (! $input->download->int()) {	// images can be displayed

					$info = $filegallib->get_file($fileId);

					if (strpos($info['filetype'], 'image/') !== false) {

						$url .= '&display';

					} else if ($prefs['fgal_viewerjs_feature'] === 'y' &&
							($info['filetype'] === 'application/pdf' or
									strpos($info['filetype'], 'application/vnd.oasis.opendocument.') !== false)) {

						$url = \ZendOpenId\OpenId::absoluteUrl($prefs['fgal_viewerjs_uri']) . '#' . $url;
					}
				}

				TikiLib::lib('access')->redirect($url);
				return array();
			}
		}

		// elfinder needs "raw" $_GET or $_POST
		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			$_POST = $input->asArray();
		} else {
			$_GET = $input->asArray();
		}

		$connector->run();
		// deals with response

		return array();

	}

	/**
	 * elFinderAccess "accessControl" callback.
	 *
	 * @param  string  $attr  attribute name (read|write|locked|hidden)
	 * @param  string  $path  file path relative to volume root directory started with directory separator
	 * @param $data
	 * @param $volume
	 * @return bool|null
	 */
	function elFinderAccess($attr, $path, $data, $volume)
	{

		$ar = explode('_', $path);
		$visible = true;		// for now
		if (count($ar) === 2) {
			$isgal = $ar[0] === 'd';
			$id = $ar[1];
			if ($isgal) {
				$visible = $this->isVisible($id, $data, $isgal);
			} else {
				$visible = $this->isVisible($this->parentIds['files'][$id], $data, $isgal);
			}
		} else {
			$isgal = true;
			$id = $path;
		}

		if ($isgal) {
			$perms = TikiLib::lib('tiki')->get_perm_object($id, 'file gallery', TikiLib::lib('filegal')->get_file_gallery_info($id));
		} else {
			$perms = TikiLib::lib('tiki')->get_perm_object($id, 'file', TikiLib::lib('filegal')->get_file($id));
		}

		switch($attr) {
			case 'read':
				if ($isgal) {
					return $visible && $perms['tiki_p_view_file_gallery'] === 'y';
				} else {
					return $visible && $perms['tiki_p_download_files'] === 'y';
				}
			case 'write':
				if ($isgal) {
					return $visible && ($perms['tiki_p_admin_file_galleries'] === 'y' || $perms['tiki_p_upload_files'] === 'y');
				} else {
					return $visible && ($perms['tiki_p_edit_gallery_file'] === 'y' || $perms['tiki_p_remove_files'] === 'y');
				}
			case 'locked':
			case 'hidden':
				return !$visible;
			default:
				return false;

		}
	}

	private function isVisible($id, $data, $isgal)
	{
		$visible = true;

		if (!empty($data['startPath'])) {
			if ($data['startPath'] == $id) { // is startPath
				$visible = true;
				return $visible;
			} else {
				$isParentOf = $this->isParentOf($id, $data['startPath'], $this->parentIds['galleries']);

				if (isset($data['deepGallerySearch']) && $data['deepGallerySearch'] == 0) { // not startPath and not deep
					if ($isParentOf && $isgal) {
						$visible = true;
						return $visible;
					} else {
						$visible = false;
						return $visible;
					}
				} else {
					if ($isParentOf && $isgal) {
						$visible = true;
					} else {
						$visible = false;
					}
					$pid = $this->parentIds['galleries'][$id];
					while ($pid) {
						if ($pid == $data['startPath']) {
							$visible = true;
							break;
						}
						$pid = $this->parentIds['galleries'][$pid];
					}
					return $visible;
				}
			}
		}
		return $visible;
	}

	private function isParentOf( $id, $child, $parentIds)
	{
		if (!isset($parentIds[$child])) {
			return false;
		} else if ($parentIds[$child] == $id) {
			return true;
		} else {
			return $this->isParentOf($child, $parentIds[$child], $parentIds);
		}
	}

}

