<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once "lib/jquery/elfinder/php/elFinderConnector.class.php";
include_once "lib/jquery/elfinder/php/elFinder.class.php";
include_once "lib/jquery/elfinder/php/elFinderVolumeDriver.class.php";

include_once 'lib/jquery_tiki/elfinder/elFinderVolumeTikiFiles.class.php';

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

		// turn off most elfinder commands here too (stops the back-end methods being accessible)
		$disabled = array('mkfile', 'edit', 'extract', 'archive', 'resize');
		// done so far: 'rename', 'rm', 'duplicate', 'upload', 'copy', 'cut', 'paste', 'mkdir',

		// check for a "userfiles" gallery - currently although elFinder can support more than one root, it always starts in the first one
		$opts = array(
			'debug' => true,
			'roots' => array(),
		);

		// gallery to start in
		$galleryId = $input->defaultGalleryId->int();
		$rootId = 0;

		if (!$galleryId) {
			$galleryId = $this->fileController->defaultGalleryId;
		}

		if ($user != '' && $prefs['feature_use_fgal_for_user_files'] == 'y' && $galleryId) {

			if ($galleryId == $prefs['fgal_root_user_id'] && ! Perms::get('file gallery', $galleryId)->admin_trackers) {
				$galleryId = (int) TikiLib::lib('filegal')->get_user_file_gallery();
			}

			if ($galleryId == $prefs['fgal_root_user_id']) {
				$rootId = $prefs['fgal_root_user_id'];
			} else {
				$gal_info = TikiLib::lib('filegal')->get_file_gallery_info($galleryId);

				if ($gal_info['type'] == 'user') {
					$rootId = $prefs['fgal_root_user_id'];
				}
			}
		}

		if (!$rootId) {
			$rootId = $this->fileController->defaultGalleryId;		// should be $prefs['fgal_root_id'];
		}

		// 'startParth' not functioning with multiple roots as yet (https://github.com/Studio-42/elFinder/issues/351)
		// so only have one volume at a time (shame)

		$opts['roots'][] = array(
			'driver'        => 'TikiFiles',   					// driver for accessing file system (REQUIRED)
			'path'          => $rootId,							// tiki root filegal - path to files (REQUIRED)
			'disabled'		=> $disabled,
//			'URL'           => 									// URL to files (seems not to be REQUIRED)
			'accessControl' => array($this, 'elFinderAccess'),	// obey tiki perms
			'uploadMaxSize' => ini_get('upload_max_filesize'),
		);

		if ($galleryId) {
			$d = $galleryId != $rootId ? 'd_' : '';				// needs to be the cached name in elfinder (with 'd_' in front) unless it's the root id
			$opts['roots'][0]['startPath'] = $d . $galleryId;
			$opts['roots'][0]['accessControlData'] = array(
				'deepGallerySearch' => $input->deepGallerySearch->int(),
				'parentIds' => $this->parentIds,
			);
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
		$elFinder = new elFinder($opts);
		$connector = new elFinderConnector($elFinder);

		if ($input->cmd->text() === 'tikiFileFromHash') {	// intercept tiki only commands
			$fileId = $elFinder->realpath($input->hash->text());
			$filegallib = TikiLib::lib('filegal');
			$info = $filegallib->get_file(str_replace('f_', '', $fileId));
			$info['wiki_syntax'] = $filegallib->getWikiSyntax($info['galleryId'], $info);
			return $info;
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
			//$objectType = 'file gallery';
			$galleryId = $id;
		} else {
			$objectType = 'file';
			// Seems individual file perms aren't set so use the gallery ones
			$galleryId = $this->parentIds['files'][$id];
		}

		$perms = TikiLib::lib('tiki')->get_perm_object($galleryId, 'file gallery', TikiLib::lib('filegal')->get_file_gallery_info($galleryId));

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

