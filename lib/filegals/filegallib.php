<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class FileGalLib extends TikiLib
{
	function isPodCastGallery($galleryId, $gal_info=null) {
		if (empty($gal_info))
			$gal_info = $this->get_file_gallery_info((int)$galleryId);
		if (($gal_info["type"]=="podcast") || ($gal_info["type"]=="vidcast")) {
			return true;
		} else {
			return false;
		}
	}

	function get_gallery_save_dir($galleryId, $galInfo = null) {
		global $prefs;

		$podCastException = $this->isPodCastGallery($galleryId, $galInfo);

		if ($prefs['fgal_use_db'] == 'y' && ! $podCastException) {
			return false;
		}
		
		if ($podCastException) {
			return $prefs['fgal_podcast_dir'];
		} else {
			return $prefs['fgal_use_dir'];
		}
	}

	private function get_file_checksum($galleryId, $path, $data) {
		global $prefs;

		$savedir = $this->get_gallery_save_dir($galleryId);

		if (false !== $savedir) {
			if ( filesize ($savedir . $path) > 0 ) {
				return md5_file($savedir . $path);
			} else {
				return md5(time());
			}
		} else {
			return md5($data);
		}
	}

	private function find_unique_name($directory, $start) {
		$fhash = md5($start);

		while (file_exists($directory . $fhash)) {
			$fhash = md5(uniqid($fhash));
		}

		return $fhash;
	}

	function get_attachment_gallery( $objectId, $objectType ) {
		switch ( $objectType ) {
			case 'wiki page': return $this->get_wiki_attachment_gallery( $objectId );
		}

		return false;
	}

	function get_wiki_attachment_gallery( $pageName ) {
		global $prefs;

		// Get the Wiki Attachment Gallery for this wiki page or create it if it does not exist
		if ( ! $return = $this->getGalleryId( $pageName, $prefs['fgal_root_wiki_attachments_id'] ) ) {

			// Create the attachment gallery only if the wiki page really exists
			if ( $this->get_page_id_from_name( $pageName ) > 0 ) {

				$return = $this->replace_file_gallery( array(
					'name' => $pageName,
					'user' => 'admin',
					'type' => 'default',
					'public' => 'y',
					'visible' => 'y',
					'parentId' => $prefs['fgal_root_wiki_attachments_id']
				) );
			}
		}

		return $return;
	}

	function get_user_file_gallery() {
		global $user, $prefs;
		$tikilib = TikiLib::lib('tiki');
		
		// Feature check + Anonymous don't have their own Users File Gallery
		if ( $user == '' || $prefs['feature_use_fgal_for_user_files'] == 'n' || $prefs['feature_userfiles'] == 'n' || ( $userId = $tikilib->get_user_id( $user ) ) <= 0  ) {
			return false;
		}

		$conditions = array(
			'type' => 'user',
			'name' => $userId,
			'user' => $user,
			'parentId' => $prefs['fgal_root_user_id']
		);

		if ( $idGallery = $this->table('tiki_file_galleries')->fetchOne('galleryId', $conditions) ) {
			return $idGallery;
		}

		$fgal_info =& $conditions;
		$fgal_info['public'] = 'n';
		$fgal_info['visible'] = 'y';

		// Create the user gallery if it does not exist yet
		$idGallery = $this->replace_file_gallery( $fgal_info );

		return $idGallery;
	}

	function remove_file($fileInfo, $galInfo='', $disable_notifications = false) {
		global $prefs, $smarty, $user;

		if ( empty( $fileInfo['fileId'] ) ) {
			return false;
		}
		$fileId = $fileInfo['fileId'];

		$savedir = $this->get_gallery_save_dir($fileInfo['galleryId'], $galInfo);

		$this->deleteBacklinks(null, $fileId);
		if ($fileInfo['path']) {
			unlink ($savedir . $fileInfo['path']);
		}
		$archives = $this->get_archives($fileId);
		foreach ($archives['data'] as $archive) {
			if ($archive['path']) {
				unlink ($savedir . $archive['path']);
			}
			$this->remove_object('file', $archive['fileId']);
		}

		$files = $this->table('tiki_files');
		$files->delete(array(
			'fileId' => $fileId,
		));
		$files->deleteMultiple(array(
			'archiveId' => $fileId,
		));

		$this->remove_draft($fileId);
		$this->remove_object('file', $fileId);

		//Watches
		if ( ! $disable_notifications ) $this->notify($fileInfo['galleryId'], $fileInfo['name'], $fileInfo['filename'], '', 'remove file', $user);

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Removed', $fileId . '/' . $fileInfo['filename'], 'file', '');
		}

		return true;
	}

	function insert_file($galleryId, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='', $author=null, $created='', $lockedby=NULL, $deleteAfter=NULL, $id=0) {
		global $prefs, $user;
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		$filesTable = $this->table('tiki_files');
		$galleriesTable = $this->table('tiki_file_galleries');

		$name = trim(strip_tags($name));
		$description = strip_tags($description);

		$checksum = $this->get_file_checksum($galleryId, $path, $data);

		if ( $prefs['fgal_allow_duplicates'] !== 'y' && !empty($data) ) {
			$conditions = array('hash' => $checksum);

			if ( $prefs['fgal_allow_duplicates'] === 'different_galleries' ) {
				$conditions['galleryId'] = $galleryId;
			}
			if ( $filesTable->fetchCount($conditions) ) {
				return false;
			}
		}

		$search_data = '';
		if ($prefs['fgal_enable_auto_indexing'] != 'n') {
			$search_data = $this->get_search_text_for_data($data,$path,$type, $galleryId);
			if ($search_data === false) {
				return false;
			}
		}
		if ( empty($created) ) $created = $this->now;

		$fileData = array(
			'galleryId' => $galleryId,
			'name' => trim($name),
			'description' => $description,
			'filename' => $filename,
			'filesize' => $size,
			'filetype' => $type,
			'data' => $data,
			'user' => $creator,
			'created' => $created,
			'hits' => 0,
			'path' => $path,
			'hash' => $checksum,
			'search_data' => $search_data,
			'lastModif' => $this->now,
			'lastModifUser' => $user,
			'comment' => $comment,
			'author' => $author,
			'lockedby' => $lockedby,
			'deleteAfter' => $deleteAfter,
		);

		if (empty($id)) {
			$fileId = $filesTable->insert($fileData);
		} else {
			$filesTable->update($fileData, array(
				'fileId' => $id,
			));
			$fileId = $id;
		}

		$galleriesTable->update(array(
			'lastModif' => $this->now,
		), array(
			'galleryId' => $galleryId,
		));

		if ($prefs['feature_score'] == 'y') {
		    $this->score_event($user, 'fgallery_new_file');
		}

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Uploaded', $galleryId, 'file gallery', "fileId=$fileId&amp;add=$size");
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('files', $fileId);

		//Watches
		$smarty->assign('galleryId', $galleryId);
                $smarty->assign('fname', $name);
                $smarty->assign('filename', $filename);
                $smarty->assign('fdescription', $description);

		$this->notify($galleryId, $name, $filename, $description, 'upload file', $user, $fileId);

		return $fileId;
	}

	/**
	 * Create or update a file draft
	 *
	 * @global array $prefs
	 * @param int $fileId
	 * @param string $filename
	 * @param int $size
	 * @param string $type
	 * @param string $data
	 * @param string $creator
	 * @param string $path
	 * @param string $checksum
	 * @param string $lockedby
	 */
	function insert_draft($fileId,$filename,$size,$type,$data,$creator,$path,$checksum,$lockedby) {
		global $prefs;
		$filesTable = $this->table('tiki_files');
		$fileDraftsTable = $this->table('tiki_file_drafts');

		if ($prefs['feature_file_galleries_save_draft'] == 'y') {
			$oldData = $filesTable->fetchOne('data', array('fileId' => (int) $fileId));

			if (empty($oldData)) {
				return $filesTable->update(array(
					'name' => $filename,
					'filename' => $filename,
					'filesize' => $size,
					'filetype' => $type,
					'data' => $data,
					'user' => $creator,
					'path' => $path,
					'hash' => $checksum,
					'lastModif' => $this->now,
					'lockedby' => $lockedby,
				), array(
					'fileId' => $fileId,
				));
			} else {
				$fileDraftsTable->delete(array(
					'fileId' => (int) $fileId,
					'user' => $creator,
				));

				return (bool) $fileDraftsTable->insert(array(
					'fileId' => $fileId,
					'filename' => $filename,
					'filesize' => $size,
					'filetype' => $type,
					'data' => $data,
					'user' => $creator,
					'path' => $path,
					'hash' => $checksum,
					'lastModif' => $this->now,
					'lockedby' => $lockedby,
				));
			}
		}

		return true;
	}

	/**
	 * Remove all drafts of a file
	 *
	 * @param int $fileId
	 * @param string $user
	 */
	function remove_draft($fileId,$user=null) {
		$fileDraftsTable = $this->table('tiki_file_drafts');

		if (isset($user)) {
			return $fileDraftsTable->delete(array(
				'fileId' => (int) $fileId,
				'user' => $user,
			));
		} else {
			return $fileDraftsTable->deleteMultiple(array(
				'fileId' => (int) $fileId,
			));
		}
	}

	/**
	 * Validate draft and replace real file
	 *
	 * @global string $user
	 * @param int $fileId
	 */
	function validate_draft($fileId) {
		global $prefs, $user;

		$fileDraftsTable = $this->table('tiki_file_drafts');
		$galleriesTable = $this->table('tiki_file_galleries');
		$filesTable = $this->table('tiki_file_galleries');

		if ($prefs['feature_file_galleries_save_draft'] == 'y') {
			if (! $draft = $fileDraftsTable->fetchFullRow(array('fileId' => (int) $fileId, 'user' => $user))) {
				return false;
			}

			$old_file = $filesTable->fetchFullRow(array('fileId' => (int) $fileId));
			$archives = $galleriesTable->fetchOne('archives', array('galleryId' => (int) $old_file['galleryId']));
			$newPath = $draft['path'];

			if ($prefs['fgal_use_db'] == 'n') {
				$savedir = $prefs['fgal_use_dir'];
				$newPath = $this->find_unique_name($savedir, $filesTable->fetchOne('name', array('fileId' => $fileId)));

				if (file_exists($savedir . $old_file['path'])) {
					// Deletes old production file
					@unlink($savedir . $old_file['path']);
				}

				if (file_exists($savedir . $draft['path'])) {
					// Renames draft into new production file
					@rename($savedir . $draft['path'], $savedir . $newPath);
				}
			}

			if ($archives == -1) {
				$filesTable->update(array(
					'path' => $newPath,
					'filename' => $draft['filename'],
					'filesize' => $draft['filesize'],
					'filetype' => $draft['filetype'],
					'data' => $draft['data'],
					'user' => $draft['user'],
					'path' => $draft['path'],
					'hash' => $draft['hash'],
					'lastModif' => $draft['lastModif'],
					'lastModifUser' => $draft['user'],
					'lockedby' => $draft['lockedby'],
				), array(
					'fileId' => (int) $fileId,
				));

				if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' && ( $prefs['fgal_asynchronous_indexing'] != 'y' || ! isset($_REQUEST['fast']) ) ) {
					require_once('lib/search/refresh-functions.php');
					refresh_index('files', $fileId);
				}
			} else {
				$this->save_archive($fileId, $old_file['galleryId'], $archives, $old_file['name'], $old_file['description'], $draft['filename'], $draft['data'], $draft['filesize'], $draft['filetype'], $old_file['creator'], $draft['path'], $old_file['comment'], $old_file['created'], $draft['lockedby']);
			}

			$this->remove_draft($fileId, $user);
		}
	}

	function save_archive($id, $galleryId, $count_archives, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment, $author, $created, $lockedby) {
		global $prefs;

		$filesTable = $this->table('tiki_files');

		if ($prefs['fgal_keep_fileId'] == 'y') {
			$res = $filesTable->fetchFullRow(array('fileId' => $id));
			$res['archiveId'] = $id;
			$res['user'] = $creator;
			$res['lockedby'] = NULL;
			unset($res['fileId']);

			$filesTable->insert($res);
		}

		// Insert and index (for search) the new file
		$idNew = $this->insert_file($galleryId, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment, $author, $created, $lockedby, NULL, $prefs['fgal_keep_fileId']=='y'?$id:0);

		if ($count_archives > 0) {
			$archives = $this->get_archives($id, 0, -1, 'created_asc');

			if ($archives['cant'] >= $count_archives) {
				$toRemove = array();

                $savedir = $this->get_gallery_save_dir($galleryId);

				foreach ($archives['data'] as $i => $values) {
					$toRemove[] = $values['fileId'];
					if ( $values['path'] ) {
						unlink($savedir . $values['path']);
					}
				}

				$filesTable->deleteMultiple(array(
					'fileId' => $filesTable->in($toRemove),
				));
			}
		}
		if ($prefs['fgal_keep_fileId'] != 'y') {
			$filesTable->update(array(
				'archiveId' => $idNew,
				'search_data' => '',
				'user' => $creator,
				'lockedby' => null,
			), array(
				'anyOf' => $filesTable->expr('(`archiveId` = ? OR `fileId` = ?)', array($id, $id)),
			));
		}

		if ($prefs['feature_categories'] == 'y') {
			global $categlib; require_once('lib/categories/categlib.php');
			$categlib->uncategorize_object('file', $id);
		}

		return $idNew;
	}

	function set_file_gallery($file, $gallery) {
		$files = $this->table('tiki_files');
		$files->updateMultiple(array(
			'galleryId' => $gallery,
		), array(
			'anyOf' => $files->expr('(`fileId` = ? OR `archiveId` = ?)', array($file, $file)),
		));

		return true;
	}

	function remove_file_gallery($id, $galleryId=0, $recurse = true) {
		global $prefs;
		$fileGalleries = $this->table('tiki_file_galleries');
		$id = (int)$id;

		if ( $id == $prefs['fgal_root_id'] || $galleryId == $prefs['fgal_root_id']) {
			return false;
		}
		if (empty($galleryId)) {
			$info = $this->get_file_info($id);
			$galleryId = $info['galleryId'];
		}

		$cachelib = TikiLib::lib('cache');
		$cachelib->empty_type_cache('fgals_perms_'.$id."_");
		if (isset($info['galleryId'])) {
			$cachelib->empty_type_cache('fgals_perms_'.$info['galleryId']."_");
		}
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		$fileGalleries->delete(array('galleryId' => $id));

		$this->remove_object('file gallery', $id);

		if ( $filesInfo = $this->get_files_info_from_gallery_id($id, false, false) ) {
			foreach ( $filesInfo as $fileInfo ) $this->remove_file($fileInfo, '', true);
		}

		// If $recurse, also recursively remove children galleries
		if ( $recurse ) {
			$galleries = $fileGalleries->fetchColumn('galleryId', array(
				'parentId' => $id,
				'galleryId' => $fileGalleries->greaterThan(0),
			));
			
			foreach ($galleries as $galleryId) {
				$this->remove_file_gallery($galleryId, $id, true);
			}
		}

		return true;
	}

	function get_file_gallery_info($id) {
		return $this->table('tiki_file_galleries')->fetchFullRow(array(
			'galleryId' => (int) $id,
		));
	}

	function move_file_gallery($galleryId, $new_parent_id) {
		if ( (int)$galleryId <= 0 || (int)$new_parent_id == 0 ) return false;

		$cachelib = TikiLib::lib('cache');
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		return $this->table('tiki_file_galleries')->updateMultiple(array(
			'parentId' => (int) $new_parent_id,
		), array(
			'galleryId' => (int) $galleryId,
		));
	}
	function default_file_gallery() {
		global $prefs;
		return array(
			'name' => '',
			'description' =>'',
			'visible' => 'y',
			'type' => 'default',
			'parentId' => -1,
			'lockable' => 'n',
			'archives' => 0,
			'quota' => $prefs['fgal_quota_default'],
			'image_max_size_x' => 0,
			'image_max_size_y' => 0,
			'backlinkPerms' => 'n',
			'show_backlinks' => 'n',
			'show_deleteAfter' => $prefs['fgal_list_deleteAfter'],
			'show_lastDownload' => 'n',
			'description' => '',
			'sort_mode' => $prefs['fgal_sort_mode'],
			'maxRows' => $prefs['maxRowsGalleries'],
			'max_desc' => 0,
			'subgal_conf' => '',
			'show_id' => $prefs['fgal_list_id'],
			'show_icon' => $prefs['fgal_list_type'],
			'show_name' => $prefs['fgal_list_name'],
			'show_description' => $prefs['fgal_list_description'],
			'show_size' => $prefs['fgal_list_size'],
			'show_created' => $prefs['fgal_list_created'],
			'show_modified' => $prefs['fgal_list_lastModif'],
			'show_creator' => $prefs['fgal_list_creator'],
			'show_author' => $prefs['fgal_list_author'],
			'show_last_user' => $prefs['fgal_list_last_user'],
			'show_comment' => $prefs['fgal_list_comment'],
			'show_files' => $prefs['fgal_list_files'],
			'show_hits' => $prefs['fgal_list_hits'],
			'show_lockedby' => $prefs['fgal_list_lockedby'],
			'show_checked' => $prefs['fgal_show_checked'],
			'show_share' => $prefs['fgal_list_share'],
			'show_explorer' => $prefs['fgal_show_explorer'],
			'show_path' => $prefs['fgal_show_path'],
			'show_slideshow' => $prefs['fgal_show_slideshow'],
			'wiki_syntax' => '',
			'default_view' => $prefs['fgal_default_view'],
			'template' => null,
		);
	}
	function replace_file_gallery($fgal_info) {

		global $prefs;
		$galleriesTable = $this->table('tiki_file_galleries');
		$objectsTable = $this->table('tiki_objects');
		$fgal_info = array_merge($this->default_file_gallery(), $fgal_info);

		// if the user is admin or the user is the same user and the gallery exists
		// then replace if not then create the gallary if the name is unused.
		$fgal_info['name'] = strip_tags($fgal_info['name']);

		$fgal_info['description'] = strip_tags($fgal_info['description']);
		if ($fgal_info['sort_mode'] == 'created_desc') {
			$fgal_info['sort_mode'] = null;
		}

		if (!empty($fgal_info['galleryId']) && $fgal_info['galleryId'] > 0) {
			$fgal_info['lastModif'] = $this->now;
			$galleryId = (int) $fgal_info['galleryId'];

			$galleriesTable->update($fgal_info, array(
				'galleryId' => $galleryId,
			));

			$objectsTable->update(array(
				'name' => $fgal_info['name'],
				'description' => $fgal_info['description'],
			), array(
				'type' => 'file gallery',
				'itemId' => $galleryId,
			));
		} else {
			unset($fgal_info['galleryId']);
			$fgal_info['created'] = $this->now;
			$fgal_info['lastModif'] = $this->now;

			$galleryId = $galleriesTable->insert($fgal_info);

			if ($prefs['feature_score'] == 'y') {
				global $user;
			    $this->score_event($user, 'fgallery_new');
			}
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('file_galleries', $galleryId);

		$cachelib = TikiLib::lib('cache');
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		// event_handler($action,$object_type,$object_id,$options);
		return $galleryId;
	}
	function get_all_galleries_cache_name($user) {
		$tikilib = TikiLib::lib('tiki');
		$categlib = TikiLib::lib('categ');

		$gs = $tikilib->get_user_groups($user);
		$tmp = "";
		if ( is_array($gs) ) {
			$tmp .= implode("\n", $gs); 
		}
		$tmp .= '----'; 
		if ( $jail = $categlib->get_jail() ) {
			$tmp .= implode("\n",$jail);
		}
		return md5($tmp);
	}
	function get_all_galleries_cache_type() {
		return 'fgals_';
	}

	function process_batch_file_upload($galleryId, $file, $user, $description, &$errors) {
		global $prefs, $smarty;

		include_once ('lib/pclzip/pclzip.lib.php');
		include_once ('lib/mime/mimelib.php');
		$extract_dir = 'temp/'.basename($file).'/';
		mkdir($extract_dir);
		$archive = new PclZip($file);
		$archive->extract(PCLZIP_OPT_PATH, $extract_dir, PCLZIP_OPT_REMOVE_ALL_PATH);
		unlink($file);
		$files = array();
		$h = opendir($extract_dir);
		$gal_info = $this->get_file_gallery_info($galleryId);
		$savedir = $this->get_gallery_save_dir($galleryId, $gal_info);

		// check filters
		$upl = 1;
		$errors = array();
		while (($file = readdir($h)) !== false) {
			if ($file != '.' && $file != '..' && is_file($extract_dir.'/'.$file)) {

				if (!empty($prefs['fgal_match_regex'])) {
					if (!preg_match('/'.$prefs['fgal_match_regex'].'/', $file, $reqs)) {
						$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $file;
						$upl = 0;
					}
				}

				if (!empty($prefs['fgal_nmatch_regex'])) {
					if (preg_match('/'.$prefs['fgal_nmatch_regex'].'/', $file, $reqs)) {
						$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $file;
						$upl = 0;
					}
				}

				if (!$this->checkQuota(filesize($extract_dir.$file), $galleryId, $error)) {
					$errors[] = $error;
					$upl = 0;
				}
			}
		}
		if (!$upl) {
			return false;
		}
		rewinddir ($h);
		while (($file = readdir($h)) !== false) {
			if ($file != '.' && $file != '..' && is_file($extract_dir.'/'.$file)) {
				if (false === $data = @file_get_contents($extract_dir.$file)) {
					$errors[] = tra('Cannot open this file:'). "temp/$file";
					return false;
				}
				$fhash = '';

				if (false !== $savedir) {
					// Store on disk
                    $fhash = $this->find_unique_name($savedir, $file);

					if (false === @file_put_contents($savedir . $fhash, $data)) {
						$errors[] = tra('Cannot write to this file:'). $fhash;
						return false;
					}

					$data = '';
				}

				$size = filesize($extract_dir.$file);
				$name = $file;
				$type = tiki_get_mime($extract_dir.$file);
				$fileId = $this->insert_file($galleryId, $name, $description, $name, $data, $size, $type, $user, $fhash);
				unlink ($extract_dir.$file);
			}
		}

		closedir ($h);
		rmdir($extract_dir);
		return true;
	}

	function get_file_info($fileId, $include_search_data = true, $include_data = true, $use_draft = false) {
		global $prefs, $user;

		$return = $this->get_files_info(null, (int)$fileId, $include_search_data, $include_data);

		if (!$return) {
			return false;
		}

		$file = $return[0];

		if ($use_draft && $prefs['feature_file_galleries_save_draft'] == 'y') {
			$draft = $this->table('tiki_file_drafts')->fetchRow(array('filename', 'filesize', 'filetype', 'data', 'user', 'path', 'hash', 'lastModif', 'lockedby'), array(
				'fileId' => (int) $fileId,
				'user' => $user,
			));

			if ($draft) {
				$file = array_merge($file, $draft);
			}
		}

		return $file;
	}

	function get_files_info_from_gallery_id($galleryId, $include_search_data = false, $include_data = false) {
		return $this->get_files_info((int)$galleryId, null, $include_search_data, $include_data);
	}

	function get_files_info($galleryIds = null, $fileIds = null, $include_search_data = false, $include_data = false) {
		$files = $this->table('tiki_files');

		$fields = array('fileId', 'galleryId', 'name', 'description', 'created', 'filename', 'filesize', 'filetype', 'user', 'author', 'hits', 'votes', 'points', 'path', 'reference_url', 'is_reference', 'hash', 'lastModif', 'lastModifUser', 'lockedby', 'comment', 'archiveId');
	
		if ($include_search_data && $include_data) {
			$fields = $files->all();
		} else {
			if ($include_search_data) {
				$fields[] = 'search_data';
			}
			if ($include_data) {
				$fields[] = 'data';
			}
		}

		$conditions = array();

		if ( ! empty($fileIds) ) {
			$conditions['fileId'] = $files->in((array) $fileIds);
		}

		if ( ! empty($galleryIds) ) {
			$conditions['galleryId'] = $files->in((array) $galleryIds);
		}

		return $files->fetchAll($fields, $conditions);
	}

	function update_file($id, $name, $description, $user, $comment = NULL) {

		// Update the fields in the database
		$updateData = array(
			'name' => strip_tags($name),
			'description' => strip_tags($description),
			'lastModif' => $this->now,
			'lastModifUser' => $user,
		);
		if ( ! is_null($comment) ) {
			$updateData['comment'] = $comment;
		}
		$bindvars[] = $id;

		$files = $this->table('tiki_files');

		$result = $files->update($updateData, array(
			'fileId' => $id,
		));

		$galleryId = $files->fetchOne('galleryId', array('fileId' => $id));

		if ( $galleryId >= 0 ) {
			$this->table('tiki_file_galleries')->update(array(
				'lastModif' => $this->now,
			), array(
				'galleryId' => $galleryId,
			));
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('files', $id);

		return $result;
	}

	function replace_file($id, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='', $gal_info, $didFileReplace, $author='', $created='', $lockedby=NULL, $deleteAfter=NULL) {
		global $prefs, $tikilib, $user;

		$filesTable = $this->table('tiki_files');
		$fileDraftsTable = $this->table('tiki_file_drafts');
		$galleriesTable = $this->table('tiki_file_galleries');

		// Update the fields in the database
		$name = trim(strip_tags($name));
		$description = strip_tags($description);

		// User avatar full images are always using db and not file location (at the curent state of feature)
		if (isset($prefs['user_store_file_gallery_picture']) && $prefs['user_store_file_gallery_picture'] == 'y' && $prefs["user_picture_gallery_id"] == $gal_info['galleryId']) {
			$userPictureGallery = true;			
		} else {
			$userPictureGallery = false;
		}

		$checksum = $this->get_file_checksum($gal_info['galleryId'], $path, $data);

		$search_data = '';
		if ($prefs['fgal_enable_auto_indexing'] != 'n') {
			$search_data = $this->get_search_text_for_data($data,$path,$type, $gal_info['galleryId']);
			if ($search_data === false)
				return false;
		}

		$oldPath = '';
		if ($prefs['feature_file_galleries_save_draft'] == 'y') {
			$oldPath = $fileDraftsTable->fetchOne('path', array(
				'fileId' => $id,
				'user' => $user,
			));
		} else {
			$oldPath = $filesTable->fetchOne('path', array(
				'fileId' => $id,
			));
		}

		if ( $gal_info['archives'] == -1 || ! $didFileReplace ) { // no archive
			if ($prefs['feature_file_galleries_save_draft'] == 'y') {
				$result = $filesTable->update(array(
					'name' => $name,
					'description' => $description,
					'lastModifUser' => $user,
					'lastModif' => $this->now,
					'author' => $author,
					'creator' => $creator,
				), array(
					'fileId' => $id,
				));

				if ( ! $result ) {
					return false;
				}

				if ($didFileReplace) {
					if (!$this->insert_draft($id,$filename,$size,$type,$data,$user,$path,$checksum,$lockedby)) {
						return false;
					}
				}

			} else {
				$result = $filesTable->update(array(
					'name' => $name,
					'description' => $description,
					'filename' => $filename,
					'filesize' => $size,
					'filetype' => $type,
					'data' => $data,
					'lastModifUser' => $user,
					'lastModif' => $this->now,
					'path' => $path,
					'hash' => $checksum,
					'search_data' => $search_data,
					'author' => $author,
					'user' => $creator,
					'lockedby' => $lockedby,
					'deleteAfter' => $deleteAfter,
				), array(
					'fileId' => $id,
				));

				if ( ! $result ) {
					return false;
				}
			}

			if ( $didFileReplace && !empty($oldPath) ) {
				$savedir = $this->get_gallery_save_dir($gal_info['galleryId'], $gal_info);

				unlink($savedir . $oldPath);
			}

			require_once('lib/search/refresh-functions.php');
			refresh_index('files', $id);

		} else { //archive the old file : change archive_id, take away from indexation and categorization
			if ($prefs['feature_file_galleries_save_draft'] == 'y') {
				$this->insert_draft($id,$filename,$size,$type,$data,$user,$path,$checksum,$lockedby);
			} else {
				$id = $this->save_archive($id, $gal_info['galleryId'], $gal_info['archives'], $name, $description, $filename, $data, $size, $type, $creator, $path, $comment, $author, $created, $lockedby);
			}
		}

		if ($gal_info['galleryId']) {
			$galleriesTable->update(array(
				'lastModif' => $this->now,
			), array(
				'galleryId' => $gal_info['galleryId'],
			));
		}

		return $id;
	}

	function change_file_handler($mime_type,$cmd) {
		$handlers = $this->table('tiki_file_handlers');

		$mime_type = trim($mime_type);

		$handlers->delete(array('mime_type' => $mime_type));
		$handlers->insert(array(
			'mime_type' => $mime_type,
			'cmd' => $cmd,
		));

		return true;
	}

	function delete_file_handler($mime_type) {
		$handlers = $this->table('tiki_file_handlers');
		return (bool) $handlers->delete(array('mime_type' => $mime_type));
	}

	function get_file_handlers($for_execution = false) {
		$cachelib = TikiLib::lib('cache');

		if ($for_execution && ! $default = $cachelib->getSerialized('file_handlers')) {
			$possibilities = array(
				'application/ms-excel' => array('xls2csv %1'),
				'application/ms-powerpoint' => array('catppt %1'),
				'application/msword' => array('catdoc %1', 'strings %1'),
				'application/pdf' => array('pstotext %1', 'pdftotext %1 -'),
				'application/postscript' => array('pstotext %1'),
				'application/ps' => array('pstotext %1'),
				'application/rtf' => array('catdoc %1'),
				'application/sgml' => array('col -b %1', 'strings %1'),
				'application/vnd.ms-excel' => array('xls2csv %1'),
				'application/vnd.ms-powerpoint' => array('catppt %1'),
				'application/x-msexcel' => array('xls2csv %1'),
				'application/x-pdf' => array('pstotext %1'),
				'application/x-troff-man' => array('man -l %1'),
				'text/enriched' => array('col -b %1', 'strings %1'),
				'text/html' => array('elinks -dump -no-home %1'),
				'text/plain' => array('col -b %1', 'strings %1'),
				'text/richtext' => array('col -b %1', 'strings %1'),
				'text/sgml' => array('col -b %1', 'strings %1'),
				'text/tab-separated-values' => array('col -b %1', 'strings %1'),
			);

			$default = array();
			$executables = array();
			foreach ($possibilities as $type => $options) {
				foreach ($options as $opt) {
					$exec = reset(explode(' ', $opt, 2));

					if (! isset($executables[$exec])) {
						$executables[$exec] = (bool) `which $exec`;
					}

					if ($executables[$exec]) {
						$default[$type] = $opt;
						break;
					}
				}
			}

			$cachelib->cacheItem('file_handlers', serialize($default));
		} elseif (! $for_execution) {
			$default = array();
		}

		$handlers = $this->table('tiki_file_handlers');
		$database = $handlers->fetchMap('mime_type', 'cmd', array() );

		return array_merge($default, $database);
	}

	function reindex_all_files_for_search_text() {
		$files = $this->table('tiki_files');

		$rows = $files->fetchAll(array('fileId', 'filename', 'filesize', 'filetype', 'data', 'path', 'galleryId'), array(
			'archiveId' => 0,
		));

		foreach($rows as $row) {
			$search_text = $this->get_search_text_for_data($row['data'],$row['path'],$row['filetype'], $row['galleryId']);
			if ($search_text!==false) {
				$files->update(array(
					'search_data' => $search_text,
				), array(
					'fileId' => $row['fileId'],
				));
			}
		}
		include_once("lib/search/refresh-functions.php");
		refresh_index('files');
	}

	function get_parse_app($type, $skipDefault = true) {
		static $fileParseApps;
		
		if (! $fileParseApps) {
			$fileParseApps = $this->get_file_handlers(true);
		}

		$partial = $type;

		if (false !== $p = strpos($partial, ';')) {
			$partial = substr($partial, 0, $p);
		}

		if (isset($fileParseApps[$type])) {
			return $fileParseApps[$type];
		} elseif (isset($fileParseApps[$partial])) {
			return $fileParseApps[$partial];
		} elseif (! $skipDefault && isset($fileParseApps['default'])) {
			return $fileParseApps['default'];
		}
	}

	function get_search_text_for_data($data,$path,$type, $galleryId) {
		global $prefs;

		if (!isset($data) && !isset($path)) {
			return false;
		}

		$parseApp = $this->get_parse_app($type);

		if (empty($parseApp))
			return '';

		if (empty($path)) {
			$tmpfname = tempnam("/tmp", "wiki_");
			if (false === $tmpFile = @file_put_contents($tmpfname, $data)) {
				return false;
			}
		} else {
			$savedir = $this->get_gallery_save_dir($galleryId);

			$tmpfname = $savedir . $path;
		}

		$cmd = str_replace('%1',escapeshellarg($tmpfname),$parseApp);
		$handle = popen("$cmd","r");
		if ($handle !== false) {
			$contents = stream_get_contents($handle);
			fclose($handle);
		} else {
			$contents = false;
		}

		if (empty($path))
			@unlink($tmpfname);

		return $contents;
	}

	function notify ($galleryId, $name, $filename, $description, $action, $user, $fileId=false) {
		global $prefs;
		if ($prefs['feature_user_watches'] == 'y') {
                        //  Deal with mail notifications.
			include_once('lib/notifications/notificationemaillib.php');
			$galleryName = $this->table('tiki_file_galleries')->fetchOne('name', array('galleryId' => $galleryId));

			sendFileGalleryEmailNotification('file_gallery_changed', $galleryId, $galleryName, $name, $filename, $description, $action, $user, $fileId);
		}
	}
	/* lock a file */
	function lock_file($fileId, $user) {
		$this->table('tiki_files')->update(array(
			'lockedby' => $user,
		), array(
			'fileId' => $fileId,
		));
	}
	/* unlock a file */
	function unlock_file($fileId) {
		$this->lock_file($fileId, null);
	}
	/* get archives of a file */
	function get_archives($fileId, $offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $fileId, true, false, false, true, false, false, false, false, '', false, true);
	}
	function duplicate_file_gallery($galleryId, $name, $description = '') {
		global $user;
		$info = $this->get_file_gallery_info($galleryId);
		$info['user'] = $user;
		$info['galleryId'] = 0;
		$info['description'] = $description;
		$info['name'] = $name;
		$newGalleryId = $this->replace_file_gallery($info);
		return $newGalleryId;
	}

	function get_download_limit( $fileId )
	{
		return $this->table('tiki_files')->fetchOne('maxhits', array('fileId' => $fileId));
	}

	function set_download_limit( $fileId, $limit )
	{
		$this->table('tiki_files')->update(array(
			'maxhits' => (int) $limit,
		), array(
			'fileId' => (int) $fileId,
		));
	}
	// not the best optimisation as using a library using files and not content
	function zip($fileIds, &$error, $zipName='') {
		global $tiki_p_admin_file_galleries, $userlib, $tikilib, $prefs, $user;
		$list = array();
		$temp = 'temp/'.md5($tikilib->now).'/';
		if (!mkdir($temp)) {
			$error = "Can not create directory $temp";
			return false;
		}
		foreach ($fileIds as $fileId) {
			$info = $tikilib->get_file($fileId);
			if ($tiki_p_admin_file_galleries == 'y' || $userlib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files')) {
				if (empty($zipName)) {
					$zipName = $info['galleryId'];
				}
				$tmp = $temp.$info['filename'];
				if ($info['path']) { // duplicate file in temp
					if (!copy($prefs['fgal_use_dir'].$info['path'], $tmp)) {
						$error = "Can not copy to $tmp";
						return false;
					}
				} else {//write file in temp
					if (@file_put_contents($tmp, $info['data']) === false) {
						$error = "Can not write to $tmp";
						return false;
					}
				}
				$list[] = $tmp;
			}
		}
		if (empty($list)) {
			$error = "No permission";
			return null;
		}
		$info['filename'] = "$zipName.zip";
		$zip = $temp.$info['filename'];
		define( PCZLIB_SEPARATOR, '\001');
		include_once ('lib/pclzip/pclzip.lib.php');
		if (!$archive = new PclZip($zip)) {
			$error = $archive->errorInfo(true);
			return false;
		}
		if (!($v_list = $archive->create($list, PCLZIP_OPT_REMOVE_PATH, $temp))) {
			$error = $archive->errorInfo(true);
			return false;
		}
		$info['data'] = file_get_contents($zip);
		$info['path'] = '';
		$info['filetype'] = 'application/x-zip-compressed';
		foreach ($list as $tmp) {
			unlink($tmp);
		}
		unlink($zip);
		rmdir($temp);
		return $info;
	}

	function getGalleriesParentIds() {
		static $return = null;

		if ( $return === null ) {
			$return = $this->table('tiki_file_galleries')->fetchAll(array('galleryId', 'parentId'), array());
		}

		return $return;
	}

	/**
	 * Recursively returns all ids of the children of the specifified parent gallery
	 * as a linear array (list).
	 * 
	 * @param Array $allIds All ids of the Gallery
	 * @param Array &$subtree Output - The children Ids are appended
	 * @param int $parentId The parent whichs children are to be listed
	 */
	function _getGalleryChildrenIdsList( $allIds, &$subtree, $parentId ) {
		foreach ( $allIds as $k => $v ) {
			if ( $v['parentId'] == $parentId ) {
				$galleryId = $v['galleryId'];
				$subtree[] = (int)$galleryId;
				$this->_getGalleryChildrenIdsList( $allIds, $subtree, $galleryId );
			}
		}
	}

	/**
	 * Recursively returns all Ids of the Children of the specifified parent gallery
	 * as a tree-array (sub-galleries are array as an element of the parent array).
	 * Thus the structure of the child galleries are preserved.
	 * 
	 * @param Array $allIds All ids of the Gallery
	 * @param Array &$subtree Output - The children Ids are appended
	 * @param int $parentId The parent whichs children are to be listed
	 */
	function _getGalleryChildrenIdsTree( $allIds, &$subtree, $parentId ) {
		foreach ( $allIds as $v ) {
			if ( $v['parentId'] == $parentId ) {
				$galleryId = $v['galleryId'];
				$subtree[ (int)$galleryId ] = array();
				$this->_getGalleryChildrenIdsTree( $allIds, $subtree[$galleryId], $galleryId );
			}
		}
	}
	// Get a tree or a list of a gallery children ids, optionnally under a specific parentId
	// To avoid a query to the database for each node, this function retrieves all gallery ids and recursively build the tree using this info
	function getGalleryChildrenIds( &$subtree, $parentId = -1, $format = 'tree' ) {
		$allIds = $this->getGalleriesParentIds();

		switch ( $format ) {
			case 'list':
				$this->_getGalleryChildrenIdsList( $allIds, $subtree, $parentId );
				break;
			case 'tree': default:
				$this->_getGalleryChildrenIdsTree( $allIds, $subtree, $parentId );
		}
	}

	// Get a tree or a list of ids of the specified gallery and its children
	function getGalleryIds( &$subtree, $parentId = -1, $format = 'tree' ) {

		switch ( $format ) {
			case 'list':
				$subtree[] = $parentId;
				$childSubtree =& $subtree;
				break;
			case 'tree': default:
				$subtree[$parentId] = array();
				$childSubtree =& $subtree[$parentId];
		}

		return $this->getGalleryChildrenIds( $childSubtree, $parentId, $format );
	}

	/* Get subgalleries for parent $parentId
	 *
	 * @param int $parentId Parent ID of subgalleries to get
	 * @param bool $wholeSpecialGallery If true, will return the subgalleries of the special gallery (User File Galleries, Wiki Attachment Galleries, File Galleries, ...) that contains the $parentId gallery
	 * @param string $permission If set, will limit the list of subgalleries to those having this permission for the current user
	 */
	function getSubGalleries( $parentId = 0, $wholeSpecialGallery = true, $permission = '' ) {

		// Use the special File Galleries root if no other special gallery root id is specified
		if ( $parentId == 0 ) {
			global $prefs;
			$parentId = $prefs['fgal_root_id'];
		}

		// If needed, get the id of the special gallery that contains the $parentId gallery
		if ( $wholeSpecialGallery ) {
			$parentId = $this->getGallerySpecialRoot( $parentId );
			$useCache = true;
		}

		global $cachelib, $user;
		if ( $useCache ) {
			$cacheName = 'pid' . $parentId . '_' . $this->get_all_galleries_cache_name($user);
			$cacheType = $this->get_all_galleries_cache_type();
		}
		if ( ! $useCache || ! $return = $cachelib->getSerialized($cacheName, $cacheType) ) {
			$return = $this->list_file_galleries(0, -1, 'name_asc', $user, '', $parentId, false, true, false, false, false, true, false );
			if ( is_array( $return ) ) {
				$return['parentId'] = $parentId;
			}
			if ( $useCache ) {
				$cachelib->cacheItem($cacheName, serialize($return), $cacheType);
			}
		}

		if ( $permission != '' ) {
			$return['data'] = Perms::filter(array('type' => 'file gallery'), 'object', $return['data'], array('object'=>'id'), $permission);
		}

		return $return;
	}

	/**
	 * Get the Id of the gallery special root, which will be a gallery of type 'special' with the parentId '-1'
	 *    (i.e. 'File Galleries', 'Users File Galleries', ...)
	 * 
	 * @param int $galleryId The id of the gallery 
	 * @return The special root gallery Id
	 */
	function getGallerySpecialRoot( $galleryId, $treeParentId = null, &$tree = null ) {
		global $prefs;

		if ( ( $treeParentId === null xor $tree === null ) || $galleryId <= 0 ) {
			// If parameters are not valid, return false (they should be null at first call and not empty when recursively called)
			return false;
		} elseif ( $treeParentId === null ) {
			// Initialize the full tree and the top root of all galleries
			$tree = array();
			$treeParentId = -1;
			$this->getGalleryChildrenIds( $tree, $treeParentId, 'tree' );
		} elseif ( $treeParentId == $galleryId ) {
			// If the searched gallery is the same as the current tree parent id, then return tree (we found the right branch of the tree)
			return true;
		}

		if ( ! empty( $tree ) ) {
			foreach ( $tree as $subGalleryId => $childs ) {
				if ( $result = $this->getGallerySpecialRoot( $galleryId, $subGalleryId, $childs ) ) {
					if ( is_integer($result) ) {
						return $result;
					} elseif ( $treeParentId == $prefs['fgal_root_user_id'] || $treeParentId == -1 ) {
						//
						// If the parent is :
						//   - either the User File Gallery, stop here to keep only the user gallery instead of all users galleries
						//   - or already the top root of all galleries, it means that the gallery is a special gallery root
						//
						return (int)$subGalleryId;
					} else {
						return true;
					}
				}
			}
		}

		return false;
	}

	// Get the tree of 'Wiki Attachment File Galleries' filegal of the specified wiki page
	function getWikiAttachmentFilegalsIdsTree( $pageName ) {
		$return = array();
		$this->getGalleryIds( $return, $this->get_wiki_attachment_gallery( $pageName ), 'tree' );
		return $return;
	}

	// Get the tree of 'Users File Galleries' filegal of the current user
	function getUserFilegalsIdsTree() {
		$return = array();
		$this->getGalleryIds( $return, $this->get_user_file_gallery(), 'tree' );
		return $return;
	}

	// Get the tree of 'File Galleries' filegal 
	function getFilegalsIdsTree() {
		global $prefs;
		$return = array();
		$this->getGalleryIds( $return, $prefs['fgal_root_id'], 'tree' );
		return $return;
	}

	// Get default phplayers tree for filegals - not actually using phplayers for tiki7+
	function getFilegalsTreePhplayers( $currentGalleryId = null ) {
		return $this->getTreePhplayers( $this->getFilegalsIdsTree(), $currentGalleryId );
	}

	// Build galleries browsing tree and current gallery path array
	function getTreePhplayers( $idTree, $currentGalleryId = null ) {
		global $prefs;

		$allGalleries = $this->getSubGalleries();

		$idTreeKeys = array_keys( $idTree );
		$rootGalleryId = $idTreeKeys[0];
		if ( $currentGalleryId === null ) $currentGalleryId = $rootGalleryId;

		$script = 'tiki-list_file_gallery.php';
		$tree = array('name' => tra('File Galleries'), 'data' => array(), 'link' => $script, 'id' => $rootGalleryId );

		if ( $rootGalleryId != $prefs['fgal_root_id'] ) {
			foreach ( $allGalleries['data'] as $k => $v ) {
				if ( $v['id'] == $rootGalleryId ) {
					$tree['name'] = $v['name'];
					break;
				}
			}
		}

		$galleryPath = array();
		$expanded = array('1');
		$fgal_mgr_param = !empty($_REQUEST['filegals_manager']) ? '&amp;filegals_manager=' . urlencode($_REQUEST['filegals_manager']) : '';
		$this->_buildTreePhplayers($tree['data'], $allGalleries['data'], $currentGalleryId, $galleryPath, $expanded, $script, $rootGalleryId, $fgal_mgr_param);
		array_unshift($galleryPath, array($rootGalleryId, $tree['name']));

		$galleryPathHtml = '';
		foreach ( $galleryPath as $dir_id ) {
			if ( $galleryPathHtml != '' ) $galleryPathHtml .= ' &nbsp;&gt;&nbsp;';
			$galleryPathHtml .= '<a href="' . $script . '?galleryId=' . $dir_id[0] . $fgal_mgr_param . '">' . $dir_id[1] . '</a>';
		}

		return array(
			'tree' => $tree,
			'expanded' => $expanded,
			'path' => $galleryPathHtml,
			'pathArray' => $galleryPath
		);
	}

	function _buildTreePhplayers( &$tree, &$galleries, &$gallery_id, &$gallery_path, &$expanded, $link = "", $cur_id = -1, $queryString = '' ) {
		static $total = 1;
		static $nb_galleries = 0;

		$i = 0;
		$current_path = array();
		$path_found = false;
		if ($nb_galleries == 0) $nb_galleries = count($galleries);
		for ($gk = 0; $gk < $nb_galleries; $gk++) {
			$gv = & $galleries[$gk];
			if ($gv['parentId'] == $cur_id && $gv['id'] != $cur_id) {
				$tree[$i] = & $galleries[$gk];
				$tree[$i]['link_var'] = 'galleryId';
				$tree[$i]['link_id'] = $gv['id'];
				$tree[$i]['link'] = $link."?".$tree[$i]['link_var']."=".$tree[$i]['link_id'] . $queryString;
				$tree[$i]['pos'] = $total++;
				$this->_buildTreePhplayers($tree[$i]['data'], $galleries, $gallery_id, $gallery_path, $expanded, $link, $gv['id'], $queryString);
				if (!$path_found && $gv['id'] == $gallery_id) {
					if ($_REQUEST['galleryId'] == $gv['id']) $tree[$i]['current'] = 1;
					array_unshift($gallery_path, array($gallery_id, $gv['name']));
					$expanded[] = $tree[$i]['pos'] + 1;
					$gallery_id = $cur_id;
					$path_found = true;
				}
				$i++;
			}
		}
	}
	// get the size in k used in a fgal and its children
	function getUsedSize($galleryId=0) {
		$files = $this->table('tiki_files');

		$conditions = array();
		if (! empty($galleryId)) {
			$galleryIds = array();
			$this->getGalleryIds( $galleryIds, $galleryId, 'list' );

			$conditions['galleryId'] = $files->in($galleryIds);
		}

		return $files->fetchOne($files->sum('filesize'), $conditions);
	}

	// get the min quota in M of a fgal and its parents
	function getQuota($galleryId=0) {
		global $prefs;
		if (empty($galleryId) || $prefs['fgal_quota_per_fgal'] == 'n') {
			return $prefs['fgal_quota'];
		}
		$list = $this->getGalleryParentsColumns($galleryId, array('galleryId', 'quota'));
		$quota = $prefs['fgal_quota'];
		foreach($list as $fgal) {
			if (empty($fgal['quota'])) {
				continue;
			}
			$quota = min($quota, $fgal['quota']);
		}
		return $quota;
	}
	// get the max quota in M of the children of a fgal
	function getMaxQuotaDescendants($galleryId=0) {
		if (empty($galleryId)) {
			return 0;
		}
		$this->getGalleryChildrenIds($subtree, $galleryId, 'list');
		if (is_array($subtree)) {
			$files = $this->table('tiki_files');
			return $files->fetchOne($files->max('filesize'), array('galleryId' => $files->in($subtree)));
		} else {
			return 0;
		}
	}
	// check quota is smaller than parent quotas and bigger than children quotas
	// return -1: too small, 0: ok, +1: too big
	function checkQuotaSetting($quota, $galleryId=0, $parentId=0) {
		if (empty($quota)) {
			return 0;
		}
		$limit = $this->getQuota($parentId);
		if (!empty($limit) && $quota > $limit) {
			return 1;// too big
		}
		if (!empty($galleryId)) {
			$limit = $this->getMaxQuotaDescendants($galleryId);
			if (!empty($limit) && $quota < $limit) {
				return -1;//too small
			}
		}
		return 0;
	}
	// get specific columns for a gallery and its parents
	function getGalleryParentsColumns($galleryId, $columns) {
		$cols = array_diff($columns, array('size', 'galleryId', 'parentId'));
		$cols[] = 'galleryId';
		$cols[] = 'parentId';

		$all = $this->table('tiki_file_galleries')->fetchAll($cols, array());
		$list = array();
		$this->_getGalleryParentsColumns($all, $list, $galleryId, $columns);
		return $list;	
	}
	function _getGalleryParentsColumns($all, &$list, $galleryId, $columns=array()) {
		foreach ($all as $fgal) {
			if ($fgal['galleryId'] == $galleryId) {
				if (in_array('size', $columns)) { // to be optimized
					$fgal['size'] = $this->getUsedSize($galleryId);
				}
				$list[] = $fgal;
				$this->_getGalleryParentsColumns($all, $list, $fgal['parentId'], $columns);
				return;
			}
		}
	}
	// check a size in K can be added to a gallery return false if problem
	function checkQuota($size, $galleryId, &$error) {
		global $prefs, $smarty;
		$error = '';
		if (!empty($prefs['fgal_quota'])) {
			$use = $this->getUsedSize();
			if ($use + $size > $prefs['fgal_quota']*1024*1024) {
				$error = tra('The upload has not been done.') . ' ' . tra('Reason: The global quota has been reached');
				$diff = $use + $size - $prefs['fgal_quota']*1024*1024;
			}
		}
		if (empty($error) && $prefs['fgal_quota_per_fgal'] == 'y') {
			$list = $this->getGalleryParentsColumns($galleryId, array('galleryId', 'quota', 'size', 'name'));
			//echo '<pre>';print_r($list);echo '</pre>';
			foreach ($list as $fgal) {
				if (!empty($fgal['quota']) && $fgal['size'] + $size > $fgal['quota']*1024*1024) {
					$error = tra('The upload has not been done.') . ' ' . sprintf( tra('Reason: The quota has been reached in "%s"'), $fgal['name'] );
					$smarty->assign('mail_fgal', $fgal);
					$diff = $fgal['size'] + $size - $fgal['quota']*1024*1024;
					break;
				}
			}
		}
		if (!empty($error)) {
			global $tikilib;
			$nots = $tikilib->get_event_watches('fgal_quota_exceeded', '*');
			if (!empty($nots)) {
				include_once ('lib/webmail/tikimaillib.php');
				$mail = new TikiMail();
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $tikilib->httpPrefix( true ) . dirname( $foo["path"] );
				$machine = preg_replace("!/$!", "", $machine); // just incase
				$smarty->assign('mail_machine', $machine);
				$smarty->assign('mail_diff', $diff);
				foreach ($nots as $not) {
					$lg = $tikilib->get_user_preference($not['user'], 'language', $prefs['site_language']);
					$mail->setSubject(tra('File gallery quota exceeded', $lg));
					$mail->setText($smarty->fetchLang($lg, 'mail/fgal_quota_exceeded.tpl'));
					$mail->buildMessage();
					$mail->send(array($not['email']));
				}
			}
			return false;
		}
		return true;			
	}
	// update backlinks of an object
	function replaceBacklinks($context, $fileIds=array()) {
		$objectlib = TikiLib::lib('object');
		$objectId = $objectlib->get_object_id($context['type'], $context['object']);
		if (empty($objectId) && !empty( $fileIds)) {
			$objectId = $objectlib->add_object($context['type'], $context['object'], $context['description'], $context['name'], $context['href']);
		}
		if (!empty($objectId)) {
			$this->_replaceBacklinks($objectId, $fileIds);
		}
		//echo 'REPLACEBACKLINK'; print_r($context);print_r($fileIds);echo '<pre>'; debug_print_backtrace(); echo '</pre>';die;
	}
	function _replaceBacklinks($objectId, $fileIds=array()) {
		$backlinks = $this->table('tiki_file_backlinks');
		$this->_deleteBacklinks($objectId);

		foreach ($fileIds as $fileId) {
			$backlinks->insert(array(
				'objectId' => (int) $objectId,
				'fileId' => (int) $fileId,
			));
		}
	}
	// delete backlinks associated to an object
	function deleteBacklinks($context, $fileId=null) {
		if (empty($fileId)) {
			$objectlib = TikiLib::lib('object');
			$objectId = $objectlib->get_object_id($context['type'], $context['object']);
			if (!empty($objectId)) {
				$this->_deleteBacklinks($objectId);
			}
		} else {
			$this->_deleteBacklinks(null, $fileId);
		}
	}
	function _deleteBacklinks($objectId, $fileId=null) {
		$backlinks = $this->table('tiki_file_backlinks');
		if (empty($fileId)) {
			$backlinks->delete(array('objectId' => (int) $objectId));
		} else {
			$backlinks->delete(array('fileId' => (int) $fileId));
		}
	}
	// get the backlinks of an object
	function getFileBacklinks($fileId, $sort_mode='type_asc') {
		$query = 'select tob.* from `tiki_file_backlinks` tfb left join `tiki_objects` tob on (tob.`objectId`=tfb.`objectId`) where `fileId`=? order by '.$this->convertSortMode($sort_mode);
		return $this->fetchAll($query, array((int)$fileId));
	}
	// can not see a file if all its backlinks are not viewable
	function hasOnlyPrivateBacklinks($fileId) {
		$objects = $this->getFileBacklinks($fileId);
		if (empty($objects)) {
			return false;
		}
		foreach ($objects as $object) {
			$pobjects[$object['type']][] = $object;
		}
		global $categlib; include_once('lib/categories/categlib.php');
		$map = CategLib::map_object_type_to_permission();
		foreach ($pobjects as $type=>$list) {
			if ($type == 'blog post') {
				$this->parentObjects($list, 'tiki_blog_posts', 'postId', 'blogId');
				$f = Perms::filter(array('type'=>'blog'), 'object', $list, array('object' => 'blogId'), str_replace('tiki_p_', '', $map['blog']));
			} elseif (strstr($type, 'comment')) {
				$this->parentObjects($list, 'tiki_comments', 'threadId', 'object');
				$t = str_replace(' comment', '', $type);
				$f = Perms::filter(array('type'=>$t), 'object', $list, array('object' => 'object'), str_replace('tiki_p_', '', $map[$t]));
			} elseif ($type == 'forum post') {
				$this->parentObjects($list, 'tiki_comments', 'threadId', 'object');
				$f = Perms::filter(array('type'=>'forum'), 'object', $list, array('object' => 'object'), str_replace('tiki_p_', '', $map['forum']));
			} elseif ($type == 'trackeritem') {
				$this->parentObjects($list, 'tiki_tracker_items', 'itemId', 'trackerId');
				$f = Perms::filter(array('type'=>'tracker'), 'object', $list, array('object' => 'trackerId'), str_replace('tiki_p_', '', $map['tracker']));
				//NEED to check item perm
			} else {
				$f = Perms::filter(array('type'=>$type), 'object', $list, array('object' => 'itemId'), str_replace('tiki_p_', '', $map[$type]));
			}
			$debug=0;
			if (!empty($debug)) {
				echo "<br />FILE$fileId";
				if (!empty($f)) echo 'OK-';else echo 'NO-';
				foreach ($list as $l) echo $l['type'].': '.$l['itemId'].'('.$l['href'].')'.',';
			}
			if (!empty($f)) {
				return false;
			}
		}
		return true;
	}
	// sync the backlinks used by a text of an object
	function syncFileBacklinks($data, $context) {
		global $tikilib;
		$fileIds = array();
		$plugins = $tikilib->getPlugins($data, array('IMG', 'FILE'));
		foreach ($plugins as $plugin) {
			if (!empty($plugin['arguments']['fileId'])) {
				$fileIds[] = $plugin['arguments']['fileId'];
			}
			if (!empty($plugin['arguments']['src']) && $fileId = $this->getLinkFileId($plugin['arguments']['src'])) {
				$fileIds[] = $fileId;
			}
		}
		if (preg_match_all('/\[(.+)\]/Umi', $data, $matches)) {
			foreach ($matches as $match) {
				if (isset($match[1]) && $fileId = $this->getLinkFileId($match[1])) {
					$fileIds[] = $fileId;
				}
			}
		}
		if (preg_match_all('/<a[^>]*href=(\'|\")?([^>*])/Umi', $data, $matches)) {
			foreach ($matches as $match) {
				if (isset($match[2]) && $fileId = $this->getLinkFileId($match[2])) {
					$fileIds[] = $fileId;
				}
			}
		}
		$fileIds = array_unique($fileIds);
		//if (!empty($fileIds)) {echo '<pre>'; print_r($context); print_r($fileIds); echo '</pre>';}
		$this->replaceBacklinks($context, $fileIds);
		return $fileIds;
	}

	function save_sync_file_backlinks($args)
	{
		$content = array();
		if (isset($args['values'])) {
			$content = $args['values'];
		}
		if (isset($args['data'])) {
			$content[] = $args['data'];
		}
		$content = implode(' ', $content);

		$this->syncFileBacklinks($content, $args);
	}

	function getLinkFileId($url) {
		if (preg_match('/^tiki-download_file.php\?.*fileId=([0-9]+)/', $url, $matches)) {
			return $matches[1];
		}
		if (preg_match('/^(dl|preview|thumbnail|thumb||display)([0-9]+)/', $url, $matches)) {
			return $matches[2];
		}
	}
	private function syncParsedText( $data, $context ) {
		// Compatbility function
		$this->object_post_save( $context, array( 'content' => $data ) );
	}
	function refreshBacklinks() {
		$result = $this->table('tiki_pages')->fetchAll(array('data', 'description', 'pageName'), array());
		foreach ($result as $res) {
			$this->syncParsedText($res['data'], array('type'=> 'wiki page', 'object'=> $res['pageName'], 'description'=> $res['description'], 'name'=>$res['pageName'], 'href'=>'tiki-index.php?page='.$res['pageName']));
		}

		$result = $this->table('tiki_articles')->fetchAll(array('heading', 'body', 'articleId', 'title'), array());
		foreach ($result as $res) {
			$this->syncParsedText($res['body'].' '.$res['heading'], array('type'=>'article', 'object'=>$res['articleId'], 'description'=>substr($res['heading'], 0, 200), 'name'=>$res['title'], 'href'=>'tiki-read_article.php?articleId='.$res['articleId']));
		}

		$result = $this->table('tiki_submissions')->fetchAll(array('heading', 'body', 'subId', 'title'), array());
		foreach ($result as $res) {
			$this->syncParsedText($res['heading'].' '.$res['body'], array('type'=>'submission', 'object'=>$res['subId'], 'description'=>substr($res['heading'], 0, 200), 'name'=>$res['title'], 'href'=>'tiki-edit_submission.php?subId='.$res['subId']));
		}

		$result = $this->table('tiki_blogs')->fetchAll(array('blogId', 'heading', 'description', 'title'), array());
		foreach ($result as $res) {
			$this->syncParsedText($res['heading'], array('type'=>'blog', 'object'=>$res['blogId'], 'description'=>$res['description'], 'name'=>$res['title'], 'href'=>'tiki-view_blog.php?blogId='.$res['blogId']));
		}

		$result = $this->table('tiki_blog_posts')->fetchAll(array('blogId', 'data', 'postId', 'title'), array());
		foreach ($result as $res) {
			$this->syncParsedText($res['data'], array('type'=>'blog post', 'object'=>$res['postId'], 'description'=>substr($res['data'], 0, 200), 'name'=>$res['title'], 'href'=>'tiki-view_blog_post.php?postId='.$res['postId']));
		}

		$result = $this->table('tiki_comments')->fetchAll(array('objectType', 'object', 'threadId', 'title', 'data'), array());
		$commentslib = TikiLib::lib('comments');
		foreach ($result as $res) {
			if ($res['objectType'] == 'forum') {
				$type = 'forum post';
			} else {
				$type = $res['objectType'].' comment';
			}
			$this->syncParsedText($res['data'], array('type'=>$type, 'object'=>$res['threadId'], 'description'=>'', 'name'=>$res['title'], 'href'=>$commentslib->getHref($res['objectType'], $res['object'], $res['threadId'])));
		}

		$result = $this->table('tiki_trackers')->fetchAll(array('description', 'name', 'trackerId'), array('descriptionIsParsed' => 'y'));
		foreach ($result as $res) {
			$this->syncParsedText($res['description'], array('type'=>'tracker', 'object'=>$res['trackerId'], 'description'=>$res['description'], 'name'=>$res['name'], 'href'=>'tiki-view_tracker.php?trackerId='.$res['trackerId']));
		}
		//TODO field description
		$query = 'select `value`, `itemId` from `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf on (ttif.`fieldId`=ttf.`fieldId`) where ttf.`type`=?';
		$result = $this->query($query, array('a'));
		while ($res = $result->fetchRow()) {
			//TODO: get the name of the item
			$this->syncParsedText($res['value'], array('type'=>'trackeritem', 'object'=>$res['itemId'], 'description'=>'', 'name'=>'', 'href'=>'tiki-view_tracker_item.php?itemId='.$res['itemId']));
		}
	}
	/* move files to file system
	 * return '' if ok otherwise error message */
	function moveFiles($to='to_fs', &$feedbacks) {
		$files = $this->table('tiki_files');

		if ($to == 'to_db') {
			$result = $files->fetchColumn('fileId', array(
				'path' => $files->not(''),
			));
			$msg = tra('Number of files transferred to the database:');
		} else {
			$result = $files->fetchColumn('fileId', array(
				'path' => '',
			));
			$msg = tra('Number of files transferred to the file system:');
		}

		$nb = 0;
		foreach ($result as $fileId) {
			if (($errors = $this->moveFile($to, $fileId)) != '') {
				$feedbacks[] = "$msg $nb";
				return $errors;
			}
			++$nb;
		}
		$feedbacks[] = "$msg $nb";
		return '';
	}
	function moveFile($to='to_fs', $file_id) {
		global $prefs;
		$files = $this->table('tiki_files');

		$file_info = $files->fetchFullRow(array('fileId' => $file_id));

		if ($to == 'to_db') {
			if (false === $data = @file_get_contents($prefs['fgal_use_dir'] .$file_info['path'])) {
				return tra('Cannot open this file:') . $prefs['fgal_use_dir'] . $file_info['path'];
			}

			$files->update(array(
				'data' => $data,
				'path' => '',
			), array(
				'fileId' => $file_info['fileId'],
			));
			unlink($prefs['fgal_use_dir'] .$file_info['path']);
		} else {
			$fhash = $this->find_unique_name($prefs['fgal_use_dir'], $file_info['name']);

			if (false === @file_put_contents($prefs['fgal_use_dir'] . $fhash, $file_info['data'])) {
				return tra('Cannot write to this file:') . $prefs['fgal_use_dir'] . $fhash;
			}

			$files->update(array(
				'data' => '',
				'path' => $fhash,
			), array(
				'fileId' => $file_info['fileId'],
			));
		}
		return '';
	}
	// find the fileId in the pool of fileId archives files that is closer before the date 
	function getArchiveJustBefore($fileId, $date) {
		$files = $this->table('tiki_files');

		$archiveId = $files->fetchOne('archiveId', array('fileId' => $fileId));
		if (empty($archiveId)) {
			$archiveId = $fileId;
		}

		return $files->fetchOne('fileId', array(
			'anyOf' => $files->expr('(`fileId`=? or `archiveId`=?)', array($archiveId, $archiveId)),
			'created' => $files->lesserThan($date+1),
		), 1, 0, array('created' => 'DESC'));
	}

	function get_objectid_from_virtual_path($path, $parentId = -1) {
		if ( empty($path) || $path[0] != '/' ) return false;

		if ( $path == '/' ) {
			//      global $prefs;
			//      return array('type' => 'filegal', 'id' => $prefs['fgal_root_id']);
			return array('type' => 'filegal', 'id' => -1);
		}

		$pathParts = explode('/', $path, 3);

		$files = $this->table('tiki_files');

		// Path detected as a file
		if ( count($pathParts) < 3 ) {
			// If we ask for a previous version (name?version)
			if ( preg_match('/^([^?]*)\?(\d*)$/', $pathParts[1], $matches) ) {
				$result = $files->fetchAll('fileId', array(
					'name' => $matches[1],
					'galleryId' => (int) $parentId,
				), 1, $matches[2], array('fileId' => 'ASC'));
			} else {
				$result = $files->fetchOne('fileId', array(
					'name' => $pathParts[1],
					'galleryId' => (int) $parentId,
				), array('fileId' => 'DESC'));
			}

			if ( $result ) {
				$res = reset($result);
				if ( ! empty($res) ) {
					return array('type' => 'file', 'id' => $res['fileId']);
				}
			}
		}

		$galleryId = $this->table('tiki_file_galleries')->fetchOne('galleryId', array(
			'name' => $pathParts[1],
			'parentId' => (int) $parentId,
		));

		if ($galleryId) {
			// as a leaf
			if ( empty($pathParts[2]) ) {
				return array('type' => 'filegal', 'id' => $galleryId);
			} else {
				return $this->get_objectid_from_virtual_path( '/' . $pathParts[2], $galleryId );
			}
		}

		return false;
	}

	function get_full_virtual_path($id, $type = 'file') {
		if ( ! $id > 0 ) return false;

		switch( $type ) {
			case 'filegal':
				if ( $id == -1 ) return '/';

				$res = $this->table('tiki_file_galleries')->fetchRow(array('name', 'parentId'), array(
					'galleryId' => (int) $id,
				));
				break;

			case 'file': default:
				$res = $this->table('tiki_files')->fetchRow(array('name', 'parentId' => 'galleryId'), array(
					'fileId' => (int) $id,
				));
		}

		if ($res) {
			$parentPath = $this->get_full_virtual_path($res['parentId'], 'filegal');

			return $parentPath . ( $parentPath == '/' ? '' : '/' ) . $res['name'];
		}

		return false;
	}

	function getFiletype($not=array()) {
		if (empty($not)) {
			$query = 'select distinct(`filetype`) from `tiki_files` order by `filetype` asc'; 
		} else {
			$query = 'select distinct(`filetype`) from `tiki_files` where `filetype` not in('.implode(',',array_fill(0, count($not),'?')).')order by `filetype` asc'; 
		}
		$result = $this->query($query, $not);
		$ret = array();
		while($res = $result->fetchRow()) {
			$ret[] = $res['filetype'];
		}
		return $ret;
	}
	function setDefault($fgalIds) {
		global $prefs;
		$defaults = array(
			'sort_mode' => $prefs['fgal_sort_mode'],
			'show_backlinks' => 'n',
			'show_deleteAfter' => $prefs['fgal_list_deleteAfter'],
			'show_lastDownload' => 'n',
			'show_id' => $prefs['fgal_list_id'],
			'show_icon' => $prefs['fgal_list_type'],
			'show_name' => $prefs['fgal_list_name'],
			'show_description' => $prefs['fgal_list_description'],
			'show_size' => $prefs['fgal_list_size'],
			'show_created' => $prefs['fgal_list_created'],
			'show_modified' => $prefs['fgal_list_lastModif'],
			'show_creator' => $prefs['fgal_list_creator'],
			'show_author' => $prefs['fgal_list_author'],
			'show_last_user' => $prefs['fgal_list_last_user'],
			'show_comment' => $prefs['fgal_list_comment'],
			'show_files' => $prefs['fgal_list_files'],
			'show_hits' => $prefs['fgal_list_hits'],
			'show_lockedby' => $prefs['fgal_list_lockedby'],
			'show_checked' => $prefs['fgal_show_checked'],
			'show_share' => $prefs['fgal_list_share'],
			'show_explorer' => $prefs['fgal_show_explorer'],
			'show_path' => $prefs['fgal_show_path'],
			'show_slideshow' => $prefs['fgal_show_slideshow'],
			'default_view' => $prefs['fgal_default_view'],
		);

		$galleries = $this->table('tiki_file_galleries');
		$galleries->update($defaults, array(
			'galleryId' => $galleries->in($fgalIds),
		));
	}
	function getGalleryId($name, $parentId) {
		return $this->table('tiki_file_galleries')->fetchOne('galleryId', array(
			'name' => $name,
			'parentId' => $parentId,
		));
	}
	function deleteOldFiles() {
		global $prefs, $tikilib, $smarty;
		include_once('lib/webmail/tikimaillib.php');
		$query = 'select * from `tiki_files` where `deleteAfter` < '.$this->now.' - `lastModif` and `deleteAfter` is not NULL and `deleteAfter` != \'\' order by galleryId asc';
		$files = $this->fetchAll($query, array());
		foreach ($files as $fileInfo) {
			if (empty($galInfo) || $galInfo['galleryId'] != $fileInfo['galleryId']) {
				$galInfo = $this->get_file_gallery_info($fileInfo['galleryId']);
				if (!empty($prefs['fgal_delete_after_email'])) {
					$smarty->assign_by_ref('galInfo', $galInfo);
				}
			}
			if (!empty($prefs['fgal_delete_after_email'])) {
				$savedir = $this->get_gallery_save_dir($galInfo['galleryId'], $galInfo);
				$fileInfo['data'] = file_get_contents($savedir.$fileInfo['path']);

				$smarty->assign_by_ref('fileInfo', $fileInfo);
				$mail = new TikiMail();
				$mail->setSubject(tra('Old File deleted:', $prefs['site_language']).' '.$fileInfo['filename']);
				$mail->setText($smarty->fetchLang($prefs['site_language'], 'mail/fgal_old_file_deleted.tpl'));
				$mail->addAttachment($fileInfo['data'], $fileInfo['filename'], $fileInfo['filetype']);
				$to = preg_split('/ *, */', $prefs['fgal_delete_after_email']);
				$mail->send($to);
			}
			$this->remove_file($fileInfo, $galInfo, false);
		}
	}
	// get the wiki_syntax - use parent's if none
	function getWikiSyntax($galleryId=0) {
		global $prefs;
		
		if (isset($_REQUEST['filegals_manager'])) {		// for use in plugin edit popup
			if ($_REQUEST['filegals_manager'] === 'fgal_picker_id') {
				return '%fileId%';		// for use in plugin edit popup
			} else if ($_REQUEST['filegals_manager'] === 'fgal_picker') {
				$href = 'tiki-download_file.php?fileId=123&amp;display';	// dummy id as sefurl expects a (/d+) pattern
				global $smarty; include_once('tiki-sefurl.php');
				$href = filter_out_sefurl($href, $smarty);
				return str_replace('123', '%fileId%', $href);
			}
		}
		
		$syntax = $this->table('tiki_file_galleries')->fetchOne('wiki_syntax', array('galleryId' => $galleryId));

		if (!empty($syntax)) {
			return $syntax;
		}

		$list = $this->getGalleryParentsColumns($galleryId, array('wiki_syntax'));
		foreach($list as $fgal) {
			if (!empty($fgal['wiki_syntax'])) {
				return $fgal['wiki_syntax'];
			}
		}
		// and no syntax set, return default
		$syntax = '{img fileId="%fileId%" thumb="y" rel="box[g]"}';	// should be a pref
		return $syntax;
	}

	function add_file_hit($id) {
		global $prefs, $user;

		$files = $this->table('tiki_files');

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			// Enforce max download per file
			if( $prefs['fgal_limit_hits_per_file'] == 'y' ) {
				$limit = $this->get_download_limit( $id );
				if( $limit > 0 ) {
					$count = $files->fetchCount(array('fileId' => $id, 'hits' => $files->lesserThan($limit)));
					if( ! $count ) {
						return false;
					}
				}
			}

			$files->update(array(
				'hits' => $files->increment(1),
				'lastDownload' => $this->now,
			), array(
				'fileId' => (int) $id,
			));
		} else {
			$files->update(array(
				'lastDownload' => $this->now,
			), array(
				'fileId' => (int) $id,
			));
		}			

		if ($prefs['feature_score'] == 'y') {
			if( ! $this->score_event($user, 'fgallery_download', $id) )
				return false;

			$owner = $files->fetchOne('user', array('fileId' => (int) $id));
			if( ! $this->score_event($owner, 'fgallery_is_downloaded', "$user:$id") )
				return false;
		}

		return true;
	}

	function add_file_gallery_hit($id) {
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$fileGalleries = $this->table('tiki_file_galleries');
			$fileGalleries->update(array(
				'hits' => $fileGalleries->increment(1),
			), array(
				'galleryId' => (int) $id,
			));
		}
		return true;
	}

	function get_file($id, $randomGalleryId='') {
		if (empty($randomGalleryId)) {
			$where = '`fileId`=?';
			$bindvars[] = (int)$id;
		} else {
			$where = 'tf.`galleryId`=? order by '.$this->convertSortMode('random'). ' limit 1 ';
			$bindvars[] = (int)$randomGalleryId;
		}
		$query = "select tf.*, tfg.`backlinkPerms` from `tiki_files` tf left join `tiki_file_galleries` tfg on (tfg.`galleryId`=tf.`galleryId`) where $where";
		$result = $this->query($query, $bindvars);
		return $result ? $result->fetchRow() : array();
	}

	/**
	 * Retrieve file draft
	 *
	 * @param int $id
	 */
	function get_file_draft($id) {
		global $user;

		$file = $this->get_file($id);

		if (!$file || empty($file)) {
			return array();
		}

		$query = "select tfd.* from `tiki_file_drafts` tfd where `fileId`=? and `user`=?";
		$result = $this->query($query, array((int)$id, $user));

		if (!($draft = $result->fetchRow())) {
			return $file;
		}

		$file['filename'] = $draft['filename'];
		$file['filesize'] = $draft['filesize'];
		$file['filetype'] = $draft['filetype'];
		$file['data'] = $draft['data'];
		$file['user'] = $draft['user'];
		$file['path'] = $draft['path'];
		$file['hash'] = $draft['hash'];
		$file['lastModif'] = $draft['lastModif'];
		$file['lockedby'] = $draft['lockedby'];

		return $file;
	}

	function get_file_by_name($galleryId, $name, $column='name') {
		$query = "select `path`,`galleryId`,`filename`,`filetype`,`data`,`filesize`,`name`,`description`, `created` from `tiki_files` where `galleryId`=? AND `$column`=? ORDER BY created DESC LIMIT 1";
		$result = $this->query($query,array((int) $galleryId, $name));
		$res = $result->fetchRow();
		return $res;
	}

	function list_files($offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {
		global $prefs;
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $prefs['fgal_root_id'], false, false, true, true, false, false, true, true);
	}

	function list_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user='', $find='', $parentId=-1, $with_archive=false, $with_subgals=true, $with_subgals_size=false, $with_files=false, $with_files_data=false, $with_parent_name=true, $with_files_count=true,$recursive=true) {
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $parentId, $with_archive, $with_subgals, $with_subgals_size, $with_files, $with_files_data, $with_parent_name, $with_files_count, $recursive, $user);
	}

	/**
	 * Get files and/or subgals list with additional data from one or all file galleries
	 *
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param int $galleryId (-1 = all galleries (default))
	 * @param bool $with_archive give back the number of archives
	 * @param bool $with_subgals include subgals in the listing
	 * @param bool $with_subgals_size calculate the size of subgals
	 * @param bool $with_files include files in the listing
	 * @param bool $with_files_data include files data in the listing
	 * @param bool $with_parent_name include parent names in the listing
	 * @param bool $recursive include all subgals recursively (yet only implemented for galleryId == -1)
	 * @param string $my_user use another user than the current one
	 * @param bool $keep_subgals_together do not mix files and subgals when sorting (if true, subgals will always be at the top)
	 * @param bool $parent_is_file use $galleryId param as $fileId (to return only archives of the file)
	 * @param array filter: creator, categId, lastModif, lastDownload, fileId
	 * @param string wiki_syntax: text to be inserted in editor onclick (from fgal manager)
	 * @return array of found files and subgals
	 */
	function get_files($offset, $maxRecords, $sort_mode, $find, $galleryId=-1, $with_archive=false, $with_subgals=false, 
						$with_subgals_size=true, $with_files=true, $with_files_data=false, $with_parent_name=false, $with_files_count=true,
						$recursive=false, $my_user='', $keep_subgals_together=true, $parent_is_file=false, $with_backlink=false, $filter='',
						$wiki_syntax = '') {

		global $user, $tiki_p_admin_file_galleries, $prefs;

		$f_jail_bind = array();
		$g_jail_bind = array();
		$f_where = '';

		if ( ( ! $with_files && ! $with_subgals ) || ( $parent_is_file && $galleryId <= 0 ) ) return array();

		$fileId = -1;
		if ( $parent_is_file ) {
			$fileId = $galleryId;
			$galleryId = -2;
		}

		if ( $recursive && ! is_array($galleryId) ) {
			$idTree = array();
			$this->getGalleryIds( $idTree, $galleryId, 'list' );
			$galleryId =& $idTree;
		} else {
			// recursive mode is only available for one parent gallery (i.e. not implemented when $galleryId is an array of multiple ids)
			$recursive = false;
		}

		$with_subgals_size = ( $with_subgals && $with_subgals_size );
		if ( $my_user == '' ) $my_user = $user;

		$f_table = '`tiki_files` as tf';
		$g_table = '`tiki_file_galleries` as tfg';
		$f_group_by = '';
		$orderby = $this->convertSortMode($sort_mode);

		$categlib = TikiLib::lib('categ');
		$f2g_corresp = array(
				'0 as `isgal`' => '1 as `isgal`',
				'tf.`fileId` as `id`' => 'tfg.`galleryId` as `id`',
				'tf.`galleryId` as `parentId`' => 'tfg.`parentId`',
				'tf.`name`' => 'tfg.`name`',
				'tf.`description`' => 'tfg.`description`',
				'tf.`filesize` as `size`' => "0 as `size`",
				'tf.`created`' => 'tfg.`created`',
				'tf.`filename`' => 'tfg.`name` as `filename`',
				'tf.`filetype` as `type`' => "tfg.`type`",
				'tf.`user` as `creator`' => 'tfg.`user` as `creator`',
				'tf.`author`' => "'' as `author`",
				'tf.`hits`' => "tfg.`hits`",
				'tf.`lastDownload`' => "0 as `lastDownload`",
				'tf.`votes`' => 'tfg.`votes`',
				'tf.`points`' => 'tfg.`points`',
				'tf.`path`' => "'' as `path`",
				'tf.`reference_url`' => "'' as `reference_url`",
				'tf.`is_reference`' => "'' as `is_reference`",
				'tf.`hash`' => "'' as `hash`",
				'tf.`search_data`' => 'tfg.`name` as `search_data`',
				'tf.`lastModif` as `lastModif`' => 'tfg.`lastModif` as `lastModif`',
				'tf.`lastModifUser` as `last_user`' => "'' as `last_user`",
				'tf.`lockedby`' => "'' as `lockedby`",
				'tf.`comment`' => "'' as `comment`",
				'tf.`deleteAfter`' => "'' as `deleteAfter`",
				'tf.`maxhits`' => "'' as `maxhits`",
				'tf.`archiveId`' => '0 as `archiveId`',
				"'' as `visible`" => 'tfg.`visible`',
				"'' as `public`" => 'tfg.`public`',

				/// Below are obsolete fields that will be removed soon (they have their new equivalents above)
				'tf.`fileId`' => 'tfg.`galleryId` as `fileId`', /// use 'id' instead
				'tf.`galleryId`' => 'tfg.`parentId` as `galleryId`', /// use 'parentId' instead
				'tf.`filesize`' => "0 as `filesize`", /// use 'size' instead
				'tf.`filetype`' => "tfg.`type` as `filetype`", /// use 'type' instead
				'tf.`user`' => 'tfg.`user`', /// use 'creator' instead	
				'tf.`lastModifUser`' => "'' as `lastModifUser`" /// use 'last_user' instead
		);
		if ( $with_files_data ) {
			$f2g_corresp['tf.`data`'] = "'' as `data`";
		}
		if ( $with_files_count ) {
			$f2g_corresp["'' as `files`"] = 'count(distinct tfc.`fileId`) as `files`';
		}
		if ( $with_archive ) {
			$f2g_corresp['count(tfh.`fileId`) as `nbArchives`'] = '0 as `nbArchives`';
			$f_table .= ' LEFT JOIN `tiki_files` tfh ON (tf.`fileId` = tfh.`archiveId`)';
			$f_group_by = ' GROUP BY tf.`fileId`';
		}
		if ( $with_files && $prefs['feature_file_galleries_save_draft'] == 'y' ) {
			$f2g_corresp['count(tfd.`fileId`) as `nbDraft`'] = '0 as `nbDraft`';
			$f_table .= ' LEFT JOIN `tiki_file_drafts` tfd ON (tf.`fileId` = tfd.`fileId` and tfd.`user`=?)';
			$f_group_by = ' GROUP BY tf.`fileId`';
		}
		if ( $with_backlink ) {
			$f2g_corresp['count(tfb.`fileId`) as `nbBacklinks`'] = '0 as `nbBacklinks`';
			$f_table .= ' LEFT JOIN `tiki_file_backlinks` tfb ON (tf.`fileId` = tfb.`fileId`)';
			$f_group_by = ' GROUP BY tf.`fileId`';
		}
		if ( !empty($filter['orphan']) && $filter['orphan'] == 'y' ) {
			$f_where .= ' AND tfb.`objectId` IS NULL';
			if (!$with_backlink) {
				$f_table .= 'LEFT JOIN `tiki_file_backlinks` tfb ON (tf.`fileId`=tfb.`fileId`)';
			}
		}

		if( !empty($filter['categId']) ) {
			$jail = $filter['categId'];
		} else {
			$jail = $categlib->get_jail();
		}
			
		if( $jail ) {
			$categlib->getSqlJoin( $jail, 'file', 'tf.`fileId`', $f_jail_join, $f_jail_where, $f_jail_bind );
		} else {
			$f_jail_join = '';
			$f_jail_where = '';
			$f_jail_bind = array();
		}

		$f_query = 'SELECT '.implode(', ', array_keys($f2g_corresp)).' FROM '.$f_table.$f_jail_join.' WHERE tf.`archiveId`='.( $parent_is_file ? $fileId : '0' ) . $f_jail_where . $f_where;
		$bindvars = array();

		$mid = '';
		$midvars = array();
		if ( $find ) {
			$findesc = '%'.$find.'%';
			$tab = $with_subgals?'tab':'tf';
			$mid = " (upper($tab.`name`) LIKE upper(?) OR upper($tab.`description`) LIKE upper(?) OR upper($tab.`filename`) LIKE upper(?))";
			$midvars = array($findesc, $findesc, $findesc);
		}
		if ( !empty($filter['creator']) ) {
			$f_query .= ' AND tf.`user` = ? ';
			$bindvars[] = $filter['creator'];
		}
		if ( !empty($filter['lastModif']) ) {
			$f_query .= ' AND tf.`lastModif` < ? ';
			$bindvars[] = $filter['lastModif'];
		}
		if ( !empty($filter['lastDownload']) ) {
			$f_query .= ' AND (tf.`lastDownload` < ? or tf.`lastDownload` is NULL)';
			$bindvars[] = $filter['lastDownload'];
		}
		if ( $with_files && $prefs['feature_file_galleries_save_draft'] == 'y' ) {
			$bindvars[] = $user;
		}
		if (!empty($filter['fileId'])) {
			$f_query .= ' AND tf.`fileId` in ('.implode(',',array_fill(0, count($filter['fileId']),'?')).')';
			$bindvars = array_merge($bindvars, $filter['fileId']);
		}
		$galleryId_str = '';
		if ( is_array($galleryId) ) {
			$galleryId_str = ' in ('.implode(',', array_fill(0, count($galleryId),'?')).')';
			$bindvars = array_merge($bindvars, $galleryId);
		} elseif ( $galleryId >= -1 ) {
			$galleryId_str = '=?';
			if ( $with_files ) $bindvars[] = $galleryId;
			if ( $with_subgals ) $bindvars[] = $galleryId;
		}
		if ( $galleryId_str != '' ) {
			$f_query .= ' AND tf.`galleryId`'.$galleryId_str;
		}
		
		if ( $with_subgals ) {

			$g_mid = '';
			$g_join = '';
			$g_group_by = '';

			$join = '';
			$select = 'tab.*';

			if ( $with_files_count ) {
				$g_join = ' LEFT JOIN `tiki_files` tfc ON (tfg.`galleryId` = tfc.`galleryId`)';
				$g_group_by = ' GROUP BY tfg.`galleryId`'; 
			}

			// If $user is admin then get ALL galleries, if not only user galleries are shown
			// If the user is not admin then select it's own galleries or public galleries
			if ( $tiki_p_admin_file_galleries != 'y' && $my_user != 'admin' && empty($parentId) ) {
				$g_mid = " AND (tfg.`user`=? OR tfg.`visible`='y' OR tfg.`public`='y')";
				$bindvars[] = $my_user;
			}

			if( $jail ) {
				$categlib->getSqlJoin( $jail, 'file gallery', '`tfg`.`galleryId`', $g_jail_join, $g_jail_where, $g_jail_bind );
			} else {
				$g_jail_join = '';
				$g_jail_where = '';
				$g_jail_bind = array();
			}
			
			$g_query = 'SELECT '.implode(', ', array_values($f2g_corresp)).' FROM '.$g_table.$g_join.$g_jail_join;
			$g_query .= " WHERE 1=1 ";

			if ( $galleryId_str != '' ) {
				$g_query .= ' AND tfg.`parentId`'.$galleryId_str;
			}
			$g_query .= $g_mid;

			$g_query .= $g_jail_where;
			$bindvars = array_merge( $bindvars, $g_jail_bind );

			if ( $with_parent_name ) {
				$select .= ', tfgp.`name` as `parentName`';
				$join .= ' LEFT OUTER JOIN `tiki_file_galleries` tfgp ON (tab.`parentId` = tfgp.`galleryId`)';
			}

			if ( $with_files ) {
				$query = "SELECT $select FROM (($f_query $f_group_by) UNION ($g_query $g_group_by)) as tab".$join;
				$bindvars = array_merge( $f_jail_bind, $bindvars );
			} else {
				$query = "SELECT $select FROM ($g_query $g_group_by) as tab".$join;
			}
			if ( $mid != '' ){
				$query .= ' WHERE'.$mid;
				$bindvars = array_merge( $bindvars, $midvars );
			}
			if ( $orderby != '' ) $orderby = 'tab.'.$orderby;

		} else {
			$query = $f_query;
			$bindvars = array_merge( $f_jail_bind, $bindvars );
			if ( $mid != '' ) {
				$query .= ' AND'.$mid;
				$bindvars = array_merge( $bindvars, $midvars );
			}
			$query .= $f_group_by;
		}

		if ( $keep_subgals_together ) {
			$query .= ' ORDER BY `isgal` desc'.($orderby == '' ? '' : ', '.$orderby);
		} elseif ( $orderby != '' ) {
			$query .= ' ORDER BY '.$orderby;
		}
		$result = $this->fetchAll($query, $bindvars);
		$ret = array();
		$gal_size_order = array();
		$cant = 0;
		$n = -1;
		$need_everything = ( $with_subgals_size && ( $sort_mode == 'size_asc' || $sort_mode == 'filesize_asc' ) );
		$cachelib = TikiLib::lib('cache');
		//TODO: perms cache for file perms (now we are using cache only for file gallery perms)
		$cacheName = md5("group:".implode("\n", $this->get_user_groups($user)));
		$cacheType = 'fgals_perms_'.$galleryId."_";
		if ($galleryId > 0 && $cachelib->isCached($cacheName, $cacheType)) {
			$fgal_perms = unserialize($cachelib->getCached($cacheName, $cacheType));
		} else {
			$fgal_perms = array();
		}
		foreach( $result as $res ) {
			$object_type = ( $res['isgal'] == 1 ? 'file gallery' : 'file');
			$galleryId = $res['isgal'] == 1 ? $res['id'] : $res['galleryId'];

			// if file is categorized uses category permisions, otherwise uses parent file gallery permissions
			// note that the file will not be displayed if categorized but its categories has no file gallery related permissions
			if ($object_type == 'file' && $categlib->is_categorized($object_type, $res['id'])) {
				$res['perms'] = $this->get_perm_object($res['id'], 'file', array(), false);
			} else if (isset($fgal_perms[$galleryId])) {
				$res['perms'] = $fgal_perms[$galleryId];
			} else {
				$fgal_perms[$galleryId] = $res['perms'] = $this->get_perm_object($galleryId, 'file gallery', array(), false);
			}
			
			if ($galleryId <=0) {
				$cachelib->cacheItem($cacheName, serialize($fgal_perms), 'fgals_perms_'.$galleryId.'_');
			}

			// Don't return the current item, if :
			//  the user has no rights to view the file gallery AND no rights to list all galleries (in case it's a gallery)
			if ( ( $res['perms']['tiki_p_view_file_gallery'] != 'y' && ! $this->user_has_perm_on_object($user,$res['id'], $object_type, 'tiki_p_view_file_gallery') )
					&& ( $res['isgal'] == 0 || ( $res['perms']['tiki_p_list_file_gallery'] != 'y' && ! $this->user_has_perm_on_object($user,$res['id'], $object_type, 'tiki_p_list_file_gallery') ) ) 
				 ) {
				continue;
			}
			if (empty($backlinkPerms[$res['galleryId']])) {
				$info = $this->get_file_gallery_info($res['galleryId']);
				$backlinkPerms[$res['galleryId']] = $info['backlinkPerms'];
			}
			if ($backlinkPerms[$res['galleryId']] == 'y' && $this->hasOnlyPrivateBacklinks($res['id'])) {
				continue;
			}
			// add markup to be inserted onclick
			// add information for share column if is active
			if ($object_type === 'file') {
				$res['wiki_syntax'] = $this->process_fgal_syntax($wiki_syntax, $res);
				
				if($prefs['auth_token_access'] == 'y'){
					$query = 'select email, sum((maxhits - hits)) as visit, sum(maxhits) as maxhits  from tiki_auth_tokens where `parameters`=? group by email';
					$share_result = $this->fetchAll($query, array('{"fileId":"'.$res['id'].'"}'));
					$res['share']['data'] = $share_result;
					$tmp = array();
					if(is_array($res['share']['data'])){
						foreach ($res['share']['data'] as $data) {
							$tmp[] = $data['email'];
						}
					}
					$string_share = implode(', ',$tmp);
					$res['share']['string'] = substr($string_share, 0, 40);
					if(strlen($string_share) >40){
						$res['share']['string'] .= '...';
					}
					$res['share']['nb'] = count($share_result);
				}
			}
				
			$n++;
			if ( ! $need_everything && $offset != -1 && $n < $offset ) continue;

			if ( $need_everything || $maxRecords == -1 || $cant < $maxRecords ) {
				$ret[$cant] = $res;
				if ( $with_subgals_size && $res['isgal'] == 1 ) {
					$ret[$cant]['size'] = (string)$this->getUsedSize($res['id']);
					$ret[$cant]['filesize'] = $ret[$cant]['size']; /// Obsolete
					if ( $keep_subgals_together ) {
						$gal_size_order[$cant] = $ret[$cant]['size'];
					}
				}
				if ( $with_subgals_size && ! $keep_subgals_together ) {
					$gal_size_order[$cant] = $ret[$cant]['size'];
				}
				// generate link for podcasts
				$ret[$cant]['podcast_filename'] = $res['path'];
			}

			$cant++;
		}
		if ($galleryId > 0)
			$cachelib->cacheItem($cacheName, serialize($fgal_perms), $cacheType);
		if ( ! $need_everything ) $cant += $offset;

		if ( count($gal_size_order) > 0 ) {
			if ( $sort_mode == 'size_asc' || $sort_mode == 'filesize_asc' ) {
				asort($gal_size_order, SORT_NUMERIC);
			} elseif ( $sort_mode == 'size_desc' || $sort_mode == 'filesize_desc' ) {
				arsort($gal_size_order, SORT_NUMERIC);
			}
			$ret2 = array();
			foreach ( $gal_size_order as $k => $v ) {
				$ret2[] = $ret[$k];
				unset($ret[$k]);
			}
			if ( count($ret) > 0 ) {
				foreach ( $ret as $k => $v ) {
					$ret2[] = $v;
				}
			}
			unset($ret);
			$ret =& $ret2;
		}

		if ( $need_everything && ( $offset > 0 || $maxRecords != -1 ) ) {
			if ( $maxRecords == -1 ) {
				$ret = array_slice($ret, $offset);
			} else {
				$ret = array_slice($ret, $offset, $maxRecords);
			}
		}

		return array('data' => $ret, 'cant' => $cant);
	}
	
	function list_visible_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user = '', $find = null) {
		// If $user is admin then get ALL galleries, if not only user galleries are shown

		$fileGalleries = $this->table('tiki_file_galleries');
		$conditions = array(
			'visible' => 'y',
		);

		// If the user is not admin then select `it` 's own galleries or public galleries
		global $tiki_p_admin_files_galleries;
		if ($tiki_p_admin_files_galleries != 'y') {
			$conditions['nonAdmin'] = $fileGalleries->expr('(`user`=? or `public`=?)', array($user, 'y'));
		}

		if (! empty($find) ) {
			$findesc = '%' . $find . '%';
			$conditions['search'] = $fileGalleries->expr('(`name` like ? or `description` like ?)', array($findesc, $findesc));
		}

		$sort = $this->convertSortMode($sort_mode);
		return array(
			"data" => $fileGalleries->fetchAll($fileGalleries->all(), $conditions, $maxRecords, $offset, $fileGalleries->expr($sort)),
			"cant" => $fileGalleries->fetchCount($conditions),
		);
	}

	function get_file_gallery($id = -1, $defaultsFallback = true) {
		static $defaultValues = null;

		if ( $defaultValues === null && $defaultsFallback ) {
			global $prefs;
			$defaultValues = $this->default_file_gallery();
		}

		if ( $id > 0 ) {
			$res = $this->table('tiki_file_galleries')->fetchFullRow(array('galleryId' => (int) $id));
		} else {
			$res = array();
		}

		// Use default values if some values are not specified
		if ( $res !== false && $defaultsFallback ) {
			foreach ( $defaultValues as $k => $v ) {
				if ( !isset($res[$k]) || $res[$k] === null ) {
					$res[$k] = $v;
				}
			}
		}

		return $res;
	}

	// convert markup to be inserted onclick - replace: %fileId%, %name%, %description% etc
	private function process_fgal_syntax($syntax, $file) {
		$replace_keys = array('fileId', 'name', 'filename', 'description', 'hits', 'author', 'filesize', 'filetype');
		foreach($replace_keys as $k) {
			if (isset($file[$k])) {
				$syntax = preg_replace("/%$k%/", $file[$k], $syntax);
			}
		}
		return $syntax;
	}

	private function print_msg($msg, $id, $htmlEntities = false) {
		global $prefs;

		if ( $htmlEntities ) {
			$msg = htmlentities($msg, ENT_QUOTES, 'UTF-8');
		}

		if ( $prefs['javascript_enabled'] == 'y' ) {
			if ( $prefs['fgal_upload_progressbar'] == 'ajax_flash' ) {
				echo $msg;
			} else {
				require_once('lib/smarty_tiki/modifier.escape.php');
				echo '<?xml version="1.0" encoding="UTF-8"?'.'>';
				ob_flush();
				echo "<script type='text/javascript'><!--//--><![CDATA[//><!--\n";
				echo "parent.progress('$id','" . smarty_modifier_escape($msg, 'javascript', 'UTF-8') . "');\n";
				echo "//--><!]]></script>\n";
			}
			ob_flush();
		}
	}

	/*shared*/
	public function actionHandler($action, $params) {
		$method_name = '_actionHandler_' . $action;
		if ( ! is_callable( array( $this, $method_name ) ) )
			return false;

		return call_user_func( array( $this, $method_name ), $params );
	}

	private function _actionHandler_removeFile( $params ) {
		// mandatory params: int fileId
		// optional params: boolean draft, array gal_info
		if ( ! empty( $params ) && isset( $params['fileId'] ) ) {
			// To remove an image the user must be the owner or the file or the gallery or admin

			if ( ! isset( $params['draft'] ) ) {
				$params['draft'] = false;
			}

			global $smarty;
			if ( ! $info = $this->get_file_info( $params['fileId'] ) ) {
				$smarty->assign('msg', tra('Incorrect param'));
				$smarty->display('error.tpl');
				die;
			}

			if ( empty( $params['gal_info'] ) || ! isset( $params['gal_info']['user'] ) ) {
				if ( isset( $info['galleryId'] ) ) {
					$params['gal_info'] = $this->get_file_gallery_info( $info['galleryId'] );
				} else {
					$params['gal_info'] = array('user' => '');
				}
			}

			global $tiki_p_admin_file_galleries, $user;
			if ( $tiki_p_admin_file_galleries != 'y' && ( ! $user || $user != $params['gal_info']['user'] ) ) {
				if ( $user != $info['user'] ) {
					$smarty->assign('errortype', 401);
					$smarty->assign('msg', tra('You do not have permission to remove files from this gallery'));
					$smarty->display('error.tpl');
					die;
				}
			}

			$backlinks = $this->getFileBacklinks( $params['fileId'] );

			if ( isset( $_POST['daconfirm'] ) && ! empty( $backlinks ) ) {
				$smarty->assign_by_ref('backlinks', $backlinks);
				$smarty->assign('file_backlinks_title', 'WARNING: The file is used in:');//get_strings tra('WARNING: The file is used in:')
				$smarty->assign('confirm_detail', $smarty->fetch('file_backlinks.tpl')); ///FIXME
			}

			global $access;
			$confirmationText = ( empty( $info['name'] ) ? '' : htmlspecialchars( $info['name']) . ' - ' ) . htmlspecialchars($info['filename']);
			if ( $params['draft'] ) {
				$access->check_authenticity( tra( 'Remove file draft: ') . $confirmationText );
				$this->remove_draft( $info['fileId'], $user );
			} else {
				$access->check_authenticity( tra('Remove file: ') . $confirmationText );
				$this->remove_file( $info, $params['gal_info'] );
			}
		}
	}

	private function _actionHandler_uploadFile( $params ) {
		global $user, $prefs, $tikilib, $logslib, $smarty, $tiki_p_admin, $tiki_p_batch_upload_files;

		$batch_job = false;
		$didFileReplace = false;
		$aux = array();
		$errors = array();
		$uploads = array();

		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$foo1 = str_replace("tiki-upload_file", "tiki-download_file", $foo["path"]);
		$smarty->assign('url_browse', $tikilib->httpPrefix() . $foo1);
		$url_browse = $tikilib->httpPrefix() . $foo1;

		// create direct download path for podcasts
		$gal_info = null;
		if ( ! empty( $params['galleryId'][0] ) ) {
			$gal_info = $this->get_file_gallery( (int) $params['galleryId'][0] );
			$podCastGallery = $this->isPodCastGallery( (int) $params["galleryId"][0], $gal_info );
			$savedir = $this->get_gallery_save_dir( (int) $params["galleryId"][0], $gal_info );
		}
		$podcast_url = str_replace("tiki-upload_file.php", "", $foo["path"]);
		$podcast_url = $tikilib->httpPrefix() . $podcast_url . $prefs['fgal_podcast_dir'];

		if ( isset( $params['fileId'] ) ) {

			$editFile = true;
			$editFileId = $params['fileId'];

			if ( $gal_info !== null && ( $nb_files = count( $params['galleryId'] ) ) != 1 ) {
				for ( $i = $nb_files - 1; $i >= 0 ; $i-- ) {
					if ( ! isset( $params['galleryId'][$i] ) || $params['galleryId'][$i] != $params['galleryId'][0] ) {
						$smarty->assign('errortype', 401);
						$smarty->assign('msg', tra('You are trying to edit multiple files in different galleries, which is not supported yet'));
						$smarty->display('error.tpl');
						die;
					}
				}
			}

			if ( isset( $params['fileInfo'] ) ) {
				$fileInfo = $params['fileInfo'];
				$fileInfo['fileId'] = $editFileId;
			} else {
				if ( ! ( $fileInfo = $this->get_file_info( $editFileId ) ) ) {
					$smarty->assign('msg', tra('The specified file does not exist'));
					$smarty->display('error.tpl');
					die;
				}
			}

			if ( ! empty( $params['name'][0] ) ) $fileInfo['name'] = $params['name'][0];
			if ( ! empty( $params['description'][0] ) ) $fileInfo['description'] = $params['description'][0];
			if ( ! empty( $params['user'][0] ) ) $fileInfo['user'] = $params['user'][0];
			if ( ! empty( $params['author'][0] ) ) $fileInfo['author'] = $params['author'][0];

		} else {
			$editFileId = 0;
			$editFile = false;
			$fileInfo = null;
		}
	
		if ( ! empty( $_FILES['userfile'] ) ) {
			$feedback_message = '';
			$aFiles['userfile'] = $_FILES['userfile'];
		
			foreach ( $aFiles["userfile"]["error"] as $key => $error ) {
				
				$formId = $params['formId'];
				if (empty($params['galleryId'][$key])) {
					continue;
				}
				
				if (!isset($params['comment'][$key])) {
					$params['comment'][$key] = '';
				}
				
				// We process here file uploads
				if (!empty($aFiles["userfile"]["name"][$key])) {
					
					// Were there any problems with the upload?  If so, report here.
					if (!is_uploaded_file($aFiles["userfile"]["tmp_name"][$key])) {
						$errors[] = $aFiles['userfile']['name'][$key] . ': ' . tra('Upload was not successful') . ': ' . $this->uploaded_file_error($error);
						continue;
					}
					// Check the name
					if (!empty($prefs['fgal_match_regex'])) {
						if (!preg_match('/' . $prefs['fgal_match_regex'] . '/', $aFiles["userfile"]['name'][$key])) {
							$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $aFiles["userfile"]["name"][$key];
							continue;
						}
					}
					if (!empty($prefs['fgal_nmatch_regex'])) {
						if (preg_match('/' . $prefs['fgal_nmatch_regex'] . '/', $aFiles["userfile"]["name"][$key])) {
							$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $aFiles["userfile"]["name"][$key];
							continue;
						}
					}
					$name = $aFiles["userfile"]["name"][$key];
					if (isset($params["isbatch"][$key]) && $params["isbatch"][$key] == 'on' && strtolower(substr($name, strlen($name) - 3)) == 'zip') {
						if ($tiki_p_batch_upload_files == 'y') {
							try {
								$this->process_batch_file_upload($params["galleryId"][$key], $aFiles["userfile"]['tmp_name'][$key], $user, isset($params["description"][$key]) ? $params["description"][$key] : '', $errors);
							} catch (Exception $e) {
								$errors[] = tra('An exception occurred:') . ' '  . $e->getMessage();
							}
							if (!empty($errors)) {
								continue;
							}
							$batch_job = true;
							$batch_job_galleryId = $params["galleryId"][$key];
							$this->print_msg(tra('Batch file processed') . " $name", $formId, true);
							continue;
						} else {
							$errors[] = tra('No permission to upload zipped file packages');
							continue;
						}
					}
					
					if (!$this->checkQuota($aFiles['userfile']['size'][$key], $params['galleryId'][$key], $error)) {
						$errors[] = $error;
						continue;
					}
		
					$size = $aFiles["userfile"]['size'][$key];
					$type = $aFiles["userfile"]['type'][$key];
					$name = stripslashes($aFiles["userfile"]['name'][$key]);
		
					$file_name = $aFiles["userfile"]["name"][$key];
					$file_tmp_name = $aFiles["userfile"]["tmp_name"][$key];
					$tmp_dest = $prefs['tmpDir'] . "/" . $file_name . ".tmp";
		
					// Handle per gallery image size limits
					$ratio = 0;
					list(,$subtype)=explode('/',strtolower($type));
					if ($subtype == "pjpeg")	// supress ie non-mime type (ie sends non standard mime-type for jpeg)
					{
						$subtype = "jpeg";
						$type = "image/jpeg";
					}
					// If it's an image format we can handle and gallery has limits on image sizes
					if ( (in_array($subtype, array("jpg","jpeg","gif","png","bmp","wbmp"))) 
						&& ( ($gal_info["image_max_size_x"]) || ($gal_info["image_max_size_y"]) && (!$podCastGallery) ) ) {
						$image_size_info = getimagesize($file_tmp_name);
						$image_x = $image_size_info[0];
						$image_y = $image_size_info[1];
						if($gal_info["image_max_size_x"]) {
							$rx=$image_x/$gal_info["image_max_size_x"];
						}else{
							$rx=0;
						}
						if($gal_info["image_max_size_y"]) {
							$ry=$image_y/$gal_info["image_max_size_y"];
						}else{
							$ry=0;
						}
						$ratio=max($rx,$ry);
						if($ratio>1) {	// Resizing will occur
							$image_new_x=$image_x/$ratio;
							$image_new_y=$image_y/$ratio;
							$resized_file = $tmp_dest;
							$image_resized_p = imagecreatetruecolor($image_new_x, $image_new_y);
							switch($subtype) {
								case "gif":
									$image_p = imagecreatefromgif($file_tmp_name);
									break;
								case "png":
									$image_p = imagecreatefrompng($file_tmp_name);
									break;
								case "bmp":
								case "wbmp":
									$image_p = imagecreatefromwbmp($file_tmp_name);
									break;
								case "jpg":
								case "jpeg":
									$image_p = imagecreatefromjpeg($file_tmp_name);
									break;
							}
							if(!imagecopyresampled($image_resized_p, $image_p, 0, 0, 0, 0, $image_new_x, $image_new_y, $image_x, $image_y)) {
								$errors[] = tra('Cannot resize the file:') . ' ' . $resized_file;
							}
							switch($subtype) {
								case "gif":
									if (!imagegif($image_resized_p, $resized_file)){
										$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
									}
									break;
								case "png":
									if (!imagepng($image_resized_p, $resized_file)){
										$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
									}
									break;
								case "bmp":
								case "wbmp":
									if (!image2wbmp($image_resized_p, $resized_file)){
										$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
									}
									break;
								case "jpg":
								case "jpeg":
									if (!imagejpeg($image_resized_p, $resized_file)){
										$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
									}
									break;
							}
							unlink($image_p);
							$feedback_message = sprintf(tra('Image was reduced: %s x %s -> %s x %s'),$image_x, $image_y, (int)$image_new_x, (int)$image_new_y);
							$size = filesize($resized_file);
						}
					}
		
					if ($ratio <=1) {
						// No resizing
						if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
							if ($tiki_p_admin == 'y') {
								$errors[] = tra('Errors detected').'. '.tra('Check that these paths exist and are writable by the web server').': '.$file_tmp_name.' '.$tmp_dest;
								continue;
							}	else	{
								$errors[] = tra('Errors detected');
								continue;
							}
							$logslib->add_log('file_gallery', tra('Errors detected').'. '.tra('Check that these paths exist and are writable by the web server').': '.$file_tmp_name.' '.$tmp_dest);
						} else {
							$logslib->add_log('file_gallery', tra('File added: ').$tmp_dest.' '.tra('by').' '.$user);
						}
					}
		
					if (false === $data = @file_get_contents($tmp_dest)) {
						$errors[] = tra('Cannot read the file:') . ' ' . $tmp_dest;
					}
	
					@unlink($tmp_dest);
	
					$fhash = '';
					$extension = '';
					if (false !== $savedir) {
						$name = $aFiles["userfile"]['name'][$key];
						$extension = '';
						// for podcast galleries add the extension so the
						// file can be called directly if name is known,
						if ($podCastGallery) {
							$path_parts = pathinfo($name);
							if (in_array(strtolower($path_parts['extension']), array('m4a', 'mp3', 'mov', 'mp4', 'm4v', 'pdf', 'flv', 'swf'))) {
								$extension = '.' . strtolower($path_parts['extension']);
							} else {
								$errors[] = tra('Incorrect file extension:').$path_parts['extension'];
							}
						}
	
						$fhash = $this->find_unique_name($savedir, $name);
	
						if (false === @file_put_contents($savedir . $fhash . $extension, $data)) {
							$errors[] = tra('Cannot write to this file:') . $savedir . $fhash;
						}
	
						$data = '';
					}
		
					if (preg_match('/.flv$/', $name)) {
						$type = "video/x-flv";
					}
	
					if (count($errors)) {
						continue;
					}
	
					if (!$size) {
						$errors[] = tra('Warning: Empty file:') . '  ' . $name . '. ' . tra('Please re-upload your file');
					}
					if (false === $savedir) {
						if (!isset($data) || strlen($data) < 1) {
							$errors[] = tra('Warning: Empty file:') . ' ' . $name . '. ' . tra('Please re-upload your file');
						}
					}
		
					if (empty($params['name'][$key])) $params['name'][$key] = $name;
					if (empty($params['user'][$key])) $params['user'][$key] = $user;
					if (!isset($params['description'][$key])) $params['description'][$key] = '';
					if (empty($params['author'][$key])) $params['author'][$key] = $user;
					if (empty($params['deleteAfter'][$key]) || empty($params['deleteAfter_unit'][$key])) {
						$deleteAfter = null;
					} else {
						$deleteAfter = $params['deleteAfter'][$key]*$params['deleteAfter_unit'][$key];
					}
	
					if ( is_array( $fileInfo ) ) {
						$fileInfo['filename'] = $file_name;
					}
	
					if (isset($data)) {
						if ($editFile) {
							$didFileReplace = true;
							$fileId = $this->replace_file($editFileId, $params["name"][$key], $params["description"][$key], $name, $data, $size, $type, $params['user'][$key], $fhash . $extension, $params['comment'][$key], $gal_info, $didFileReplace, $params['author'][$key], $fileInfo['lastModif'], $fileInfo['lockedby'], $deleteAfter);
							if ($prefs['fgal_limit_hits_per_file'] == 'y') {
								$this->set_download_limit($editFileId, $params['hit_limit'][$key]);
							}
						} else {
							$fileId = $this->insert_file($params["galleryId"][$key], $params["name"][$key], $params["description"][$key], $name, $data, $size, $type, $params['user'][$key], $fhash . $extension, '', $params['author'][$key], '', '', $deleteAfter);
						}
						if (!$fileId) {
							$errors[] = tra('Upload was not successful. Duplicate file content') . ': ' . $name;
							if (false !== $savedir) {
								@unlink($savedir . $fhash);
							}
						} else {
							$_SESSION['allowed'][$fileId] = true;
						}
						if ( $prefs['fgal_limit_hits_per_file'] == 'y' && isset($params['hit_limit'][$key]) ) {
							$this->set_download_limit($fileId, $params['hit_limit'][$key]);
						}
						if (count($errors) == 0) {
							$aux['name'] = $name;
							$aux['size'] = $size;
							$aux['fileId'] = $fileId;
							if ($podCastGallery) {
								$aux['dllink'] = $podcast_url . $fhash . $extension . '&amp;thumbnail=y';
							} else {
								$aux['dllink'] = $url_browse . "?fileId=" . $fileId;
							}
							$uploads[] = $aux;
							$cat_type = 'file';
							$cat_objid = $fileId;
							$cat_desc = substr($params["description"][$key], 0, 200);
							$cat_name = empty($params['name'][$key]) ? $name : $params['name'][$key];
							$cat_href = $aux['dllink'];
							$cat_object_exists = (bool) $fileId;
							if ($prefs['feature_groupalert'] == 'y' && isset($params['listtoalert'])) {
								global $groupalertlib; include_once ('lib/groupalert/groupalertlib.php');
								$groupalertlib->Notify($params['listtoalert'], "tiki-download_file.php?fileId=" . $fileId);
							}
							include_once ('categorize.php');
							// Print progress
							if (empty($params['returnUrl']) && $prefs['javascript_enabled'] == 'y') {
								$smarty->assign("name", $aux['name']);
								$smarty->assign("size", $aux['size']);
								$smarty->assign("fileId", $aux['fileId']);
								$smarty->assign("dllink", $aux['dllink']);
								$smarty->assign("nextFormId", $formId + 1);
								$smarty->assign("feedback_message", $feedback_message);
								$syntax = $this->getWikiSyntax($params["galleryId"][$key]);
								$syntax = $this->process_fgal_syntax($syntax, $aux);
								$smarty->assign('syntax', $syntax);
								$this->print_msg($smarty->fetch("tiki-upload_file_progress.tpl"), $formId);
							}
						}
					}
				}
			}
		}

		if (empty($params['returnUrl']) && count($errors)) {
			foreach($errors as $error) {
				$this->print_msg($error, $formId, true);
			}
		}
		if ($editFile && !$didFileReplace) {
			if (empty($params['deleteAfter']) || empty($params['deleteAfter_unit'])) {
				$deleteAfter = null;
			} else {
				$deleteAfter = $params['deleteAfter']*$params['deleteAfter_unit'];
			}
			$fileInfo['fileId'] = $this->replace_file($editFileId, $params['name'][0], $params['description'][0], $fileInfo['filename'], $fileInfo['data'], $fileInfo['filesize'], $fileInfo['filetype'], $fileInfo['user'], $fileInfo['path'], $params['comment'][0], $gal_info, $didFileReplace, $params['author'][0], $fileInfo['lastModif'], $fileInfo['lockedby'], $deleteAfter);
			$fileChangedMessage = tra('File update was successful') . ': ' . $params['name'];
			$smarty->assign('fileChangedMessage', $fileChangedMessage);
			$cat_type = 'file';
			$cat_objid = $editFileId;
			$cat_desc = substr($params["description"][0], 0, 200);
			$cat_name = empty($fileInfo['name']) ? $fileInfo['filename'] : $fileInfo['name'];
			$cat_href = $podCastGallery ? $podcast_url . $fhash : "$url_browse?fileId=" . $editFileId;
			$cat_object_exists = (bool) $cat_objid;
			if ($prefs['fgal_limit_hits_per_file'] == 'y') {
				$this->set_download_limit($editFileId, $params['hit_limit'][0]);
			}
			include_once ('categorize.php');
		}
		if ($batch_job and count($errors) == 0) {
			header("location: tiki-list_file_gallery.php?galleryId=" . $batch_job_galleryId);
			die;
		}

		if (!empty($editFileId) and count($errors) == 0) {
			header("location: tiki-list_file_gallery.php?galleryId=" . $params["galleryId"][0]);
			die;
		}

		$smarty->assign('errors', $errors);
		$smarty->assign('uploads', $uploads);

		if (!empty($params['returnUrl'])) {
			if (!empty($errors)) {
				$smarty->assign('msg', implode($errors, '<br />'));
				$smarty->display('error.tpl');
				die;
			}
			header('location: '.$params['returnUrl']);
			die;
		}

		// Returns fileInfo of the new file if only one file has been edited / uploaded
		return $fileInfo;
	}

	function handle_file_upload($fileKey, $file)
	{
		global $prefs, $user, $tiki_p_edit_gallery_file, $tiki_p_admin_file_galleries, $smarty;

		$savedir = $prefs['fgal_use_dir'];

		$msg = null;
		if (! is_uploaded_file($file['tmp_name'])) {
			$msg = array('error' => tra('Upload was not successful') . ': ' . $this->uploaded_file_error($file['error']));
		} elseif (! $file['size']) {
			$msg = tra('Warning: Empty file:') . '  ' . htmlentities($file['name']) . '. ' . tra('Please re-upload your file');
		} elseif (empty($file['name']) || !preg_match('/^upfile(\d+)$/', $fileKey, $regs) || !($fileInfo = $this->get_file_info($regs[1]))) {
			$msg = tra('Could not upload the file') . ': ' . htmlentities($file['name']);
		} elseif ((!empty($prefs['fgal_match_regex']) && (!preg_match('/' . $prefs['fgal_match_regex'] . '/', $file['name']))) || (!empty($prefs['fgal_nmatch_regex']) && (preg_match('/' . $prefs['fgal_nmatch_regex'] . '/', $file['name'])))) {
			$msg = tra('Invalid filename (using filters for filenames)') . ': ' . htmlentities($file['name']);
		} elseif ($_REQUEST['galleryId'] != $fileInfo['galleryId']) {
			$msg = tra('Could not find the file requested');
		} elseif (!empty($fileInfo['lockedby']) && $fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y') {
			// if locked, user must be the locker
			$msg = tra(sprintf('The file is locked by %s', $fileInfo['lockedby']));
		} elseif (!($tiki_p_edit_gallery_file == 'y' || (!empty($user) && ($user == $fileInfo['user'] || $user == $fileInfo['lockedby'])))) {
			// must be the owner or the locker or have the perms
			$smarty->assign('errortype', 401);
			$msg = tra('You do not have permission to edit this file');
		}

		if ($msg) {
			@unlink($file['tmp_name']);
			return array('error' => $msg);
		}

		$data = '';
		$fhash = '';
		if ($prefs['fgal_use_db'] == 'n') {
			$fhash = $this->find_unique_name($savedir, $file['name']);
			if ($prefs['feature_file_galleries_save_draft'] == 'y') {
				$fhash .= '.' . $user . '.draft';
			}

			if (! move_uploaded_file($file['tmp_name'], $savedir . $fhash)) {
				@unlink($file['tmp_name']);
				return array('error' => tra('Cannot write to this file:') . $savedir . $fhash);
			}
		} else {
			$data = file_get_contents($file['tmp_name']);

			if (false === $data) {
				@unlink($file['tmp_name']);
				return array('error' => tra('Cannot read uploaded file.'));
			}
		}

		return array(
			'filename' => $file['name'],
			'fhash' => $fhash,
			'data' => $data,
			'type' => preg_match('/.flv$/', $file['name']) ? 'video/x-flv' : $file['type'],
			'size' => $file['size'],
		);
	}

	function handle_batch_upload($galleryId, $info, $ext = '')
	{
		$savedir = $this->get_gallery_save_dir($galleryId);

		$fhash = null;
		$data = null;

		if ($savedir) {
			$fhash = $this->find_unique_name($savedir, $info['name']);

			if (in_array($ext, array(
				"m4a",
				"mp3",
				"mov",
				"mp4",
				"m4v",
				"pdf"
			))) {
				$fhash.= "." . $ext;
			}

			if (! rename($info['source'], $savedir . $fhash)) {
				return array('error' => tra('Cannot write to this file:') . $savedir . $fhash);
			}
		} else {
			$data = file_get_contents($info['source']);

			if (false === $data) {
				return array('error' => tra('Cannot read file on disk.'));
			}
		}

		return array(
			'data' => $data,
			'fhash' => $fhash,
		);
	}
}
$filegallib = new FileGalLib;
