<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

use Tiki\FileGallery\Definition as GalleryDefinition;
use Tiki\FileGallery\FileWrapper\WrapperInterface as FileWrapper;

class FileGalLib extends TikiLib
{

	private $wikiupMoved = [];


	function isPodCastGallery($galleryId, $gal_info=null)
	{
		if (empty($gal_info))
			$gal_info = $this->get_file_gallery_info((int)$galleryId);
		if (($gal_info["type"]=="podcast") || ($gal_info["type"]=="vidcast")) {
			return true;
		} else {
			return false;
		}
	}

	public function get_gallery_save_dir($galleryId, $galInfo = null)
	{
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

	private function get_file_checksum($galleryId, $path, $data)
	{
		$savedir = $this->get_gallery_save_dir($galleryId);

		if (false !== $savedir) {
			if ( filesize($savedir . $path) > 0 ) {
				if (empty($data)) {
					return md5_file($savedir . $path);
				} else {
					return md5($savedir . $path . $data);	// for svg images with a background or avatars
				}
			} else {
				return md5(time());
			}
		} else {
			return md5($data);
		}
	}

	protected function find_unique_name($directory, $start)
	{
		$fhash = md5($start);

		while (file_exists($directory . $fhash)) {
			$fhash = md5(uniqid($fhash));
		}

		return $fhash;
	}

	function get_attachment_gallery( $objectId, $objectType, $create = false )
	{
		switch ( $objectType ) {
			case 'wiki page':
				return $this->get_wiki_attachment_gallery($objectId, $create);
		}

		return false;
	}

	function get_wiki_attachment_gallery( $pageName, $create = false )
	{
		global $prefs;

		$return = $this->getGalleryId($pageName, $prefs['fgal_root_wiki_attachments_id']);

		// Get the Wiki Attachment Gallery for this wiki page or create it if it does not exist
		if ( $create && !$return ) {

			// Create the attachment gallery only if the wiki page really exists
			if ( $this->get_page_id_from_name($pageName) > 0 ) {

				$return = $this->replace_file_gallery(
					array(
						'name' => $pageName,
						'user' => 'admin',
						'type' => 'attachments',
						'public' => 'y',
						'visible' => 'y',
						'parentId' => $prefs['fgal_root_wiki_attachments_id']
					)
				);
			}
		}

		return $return;
	}

	/**
	 * Looks for and returns a user's file gallery, depending on the various prefs
	 *
	 * @return bool|int		false if none found, id of user's filegal otherwise
	 */

	function get_user_file_gallery($auser = '')
	{
		global $user, $prefs;
		$tikilib = TikiLib::lib('tiki');

		if (empty($auser)) {
			$auser = $user;
		}

		// Feature check + Anonymous don't have their own Users File Gallery
		if ( empty($auser) || $prefs['feature_use_fgal_for_user_files'] == 'n' || $prefs['feature_userfiles'] == 'n' || ( $userId = $tikilib->get_user_id($auser) ) <= 0  ) {
			return false;
		}

		$conditions = array(
			'type' => 'user',
			'name' => $userId,
			'user' => $auser,
			'parentId' => $prefs['fgal_root_user_id']
		);

		if ( $idGallery = $this->table('tiki_file_galleries')->fetchOne('galleryId', $conditions) ) {
			// upgrades from very old tikis may have multiple user filegals per user, so merge them into one here
			unset($conditions['name']);
			$conditions['galleryId'] = $this->table('tiki_file_galleries')->not($idGallery);
			$rows = $this->table('tiki_file_galleries')->fetchAll(array('galleryId'), $conditions);
			foreach ($rows as $row) {
				$this->table('tiki_files')->updateMultiple(
					array('galleryId' => $idGallery), 			// set gallery to the proper one (name eq userId)
					array('galleryId' => $row['galleryId']) 	// where gallery is this one
				);
				$this->remove_file_gallery($row['galleryId']);
			}

			return $idGallery;
		}

		$fgal_info = $conditions;
		$fgal_info['public'] = 'n';
		$fgal_info['visible'] = $prefs['userfiles_private'] === 'y' || $prefs['userfiles_hidden'] === 'y' ? 'n' : 'y';
		$fgal_info['quota'] = $prefs['userfiles_quota'];

		// Create the user gallery if it does not exist yet
		$idGallery = $this->replace_file_gallery($fgal_info);

		return $idGallery;
	}

	/**
	 * Calculate gallery name for user galleries
	 *
	 * @param array $gal_info	gallery info array
	 * @param string $auser		optional user (global used if not supplied)
	 * @return string			name of gallery modified if a "top level" user galley
	 */
	public function get_user_gallery_name($gal_info, $auser = null)
	{
		global $user, $prefs;

		if ($auser === null) {
			$auser = $user;
		}
		$name = $gal_info['name'];

		if ( !empty($auser) && $prefs['feature_use_fgal_for_user_files'] == 'y' ) {

			if ($gal_info['type'] === 'user' && $gal_info['parentId'] == $prefs['fgal_root_user_id']) {
				if ($gal_info['user'] === $auser) {
					$name = tra('My Files');
				} else {
					$name = tr('Files of %0', TikiLib::lib('user')->clean_user($gal_info['user']));
				}
			}
		}
		return $name;
	}

	/**
	 * Checks if a galleryId is the user filegal root and converts it to the correct user gallery for that user
	 * Otherwise just passes through
	 *
	 * @param $galleryId	gallery id to check and change if necessary
	 * @return int			user's gallery id if applicable
	 */

	function check_user_file_gallery($galleryId)
	{
		global $prefs;

		if ($prefs['feature_use_fgal_for_user_files'] === 'y' && $galleryId == $prefs['fgal_root_user_id']) {
			$galleryId = $this->get_user_file_gallery();
		}

		return (int) $galleryId;
	}

	function remove_file($fileInfo, $galInfo='', $disable_notifications = false)
	{
		global $prefs, $user;

		if ( empty( $fileInfo['fileId'] ) ) {
			return false;
		}
		$fileId = $fileInfo['fileId'];

		if ($prefs['vimeo_upload'] === 'y' && $prefs['vimeo_delete'] === 'y' && $fileInfo['filetype'] === 'video/vimeo') {
			$attributes = TikiLib::lib('attribute')->get_attributes('file', $fileId);
			if($url = $attributes['tiki.content.url']) {
				$video_id = substr($url, strrpos($url, '/') + 1);	// not ideal, but video_id not stored elsewhere (yet)
				$result = TikiLib::lib('vimeo')->deleteVideo($video_id);
				if ($result['stat'] != 'ok') {
					$errMsg = tra('Vimeo error:') . ' ' . tra($result['err']['msg']) .
						'<br>' . tra($result['err']['expl']) .
						'<br>' . tr('File "%0" removed (id %1) Remote link was: "%2"', $fileInfo['name'], $fileInfo['fileId'], $url);
					TikiLib::lib('errorreport')->report($errMsg);
					// just report the error and continue to delete the tiki file for now
				}
			}

		}

		$definition = $this->getGalleryDefinition($fileInfo['galleryId']);

		$this->deleteBacklinks(null, $fileId);
		$definition->delete($fileInfo['data'], $fileInfo['path']);

		$archives = $this->get_archives($fileId);
		foreach ($archives['data'] as $archive) {
			$definition->delete($archive['data'], $archive['path']);
			$this->remove_object('file', $archive['fileId']);
		}

		$files = $this->table('tiki_files');
		$files->delete(array('fileId' => $fileId));
		$files->deleteMultiple(array('archiveId' => $fileId));

		$this->remove_draft($fileId);
		$this->remove_object('file', $fileId);

		//Watches
		if ( ! $disable_notifications ) {
			$this->notify($fileInfo['galleryId'], $fileInfo['name'], $fileInfo['filename'], '', 'remove file', $user);
		}

		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action('Removed', $fileId . '/' . $fileInfo['filename'], 'file', '');
		}

		TikiLib::events()->trigger(
			'tiki.file.delete',
			array(
				'type' => 'file',
				'object' => $fileId,
				'galleryId' => $fileInfo['galleryId'],
				'filetype' => $fileInfo['filetype'],
				'user' => $GLOBALS['user'],
			)
		);

		return true;
	}

	function insert_file($galleryId, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='',
						 $author=null, $created='', $lockedby=NULL, $deleteAfter=NULL, $id=0, $metadata = null,$image_x=NULL,$image_y=NULL)
	{
		global $prefs, $user;

		if (! $this->is_filename_valid($filename)) {
			TikiLib::lib('errorreport')->report(tr('`%0` does not match acceptable naming patterns.', $filename));
			return false;
		}

		$gal_info = $this->get_file_gallery_info((int)$galleryId);
		if (0 === strpos($type, 'image/')) {
			$this->transformImage($path, $data, $size, $gal_info, $type, $metadata,$image_x,$image_y);
		}

		$smarty = TikiLib::lib('smarty');
		$filesTable = $this->table('tiki_files');
		$galleriesTable = $this->table('tiki_file_galleries');

		if ($name === $filename) {
			$name = $this->getTitleFromFilename($name);
		}
		$name = trim(strip_tags($name));
		$description = strip_tags($description);
		$filename = $this->truncate_filename($filename);

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
			$search_data = $this->get_search_text_for_data($data, $path, $type, $galleryId);
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
			'metadata' => $metadata,
			'lastModif' => $this->now,
			'lastModifUser' => $user,
			'comment' => $comment,
			'author' => $author,
			'lockedby' => $lockedby,
			'deleteAfter' => $deleteAfter,
		);
		$fileData['filetype'] = $this->fixMime($fileData);
		if (empty($id)) {
			$fileId = $filesTable->insert($fileData);
			$final_event = 'tiki.file.create';
		} else {
			$filesTable->update($fileData, array('fileId' => $id));
			$fileId = $id;
			$final_event = 'tiki.file.update';
		}

		$galleriesTable->update(array('lastModif' => $this->now), array('galleryId' => $galleryId));

		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action('Uploaded', $galleryId, 'file gallery', "fileId=$fileId&amp;add=$size");
		}

		if (isset($final_event) && $final_event) {
			TikiLib::events()->trigger(
				$final_event,
				array(
					'type' => 'file',
					'object' => $fileId,
					'user' => $GLOBALS['user'],
					'galleryId' => $galleryId,
					'filetype' => $type,
				)
			);
		}

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
	function insert_draft($fileId,$filename,$size,$type,$data,$creator,$path,$checksum,$lockedby, $metadata = null)
	{
		global $prefs;
		$filesTable = $this->table('tiki_files');
		$fileDraftsTable = $this->table('tiki_file_drafts');

		if ($prefs['feature_file_galleries_save_draft'] == 'y') {
			if ($prefs['fgal_use_db'] == 'y') {
				$oldData = $filesTable->fetchOne('data', array('fileId' => (int) $fileId));
			} else {
				$oldData = $filesTable->fetchOne('path', array('fileId' => (int) $fileId));
			}

			if (empty($oldData)) {
				return $filesTable->update(
					array(
						'name' => $filename,
						'filename' => $filename,
						'filesize' => $size,
						'filetype' => $type,
						'data' => $data,
						'user' => $creator,
						'path' => $path,
						'hash' => $checksum,
						'metadata' => $metadata,
						'lastModif' => $this->now,
						'lockedby' => $lockedby,
					),
					array('fileId' => $fileId)
				);
			} else {
				$fileDraftsTable->delete(array('fileId' => (int) $fileId, 'user' => $creator));

				$fileDraftsTable->insert(
					array(
						'fileId' => $fileId,
						'filename' => $filename,
						'filesize' => $size,
						'filetype' => $type,
						'data' => $data,
						'user' => $creator,
						'path' => $path,
						'hash' => $checksum,
						'metadata' => $metadata,
						'lastModif' => $this->now,
						'lockedby' => $lockedby,
					)
				);
				if ($prefs['fgal_use_db'] == 'y') {
					$newData = $fileDraftsTable->fetchOne('data', array('fileId' => (int) $fileId));
				} else {
					$newData = $fileDraftsTable->fetchOne('path', array('fileId' => (int) $fileId));
				}
				if (empty($newData)) {
					return false;
				}
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
	function remove_draft($fileId,$user=null)
	{
		$fileDraftsTable = $this->table('tiki_file_drafts');

		if (isset($user)) {
			return $fileDraftsTable->delete(array('fileId' => (int) $fileId, 'user' => $user));
		} else {
			return $fileDraftsTable->deleteMultiple(array('fileId' => (int) $fileId));
		}
	}

	/**
	 * Validate draft and replace real file
	 *
	 * @global string $user
	 * @param int $fileId
	 */
	function validate_draft($fileId)
	{
		global $prefs, $user;

		$fileDraftsTable = $this->table('tiki_file_drafts');
		$galleriesTable = $this->table('tiki_file_galleries');
		$filesTable = $this->table('tiki_files');

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
			//if no archives allowed by user, then replace certain original file information with
			//information from the validated draft
			if ($archives == -1) {
				$filesTable->update(
					array(
						'filename' => $draft['filename'],
						'filesize' => $draft['filesize'],
						'filetype' => $draft['filetype'],
						'data' => $draft['data'],
						'user' => $draft['user'],
						'path' => $draft['path'],
						'hash' => $draft['hash'],
						'metadata' => $draft['metadata'],
						'lastModif' => $draft['lastModif'],
						'lastModifUser' => $draft['user'],
						'lockedby' => $draft['lockedby'],
					),
					array('fileId' => (int) $fileId)
				);

				TikiLib::events()->trigger(
					'tiki.file.update',
					array(
						'type' => 'file',
						'object' => $fileId,
						'galleryId' => $old_file['galleryId'],
						'initialFileId' => $fileId,
						'filetype' => $draft['filetype'],
					)
				);
			//if archives are allowed, the validated draft becomes an archive copy with some db info
			//from the original file carried over
			} else {
				$this->save_archive(
					$fileId,
					$old_file['galleryId'],
					$archives,
					$old_file['name'],
					$old_file['description'],
					$draft['filename'],
					$draft['data'],
					$draft['filesize'],
					$draft['filetype'],
					$draft['user'],
					$draft['path'],
					$old_file['comment'],
					$old_file['author'],
					$old_file['created'],
					$draft['lockedby'],
					$draft['metadata']
				);
			}

			$this->remove_draft($fileId, $user);
		}
	}

	function save_archive($id, $galleryId, $count_archives, $name, $description, $filename, $data, $size, $type,
						  $creator, $path, $comment = '', $author = null, $created = '', $lockedby = null, $metadata = null)
	{
		global $prefs;

		$filesTable = $this->table('tiki_files');
		$initialFileId = $id;

		// fgal_keep_fileId == n means that the archive will keep the same fileId and the latest version will have a new fileId
		// fgal_keep_fileId = y the new version will keep the current fileId, the archive will have a new fileId
		if ($prefs['fgal_keep_fileId'] == 'y') {
			// create archive by inserting the old file with a new fileId and archivId field set to original fileId
			$res = $filesTable->fetchFullRow(array('fileId' => $id));
			if ($res) {
				$res['archiveId'] = $id;
				$res['user'] = $creator;
				$res['lockedby'] = NULL;
				unset($res['fileId']);

				$oldFileId = $filesTable->insert($res);
				$this->updateReference($id, $oldFileId);
			}
		}

		// Insert or update and index (for search) the new file
		//for validated drafts, this will include the new information from the draft file
		$idNew = $this->insert_file(
			$galleryId,
			$name,
			$description,
			$filename,
			$data,
			$size,
			$type,
			$creator,
			$path,
			$comment,
			$author,
			$created,
			$lockedby,
			null,
			$prefs['fgal_keep_fileId']=='y'?$id:false,
			$metadata
		);

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

				$filesTable->deleteMultiple(array('fileId' => $filesTable->in($toRemove)));
			}
		}
		if ($prefs['fgal_keep_fileId'] != 'y') {
			$filesTable->updateMultiple(
				array('archiveId' => $idNew,'search_data' => '','user' => $creator,'lockedby' => null),
				array('anyOf' => $filesTable->expr('(`archiveId` = ? OR `fileId` = ?)', array($id, $id)))
			);
		}

		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$categlib->uncategorize_object('file', $id);
		}

		TikiLib::events()->trigger(
			'tiki.file.update',
			array(
				'type' => 'file',
				'object' => $idNew,
				'galleryId' => $galleryId,
				'initialFileId' => $initialFileId,
				'filetype' => $type
			)
		);



		return $idNew;
	}

	function set_file_gallery($file, $gallery)
	{
		$files = $this->table('tiki_files');
		$files->updateMultiple(
			array('galleryId' => $gallery),
			array('anyOf' => $files->expr('(`fileId` = ? OR `archiveId` = ?)', array($file, $file)))
		);

		require_once('lib/search/refresh-functions.php');
		refresh_index('files', $file);

		return true;
	}

	function remove_file_gallery($id, $galleryId=0, $recurse = true)
	{
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

		TikiLib::events()->trigger('tiki.filegallery.delete', [
			'type' => 'file gallery',
			'object' => $galleryId,
			'user' => $GLOBALS['user'],
		]);

		// If $recurse, also recursively remove children galleries
		if ( $recurse ) {
			$galleries = $fileGalleries->fetchColumn(
				'galleryId',
				array('parentId' => $id, 'galleryId' => $fileGalleries->greaterThan(0))
			);

			foreach ($galleries as $galleryId) {
				$this->remove_file_gallery($galleryId, $id, true);
			}
		}

		return true;
	}

	function get_file_gallery_info($id)
	{
		return $this->table('tiki_file_galleries')->fetchFullRow(array('galleryId' => (int) $id));
	}

	function move_file_gallery($galleryId, $new_parent_id)
	{
		if ( (int)$galleryId <= 0 || (int)$new_parent_id == 0 || $galleryId == $new_parent_id ) return false;

		$cachelib = TikiLib::lib('cache');
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		return $this->table('tiki_file_galleries')->updateMultiple(
			array('parentId' => (int) $new_parent_id),
			array('galleryId' => (int) $galleryId)
		);
	}

	function default_file_gallery()
	{
		global $prefs;
		return array(
			'name' => '',
			'description' =>'',
			'visible' => 'y',
			'public' => 'n',
			'type' => 'default',
			'parentId' => $prefs['fgal_root_id'],
			'lockable' => 'n',
			'archives' => 0,
			'quota' => $prefs['fgal_quota_default'],
			'image_max_size_x' => $prefs['fgal_image_max_size_x'],
			'image_max_size_y' => $prefs['fgal_image_max_size_y'],
			'backlinkPerms' => 'n',
			'show_backlinks' => 'n',
			'show_deleteAfter' => $prefs['fgal_list_deleteAfter'],
			'show_lastDownload' => 'n',
			'sort_mode' => $prefs['fgal_sort_mode'],
			'maxRows' => intval($prefs['maxRowsGalleries']),
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
			'show_checked' => !empty($prefs['fgal_checked']) ? $prefs['fgal_checked'] : 'y' ,
			'show_share' => $prefs['fgal_list_share'],
			'show_explorer' => $prefs['fgal_show_explorer'],
			'show_path' => $prefs['fgal_show_path'],
			'show_slideshow' => $prefs['fgal_show_slideshow'],
			'show_source' => 'o',
			'wiki_syntax' => '',
			'default_view' => $prefs['fgal_default_view'],
			'template' => null,
			'icon_fileId' => !empty($prefs['fgal_icon_fileId']) ? $prefs['fgal_icon_fileId'] : null,
		);
	}
	function replace_file_gallery($fgal_info)
	{

		global $prefs;
		$galleriesTable = $this->table('tiki_file_galleries');
		$objectsTable = $this->table('tiki_objects');
		$fgal_info = array_merge($this->default_file_gallery(), $fgal_info);

		// ensure gallery name is userId for root user gallery
		if ($prefs['feature_use_fgal_for_user_files'] === 'y' &&
				$fgal_info['type'] === 'user' &&
				$fgal_info['parentId'] == $prefs['fgal_root_user_id']) {

			$userId = TikiLib::lib('user')->get_user_id($fgal_info['user']);

			if ($userId) {
				$fgal_info['name'] = $userId;
			}
		}

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

			$galleriesTable->update($fgal_info, array('galleryId' => $galleryId));

			$objectsTable->update(
				array('name' => $fgal_info['name'],	'description' => $fgal_info['description']),
				array('type' => 'file gallery', 'itemId' => $galleryId)
			);
			$finalEvent = 'tiki.filegallery.update';
		} else {
			unset($fgal_info['galleryId']);
			$fgal_info['created'] = $this->now;
			$fgal_info['lastModif'] = $this->now;

			$galleryId = $galleriesTable->insert($fgal_info);

			$finalEvent = 'tiki.filegallery.create';
		}

		$cachelib = TikiLib::lib('cache');
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		TikiLib::events()->trigger($finalEvent, [
			'type' => 'file gallery',
			'object' => $galleryId,
			'user' => $GLOBALS['user'],
		]);

		// event_handler($action,$object_type,$object_id,$options);
		return $galleryId;
	}

	function get_all_galleries_cache_name($user)
	{
		$tikilib = TikiLib::lib('tiki');
		$categlib = TikiLib::lib('categ');

		$gs = $tikilib->get_user_groups($user);
		$tmp = "";
		if ( is_array($gs) ) {
			$tmp .= implode("\n", $gs);
		}
		$tmp .= '----';
		if ( $jail = $categlib->get_jail() ) {
			$tmp .= implode("\n", $jail);
		}
		return md5($tmp);
	}

	function get_all_galleries_cache_type()
	{
		return 'fgals_';
	}

	function process_batch_file_upload($galleryId, $file, $user, $description, &$errors)
	{
		include_once ('vendor_extra/pclzip/pclzip.lib.php');
		$extract_dir = 'temp/'.basename($file).'/';
		mkdir($extract_dir);
		$archive = new PclZip($file);
		$archive->extract(PCLZIP_OPT_PATH, $extract_dir, PCLZIP_OPT_REMOVE_ALL_PATH);
		unlink($file);
		$h = opendir($extract_dir);
		$gal_info = $this->get_file_gallery_info($galleryId);
		$savedir = $this->get_gallery_save_dir($galleryId, $gal_info);

		// check filters
		$upl = 1;
		$errors = array();
		while (($file = readdir($h)) !== false) {
			if ($file != '.' && $file != '..' && is_file($extract_dir.'/'.$file)) {

				if (! $this->is_filename_valid($file)) {
					$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $file;
					$upl = 0;
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
		rewinddir($h);
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
				$type = TikiLib::lib('mime')->from_path($file, $extract_dir.$file);
				$fileId = $this->insert_file($galleryId, $name, $description, $name, $data, $size, $type, $user, $fhash);
				unlink($extract_dir.$file);
			}
		}

		closedir($h);
		rmdir($extract_dir);
		return true;
	}

	function get_file_info($fileId, $include_search_data = true, $include_data = true, $use_draft = false)
	{
		global $prefs, $user;

		$return = $this->get_files_info(null, (int)$fileId, $include_search_data, $include_data, 1);

		if (!$return) {
			return false;
		}

		$file = $return[0];

		if ($use_draft && $prefs['feature_file_galleries_save_draft'] == 'y') {
			$draft = $this->table('tiki_file_drafts')->fetchRow(
				array('filename', 'filesize', 'filetype', 'data', 'user', 'path', 'hash', 'lastModif', 'lockedby'),
				array('fileId' => (int) $fileId,	'user' => $user)
			);

			if ($draft) {
				$file = array_merge($file, $draft);
			}
		}

		return $file;
	}

	function get_file_label($fileId)
	{
		$info = $this->get_file_info($fileId, false, false, false);

		$arr = array_filter(array($info['name'], $info['filename']));

		return reset($arr);
	}

	function get_files_info_from_gallery_id($galleryId, $include_search_data = false, $include_data = false)
	{
		return $this->get_files_info((int)$galleryId, null, $include_search_data, $include_data);
	}

	function get_files_info($galleryIds = null, $fileIds = null, $include_search_data = false, $include_data = false, $numrows = -1)
	{
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

		return $files->fetchAll($fields, $conditions, $numrows);
	}

	function update_file($id, $name, $description, $user, $comment = NULL)
	{

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

		$files = $this->table('tiki_files');

		$result = $files->update($updateData, array('fileId' => $id));

		$galleryId = $files->fetchOne('galleryId', array('fileId' => $id));

		if ( $galleryId >= 0 ) {
			$this->table('tiki_file_galleries')->update(array('lastModif' => $this->now), array('galleryId' => $galleryId));
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('files', $id);

		return $result;
	}

	function duplicate_file($id, $galleryId = null, $newName = false)
	{
		global $user;

		$file = $this->get_file($id);
		if (!$galleryId) {
			$galleryId = $file['galleryId'];
		}

		$path = $file['path'];
		$savedir = $this->get_gallery_save_dir($galleryId);

		if ($savedir !== false) {
			$fhash = $this->find_unique_name($savedir, $path);
			if (copy($savedir . $file['path'], $savedir . $fhash)) {
				$path = $fhash;
			} else {
				$path = '';		// something wrong?
				if (empty($file['data'])) {
					TikiLib::lib('errorreport')->report(tr('Error duplicating file %0', $id));
				}
			}
		}

		$id = $this->insert_file(
			$galleryId,
			($newName ? $newName : $file['name'] . tra(' copy')),
			$file['description'],
			$file['filename'],
			$file['data'],
			$file['filesize'],
			$file['filetype'],
			$user,
			$path,
			$file['comment'],
			$file['author'],
			'', // created now
			$file['lockedby'],
			$file['deleteAfter'],
			0, // id
			$file['metadata']
		);

		$attributes = TikiLib::lib('attribute')->get_attributes('file', $file['fileId']);
		if ($url = $attributes['tiki.content.url']) {
			$this->attach_file_source($id, $url, $file, true);
		}

		return $id;
	}

	function replace_file(
		$id, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='',
		$gal_info, $didFileReplace, $author='', $created='', $lockedby = null, $deleteAfter = null,
		$metadata = null)
	{
		global $prefs, $user;

		if (! $this->is_filename_valid($filename)) {
			return false;
		}
		$filename = $this->truncate_filename($filename);

		if (0 === strpos($type, 'image/')) {
			$this->transformImage($path, $data, $size, $gal_info, $type, $metadata);
		}

		$filesTable = $this->table('tiki_files');
		$fileDraftsTable = $this->table('tiki_file_drafts');
		$galleriesTable = $this->table('tiki_file_galleries');

		$initialFileId = $id;

		// Update the fields in the database
		$name = trim(strip_tags($name));
		$description = strip_tags($description);

		// User avatar full images are always using db and not file location (at the curent state of feature)
		if (
			isset($prefs['user_store_file_gallery_picture']) && $prefs['user_store_file_gallery_picture'] == 'y'
			&& $prefs["user_picture_gallery_id"] == $gal_info['galleryId']
		) {
			$userPictureGallery = true;
		} else {
			$userPictureGallery = false;
		}

		$checksum = $this->get_file_checksum($gal_info['galleryId'], $path, $data);

		$search_data = '';
		if ($prefs['fgal_enable_auto_indexing'] != 'n') {
			$search_data = $this->get_search_text_for_data($data, $path, $type, $gal_info['galleryId']);
			if ($search_data === false)
				return false;
		}

		if ($prefs['feature_file_galleries_save_draft'] == 'y') {
			$oldPath = $fileDraftsTable->fetchOne('path', array('fileId' => $id, 'user' => $user));
		} else {
			$oldPath = $filesTable->fetchOne('path', array('fileId' => $id));
		}

		if ( $gal_info['archives'] == -1 || ! $didFileReplace ) { // no archive
			if ($prefs['feature_file_galleries_save_draft'] == 'y') {
				$result = $filesTable->update(
					array(
						'name' => $name,
						'description' => $description,
						'metadata' => $metadata,
						'lastModifUser' => $user,
						'lastModif' => $this->now,
						'author' => $author,
						'user' => $creator,
					),
					array('fileId' => $id)
				);

				if ( ! $result ) {
					return false;
				}

				if ($didFileReplace) {
					if (
						!$this->insert_draft($id, $filename, $size, $type, $data, $user, $path, $checksum, $lockedby, $metadata)
					) {
						return false;
					}
				}

			} else {
				$result = $filesTable->update(
					array(
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
						'metadata' => $metadata,
						'author' => $author,
						'user' => $creator,
						'lockedby' => $lockedby,
						'deleteAfter' => $deleteAfter,
					),
					array('fileId' => $id)
				);

				if ( ! $result ) {
					return false;
				}
			}

			if ( $didFileReplace && !empty($oldPath) ) {
				$definition = $this->getGalleryDefinition($gal_info['galleryId']);
				$definition->delete(null, $oldPath);
			}

			TikiLib::events()->trigger(
				'tiki.file.update',
				array(
					'type' => 'file',
					'object' => $id,
					'galleryId' => $gal_info['galleryId'],
					'initialFileId' => $initialFileId,
					'filetype' => $type,
					'user' => $GLOBALS['user'],
				)
			);

		} else { //archive the old file : change archive_id, take away from indexation and categorization
			if ($prefs['feature_file_galleries_save_draft'] == 'y') {
				$this->insert_draft($id, $filename, $size, $type, $data, $user, $path, $checksum, $lockedby, $metadata);
			} else {
				$id = $this->save_archive(
					$id,
					$gal_info['galleryId'],
					$gal_info['archives'],
					$name,
					$description,
					$filename,
					$data,
					$size,
					$type,
					$creator,
					$path,
					$comment,
					$author,
					$created,
					$lockedby,
					$metadata
				);
			}
		}

		if ($gal_info['galleryId']) {
			$galleriesTable->update(array('lastModif' => $this->now), array('galleryId' => $gal_info['galleryId']));
		}

		return $id;
	}

	function updateReference($oldFileId, $newFileId)
	{
		global $prefs;
		$attributelib = TikiLib::lib('attribute');

		if ($prefs['fgal_keep_fileId'] == 'y') {
			$attributes = $attributelib->get_attributes('file', $oldFileId);
			$attributelib->set_attribute('file', $oldFileId, 'tiki.content.url', '');

			if (isset($attributes['tiki.content.url'])) {
				//we don't delete or update the attribute, so that it remains working if the user changes the fgal_keep_fileId
				$attributelib->set_attribute('file', $newFileId, 'tiki.content.url', $attributes['tiki.content.url']);
			}
		}
	}

	function change_file_handler($mime_type,$cmd)
	{
		$handlers = $this->table('tiki_file_handlers');

		$mime_type = trim($mime_type);

		$handlers->delete(array('mime_type' => $mime_type));
		$handlers->insert(array('mime_type' => $mime_type, 'cmd' => $cmd));

		return true;
	}

	function delete_file_handler($mime_type)
	{
		$handlers = $this->table('tiki_file_handlers');
		return (bool) $handlers->delete(array('mime_type' => $mime_type));
	}

	function get_native_handler($type)
	{
		switch ($type) {
		case 'text/plain':
			return function (FileWrapper $wrapper) {
				return $wrapper->getContents();
			};
		case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			return function (FileWrapper $wrapper) {
				$document = ZendSearch\Lucene\Document\Docx::loadDocxFile($wrapper->getReadableFile(), true);
				return $document->getField('body')->getUtf8Value();
			};
		case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			return function (FileWrapper $wrapper) {
				$document = ZendSearch\Lucene\Document\Pptx::loadPptxFile($wrapper->getReadableFile(), true);
				return $document->getField('body')->getUtf8Value();
			};
		case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			return function (FileWrapper $wrapper) {
				$document = ZendSearch\Lucene\Document\Xlsx::loadXlsxFile($wrapper->getReadableFile(), true);
				return $document->getField('body')->getUtf8Value();
			};
		}
	}

	function get_file_handlers($for_execution = false)
	{
		$cachelib = TikiLib::lib('cache');

		if ($for_execution && ! $default = $cachelib->getSerialized('file_handlers')) {
			// n.b. this array is partially duplicated in tiki-check.php for standalone mode checks
			$possibilities = array(
				'application/ms-excel' => array('xls2csv %1'),
				'application/msexcel' => array('xls2csv %1'),
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('xlsx2csv.py %1'),
				'application/ms-powerpoint' => array('catppt %1'),
				'application/mspowerpoint' => array('catppt %1'),
				'application/vnd.openxmlformats-officedocument.presentationml.presentation' => array('pptx2txt.pl %1 -'),
				'application/msword' => array('catdoc %1', 'strings %1'),
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array('docx2txt.pl %1 -'),
				'application/pdf' => array('pstotext %1', 'pdftotext %1 -'),
				'application/postscript' => array('pstotext %1'),
				'application/ps' => array('pstotext %1'),
				'application/rtf' => array('catdoc %1'),
				'application/sgml' => array('col -b %1', 'strings %1'),
				'application/vnd.ms-excel' => array('xls2csv %1'),
				'application/vnd.ms-powerpoint' => array('catppt %1'),
				'application/x-msexcel' => array('xls2csv %1'),
				'application/x-pdf' => array('pstotext %1', 'pdftotext %1 -'),
				'application/x-troff-man' => array('man -l %1'),
				'application/zip' => array('unzip -l %1'),
				'text/enriched' => array('col -b %1', 'strings %1'),
				'text/html' => array('elinks -dump -no-home %1'),
				'text/richtext' => array('col -b %1', 'strings %1'),
				'text/sgml' => array('col -b %1', 'strings %1'),
				'text/tab-separated-values' => array('col -b %1', 'strings %1'),
			);

			$default = array();
			$executables = array();
			foreach ($possibilities as $type => $options) {
				foreach ($options as $opt) {
					$optArray = explode(' ', $opt, 2);
					$exec = reset($optArray);

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
		$database = $handlers->fetchMap('mime_type', 'cmd', array());

		return array_merge($default, $database);
	}

	function reindex_all_files_for_search_text()
	{
		@ini_set('memory_limit', -1);
		$files = $this->table('tiki_files');

		for ($offset = 0, $maxRecords = 10; ; $offset += $maxRecords) {
			$rows = $files->fetchAll(array('fileId', 'filename', 'filesize', 'filetype', 'data', 'path', 'galleryId'), array('archiveId' => 0), $maxRecords, $offset);
			if (empty($rows)) {
				break;
			}

			foreach ($rows as $row) {
				$search_text = $this->get_search_text_for_data($row['data'], $row['path'], $row['filetype'], $row['galleryId']);
				if ($search_text!==false) {
					$files->update(array('search_data' => $search_text), array('fileId' => $row['fileId']));
				}
			}
		}
		include_once("lib/search/refresh-functions.php");
		refresh_index('files');
	}

	function get_parse_app($type, $skipDefault = true)
	{
		static $fileParseApps;

		$partial = $type;

		if (false !== $p = strpos($partial, ';')) {
			$partial = substr($partial, 0, $p);
		}

		if ($handler = $this->get_native_handler($type)) {
			return $handler;
		}

		if ($handler = $this->get_native_handler($partial)) {
			return $handler;
		}

		if (! $fileParseApps) {
			$fileParseApps = $this->get_file_handlers(true);
		}

		if (isset($fileParseApps[$type])) {
			return $this->shellExecuteCallback($fileParseApps[$type]);
		} elseif (isset($fileParseApps[$partial])) {
			return $this->shellExecuteCallback($fileParseApps[$partial]);
		} elseif (! $skipDefault && isset($fileParseApps['default'])) {
			return $this->shellExecuteCallback($fileParseApps['default']);
		}
	}

	private function shellExecuteCallback($command)
	{
		if (! $command) {
			return function () {
				return '';
			};
		}

		return function (FileWrapper $wrapper) use ($command) {
			$tmpfname = $wrapper->getReadableFile();

			$cmd = str_replace('%1', escapeshellarg($tmpfname), $command);
			$handle = popen($cmd, "r");

			if ($handle !== false) {
				$contents = stream_get_contents($handle);
				fclose($handle);

				return $contents;
			}

			return false;
		};
	}

	private function get_search_text_for_data($data, $path, $type, $galleryId)
	{
		$definition = $this->getGalleryDefinition($galleryId);

		if (!isset($data) && !isset($path)) {
			return false;
		}

		$parseApp = $this->get_parse_app($type);

		if (empty($parseApp))
			return '';

		$wrapper = $definition->getFileWrapper($data, $path);
		try {
			$content = $parseApp($wrapper);
		} catch (Exception $e) {
			TikiLib::lib('errorreport')->report(tr('Processing search text from a "%0" file in gallery #%1', $type, $galleryId) .
				'<br>' . $e->getMessage());
			$content = '';
		}
		return $content;
	}

	function fix_vnd_ms_files() {
		$files = $this->table('tiki_files');
		$files->update(
			array('filetype' => $files->expr("REPLACE(`filetype`, 'application/vnd.ms-', 'application/ms')")),
			array('filetype' => $files->like('application/vnd.ms-%'))
		);
	}

	private function getGalleryDefinition($galleryId)
	{
		static $loaded = [];

		if (! isset($loaded[$galleryId])) {
			$info = $this->get_file_gallery_info($galleryId);
			$loaded[$galleryId] = new GalleryDefinition($info);
		}

		return $loaded[$galleryId];
	}

	function notify ($galleryId, $name, $filename, $description, $action, $user, $fileId=false)
	{
		global $prefs;
		if ($prefs['feature_user_watches'] == 'y') {
                        //  Deal with mail notifications.
			include_once('lib/notifications/notificationemaillib.php');
			$galleryName = $this->table('tiki_file_galleries')->fetchOne('name', array('galleryId' => $galleryId));

			sendFileGalleryEmailNotification('file_gallery_changed', $galleryId, $galleryName, $name, $filename, $description, $action, $user, $fileId);
		}
	}
	/* lock a file */
	function lock_file($fileId, $user)
	{
		$this->table('tiki_files')->update(array('lockedby' => $user), array('fileId' => $fileId));
	}
	/* unlock a file */
	function unlock_file($fileId)
	{
		$this->lock_file($fileId, null);
	}
	/* get archives of a file */
	function get_archives($fileId, $offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='')
	{
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $fileId, true, false, false, true, false, false, false, false, '', false, true);
	}
	function duplicate_file_gallery($galleryId, $name, $description = '')
	{
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
		$this->table('tiki_files')->update(array('maxhits' => (int) $limit), array('fileId' => (int) $fileId));
	}
	// not the best optimisation as using a library using files and not content
	function zip($fileIds, &$error, $zipName='')
	{
		global $tiki_p_admin_file_galleries, $prefs, $user;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$list = array();
		$temp = 'temp/'.md5($tikilib->now).'/';
		if (!mkdir($temp)) {
			$error = "Can not create directory $temp";
			return false;
		}
		foreach ($fileIds as $fileId) {
			$info = $this->get_file($fileId);
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
		define(PCZLIB_SEPARATOR, '\001');
		include_once ('vendor_extra/pclzip/pclzip.lib.php');
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

	function getGalleriesParentIds()
	{
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
	function _getGalleryChildrenIdsList( $allIds, &$subtree, $parentId )
	{
		foreach ( $allIds as $k => $v ) {
			if ( $v['parentId'] == $parentId ) {
				$galleryId = $v['galleryId'];
				$subtree[] = (int)$galleryId;
				$this->_getGalleryChildrenIdsList($allIds, $subtree, $galleryId);
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
	function _getGalleryChildrenIdsTree( $allIds, &$subtree, $parentId )
	{
		foreach ( $allIds as $v ) {
			if ( $v['parentId'] == $parentId ) {
				$galleryId = $v['galleryId'];
				$subtree[ (int)$galleryId ] = array();
				$this->_getGalleryChildrenIdsTree($allIds, $subtree[$galleryId], $galleryId);
			}
		}
	}
	// Get a tree or a list of a gallery children ids, optionnally under a specific parentId
	// To avoid a query to the database for each node, this function retrieves all gallery ids and recursively build the tree using this info
	function getGalleryChildrenIds( &$subtree, $parentId = -1, $format = 'tree' )
	{
		$allIds = $this->getGalleriesParentIds();

		switch ( $format ) {
			case 'list':
				$this->_getGalleryChildrenIdsList($allIds, $subtree, $parentId);
    			break;
			case 'tree': default:
				$this->_getGalleryChildrenIdsTree($allIds, $subtree, $parentId);
		}
	}

	// Get a tree or a list of ids of the specified gallery and its children
	function getGalleryIds( &$subtree, $parentId = -1, $format = 'tree' )
	{

		switch ( $format ) {
			case 'list':
				$subtree[] = $parentId;
				$childSubtree =& $subtree;
    			break;
			case 'tree': default:
				$subtree[$parentId] = array();
				$childSubtree =& $subtree[$parentId];
		}

		return $this->getGalleryChildrenIds($childSubtree, $parentId, $format);
	}

	/* Get the subgalleries of a gallery, the one identified by $parentId if $wholeSpecialGallery is false, or the special gallery containing the gallery identified by $parentId if $wholeSpecialGallery is true.
	 *
	 * @param int $parentId Identifier of a gallery
	 * @param bool $wholeSpecialGallery If true, will return the subgalleries of the special gallery (User File Galleries, Wiki Attachment Galleries, File Galleries, ...) that contains the $parentId gallery
	 * @param string $permission If set, will limit the list of subgalleries to those having this permission for the current user
	 */
	function getSubGalleries( $parentId = 0, $wholeSpecialGallery = true, $permission = 'view_file_gallery' )
	{

		// Use the special File Galleries root if no other special gallery root id is specified
		if ( $parentId == 0 ) {
			global $prefs;
			$parentId = $prefs['fgal_root_id'];
		}

		// If needed, get the id of the special gallery that contains the $parentId gallery
		if ( $wholeSpecialGallery ) {
			$parentId = $this->getGallerySpecialRoot($parentId);
			$useCache = true;
		}

		global $user;
		$cachelib = TikiLib::lib('cache');

		if ( $useCache ) {
			$cacheName = 'pid' . $parentId . '_' . $this->get_all_galleries_cache_name($user);
			$cacheType = $this->get_all_galleries_cache_type();
		}
		if ( ! $useCache || ! $return = $cachelib->getSerialized($cacheName, $cacheType) ) {
			$return = $this->list_file_galleries(0, -1, 'name_asc', $user, '', $parentId, false, true, false, false, false, true, false);
			if ( is_array($return) ) {
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
	// WARNING: Semi-private function. "Public callers" should only pass the galleryId parameter.
	function getGallerySpecialRoot( $galleryId, $treeParentId = null, &$tree = null /* Pass by reference for performance */)
	{
		global $prefs;

		if ( ( $treeParentId === null xor $tree === null ) || $galleryId <= 0 ) {
			// If parameters are not valid, return false (they should be null at first call and not empty when recursively called)
			return false;
		} elseif ( $treeParentId === null ) {
			// Initialize the full tree and the top root of all galleries
			$tree = array();
			$treeParentId = -1;
			$this->getGalleryChildrenIds($tree, $treeParentId, 'tree');
		} elseif ( $treeParentId == $galleryId ) {
			// If the searched gallery is the same as the current tree parent id, then return tree (we found the right branch of the tree)
			return true;
		}

		if ( ! empty( $tree ) ) {
			foreach ( $tree as $subGalleryId => $childs ) {
				if ( $result = $this->getGallerySpecialRoot($galleryId, $subGalleryId, $childs) ) {
					if ( is_integer($result) ) {
						return $result;
					} elseif ($treeParentId == -1 ) {
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
	function getWikiAttachmentFilegalsIdsTree( $pageName )
	{
		$return = array();
		$this->getGalleryIds($return, $this->get_wiki_attachment_gallery($pageName), 'tree');
		return $return;
	}

	// Get the tree of 'Users File Galleries' filegal of the current user
	function getUserFilegalsIdsTree()
	{
		$return = array();
		$this->getGalleryIds($return, $this->get_user_file_gallery(), 'tree');
		return $return;
	}

	// Get the tree of 'File Galleries' filegal
	function getFilegalsIdsTree()
	{
		global $prefs;
		$return = array();
		$this->getGalleryIds($return, $prefs['fgal_root_id'], 'tree');
		return $return;
	}

	// Return HTML code to display the complete file galleries tree for the special root containing the given gallery.
	// If $galleryIdentifier is not given, default to the "default" / normal / "File Galleries" file galleries.
	function getTreeHTML($galleryIdentifier = NULL)
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		require_once ('lib/tree/BrowseTreeMaker.php');
		$galleryIdentifier = is_null($galleryIdentifier) ? $prefs['fgal_root_id'] : $galleryIdentifier;
		$subGalleries = $this->getSubGalleries($galleryIdentifier);

		$smarty->loadPlugin('smarty_function_icon');
		$icon = '&nbsp;' . smarty_function_icon(array('name' => 'file-archive-open'), $smarty) . '&nbsp;';

		$smarty->loadPlugin('smarty_block_self_link');
		$linkParameters = array('_script' => 'tiki-list_file_gallery.php', '_class' => 'fgalname');
		if (!empty($_REQUEST['filegals_manager'])) {
			$linkParameters['filegals_manager'] = $_REQUEST['filegals_manager'];
		}
		$nodes = array();
		foreach ($subGalleries['data'] as $subGallery) {
			$linkParameters['galleryId'] = $subGallery['id'];
			$nodes[] = array(
				'id' => $subGallery['id'],
				'parent' => $subGallery['parentId'],
				'data' => smarty_block_self_link($linkParameters, $icon . htmlspecialchars($subGallery['name']), $smarty),
			);
		}
		$browseTreeMaker = new BrowseTreeMaker('Galleries');
		return $browseTreeMaker->make_tree($this->getGallerySpecialRoot($galleryIdentifier), $nodes);
	}

	// Return the given gallery's path relative to its special root. The path starts with a constant component, File Galleries for default galleries.
	// It would be File Galleries > Foo for a root default file gallery named "Foo". Other constant components are "User File Galleries" and "Wiki Attachment File Galleries".
	// Returns an array with 2 elements, "Array" and "HTML".
	// Array is a numerically-indexed array with one element per path component. Each value is the name of the component (usually a file gallery name). Keys are file gallery OIDs.
	// HTML is a string of HTML code to display the path.
	function getPath($galleryIdentifier)
	{
		global $prefs, $user;
		$rootIdentifier = $this->getGallerySpecialRoot($galleryIdentifier);
		$root = $this->get_file_gallery_info($galleryIdentifier);
		if ( $user != '' && $prefs['feature_use_fgal_for_user_files'] == 'y' ) {
			$userGallery = $this->get_user_file_gallery();
			if ($userGallery == $prefs['fgal_root_user_id']) {
				$rootIdentifier = $userGallery;
			}
		}
		$path = array();
		for ($node = $this->get_file_gallery_info($galleryIdentifier); $node && $node['galleryId'] != $rootIdentifier; $node = $this->get_file_gallery_info($node['parentId'])) {
			$path[$node['galleryId']] = $node['name'];
		}
		if (isset($userGallery) && $rootIdentifier == $prefs['fgal_root_user_id']) {
			$path[$rootIdentifier] = tra('User File Galleries');
		} elseif ($rootIdentifier == $prefs['fgal_root_wiki_attachments_id']) {
			$path[$rootIdentifier] = tra('Wiki Attachment File Galleries');
		} else {
			$path[$rootIdentifier] = tra('File Galleries');
		}
		$path = array_reverse($path, true);

		$pathHtml = '';
		foreach ( $path as $identifier => $name ) {
			if ( $pathHtml != '' ) $pathHtml .= ' &nbsp;&gt;&nbsp;';
			$pathHtml .= '<a href="tiki-list_file_gallery.php?galleryId=' . $identifier . (!empty($_REQUEST['filegals_manager']) ? '&amp;filegals_manager=' . urlencode($_REQUEST['filegals_manager']) : '') . '">' . htmlspecialchars($name) . '</a>';
		}

		return array(
			'HTML' => $pathHtml,
			'Array' => $path
		);
	}

	// get the size in k used in a fgal and its children
	function getUsedSize($galleryId=0)
	{
		$files = $this->table('tiki_files');

		$conditions = array();
		if (! empty($galleryId)) {
			$galleryIds = array();
			$this->getGalleryIds($galleryIds, $galleryId, 'list');

			$conditions['galleryId'] = $files->in($galleryIds);
		}

		return $files->fetchOne($files->sum('filesize'), $conditions);
	}

	// get the min quota in M of a fgal and its parents
	function getQuota($galleryId=0)
	{
		global $prefs;
		if (empty($galleryId) || $prefs['fgal_quota_per_fgal'] == 'n') {
			return $prefs['fgal_quota'];
		}
		$list = $this->getGalleryParentsColumns($galleryId, array('galleryId', 'quota'));
		$quota = $prefs['fgal_quota'];
		foreach ($list as $fgal) {
			if (empty($fgal['quota'])) {
				continue;
			}
			$quota = min($quota, $fgal['quota']);
		}
		return $quota;
	}

	/**
	 * get the max quota in MB of the children of a fgal,
	 * or total contents size where no quota is set
	 *
	 * @param int $galleryId
	 * @return float
	 */

	function getMaxQuotaDescendants($galleryId=0)
	{
		if (empty($galleryId)) {
			return 0;
		}
		$this->getGalleryChildrenIds($subtree, $galleryId, 'list');
		if (is_array($subtree) && !empty($subtree)) {
			$files = $this->table('tiki_files');
			$gals = $this->table('tiki_file_galleries');
			$size = 0;
			foreach ($subtree as $subGalleryId) {
				$quota = $gals->fetchOne('quota', array('galleryId' => $subGalleryId));
				if ($quota) {
					$size += $quota;
				} else {
					$size += $files->fetchOne($files->sum('filesize'), array('galleryId' => $subGalleryId)) / (1024 * 1024);
				}
			}
			return $size;
		} else {
			return 0.0;
		}
	}
	// check quota is smaller than parent quotas and bigger than children quotas
	// return -1: too small, 0: ok, +1: too big
	function checkQuotaSetting($quota, $galleryId=0, $parentId=0)
	{
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
	function getGalleryParentsColumns($galleryId, $columns)
	{
		$cols = array_diff($columns, array('size', 'galleryId', 'parentId'));
		$cols[] = 'galleryId';
		$cols[] = 'parentId';

		$all = $this->table('tiki_file_galleries')->fetchAll($cols, array());
		$list = array();
		$this->_getGalleryParentsColumns($all, $list, $galleryId, $columns);
		return $list;
	}
	function _getGalleryParentsColumns($all, &$list, $galleryId, $columns=array())
	{
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
	function checkQuota($size, $galleryId, &$error)
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$error = '';
		if (!empty($prefs['fgal_quota'])) {
			$use = $this->getUsedSize();
			if ($use + $size > $prefs['fgal_quota']*1024*1024) {
				$error = tra('The upload was not completed.') . ' ' . tra('Reason: The global quota has been reached');
				$diff = $use + $size - $prefs['fgal_quota']*1024*1024;
			}
		}
		if (empty($error) && $prefs['fgal_quota_per_fgal'] == 'y') {
			$list = $this->getGalleryParentsColumns($galleryId, array('galleryId', 'quota', 'size', 'name'));
			//echo '<pre>';print_r($list);echo '</pre>';
			foreach ($list as $fgal) {
				if (!empty($fgal['quota']) && $fgal['size'] + $size > $fgal['quota']*1024*1024) {
					$error = tra('The upload was not completed.') . ' ' . sprintf(tra('Reason: The quota has been reached in "%s"'), $fgal['name']);
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
				$machine = $tikilib->httpPrefix(true) . dirname($foo["path"]);
				$machine = preg_replace("!/$!", "", $machine); // just incase
				$smarty->assign('mail_machine', $machine);
				$smarty->assign('mail_diff', $diff);
				foreach ($nots as $not) {
					$lg = $tikilib->get_user_preference($not['user'], 'language', $prefs['site_language']);
					$mail->setSubject(tra('File gallery quota exceeded', $lg));
					$mail->setText($smarty->fetchLang($lg, 'mail/fgal_quota_exceeded.tpl'));
					$mail->send(array($not['email']));
				}
			}
			return false;
		}
		return true;
	}
	// update backlinks of an object
	function replaceBacklinks($context, $fileIds=array())
	{
		$objectlib = TikiLib::lib('object');
		$objectId = $objectlib->get_object_id($context['type'], $context['object']);
		if (empty($objectId) && !empty( $fileIds)) {
			$context = array_merge($context, array(
				'description' => null,
				'name' => null,
				'href' => null,
			));
			$objectId = $objectlib->add_object($context['type'], $context['object'], FALSE, $context['description'], $context['name'], $context['href']);
		}
		if (!empty($objectId)) {
			$this->_replaceBacklinks($objectId, $fileIds);
		}
		//echo 'REPLACEBACKLINK'; print_r($context);print_r($fileIds);echo '<pre>'; debug_print_backtrace(); echo '</pre>';die;
	}
	function _replaceBacklinks($objectId, $fileIds=array())
	{
		$backlinks = $this->table('tiki_file_backlinks');
		$this->_deleteBacklinks($objectId);

		foreach ($fileIds as $fileId) {
			$backlinks->insert(array('objectId' => (int) $objectId, 'fileId' => (int) $fileId));
		}
	}
	// delete backlinks associated to an object
	function deleteBacklinks($context, $fileId=null)
	{
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
	function _deleteBacklinks($objectId, $fileId=null)
	{
		$backlinks = $this->table('tiki_file_backlinks');
		if (empty($fileId)) {
			$backlinks->delete(array('objectId' => (int) $objectId));
		} else {
			$backlinks->delete(array('fileId' => (int) $fileId));
		}
	}
	// get the backlinks of an object
	function getFileBacklinks($fileId, $sort_mode='type_asc')
	{
		$query = 'select tob.* from `tiki_file_backlinks` tfb left join `tiki_objects` tob on (tob.`objectId`=tfb.`objectId`) where `fileId`=? order by '.$this->convertSortMode($sort_mode);
		return $this->fetchAll($query, array((int)$fileId));
	}

	/**
	 * "can not see a file if all its backlinks are not viewable"
	 *
	 * Checks if a file is used in various object types and all the uses of it are "private"
	 *
	 * @param int $fileId    numeric id of the file in question
	 *
	 * @return bool          true:  if all the uses of a file are _not_ visible to the current user,
	 *                       false: if any objects using of the file are visible or the file is not used
	 *
	 * @throws Exception
	 */

	function hasOnlyPrivateBacklinks($fileId)
	{
		$objects = $this->getFileBacklinks($fileId);
		if (empty($objects)) {
			return false;
		}
		$pobjects = [];
		foreach ($objects as $object) {
			$pobjects[$object['type']][] = $object;
		}

		$map = ObjectLib::map_object_type_to_permission();
		foreach ($pobjects as $type=>$list) {
			if ($type == 'blog post') {
				$this->parentObjects($list, 'tiki_blog_posts', 'postId', 'blogId');
				$filtered = Perms::filter(array('type'=>'blog'), 'object', $list, array('object' => 'blogId'), str_replace('tiki_p_', '', $map['blog']));
			} elseif (strstr($type, 'comment')) {
				$this->parentObjects($list, 'tiki_comments', 'threadId', 'object');
				$t = str_replace(' comment', '', $type);
				$filtered = Perms::filter(array('type'=>$t), 'object', $list, array('object' => 'object'), str_replace('tiki_p_', '', $map[$t]));
			} elseif ($type == 'forum post') {
				$this->parentObjects($list, 'tiki_comments', 'threadId', 'object');
				$filtered = Perms::filter(array('type'=>'forum'), 'object', $list, array('object' => 'object'), str_replace('tiki_p_', '', $map['forum']));
			} elseif ($type == 'trackeritem') {
				foreach ($list as $object) {
					$item = Tracker_Item::fromId($object['itemId']);
					if ($item->canView()) {
						return false;
					}
				}
			} else {
				$filtered = Perms::filter(array('type'=>$type), 'object', $list, array('object' => 'itemId'), str_replace('tiki_p_', '', $map[$type]));
			}

			if (!empty($filtered)) {	// some objects linkling to this file are visible
				return false;
			}
		}
		return true;
	}
	// sync the backlinks used by a text of an object
	function syncFileBacklinks($data, $context)
	{
		$fileIds = array();
		$parserlib = TikiLib::lib('parser');
		$plugins = $parserlib->getPlugins($data, array('IMG', 'FILE'));
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

	function getLinkFileId($url)
	{
		if (preg_match('/^tiki-download_file.php\?.*fileId=([0-9]+)/', $url, $matches)) {
			return $matches[1];
		}
		if (preg_match('/^(dl|preview|thumbnail|thumb||display)([0-9]+)/', $url, $matches)) {
			return $matches[2];
		}
	}
	private function syncParsedText( $data, $context )
	{
		// Compatbility function
		$this->object_post_save($context, array( 'content' => $data ));
	}
	function refreshBacklinks()
	{
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
	function moveFiles($to='to_fs', &$feedbacks)
	{
		$files = $this->table('tiki_files');

		if ($to == 'to_db') {
			$result = $files->fetchColumn('fileId', array('path' => $files->not('')));
			$msg = tra('Number of files transferred to the database:');
		} else {
			$result = $files->fetchColumn('fileId', array('path' => '', 'filetype' => $files->not('image/svg+xml')));
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
	function moveFile($to='to_fs', $file_id)
	{
		global $prefs;
		$files = $this->table('tiki_files');

		$file_info = $files->fetchFullRow(array('fileId' => $file_id));

		if ($to == 'to_db') {
			if (false === $data = @file_get_contents($prefs['fgal_use_dir'] .$file_info['path'])) {
				return tra('Cannot open this file:') . $prefs['fgal_use_dir'] . $file_info['path'];
			}

			$files->update(array('data' => $data, 'path' => ''), array('fileId' => $file_info['fileId']));
			unlink($prefs['fgal_use_dir'] .$file_info['path']);
		} else {
			$fhash = $this->find_unique_name($prefs['fgal_use_dir'], $file_info['name']);

			if (false === @file_put_contents($prefs['fgal_use_dir'] . $fhash, $file_info['data'])) {
				return tra('Cannot write to this file:') . $prefs['fgal_use_dir'] . $fhash;
			}

			$files->update(array('data' => '', 'path' => $fhash), array('fileId' => $file_info['fileId']));
		}
		return '';
	}
	// find the fileId in the pool of fileId archives files that is closer before the date
	function getArchiveJustBefore($fileId, $date)
	{
		$files = $this->table('tiki_files');

		$archiveId = $files->fetchOne('archiveId', array('fileId' => $fileId));
		if (empty($archiveId)) {
			$archiveId = $fileId;
		}

		return $files->fetchOne(
			'fileId',
			array(
				'anyOf' => $files->expr('(`fileId`=? or `archiveId`=?)', array($archiveId, $archiveId)),
				'created' => $files->lesserThan($date+1)
			),
			1,
			0,
			array('created' => 'DESC')
		);
	}

	function get_objectid_from_virtual_path($path, $parentId = -1)
	{
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
				$result = $files->fetchAll(
					array('fileId'),
					array('filename' => $matches[1], 'galleryId' => (int) $parentId, 'archiveId' => $files->greaterThan(0)),
					1,
					$matches[2], array('fileId' => 'ASC')
				);
			} else {
				$result = $files->fetchOne(
					'fileId',
					array('filename' => $pathParts[1], 'galleryId' => (int) $parentId, 'archiveId' => 0),
					array('fileId' => 'DESC')
				);
			}

			if ( is_array($result) ) {
				$res = reset($result);
				if ( ! empty($res) ) {
					return array('type' => 'file', 'id' => $res['fileId']);
				}
			} elseif ( !empty($result) ) {
					return array('type' => 'file', 'id' => $result);
			}
		}

		$galleryId = $this->table('tiki_file_galleries')->fetchOne('galleryId', array('name' => $pathParts[1], 'parentId' => (int) $parentId));

		if ($galleryId) {
			// as a leaf
			if ( empty($pathParts[2]) ) {
				return array('type' => 'filegal', 'id' => $galleryId);
			} else {
				return $this->get_objectid_from_virtual_path('/' . $pathParts[2], $galleryId);
			}
		}

		return false;
	}

	function get_full_virtual_path($id, $type = 'file')
	{
		if ( ! $id > 0 ) return false;

		switch( $type ) {
			case 'filegal':
				if ( $id == -1 ) {
					return '/';
				}
				$res = $this->table('tiki_file_galleries')->fetchRow(array('name', 'parentId'), array('galleryId' => (int) $id));
    			break;

			case 'file': default:
				$res = $this->table('tiki_files')->fetchRow(array('filename', 'parentId' => 'galleryId'), array('fileId' => (int) $id));
				$res['name'] = $res['filename'];
		}

		if ($res) {
			$parentPath = $this->get_full_virtual_path($res['parentId'], 'filegal');

			return $parentPath . ( $parentPath == '/' ? '' : '/' ) . $res['name'];
		}

		return false;
	}

	function getFiletype($not=array())
	{
		if (empty($not)) {
			$query = 'select distinct(`filetype`) from `tiki_files` order by `filetype` asc';
		} else {
			$query = 'select distinct(`filetype`) from `tiki_files` where `filetype` not in('.implode(',', array_fill(0, count($not), '?')).')order by `filetype` asc';
		}
		$result = $this->query($query, $not);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['filetype'];
		}
		return $ret;
	}
	function setDefault($fgalIds)
	{
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
			'show_checked' => $prefs['fgal_checked'],
			'show_share' => $prefs['fgal_list_share'],
			'show_explorer' => $prefs['fgal_show_explorer'],
			'show_path' => $prefs['fgal_show_path'],
			'show_slideshow' => $prefs['fgal_show_slideshow'],
			'default_view' => $prefs['fgal_default_view'],
			'icon_fileId' => !empty($prefs['fgal_icon_fileId']) ? $prefs['fgal_icon_fileId'] : '',
			'show_source' => $prefs['fgal_list_source'],
		);

		$galleries = $this->table('tiki_file_galleries');
		$galleries->updateMultiple($defaults, array('galleryId' => $galleries->in($fgalIds)));
	}
	function getGalleryId($name, $parentId)
	{
		return $this->table('tiki_file_galleries')->fetchOne('galleryId', array('name' => $name, 'parentId' => $parentId));
	}
	function deleteOldFiles()
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');

		include_once('lib/webmail/tikimaillib.php');
		$query = 'select * from `tiki_files` where `deleteAfter` < ? - `lastModif` and `deleteAfter` is not NULL and `deleteAfter` != \'\' order by galleryId asc';
		$files = $this->fetchAll($query, array($this->now));
		foreach ($files as $fileInfo) {
			$definition = $this->getGalleryDefinition($fileInfo['galleryId']);
			$galInfo = $definition->getInfo();

			if (!empty($prefs['fgal_delete_after_email'])) {
				$wrapper = $definition->getFileWrapper($fileInfo['data'], $fileInfo['path']);

				$fileInfo['data'] = $wrapper->getContent();

				$smarty->assign('fileInfo', $fileInfo);
				$smarty->assign('galInfo', $galInfo);
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

	/**
	 * get the wiki_syntax - use parent's if none
	 *
	 * @param int $galleryId	gallery to get syntax from
	 * @param array $fileinfo	optional file info to process syntax on
	 * @return string			wiki markup
	 */

	function getWikiSyntax($galleryId=0, $fileinfo =null, $params = null)
	{
		if (!$params) {
			$params = $_REQUEST;
		}
		if (isset($params['insertion_syntax']) && $params['insertion_syntax'] == 'file') {	// for use in 'Choose or Upload' toolbar item (tikifile)
			$syntax = '{file type="gallery" fileId="%fileId%" showicon="y"}';
		} else if (isset($params['filegals_manager'])) {		// for use in plugin edit popup
			if ($params['filegals_manager'] === 'fgal_picker_id') {
				$syntax = '%fileId%';		// for use in plugin edit popup
			} else if ($params['filegals_manager'] === 'fgal_picker') {
				$href = 'tiki-download_file.php?fileId=123&amp;display';	// dummy id as sefurl expects a (/d+) pattern
				global $smarty; include_once('tiki-sefurl.php');
				$href = filter_out_sefurl($href);
				$syntax =  str_replace('123', '%fileId%', $href);
			}
		}

		if (empty($syntax)) {
			$syntax = $this->table('tiki_file_galleries')->fetchOne('wiki_syntax', array('galleryId' => $galleryId));

			$list = $this->getGalleryParentsColumns($galleryId, array('wiki_syntax'));
			foreach ($list as $fgal) {
				if (!empty($fgal['wiki_syntax'])) {
					$syntax = $fgal['wiki_syntax'];
					break;
				}
			}
		}
		// and no syntax set, return default
		if (empty($syntax)) {
			$syntax = '{img fileId="%fileId%" thumb="box"}';	// should be a pref
		}

		if ($fileinfo) {	// if fileinfo provided then process it now
			$syntax = $this->process_fgal_syntax($syntax, $fileinfo);
		}

		return $syntax;
	}

	function add_file_hit($id)
	{
		global $prefs, $user;

		$files = $this->table('tiki_files');

		if (StatsLib::is_stats_hit()) {
			// Enforce max download per file
			if ( $prefs['fgal_limit_hits_per_file'] == 'y' ) {
				$limit = $this->get_download_limit($id);
				if ( $limit > 0 ) {
					$count = $files->fetchCount(array('fileId' => $id, 'hits' => $files->lesserThan($limit)));
					if ( ! $count ) {
						return false;
					}
				}
			}

			$files->update(array('hits' => $files->increment(1), 'lastDownload' => $this->now), array('fileId' => (int) $id));
		} else {
			$files->update(array('lastDownload' => $this->now), array('fileId' => (int) $id));
		}

		if ($prefs['feature_score'] == 'y' && $prefs['fgal_prevent_negative_score'] == 'y') {
			$score = TikiLib::lib('score')->get_user_score($user);
			if ($score < 0) {
				return false;
			}
		}

		$owner = $files->fetchOne('user', array('fileId' => (int) $id));

		TikiLib::events()->trigger('tiki.file.download',
			array(
				'type' => 'file',
				'object' => $id,
				'user' => $user,
				'owner' => $owner,
			)
		);

		return true;
	}

	function add_file_gallery_hit($id)
	{
		global $prefs, $user;
		if (StatsLib::is_stats_hit()) {
			$fileGalleries = $this->table('tiki_file_galleries');
			$fileGalleries->update(array('hits' => $fileGalleries->increment(1)), array('galleryId' => (int) $id));
		}
		return true;
	}

	function get_file($id, $randomGalleryId='')
	{
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
	function get_file_draft($id)
	{
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

	function get_file_by_name($galleryId, $name, $column='name')
	{
		$query = "select `fileId`,`path`,`galleryId`,`filename`,`filetype`,`data`,`filesize`,`name`,`description`,
				`created` from `tiki_files` where `galleryId`=? AND `$column`=? ORDER BY created DESC LIMIT 1";
		$result = $this->query($query, array((int) $galleryId, $name));
		$res = $result->fetchRow();
		return $res;
	}

	function list_files($offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='')
	{
		global $prefs;
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $prefs['fgal_root_id'], false, false, true, true, false, false, true, true);
	}

	function list_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user='', $find='', $parentId=-1, $with_archive=false, $with_subgals=true, $with_subgals_size=false, $with_files=false, $with_files_data=false, $with_parent_name=true, $with_files_count=true,$recursive=true)
	{
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
						$wiki_syntax = '')
	{

		global $user, $tiki_p_admin, $tiki_p_admin_file_galleries, $prefs;

		$f_jail_bind = array();
		$g_jail_bind = array();
		$f_where = '';

		if ( ( ! $with_files && ! $with_subgals ) || ( $parent_is_file && $galleryId <= 0 ) ) return array();

		$fileId = -1;
		if ( $parent_is_file ) {
			$fileId = $galleryId;
			$galleryId = -2;
		}

		if ( $recursive ) {
			$idTree = array();
			if (is_array($galleryId)) {
				foreach ($galleryId as $galId) {
					$this->getGalleryIds($idTree, $galId, 'list');
				}
			} else {
				$this->getGalleryIds($idTree, $galleryId, 'list');
			}
			$galleryId =& $idTree;
		}

		$with_subgals_size = ( $with_subgals && $with_subgals_size );
		if ( empty($my_user) ) $my_user = $user;

		$f_table = '`tiki_files` as tf';
		$g_table = '`tiki_file_galleries` as tfg';
		$f_group_by = '';
		$orderby = $this->convertSortMode($sort_mode);
		// order by must handle "1", which is the convertSortMode error return
		if ( $orderby == '1' ) {
			$orderby = '';
		}
		
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
				'tf.`metadata`' => "'' as `metadata`",
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
				'tf.`lastModifUser`' => "'' as `lastModifUser`", /// use 'last_user' instead
				'0 as `icon_fileId`' => '`icon_fileId`'			// icon for galleries in browse mode
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

		if ( !empty($filter['categId']) ) {
			$jail = $filter['categId'];
		} else {
			$jail = $categlib->get_jail();
		}

		$f_jail_join = '';
		$f_jail_where = '';
		$f_jail_bind = array();
		if ( $jail ) {
			$categlib->getSqlJoin($jail, 'file', 'tf.`fileId`', $f_jail_join, $f_jail_where, $f_jail_bind);
		}
		if ($with_parent_name && !$with_subgals) {
			$f2g_corresp['tfgp.`name` as `parentName`'] = '';
			$f_table .= ' LEFT OUTER JOIN `tiki_file_galleries` tfgp ON (tf.`galleryId` = tfgp.`galleryId`)';
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
		if ( !empty($filter['fileType']) ) {
			$f_query .= ' AND (tf.`filetype` = ?)';
			$bindvars[] = $filter['fileType'];
		}
		if ( $with_files && $prefs['feature_file_galleries_save_draft'] == 'y' ) {
			$bindvars[] = $user;
		}
		if (!empty($filter['fileId'])) {
			$f_query .= ' AND tf.`fileId` in ('.implode(',', array_fill(0, count($filter['fileId']), '?')).')';
			$bindvars = array_merge($bindvars, $filter['fileId']);
		}
		$galleryId_str = '';
		if ( is_array($galleryId) ) {
			$galleryId_str = ' in ('.implode(',', array_fill(0, count($galleryId), '?')).')';
			$bindvars = array_merge($bindvars, $galleryId);
		} elseif ( $galleryId >= -1 ) {
			$galleryId_str = '=?';
			$bindvars[] = $galleryId;
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

			$g_jail_join = '';
			$g_jail_where = '';
			$g_jail_bind = array();
			if ( $jail ) {
				$categlib->getSqlJoin($jail, 'file gallery', '`tfg`.`galleryId`', $g_jail_join, $g_jail_where, $g_jail_bind);
			}

			$g_query = 'SELECT '.implode(', ', array_values($f2g_corresp)).' FROM '.$g_table.$g_join.$g_jail_join;
			$g_query .= " WHERE 1=1 ";

			if ( $galleryId_str != '' ) {
				$g_query .= ' AND tfg.`parentId`'.$galleryId_str;
				if ($with_files) { // f_query is not used if !with_files
					if (is_array($galleryId))
						$bindvars = array_merge($bindvars, $galleryId);
					else
						$bindvars[] = $galleryId;
				}
			}

			// If $user is admin then get ALL galleries, if not only user galleries are shown
			// If the user is not admin then select it's own galleries or public galleries
			if ( $tiki_p_admin !== 'y' && $tiki_p_admin_file_galleries !== 'y' && empty($parentId) ) {
				$g_mid = " AND (tfg.`user`=? OR tfg.`visible`='y' OR tfg.`public`='y')";
				$bindvars[] = $my_user;
			}
			$g_query .= $g_mid;

			$g_query .= $g_jail_where;
			$bindvars = array_merge($bindvars, $g_jail_bind);

			if ( $with_parent_name ) {
				$select .= ', tfgp.`name` as `parentName`';
				$join .= ' LEFT OUTER JOIN `tiki_file_galleries` tfgp ON (tab.`parentId` = tfgp.`galleryId`)';
			}

			if ( $with_files ) {
				$query = "SELECT $select FROM (($f_query $f_group_by) UNION ($g_query $g_group_by)) as tab".$join;
				$bindvars = array_merge($f_jail_bind, $bindvars);
			} else {
				$query = "SELECT $select FROM ($g_query $g_group_by) as tab".$join;
			}
			if ( $mid != '' ) {
				$query .= ' WHERE'.$mid;
				$bindvars = array_merge($bindvars, $midvars);
			}
			//ORDER BY RAND() can be slow on large databases
			if ($orderby != 'RAND()' && $orderby != '' && $orderby != '1') {
				$orderby = 'tab.'.$orderby;
			}

		} else {
			$query = $f_query;
			$bindvars = array_merge($f_jail_bind, $bindvars);
			if ( $mid != '' ) {
				$query .= ' AND'.$mid;
				$bindvars = array_merge($bindvars, $midvars);
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
		if ( !is_array($galleryId) ) {
			$cacheType = 'fgals_perms_'.$galleryId."_";
			if ($galleryId > 0 && $cachelib->isCached($cacheName, $cacheType)) {
				$fgal_perms = unserialize($cachelib->getCached($cacheName, $cacheType));
			} else {
				$fgal_perms = array();
			}
		} else {
			$cacheType = 'fgals_perms_'.implode('_', $galleryId)."_";
			if ($cachelib->isCached($cacheName, $cacheType)) {
				$fgal_perms = unserialize($cachelib->getCached($cacheName, $cacheType));
			} else {
				$fgal_perms = array();
			}
		}
		foreach ( $result as $res ) {
			$object_type = ( $res['isgal'] == 1 ? 'file gallery' : 'file');
			$galleryId = $res['isgal'] == 1 ? $res['id'] : $res['galleryId'];

			if ($prefs['fgal_upload_from_source'] == 'y' && $object_type == 'file') {
				$attributes = TikiLib::lib('attribute')->get_attributes('file', $res['id']);
				if (isset($attributes['tiki.content.source'])) {
					$res['source'] = $attributes['tiki.content.source'];
				}
			}

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

            // If the current user is the file owner, then list the file (fix for the userfiles - wasn't listing even if trying to list own files)
            if ($my_user == $res['creator']){
                $res['perms']['tiki_p_view_file_gallery'] = 'y';
            }
            
			// Don't return the current item, if :
			//  the user has no rights to view the file gallery AND no rights to list all galleries (in case it's a gallery)
			if ( ( $res['perms']['tiki_p_view_file_gallery'] != 'y' && ! $this->user_has_perm_on_object($user, $res['id'], $object_type, 'tiki_p_view_file_gallery') )
					|| ( $res['isgal'] && ! Perms::get()->list_file_galleries )) {
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

				if ($prefs['auth_token_access'] == 'y') {
					$query = 'select email, sum((maxhits - hits)) as visit, sum(maxhits) as maxhits  from tiki_auth_tokens where `parameters`=? group by email';
					$share_result = $this->fetchAll($query, array('{"fileId":"'.$res['id'].'"}'));
					if ($share_result) {
						$res['share']['data'] = $share_result;
						$tmp = array();
						if (is_array($res['share']['data'])) {
							foreach ($res['share']['data'] as $data) {
								$tmp[] = $data['email'];
							}
						}
						$string_share = implode(', ', $tmp);
						$res['share']['string'] = substr($string_share, 0, 40);
						if (strlen($string_share) > 40) {
							$res['share']['string'] .= '...';
						}
						$res['share']['nb'] = count($share_result);
					} else {
						$res['share'] = null;
					}
				}
			} else {	// a gallery

				$res['name'] = $this->get_user_gallery_name($res);
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

	/**
	 * No longer used (12.x) - was only called from listfgal_pref() in /lib/prefs/home.php
	 *
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $user
	 * @param null $find
	 * @return array
	 */
	function list_visible_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user = '', $find = null)
	{
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

	function get_file_gallery($id = -1, $defaultsFallback = true)
	{
		static $defaultValues = null;

		if ( $defaultValues === null && $defaultsFallback ) {
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

		$res['name'] = $this->get_user_gallery_name($res);

		return $res;
	}

	/**
	 * convert markup to be inserted onclick - replace: %fileId%, %name%, %description% etc
	 * also will convert attributes, e.g. %tiki.content.url% will be replaced with the remote url
	 *
	 * @param $syntax string	template syntax
	 * @param $file array		file info
	 * @return string			wiki syntax for that file
	 */

	private function process_fgal_syntax($syntax, $file)
	{
		$replace_keys = array('fileId', 'name', 'filename', 'description', 'hits', 'author', 'filesize', 'filetype');
		foreach ($replace_keys as $k) {
			if (isset($file[$k])) {
				$syntax = preg_replace("/%$k%/", $file[$k], $syntax);
			}
		}
		$attributes = TikiLib::lib('attribute')->get_attributes('file', $file['fileId']);
		foreach ($attributes as $k => $v) {
			$syntax = preg_replace("/%$k%/", $v, $syntax);
		}
		return $syntax;
	}

	private function print_msg($msg, $htmlEntities = false)
	{
		global $prefs;

		if ( $htmlEntities ) {
			$msg = htmlentities($msg, ENT_QUOTES, 'UTF-8');
		}

		if ( $prefs['javascript_enabled'] == 'y' ) {
			echo $msg;
		}
	}

	/*shared*/
	public function actionHandler($action, $params)
	{
		$method_name = '_actionHandler_' . $action;
		if ( ! is_callable(array( $this, $method_name )))
			return false;

		return call_user_func(array( $this, $method_name ), $params);
	}

	private function _actionHandler_removeFile( $params )
	{
		// mandatory params: int fileId
		// optional params: boolean draft, array gal_info
		if ( ! empty( $params ) && isset( $params['fileId'] ) ) {
			// To remove an image the user must be the owner or the file or the gallery or admin

			if ( ! isset( $params['draft'] ) ) {
				$params['draft'] = false;
			}

			global $smarty;
			if ( ! $info = $this->get_file_info($params['fileId']) ) {
				$smarty->assign('msg', tra('Incorrect param'));
				$smarty->display('error.tpl');
				die;
			}

			if ( empty( $params['gal_info'] ) || ! isset( $params['gal_info']['user'] ) ) {
				if ( isset( $info['galleryId'] ) ) {
					$params['gal_info'] = $this->get_file_gallery_info($info['galleryId']);
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

			$backlinks = $this->getFileBacklinks($params['fileId']);

			if ( isset( $_POST['daconfirm'] ) && ! empty( $backlinks ) ) {
				$smarty->assign_by_ref('backlinks', $backlinks);
				$smarty->assign('file_backlinks_title', 'Warning: The file is used in:');//get_strings tra('Warning: The file is used in:')
				$smarty->assign('confirm_detail', $smarty->fetch('file_backlinks.tpl')); ///FIXME
			}

			$access = TikiLib::lib('access');
			$confirmationText = ( empty( $info['name'] ) ? '' : htmlspecialchars($info['name']) . ' - ') . htmlspecialchars($info['filename']);
			if ( $params['draft'] ) {
				$access->check_authenticity(tra('Remove file draft: ') . $confirmationText);
				$this->remove_draft($info['fileId'], $user);
			} else {
				$access->check_authenticity(tra('Remove file: ') . $confirmationText);
				$this->remove_file($info, $params['gal_info']);
			}
		}
	}

	// TODO: This does not necessarily handle a file upload. Just edits a file. File replacements are handled somewhere else.
	private function _actionHandler_uploadFile( $params )
	{
		global $user, $prefs, $tiki_p_admin, $tiki_p_batch_upload_files;
		$logslib = TikiLib::lib('logs');
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');

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
			$gal_info = $this->get_file_gallery((int) $params['galleryId'][0]);
			$podCastGallery = $this->isPodCastGallery((int) $params["galleryId"][0], $gal_info);
			$savedir = $this->get_gallery_save_dir((int) $params["galleryId"][0], $gal_info);
		}
		$podcast_url = str_replace("tiki-upload_file.php", "", $foo["path"]);
		$podcast_url = $tikilib->httpPrefix() . $podcast_url . $prefs['fgal_podcast_dir'];

		if ( isset( $params['fileId'] ) ) {

			$editFile = true;
			$editFileId = $params['fileId'];

			if ( $gal_info !== null && ( $nb_files = count($params['galleryId']) ) != 1 ) {
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
				if ( ! ( $fileInfo = $this->get_file_info($editFileId) ) ) {
					$smarty->assign('msg', tra('The specified file does not exist'));
					$smarty->display('error.tpl');
					die;
				}
			}

			if ( ! empty( $params['name'][0] ) ) $fileInfo['name'] = $params['name'][0];
			if ( ! empty( $params['description'][0] ) ) $fileInfo['description'] = $params['description'][0];
			if ( ! empty( $params['user'][0] ) ) $fileInfo['user'] = $params['user'][0];
			if ( ! empty( $params['author'][0] ) ) $fileInfo['author'] = $params['author'][0];
			if ( ! empty( $params['filetype'][0] ) ) $fileInfo['filetype'] = $params['filetype'][0];
			if ( ! empty( $params['comment'][0] ) ) $fileInfo['comment'] = $params['comment'][0];

		} else {
			$editFileId = 0;
			$editFile = false;
			$fileInfo = null;
		}

		if ( ! empty( $_FILES['userfile'] ) ) {
			$feedback_message = '';
			$aFiles['userfile'] = $_FILES['userfile'];

			foreach ( $aFiles["userfile"]["error"] as $key => $error ) {
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
					if (! $this->is_filename_valid($aFiles['userfile']['name'][$key])) {
						$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $aFiles["userfile"]["name"][$key];
						continue;
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
							$this->print_msg(tra('Batch file processed') . " $name", true);
							continue;
						} else {
							$errors[] = tra('You don\'t have permission to upload zipped file packages');
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
					list(,$subtype)=explode('/', strtolower($type));
					// supress ie non-mime type (ie sends non standard mime-type for jpeg)
					if ($subtype == "pjpeg") {
						$subtype = "jpeg";
						$type = "image/jpeg";
					}

					// No resizing
					if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
						if ($tiki_p_admin == 'y') {
							$errors[] = tra('Errors detected').'. '.tra('Check that these paths exist and are writable by the web server').': '.$file_tmp_name.' '.$tmp_dest;
							continue;
						} else {
							$errors[] = tra('Errors detected');
							continue;
						}
						$logslib->add_log('file_gallery', tra('Errors detected').'. '.tra('Check that these paths exist and are writable by the web server').': '.$file_tmp_name.' '.$tmp_dest);
					} else {
						$logslib->add_log('file_gallery', tra('File added: ').$tmp_dest.' '.tra('by').' '.$user);
					}

					if (false === $data = file_get_contents($tmp_dest)) {
						$errors[] = tra('Cannot read the file:') . ' ' . $tmp_dest;
					}

					//Add metadata
					$filemeta = $this->extractMetadataJson($tmp_dest);

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
							if (in_array(strtolower($path_parts['extension']), array('m4a', 'mp3', 'mov', 'mp4', 'm4v', 'pdf', 'flv', 'swf', 'wmv'))) {
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
						$errors[] = tra('Warning: Empty file:') . '  ' . $name . '. ' . tra('Please re-upload the file');
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

					if ( is_array($fileInfo) ) {
						$fileInfo['filename'] = $file_name;
					}

					if (isset($data)) {
						if ($editFile) {
							$didFileReplace = true;
							$fileId = $this->replace_file(
								$editFileId, $params["name"][$key],
								$params["description"][$key], $name, $data, $size, $type, $params['user'][$key],
								$fhash . $extension, $params['comment'][$key], $gal_info, $didFileReplace,
								$params['author'][$key], $fileInfo['lastModif'], $fileInfo['lockedby'], $deleteAfter
							);
							if ($prefs['fgal_limit_hits_per_file'] == 'y') {
								$this->set_download_limit($editFileId, $params['hit_limit'][$key]);
							}
						} else {
							$title = $this->getTitleFromFilename($params["name"][$key]);
							  if(!$params['imagesize'][$key])
							  {
		 						$image_x=$params["image_max_size_x"];
		   						$image_y=$params["image_max_size_y"];
								
		  					  }
							else{
								 $image_x=$gal_info["image_max_size_x"];
		                         $image_y=$gal_info["image_max_size_y"];
								}							
							$fileId = $this->insert_file(
								$params["galleryId"][$key], $title,
								$params["description"][$key], $name, $data, $size, $type, $params['user'][$key],
								$fhash . $extension, '', $params['author'][$key], '', '', $deleteAfter, '', $filemeta,$image_x,$image_y
							);


						}
						if (!$fileId) {
							$errors[] = tra('The upload was not successful due to duplicate file content') . ': ' . $name;
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
							if ($prefs['feature_groupalert'] == 'y' && isset($params['listtoalert'])) {
								$groupalertlib = TikiLib::lib('groupalert');
								$groupalertlib->Notify($params['listtoalert'], "tiki-download_file.php?fileId=" . $fileId);
							}
							include_once ('categorize.php');
							// Print progress
							if (empty($params['returnUrl']) && $prefs['javascript_enabled'] == 'y') {
								$smarty->assign("name", $aux['name']);
								$smarty->assign("size", $aux['size']);
								$smarty->assign("fileId", $aux['fileId']);
								$smarty->assign("dllink", $aux['dllink']);
								$smarty->assign("feedback_message", $feedback_message);
								$syntax = $this->getWikiSyntax($params["galleryId"][$key]);
								$syntax = $this->process_fgal_syntax($syntax, $aux);
								$smarty->assign('syntax', $syntax);
								if (!empty($_REQUEST['filegals_manager'])) {
									$smarty->assign('filegals_manager', $_REQUEST['filegals_manager']);
								}
								$smarty->display("tiki-upload_file_progress.tpl");
							}
						}
					}
				}
			}
		}

		if (empty($params['returnUrl']) && count($errors)) {
			foreach ($errors as $error) {
				$this->print_msg($error, true);
			}
		}
		if ($editFile && !$didFileReplace) {
			if (empty($params['deleteAfter']) || empty($params['deleteAfter_unit'])) {
				$deleteAfter = null;
			} else {
				$deleteAfter = $params['deleteAfter']*$params['deleteAfter_unit'];
			}
			$fileInfo['fileId'] = $this->replace_file(
				$editFileId,
				$fileInfo['name'],
				$fileInfo['description'],
				$fileInfo['filename'],
				$fileInfo['data'],
				$fileInfo['filesize'],
				$fileInfo['filetype'],
				$fileInfo['user'],
				$fileInfo['path'],
				$fileInfo['comment'],
				$gal_info,
				$didFileReplace,
				$fileInfo['author'],
				$fileInfo['lastModif'],
				$fileInfo['lockedby'],
				$deleteAfter,
				$fileInfo['metadata']
			);
			$fileChangedMessage = tra('File update was successful') . ': ' . $params['name'];
			$smarty->assign('fileChangedMessage', $fileChangedMessage);
			$cat_type = 'file';
			$cat_objid = $editFileId;
			$cat_desc = substr($params["description"][0], 0, 200);
			$cat_name = empty($fileInfo['name']) ? $fileInfo['filename'] : $fileInfo['name'];
			$cat_href = $podCastGallery ? $podcast_url . $fhash : "$url_browse?fileId=" . $editFileId;
			if ($prefs['fgal_limit_hits_per_file'] == 'y') {
				$this->set_download_limit($editFileId, $params['hit_limit'][0]);
			}
			include_once ('categorize.php');
			if (count($errors) == 0) {
				header("location: tiki-list_file_gallery.php?galleryId=" . $params["galleryId"][0]);
				die;
			}
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

	private function getTitleFromFilename($title)
	{
		if (strpos($title, '.zip') !== strlen($title) - 4) {
			$title = preg_replace('/\.[^\.]*$/', '', $title); // remove extension
			$title = preg_replace('/[\-_]+/', ' ', $title); // turn _ etc into spaces
			$title = ucwords($title);
		}
		if (strlen($title) > 200) {				// trim to length of name column in database
			$title = substr($title, 0, 200);
		}
		return $title;
	}

	private function transformImage($path, & $data, & $size, $gal_info, $type, & $metadata,$image_size_x=null,$image_size_y=null)
	{
		$imageReader = $this->getImageReader($type);
		$imageWriter = $this->getImageWriter($type);

		if (! $imageReader || ! $imageWriter) {
			return;
		}

		// If it's an image format we can handle and gallery has limits on image sizes
		if (! ($gal_info["image_max_size_x"] && ! $gal_info["image_max_size_y"]) && ($image_size_x==null && $image_size_y==null)) {
			return;
		}

		if ($data) {
			$work_file = tempnam('temp/', 'imgresize');
			file_put_contents($work_file, $data);
		} else {
			$savedir = $this->get_gallery_save_dir($gal_info['galleryId'], $gal_info);
			$work_file = $savedir . $path;
		}
		if(is_null($image_size_x))
           $image_size_x=$gal_info["image_max_size_x"];
		if(is_null($image_size_y))
           $image_size_y=$gal_info["image_max_size_y"];
		
		
		$image_size_info = getimagesize($work_file);
		$image_x = $image_size_info[0];
		$image_y = $image_size_info[1];
		if ($image_size_x) {
			//$rx=$image_x/$gal_info["image_max_size_x"];
			$rx=$image_x/ $image_size_x;
		} else {
			$rx=0;
		}
		if ( $image_size_y) {
			$ry=$image_y/ $image_size_y;
		} else {
			$ry=0;
		}
		$dataforsize = null;
		$ratio=max($rx, $ry);
		if ($ratio>1) {	// Resizing will occur
			$image_new_x=$image_x/$ratio;
			$image_new_y=$image_y/$ratio;
			$resized_file = $work_file;
			$image_resized_p = imagecreatetruecolor($image_new_x, $image_new_y);

			$image_p = $imageReader($work_file);

			if (!imagecopyresampled($image_resized_p, $image_p, 0, 0, 0, 0, $image_new_x, $image_new_y, $image_x, $image_y)) {
				$errors[] = tra('Cannot resize the file:') . ' ' . $work_file;
			}

			imagedestroy($image_p);

			if (! $imageWriter($image_resized_p, $work_file)) {
				$errors[] = tra('Cannot write the file:') . ' ' . $work_file;
			}
			$feedback_message = sprintf(tra('Image was reduced: %s x %s -> %s x %s'), $image_x, $image_y, (int)$image_new_x, (int)$image_new_y);
			$dataforsize = file_get_contents($work_file);
			$size = function_exists('mb_strlen') ? mb_strlen($dataforsize, '8bit') : strlen($dataforsize);
			$metadata = $this->extractMetadataJson($work_file);

		}
		if ($data) {					// image stored in $data so the file $work_file is temporary
			if ($dataforsize) {			// replace data only if actually resized
				$data = $dataforsize;
			}
			unlink($work_file);			// otherwise it's the actual filesystem version of the image so should not be deleted
		}
	}

	private function getImageReader($type)
	{
		switch($type) {
			case "image/gif":
				return 'imagecreatefromgif';
			case "image/png":
				return 'imagecreatefrompng';
			case "image/bmp":
			case "image/wbmp":
				return 'imagecreatefromwbmp';
			case "image/jpg":
			case "image/jpeg":
			case "image/pjpeg":
				return 'imagecreatefromjpeg';
		}
	}

	private function getImageWriter($type)
	{
		switch($type) {
			case "image/gif":
				return 'imagegif';
			case "image/png":
				return 'imagepng';
			case "image/bmp":
			case "image/wbmp":
				return 'image2wbmp';
			case "image/jpg":
			case "image/jpeg":
			case "image/pjpeg":
				return 'imagejpeg';
		}
	}

	function handle_file_upload($fileKey, $file)
	{
		global $prefs, $user, $tiki_p_edit_gallery_file, $tiki_p_admin_file_galleries, $smarty;

		$savedir = $prefs['fgal_use_dir'];

		$msg = null;
		if (! is_uploaded_file($file['tmp_name'])) {
			$msg = array('error' => tra('Upload was not successful') . ': ' . $this->uploaded_file_error($file['error']));
		} elseif (! $file['size']) {
			$msg = tra('Warning: Empty file:') . '  ' . htmlentities($file['name']) . '. ' . tra('Please re-upload the file');
		} elseif (empty($file['name']) || !preg_match('/^upfile(\d+)$/', $fileKey, $regs) || !($fileInfo = $this->get_file_info($regs[1]))) {
			$msg = tra('Could not upload the file') . ': ' . htmlentities($file['name']);
		} elseif (! $this->is_filename_valid($file['name'])) {
			$msg = tra('Invalid filename (using filters for filenames)') . ': ' . htmlentities($file['name']);
		} elseif ($_REQUEST['galleryId'] != $fileInfo['galleryId']) {
			$msg = tra('Could not find the file requested');
		} elseif (!empty($fileInfo['lockedby']) && $fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y') {
			// if locked, user must be the locker
			$msg = sprintf(tra('The file has been locked by %s'), $fileInfo['lockedby']);
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
			//get metadata
			$filemeta = $this->extractMetadataJson($file['tmp_name']);
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
			} else {
				//Add metadata
				$filemeta = $this->extractMetadataJson($file['tmp_name']);
			}
		}

		return array(
			'filename' => $file['name'],
			'fhash' => $fhash,
			'data' => $data,
			'type' => preg_match('/.flv$/', $file['name']) ? 'video/x-flv' : $file['type'],
			'size' => $file['size'],
			'metadata' => isset($filemeta) && count($filemeta) > 0 ? $filemeta : null,
		);
	}

	function upload_single_file($gal_info, $name, $size, $type, $data, $asuser = null,$image_x=null,$image_y=null)
	{
		global $user;
		if (empty($asuser) || ! Perms::get()->admin) {
			$asuser = $user;
		}
		if ($this->convert_from_data($gal_info, $fhash, $data)) {
			$data = null;
		}

		$tx = $this->begin();
		$ret = $this->insert_file($gal_info['galleryId'], $name, '', $name, $data, $size, $type, $asuser, $fhash, '','','','','','','',$image_x,$image_y);

		
		$tx->commit();

		return $ret;
	}

	function update_single_file($gal_info, $name, $size, $type, $data, $id, $asuser = null)
	{
		global $user;
		if (empty($asuser)) {
			$asuser = $user;
		}
		if ($this->convert_from_data($gal_info, $fhash, $data)) {
			$data = null;
		}

		$didFileReplace = true;

		$tx = $this->begin();
		$ret = $this->replace_file($id, $name, '', $name, $data, $size, $type, $asuser, $fhash, '', $gal_info, $didFileReplace);
		$tx->commit();

		return $ret;
	}

	private function convert_from_data($gal_info, & $fhash, $data)
	{
		$savedir = $this->get_gallery_save_dir($gal_info['galleryId']);
		$fhash = '';

		if ($savedir) {
			$fhash = $this->find_unique_name($savedir, $gal_info['name']);
			file_put_contents($savedir . $fhash, $data);

			return true;
		}

		return false;
	}

	function get_info_from_url($url, $lastCheck = false, $eTag = false)
	{
		if (! $url) {
			return false;
		}

		$data = parse_url($url);

		switch ($data['scheme']) {
		case 'http':
		case 'https':
			return $this->get_info_from_http($url, $lastCheck, $eTag);
		default:
			return false;
		}
	}

	private function get_info_from_http($url, $lastCheck, $eTag)
	{
		$action = $lastCheck ? 'Refresh' : 'Fetch';

		try {
			$client = TikiLib::lib('tiki')->get_http_client($url);

			$http_headers = array();
			if ($lastCheck) {
				$http_headers['If-Modified-Since'] = gmdate('D, d M Y H:i:s T', $lastCheck);
			}

			if ($eTag) {
				$http_headers['If-None-Match'] = $eTag;
			}

			if(count($http_headers)){
				$client->setHeaders($http_headers);
			}

			$response = TikiLib::lib('tiki')->http_perform_request($client);

			if ($response->isClientError()) {
				TikiLib::lib('logs')->add_action($action, $url, 'url', 'error=' . $response->getStatusCode());
				return false;
			}

			// 300 code, likely not modified or other non-critical error
			if (! $response->isSuccess()) {
				return false;
			}

			$name = basename($client->getUri()->getPath());
			$expiryDate = time();

			$result = $response->getBody();
			if ($disposition = $response->getHeaders()->get('Content-Disposition')) {
				if (preg_match('/filename=[\'"]?([^;\'"]+)[\'"]?/i', $disposition, $parts)) {
					$name = $parts[1];
				}
			}

			$name = rawurldecode($name);
			// Check expires
			if ($expires = $response->getHeaders()->get('Expires')) {
				$potential = strtotime($expires);
				$expiryDate = max($expiryDate, $potential);
			}

			// Check cache-control for max-age, which has priority
			if ($cacheControl = $response->getHeaders()->get('Cache-Control')) {
				if (preg_match('/max-age=(\d+)/', $cacheControl, $parts)) {
					$expiryDate = time() + $parts[1];
				}
			}

			$mimelib = TikiLib::lib('mime');
			$type = $mimelib->from_content($name, $result);

			$size = function_exists('mb_strlen') ? mb_strlen($result, '8bit') : strlen($result);

			if (empty ($name)) {
				$name = tr('unknown');
			}

			TikiLib::lib('logs')->add_action($action, $url, 'url', 'success=' . $response->getStatusCode());
			return array(
				'data' => $result,
				'size' => $size,
				'type' => $type,
				'name' => $name,
				'expires' => $expiryDate,
				'etag' => $response->getHeaders()->get('Etag'),
			);
		} catch (Zend\Http\Exception\ExceptionInterface $e) {
			TikiLib::lib('logs')->add_action($action, $url, 'url', 'error=' . $e->getMessage());
			return false;
		}
	}

	function attach_file_source($fileId, $url, $info, $isReference = false)
	{
		$attributelib = TikiLib::lib('attribute');
		$attributelib->set_attribute('file', $fileId, 'tiki.content.source', $url);
		$attributelib->set_attribute('file', $fileId, 'tiki.content.lastcheck', time());
		$attributelib->set_attribute('file', $fileId, 'tiki.content.expires', $info['expires']);

		if ($info['etag']) {
			$attributelib->set_attribute('file', $fileId, 'tiki.content.etag', $info['etag']);
		}

		if ($isReference) {
			$attributelib->set_attribute('file', $fileId, 'tiki.content.url', $url);
		}
	}

	function lookup_source($url)
	{
		$attributelib = TikiLib::lib('attribute');
		$objects = $attributelib->find_objects_with('tiki.content.source', $url);

		foreach ($objects as $object) {
			if ($object['type'] == 'file') {
				return $this->table('tiki_files')->fetchRow(
					array(
						'fileId',
						'size' => 'filesize',
						'name',
						'type' => 'filetype',
						'galleryId',
						'md5sum' => 'hash',
					),
					array('fileId' => $object['itemId'])
				);
			}
		}
	}

	function refresh_file($fileId)
	{
		global $prefs;

		$attributelib = TikiLib::lib('attribute');
		$attributes = $attributelib->get_attributes('file', $fileId);

		// Must have a source to begin with
		if (! isset($attributes['tiki.content.source'])) {
			return false;
		}

		$lastCheck = false;
		// Make sure not to flood the remote server with update requests
		if (isset($attributes['tiki.content.lastcheck'])) {
			$lastCheck = $attributes['tiki.content.lastcheck'];
			if ($lastCheck + $prefs['fgal_source_refresh_frequency'] > time()) {
				return false;
			}
		}

		// Respect cache headers too
		if (isset($attributes['tiki.content.expires']) && $attributes['tiki.content.expires'] > time()) {
			return false;
		}

		$files = $this->table('tiki_files');
		$info = $files->fetchRow(
			array('galleryId', 'name', 'filename', 'description', 'hash'),
			array('fileId' => $fileId, 'archiveId' => 0)
		);

		if (! $info) {
			// Either a missing file or an archive, in both cases, we don't process
			return false;
		}

		$eTag = false;
		if (isset($attributes['tiki.content.etag'])) {
			$eTag = $attributes['tiki.content.etag'];
		}

		// Record as a check before checking in the case the server is overloaded and times out
		$attributelib->set_attribute('file', $fileId, 'tiki.content.lastcheck', time());
		$remote = $this->get_info_from_url($attributes['tiki.content.source'], $lastCheck, $eTag);
		$attributelib->set_attribute('file', $fileId, 'tiki.content.expires', $remote['expires']);

		if ($remote['etag']) {
			$attributelib->set_attribute('file', $fileId, 'tiki.content.etag', $remote['etag']);
		}

		if (! $remote) {
			return false;
		}

		$sum = md5($remote['data']);

		if ($sum === $info['hash']) {
			// Content is the same, no new version to create
			return false;
		}

		$name = $remote['name'];
		$data = $remote['data'];
		if ($info['name'] != $info['filename']) {
			// Name was changed on the file, preserve the manually entered one
			$name = $info['name'];
		}

		$gal_info = $this->get_file_gallery_info($info['galleryId']);
		if ($this->convert_from_data($gal_info, $fhash, $data)) {
			$data = null;
		}
		return (bool) $this->replace_file($fileId, $name, $info['description'], $remote['name'], $data, $remote['size'], $remote['type'], null, $fhash, tra('Automatic revision from source'), $gal_info, true);
	}

	private function is_filename_valid($filename)
	{
		global $prefs;
		if (!empty($prefs['fgal_match_regex'])) {
			if (! preg_match('/' . $prefs['fgal_match_regex'] . '/', $filename)) {
				return false;
			}
		}
		if (!empty($prefs['fgal_nmatch_regex'])) {
			if (preg_match('/' . $prefs['fgal_nmatch_regex'] . '/', $filename)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * truncate a filename to the max length of filename column in database (varchar 80)
	 * remove chars in the middle to preserve the start and the extension
	 *
	 * @param $filename string
	 * @return string
	 */

	private function truncate_filename($filename) {
		if (strlen($filename) > 80) {
			$filename = substr($filename, 0, 38) . '...' . substr($filename, strlen($filename) - 38);
		}

		return $filename;
	}

	function moveAllWikiUpToFgal($fgalId, &$errors, &$feedbacks)
	{
		$tikilib = TikiLib::lib('tiki');

		$maxRecords = 100;
		// The outer loop attemps to limit memory usage by fetching pages gradually
		for ($offset = 0; $pages = $tikilib->list_pages($offset, $maxRecords), !empty($pages['data']); $offset += $maxRecords) {
			foreach ($pages['data'] as $page) {
				$this->moveWikiUpToFgal($page, $fgalId, $errors, $feedbacks);
			}
		}
		$this->wikiupMoved = [];
	}
	function moveWikiUpToFgal($page_info, $fgalId, &$errors, &$feedbacks)
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');
		$mimelib = TikiLib::lib('mime');
		$argumentParser = new WikiParser_PluginArgumentParser;
		$files = array();
		if (strpos($page_info['data'], 'img/wiki_up') === false) {
			return;
		}
		$matches = WikiParser_PluginMatcher::match($page_info['data']);
		foreach ($matches as $match) {
			$modif = false;
			$plugin_name = $match->getName();
			if ($plugin_name == 'img') {
				$arguments = $argumentParser->parse($match->getArguments());
				$newArgs = array();
				foreach ($arguments as $key=>$val) {
					if ($key === 'src' && strpos($val, 'img/wiki_up') !== false) {
						//first time the wiki_up file is found
						if (!isset($this->wikiupMoved[$val])) {
							if (false === $data = @file_get_contents($val)) {
								$errors[] = tra('Cannot open this file:').' '.$val.' '.tra('Page:').' '.$page_info['pageName'];
								continue;
							}
							$name = preg_replace('|.*/([^/]*)|', '$1', $val);
							$fileId = $this->insert_file($fgalId, $name, 'Used in '.$page_info['pageName'], $name, $data, strlen($data), $mimelib->from_path($name, $val), $user, '', 'wiki_up conversion');
							if (empty($fileId)) {
								$errors[] = tra('Cannot upload this file').' '.$val.' '.tra('Page:').' '.$page_info['pageName'];
								continue;
							} else {
								$files[] = $val;
								$modif = true;
								$newArgs[] = 'fileId="'.$fileId.'"';
								//save wiki_up file name and fileId pair in case there are more instances using this file
								$this->wikiupMoved[$val] = $fileId;
							}
						//wiki_up file was already moved to file galleries
						} else {
							$files[] = $val;
							$modif = true;
							$newArgs[] = 'fileId="' . $this->wikiupMoved[$val] . '"';
						}
					} else
						$newArgs[] = "$key=\"$val\"";
				}
				if ($modif) {
					$match->replaceWith('{img '.implode(' ', $newArgs).'}');
				}
			}
		}
		if (!empty($files)) {
			$tikilib->update_page($page_info['pageName'], $matches->getText(), 'wiki_up conversion', $user, $tikilib->get_ip_address());
			$files = array_unique($files);
			foreach ($files as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
			$feedbacks[] = $page_info['pageName'];
		}
	}

	function fixMime($fileData)
	{
		global $prefs;
		if ($prefs['fgal_fix_mime_type'] != 'y') {
			return $fileData['filetype'];
		}
		if ($fileData['filetype'] != "application/octet-stream") {
			return $fileData['filetype'];
		}

		$mimelib = TikiLib::lib('mime');
		return $mimelib->from_filename($fileData['filename']);
	}

	/**
	 * Get basic and extended metadata included in the file itself and return as JSON string
	 *
	 * @param    string         $file              path to file or content of file
	 * @param    bool           $ispath            indicates whether $file is a path (true) or the file contents (false)
	 * @param    bool           $extended          indicates whether to retrieve extended metadata information
	 *
	 * @return   string         $filemeta          JSON string of metadata
	 */
	function extractMetadataJson($file, $ispath = true, $extended = true)
	{
		include_once 'lib/metadata/metadatalib.php';
		$metadata = new FileMetadata;
		$filemeta = json_encode($metadata->getMetadata($file, $ispath, $extended)->typemeta['best']);
		return $filemeta;
	}

	/**
	 * Perform actions with file metadata stored in the database
	 *
	 * @param		numeric		$fileId					fileId of the file in the file gallery
	 * @param		string		$action					action to perform regarding metadata
	 *
	 * The following actions are handled:
	 * 		'get_array'			Get file metadata from database column or, if that is empty, extract metadata from the
	 * 							file, update the database and return an array of the data.
	 *
	 * 		'refresh'			Extract metadata from the file and update database - nothing is returned
	 *
	 * @return 		array		$metadata				array of metadata is returned is action is 'get_array'
	 */
	function metadataAction($fileId, $action = 'get_array')
	{
		//get the tiki_files table
		$filesTable = $this->table('tiki_files');
		if ($action == 'get_array') {
			//get metadata for the file from the database
			$metacol = $filesTable->fetchColumn('metadata', array('fileId' => $fileId));
		}
		//if metadata field is empty, or if a refresh, extract from the file
		if (($action == 'get_array' && empty($metacol[0])) || $action == 'refresh') {
			//preparing parameters
			$path = $filesTable->fetchColumn('path', array('fileId' => $fileId));
			if (!empty($path[0])) {
				global $prefs;
				$file = $prefs['fgal_use_dir'] . $path[0];
				$ispath = true;
			} else {
				$file = $filesTable->fetchColumn('data', array('fileId' => $fileId));
				$file = $file[0];
				$ispath = false;
			}
			//extract metadata
			$metadata = $this->extractMetadataJson($file, $ispath);
			//update database for newly extracted metadata
			$filesTable->update(array('metadata' => $metadata), array('fileId' => $fileId));
			// update search index
			require_once('lib/search/refresh-functions.php');
			refresh_index('files', $fileId);
		} else {
			$metadata = $metacol[0];
		}
		if ($action == 'get_array') {
			//return metadata as an array
			return json_decode($metadata, true);
		}
	}
}

