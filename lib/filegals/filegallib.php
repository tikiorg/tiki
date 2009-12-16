<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/filegals/filegallib.php,v 1.76.2.10 2008-03-16 00:07:11 nyloth Exp $

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

	function remove_file($fileInfo, $galInfo='', $disable_notifications = false) {
		global $prefs, $smarty, $user;

		if ($podCastGallery = $this->isPodCastGallery($fileInfo['galleryId'], $galInfo)) {
			$savedir=$prefs['fgal_podcast_dir'];
		} else {
			$savedir=$prefs['fgal_use_dir'];
		}

		$this->deleteBacklinks(null, $fileInfo['fileId']);
		if ($fileInfo['path']) {
			unlink ($savedir . $fileInfo['path']);
		}
		$archives = $this->get_archives($fileInfo['fileId']);
		foreach ($archives['data'] as $archive) {
			if ($archive['path']) {
				unlink ($savedir . $archive['path']);
			}
			$this->remove_object('file', $archive['fileId']);
		}

		$query = 'delete from `tiki_files` where `fileId`=? or `archiveId`=?';
		$result = $this->query($query,array($fileInfo['fileId'], $fileInfo['fileId']));
		$this->remove_object('file', $fileInfo['fileId']);

		//Watches
		if ( ! $disable_notifications ) $this->notify($fileInfo['galleryId'], $fileInfo['name'], $fileInfo['filename'], '', 'remove file', $user);

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Removed', $fileInfo['fileId'].'/'.$fileInfo['filename'], 'file', '');
		}
		return true;
	}

	function insert_file($galleryId, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='', $author, $created='', $lockedby=NULL) {
	  global $prefs, $tikilib, $smarty, $user;

		$name = strip_tags($name);
		if ($podCastGallery = $this->isPodCastGallery($galleryId)) {
			$savedir=$prefs['fgal_podcast_dir'];
		} else {
			$savedir=$prefs['fgal_use_dir'];
		}
		if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
			if (function_exists('md5_file')) {
				$checksum = md5_file($savedir . $path);
			} else {
				$checksum = md5(implode('', file($savedir . $path)));
			}
		} else {
			$checksum = md5($data);
		}
		$description = strip_tags($description);

		if ( $prefs['fgal_allow_duplicates'] != 'y' ) {
			$fgal_query = 'select count(*) from `tiki_files` where `hash`=?';
			$fgal_vars = array($checksum);
			if ( $prefs['fgal_allow_duplicates'] == 'different_galleries' ) {
				$fgal_query .= ' and `galleryId`=?';
				$fgal_vars[] = $galleryId;
			}
			if ( $this->getOne($fgal_query, $fgal_vars) > 0 ) return false;
		}

		$search_data = '';
		if ($prefs['fgal_enable_auto_indexing'] != 'n') {
			$search_data = $this->get_search_text_for_data($data,$path,$type, $galleryId);
			if ($search_data === false)
				return false;
		}
		if ( empty($created) ) $created = $this->now;
		$query = "insert into `tiki_files`(`galleryId`,`name`,`description`,`filename`,`filesize`,`filetype`,`data`,`user`,`created`,`hits`,`path`,`hash`,`search_data`,`lastModif`,`lastModifUser`, `comment`, `author`, `lockedby`)
                          values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array($galleryId,trim($name),$description,$filename,$size,$type,$data,$creator,$created,0,$path,$checksum,$search_data,(int)$this->now,$user,$comment, $author, $lockedby));
		$query = "update `tiki_file_galleries` set `lastModif`=? where `galleryId`=?";
		$result = $this->query($query,array((int) $this->now,$galleryId));
		$query = "select max(`fileId`) from `tiki_files` where `created`=?";
		$fileId = $this->getOne($query,array((int) $created));

		if ($prefs['feature_score'] == 'y') {
		    $this->score_event($user, 'fgallery_new_file');
		}

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Uploaded', $galleryId, 'file gallery', "fileId=$fileId&amp;add=$size");
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' && ( $prefs['fgal_asynchronous_indexing'] != 'y' || ! isset($_REQUEST['fast']) ) ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('files', $fileId);
		}

		//Watches
		$smarty->assign('galleryId', $galleryId);
                $smarty->assign('fname', $name);
                $smarty->assign('filename', $filename);
                $smarty->assign('fdescription', $description);

		$this->notify($galleryId, $name, $filename, $description, 'upload file', $user, $fileId);

		return $fileId;
	}

	function set_file_gallery($file, $gallery) {
		$query = "update `tiki_files` set `galleryId`=? where `fileId`=?";

		$this->query($query,array($gallery,$file));
	}

	function remove_file_gallery($id, $galleryId=0, $recurse = true) {
		global $prefs;
		$id = (int)$id;

		if (empty($galleryId)) {
			$info = $this->get_file_info($id);
			$galleryId = $info['galleryId'];
		}

		global $cachelib; require_once("lib/cache/cachelib.php");
		$cachelib->empty_type_cache('fgals_perms_'.$id."_");
		$cachelib->empty_type_cache('fgals_perms_'.$info['galleryId']."_");
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		$this->query('delete from `tiki_file_galleries` where `galleryId`=?', array($id));
		$this->remove_object('file gallery', $id);

		if ( $filesInfo = $this->get_files_info_from_gallery_id($id, false, false) ) {
			foreach ( $filesInfo as $fileInfo ) $this->remove_file($fileInfo, '', true);
		}

		// If $recurse, also recursively remove children galleries
		if ( $recurse ) {
			$result = $this->query('SELECT `galleryId` FROM `tiki_file_galleries` WHERE `parentId`=?', array($id));
			while ( $res = $result->fetchRow() ) {
				if ( $res['galleryId'] <= 0 ) continue;
				$this->remove_file_gallery($res['galleryId'], $id, true);
			}
		}

		return true;
	}

	function get_file_gallery_info($id) {
		$query = "select * from `tiki_file_galleries` where `galleryId`=?";

		$result = $this->query($query,array((int) $id));
		$res = $result->fetchRow();
		return $res;
	}

	function move_file_gallery($galleryId, $new_parent_id) {
		if ( (int)$galleryId <= 0 || (int)$new_parent_id == 0 ) return false;

		global $cachelib; require_once("lib/cache/cachelib.php");
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		return $this->query(
			'update `tiki_file_galleries` set `parentId`=? where `galleryId`=?',
			array((int)$new_parent_id, (int)$galleryId)
		);
	}

	function replace_file_gallery($fgal_info) {

		global $prefs;
		// Default values
		if (!isset($fgal_info['visible']))  $fgal_info['visible'] = 'y';
		if (!isset($fgal_info['type']))  $fgal_info['type'] = 'default';
		if (!isset($fgal_info['parentId']))  $fgal_info['parentId'] = -1;
		if (!isset($fgal_info['lockable']))  $fgal_info['lockable'] = 'n';
		if (!isset($fgal_info['show_lockedby']))  $fgal_info['show_lockedby'] = 'y';
		if (!isset($fgal_info['archives']))  $fgal_info['archives'] = -1;
		if (!isset($fgal_info['show_modified']))  $fgal_info['show_modified'] = 'n';
		if (!isset($fgal_info['show_creator']))  $fgal_info['show_creator'] = 'n';
		if (!isset($fgal_info['show_author']))  $fgal_info['show_author'] = 'n';
		if (!isset($fgal_info['quota']))  $fgal_info['quota'] = 0;
		if (!isset($fgal_info['backlinkPerms'])) $fgal_info['backlinkPerms'] = 'n';

		// if the user is admin or the user is the same user and the gallery exists
		// then replace if not then create the gallary if the name is unused.
		$fgal_info['name'] = strip_tags($fgal_info['name']);

		$fgal_info['description'] = strip_tags($fgal_info['description']);
		if ($fgal_info['sort_mode'] == 'created_desc') {
			$fgal_info['sort_mode'] = null;
		}

		if ($fgal_info['galleryId'] > 0) {
			$query = "update `tiki_file_galleries` set `name`=?, `maxRows`=?,
			`description`=?, `lastModif`=?, `public`=?, `visible`=?, `show_icon`=?,
			`show_id`=?, `show_name`=?, `show_description`=?, `show_size`=?,
			`show_created`=?, `show_hits`=?, `max_desc`=?, `type`=?, `parentId`=?,
			`user`=?, `lockable`=?, `show_lockedby`=?, `archives`=?, `sort_mode`=?,
			`show_modified`=?, `show_creator`=?, `show_author`=?, `subgal_conf`=?,
			`show_last_user`=?, `show_comment`=?, `show_files`=?, `show_explorer`=?,
			`show_path`=?, `show_slideshow`=?, `default_view`=?, `quota`=?, `backlinkPerms`=? where `galleryId`=?";

			$bindvars=array(trim($fgal_info['name']), (int) $fgal_info['maxRows'],
			$fgal_info['description'], (int) $this->now, $fgal_info['public'],
			$fgal_info['visible'], $fgal_info['show_icon'], $fgal_info['show_id'],
			$fgal_info['show_name'], $fgal_info['show_description'],
			$fgal_info['show_size'], $fgal_info['show_created'],
			$fgal_info['show_hits'], (int) $fgal_info['max_desc'],
			$fgal_info['type'], $fgal_info['parentId'], $fgal_info['user'],
			$fgal_info['lockable'], $fgal_info['show_lockedby'],
			$fgal_info['archives'], $fgal_info['sort_mode'],
			$fgal_info['show_modified'], $fgal_info['show_creator'],
			$fgal_info['show_author'], $fgal_info['subgal_conf'],
			$fgal_info['show_last_user'], $fgal_info['show_comment'],
			$fgal_info['show_files'], $fgal_info['show_explorer'],
			$fgal_info['show_path'], $fgal_info['show_slideshow'],
							$fgal_info['default_view'], $fgal_info['quota'], $fgal_info['backlinkPerms'], (int)$fgal_info['galleryId']);

			$result = $this->query($query,$bindvars);

			$query = "update `tiki_objects` set `name`=?, `description`=? where
				`type`=? and `itemId`=?";
			$bindvars = array($fgal_info['name'],$fgal_info['description'],'file
				gallery',(int)$fgal_info['galleryId']);
			$this->query($query,$bindvars);
			$galleryId = $fgal_info['galleryId'];
		} else {
			// Create a new record
			$query = "insert into `tiki_file_galleries`(`name`, `description`,
			`created`, `user`, `lastModif`, `maxRows`, `public`, `hits`, `visible`,
			`show_id`, `show_icon`, `show_name`, `show_description`, `show_created`,
			`show_hits`, `max_desc`, `type`, `parentId`, `lockable`, `show_lockedby`,
			`archives`, `sort_mode`, `show_modified`, `show_creator`, `show_author`,
			`subgal_conf`, `show_last_user`, `show_comment`, `show_files`,
			`show_explorer`, `show_path`, `show_slideshow`, `default_view`, `quota`, `backlinkPerms`)
			values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$bindvars=array($fgal_info['name'], $fgal_info['description'], (int)
			$this->now, $fgal_info['user'], (int) $this->now, (int)
			$fgal_info['maxRows'], $fgal_info['public'], 0, $fgal_info['visible'],
			$fgal_info['show_id'], $fgal_info['show_icon'], $fgal_info['show_name'],
			$fgal_info['show_description'], $fgal_info['show_created'],
			$fgal_info['show_hits'], (int) $fgal_info['max_desc'],
			$fgal_info['type'], $fgal_info['parentId'], $fgal_info['lockable'],
			$fgal_info['show_lockedby'], $fgal_info['archives'],
			$fgal_info['sort_mode'], $fgal_info['show_modified'],
			$fgal_info['show_creator'], $fgal_info['show_author'],
			$fgal_info['subgal_conf'], $fgal_info['show_last_user'],
			$fgal_info['show_comment'], $fgal_info['show_files'],
			$fgal_info['show_explorer'], $fgal_info['show_path'],
			$fgal_info['show_slideshow'], $fgal_info['default_view'], $fgal_info['quota'], $fgal_info['backlinkPerms']);

			$result = $this->query($query,$bindvars);
			$galleryId = $this->getOne("select max(`galleryId`) from
			`tiki_file_galleries` where `name`=? and
			`lastModif`=?",array($fgal_info['name'],(int) $this->now));

			if ($prefs['feature_score'] == 'y') {
				global $user;
			    $this->score_event($user, 'fgallery_new');
			}
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('file_galleries', $galleryId);
		}
		global $cachelib; include_once('lib/cache/cachelib.php');
		$cachelib->empty_type_cache($this->get_all_galleries_cache_type());

		// event_handler($action,$object_type,$object_id,$options);
		return $galleryId;
	}
	function get_all_galleries_cache_name($user) {
		global $tikilib, $categlib; require_once 'lib/categories/categlib.php';
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

	function process_batch_file_upload($galleryId, $file, $user, $description) {
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
		if ($podCastGallery = $this->isPodCastGallery($galleryId, $gal_info)) {
			$savedir=$prefs['fgal_podcast_dir'];
		} else {
			$savedir=$prefs['fgal_use_dir'];
		}

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

				if ($this->checkQuota(filesize($extract_dir.$file), $galleryId, $error)) {
					$errors[] = $error;
					$upl = 0;
				}
			}
		}
		if (!$upl) {
			$smarty->assign('msg', implode('<br />', $errors));
			$smarty->display('error.tpl');
			die;
		}
		
		while (($file = readdir($h)) !== false) {
			if ($file != '.' && $file != '..' && is_file($extract_dir.'/'.$file)) {
				if (!($fp = fopen($extract_dir.$file, "rb"))) {
					$smarty->assign('msg', tra('Cannot open this file:'). "temp/$file");
					$smarty->display("error.tpl");
					die;
				}
				$data = '';
				$fhash = '';

				if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
					$fhash = md5($name = $file);

					@$fw = fopen($savedir . $fhash, "wb");

					if (!$fw) {
						$smarty->assign('msg', tra('Cannot write to this file:'). $fhash);

						$smarty->display("error.tpl");
						die;
					}
				}
				while (!feof($fp)) {
					if (($prefs['fgal_use_db'] == 'y') && (!$podCastGallery)) {
						$data .= fread($fp, 8192 * 16);
					} else {
						$data = fread($fp, 8192 * 16);

						fwrite($fw, $data);
					}
				}

				fclose ($fp);

				if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
					fclose ($fw);

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
	}

	function get_file_info($fileId, $include_search_data = true, $include_data = true) {
		$return = $this->get_files_info(null, (int)$fileId, $include_search_data, $include_data);
		return $return ? $return[0] : false;
	}
	function get_files_info_from_gallery_id($galleryId, $include_search_data = false, $include_data = false) {
		return $this->get_files_info((int)$galleryId, null, $include_search_data, $include_data);
	}

	function get_files_info($galleryIds = null, $fileIds = null, $include_search_data = false, $include_data = false) {
		$query = 'SELECT '
			. ( ( $include_search_data && $include_data ) ? '*' :
				'`fileId`,`galleryId`,`name`,`description`,`created`,`filename`,`filesize`,`filetype`,`user`,`author`,`hits`,`votes`,`points`,`path`,`reference_url`,`is_reference`,`hash`,`lastModif`,`lastModifUser`,`lockedby`,`comment`,`archiveId`'
				. ( $include_search_data ? ',`search_data`' : '' )
				. ( $include_data ? ',`data`' : '' )
			) . ' FROM `tiki_files`';

		$where = '';
		$bindvars = null;
		if ( ! empty($fileIds) ) {
			$bindvars = (array)$fileIds;
			$where .= ' WHERE `fileId`' . ( is_array($fileIds) ? $this->bindvars_to_sql_in($fileIds, true, true) : '=?' );
		}
		if ( ! empty($galleryIds) ) {
			if ( $where != '' ) {
				$where .= ' OR ';
				$bindvars = array_merge($bindvars, (array)$galleryIds);
			} else {
				$where = ' WHERE ';
				$bindvars = (array)$galleryIds;
			}
			$where .= ' `galleryId`' . ( is_array($galleryIds) ? $this->bindvars_to_sql_in($galleryIds, true, true) : '=?' );
		}

		$return = false;
		$result = $this->query($query . $where, $bindvars);
		if ( $result ) {
			$return = array();
			while ( $res = $result->fetchRow() ) $return[] = $res;
		}

		return $return;
	}

	function update_file($id, $name, $description, $user, $comment = NULL, $reindex = true) {

		// Update the fields in the database
		$name = strip_tags($name);

		$bindvars = array($name, $description, (int)$this->now, $user);
		if ( $comment === NULL ) {
			$comment_set = '';
		} else {
			$comment_set = ', `comment`=?';
			$bindvars[] = $comment;
		}
		$bindvars[] = $id;

		$description = strip_tags($description);
		$query = 'UPDATE `tiki_files` SET `name`=?, `description`=?, `lastModif`=?, `lastModifUser`=?'.$comment_set.' WHERE `fileId`=?';
		$result = $this->query($query, $bindvars);

		// Get the gallery id for the file and update the last modified field
		$galleryId = $this->getOne('SELECT `galleryId` FROM `tiki_files` WHERE `fileId`=?', array($id));

		if ( $galleryId >= 0 ) {
			$query = 'UPDATE `tiki_file_galleries` SET `lastModif`=? WHERE `galleryId`=?';
			$this->query($query, array($this->now, $galleryId));
		}

		global $prefs;
		if ( $reindex && $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('files', $id);
		}

		return $result;
	}

	function replace_file($id, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='', $gal_info, $didFileReplace, $author='', $created='', $lockedby=NULL) {
	  global $prefs, $tikilib, $user;

		// Update the fields in the database
		$name = strip_tags($name);

		if ($podCastGallery = $this->isPodCastGallery($gal_info['galleryId'], $gal_info)) {
			$savedir=$prefs['fgal_podcast_dir'];
		} else {
			$savedir=$prefs['fgal_use_dir'];
		}

		if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
			if (function_exists('md5_file')) {
				if (!($checksum = md5_file($savedir . $path)))
					$checksum = '';
			} else {
				$checksum = md5(implode('', file($savedir . $path)));
			}
		} else {
			$checksum = md5($data);
		}

		$description = strip_tags($description);

		$search_data = '';
		if ($prefs['fgal_enable_auto_indexing'] != 'n') {
			$search_data = $this->get_search_text_for_data($data,$path,$type, $gal_info['galleryId']);
			if ($search_data === false)
				return false;
		}
		$oldPath = $this->getOne("select `path` from `tiki_files` where `fileId`=?",array($id));

		if ( $gal_info['archives'] == -1 || ! $didFileReplace ) { // no archive

			$query = "update `tiki_files` set `name`=?, `description`=?, `filename`=?, `filesize`=?, `filetype`=?, `data`=?, `lastModifUser`=?, `lastModif`=?, `path`=?, `hash`=?, `search_data`=?, `author`=?, `user`=?, `lockedby`=?  where `fileId`=?";
			if ( ! ( $result = $this->query($query,array(trim($name),$description,$filename,$size,$type,$data,$user,(int)$this->now,$path,$checksum,$search_data,$author,$creator,$lockedby, $id)) ) ) {
				return false;
			}

			if ( $didFileReplace && !empty($oldPath) ) {
				unlink($savedir . $oldPath);
			}

			if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' && ( $prefs['fgal_asynchronous_indexing'] != 'y' || ! isset($_REQUEST['fast']) ) ) {
				require_once('lib/search/refresh-functions.php');
				refresh_index('files', $id);
			}

		} else { //archive the old file : change archive_id, take away from indexation and categorization

			// Insert and index (for search) the new file
			$idNew = $this->insert_file($gal_info['galleryId'], $name, $description, $filename, $data, $size, $type, $creator, $path, $comment, $author, $created, $lockedby);

			if ($gal_info['archives'] > 0) {
				$archives = $this->get_archives($id, 0, -1, 'created_asc');
				if ($archives['cant'] >= $gal_info['archives']) {
					$nb = $archives['cant'] - $gal_info['archives'] + 1;
					$query = "delete from `tiki_files` where `fileId` in (".implode(',', array_fill(0, $nb, '?')).")";
					for ($i = 0; $i < $nb; ++$i) {
						$bindvars[] = $archives['data'][$i]['fileId'];
						if ( $archives['data'][$i]['path'] ) {
							unlink ($savedir . $archives['data'][$i]['path']);
						}
					}
					$this->query($query, $bindvars);
				}
			}
			$query = "update `tiki_files` set `archiveId`=?, `search_data`=?,`user`=?, `lockedby`=? where `archiveId`=? or `fileId`=?";
			$this->query($query,array($idNew, '',$creator,NULL, $id, $id));

			if ($prefs['feature_categories'] == 'y') {
				global $categlib; require_once('lib/categories/categlib.php');
				$categlib->uncategorize_object('file', $id);
			}

			$id = $idNew;
		}

		if ($gal_info['galleryId']) {
			$query = "update `tiki_file_galleries` set `lastModif`=? where `galleryId`=?";

			$this->query($query,array($this->now,$gal_info['galleryId']));
		}

		return $id;
	}

	function change_file_handler($mime_type,$cmd) {
		$found = $this->getOne("select `mime_type` from `tiki_file_handlers` where `mime_type`=?",array($mime_type));

		if ($found) {
			$query = "update `tiki_file_handlers` set `cmd`=? where `mime_type`=?";
			$result = $this->query($query,array($cmd,$mime_type));
		}
		else {
			$query = "insert into `tiki_file_handlers` (`mime_type`,`cmd`) values (?,?)";
			$result = $this->query($query,array($mime_type,$cmd));
		}

		return $result;
	}

	function delete_file_handler($mime_type) {
		$query = "delete from `tiki_file_handlers` where `mime_type`=?";
		$result = $this->query($query,array($mime_type));
		return (($result) ? true : false);
	}

	function get_file_handlers() {
		$query = "select * from `tiki_file_handlers`";
		$result = $this->query($query);
		$fileParseApps = array();
		while ($row = $result->fetchRow()) {
			$fileParseApps[$row['mime_type']] = $row['cmd'];
		}

		return $fileParseApps;
	}

	function reindex_all_files_for_search_text() {
		$query = "select fileId, filename, filesize, filetype, data, path, galleryId from `tiki_files` where `archiveId`=?";
		$result = $this->query($query, array(0));
		$rows = array();
		while($row = $result->fetchRow()) {
			$rows[] = $row;
		}

		foreach($rows as $row) {
			$search_text = $this->get_search_text_for_data($row['data'],$row['path'],$row['filetype'], $row['galleryId']);
			if ($search_text!==false) {
				$query = "update `tiki_files` set `search_data`=? where `fileId`=?";
				$result = $this->query($query,array($search_text,$row['fileId']));
			}
		}
		include_once("lib/search/refresh-functions.php");
		refresh_index('files');
	}

	function get_search_text_for_data($data,$path,$type, $galleryId) {
		global $prefs;

		if (!isset($data) && !isset($path)) {
			return false;
		}

		if ($podCastGallery = $this->isPodCastGallery($galleryId)) {
			$savedir=$prefs['fgal_podcast_dir'];
		} else {
			$savedir=$prefs['fgal_use_dir'];
		}

		$fileParseApps = $this->get_file_handlers();

		$parseApp = '';
		if (array_key_exists($type,$fileParseApps))
			$parseApp = $fileParseApps[$type];
		elseif (array_key_exists('default',$fileParseApps))
			$parseApp = $fileParseApps['default'];

		if (empty($parseApp))
			return '';

		if (empty($path)) {
			$tmpfname = tempnam("/tmp", "wiki_");
			$tmpFile = fopen($tmpfname,'w');
			if ($tmpFile === false)
				return false;

			if (fwrite($tmpFile,$data) === false)
				return false;
			fflush($tmpFile);
			fclose($tmpFile);
		}
		else {
			$tmpfname = $savedir . $path;
		}

		$cmd = str_replace('%1',$tmpfname,$parseApp);
		$handle = popen("$cmd","r");
		if ($handle === false) {
			if (empty($path))
				@unlink($tmpfname);
			return false;
		}

		$contents = '';
		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
		}
		fclose($handle);

		if (empty($path))
			@unlink($tmpfname);

		return $contents;
	}

	function notify ($galleryId, $name, $filename, $description, $action, $user, $fileId=false) {
		global $prefs;
                if ($prefs['feature_user_watches'] == 'y') {
                        //  Deal with mail notifications.
                        include_once('lib/notifications/notificationemaillib.php');
                        $foo = parse_url($_SERVER["REQUEST_URI"]);
                        $machine = $this->httpPrefix( true ). dirname( $foo["path"]);
			$galleryName = $this->getOne("select `name` from `tiki_file_galleries` where `galleryId`=?",array($galleryId));

                        sendFileGalleryEmailNotification('file_gallery_changed', $galleryId, $galleryName, $name, $filename, $description, $action, $user, $fileId);
                }
	}
	/* lock a file */
	function lock_file($fileId, $user) {
		$query = 'update `tiki_files` set `lockedby`=? where `fileId`=?';
		$this->query($query, array($user, $fileId));
	}
	/* unlock a file */
	function unlock_file($fileId) {
		$query = 'update `tiki_files` set `lockedby`=? where `fileId`=?';
		$this->query($query, array(NULL, $fileId));
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
		global $tikilib;
		return (int) $tikilib->get_preference( "fgal_{$fileId}_hit_limit" );
	}

	function set_download_limit( $fileId, $limit )
	{
		global $tikilib;
		$limit = (int) $limit;
		$pref = "fgal_{$fileId}_hit_limit";

		if( $limit <= 0 )
			$tikilib->delete_preference( $pref );
		else
			$tikilib->set_preference( $pref, $limit );
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
					if (file_put_contents($tmp, $info['data']) === false) {
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
		return $this->fetchAll( 'SELECT `galleryId`, `parentId` FROM `tiki_file_galleries`' );
	}

	function _getGalleryChildrenIdsList( &$allIds, &$subtree, $parentId ) {
		foreach ( $allIds as $k => $v ) {
			if ( $v['parentId'] == $parentId ) {
				$galleryId = $v['galleryId'];
				$subtree[] = (int)$galleryId;
				$this->_getGalleryChildrenIdsList( $allIds, $subtree, $galleryId );
			}
		}
	}

	function _getGalleryChildrenIdsTree( &$allIds, &$subtree, $parentId ) {
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

	function getFileGalleriesData() {
		static $return = null;

		if ( $return === null ) {
			global $prefs, $cachelib, $user;
			$cacheName = $this->get_all_galleries_cache_name($user);
			$cacheType = $this->get_all_galleries_cache_type();
			if ( ! $cachelib->isCached($cacheName, $cacheType) ) {
				$return = $this->list_file_galleries(0, -1, 'name_asc', $user, '', $prefs['fgal_root_id'], false, true, false, false,false,true, false );
				$cachelib->cacheItem($cacheName, serialize($return), $cacheType);
			} else {
				$return = unserialize($cachelib->getCached($cacheName, $cacheType));
			}
		}

		return $return;
	}

	function getFilegalsIdsTree() {
		static $return = null;

		if ( $return === null ) {
			global $prefs;
			$return = array();
			$this->getGalleryIds( $return, $prefs['fgal_root_id'], 'tree' );
		}

		return $return;
	}

	// Get default phplayers tree for filegals
	function getFilegalsTreePhplayers( $currentGalleryId = null ) {
		return $this->getTreePhplayers( $this->getFilegalsIdsTree(), $currentGalleryId );
	}

	// Build galleries browsing tree and current gallery path array
	function getTreePhplayers( $idTree, $currentGalleryId = null ) {
		global $prefs;

		$allGalleries = $this->getFileGalleriesData();

		$idTreeKeys = array_keys( $idTree );
		$rootGalleryId = $idTreeKeys[0];
		if ( $currentGalleryId === null ) $currentGalleryId = $rootGalleryId;

		$script = 'tiki-list_file_gallery.php';
		$tree = array('name' => tra('File Galleries'), 'data' => array(), 'link' => $script);

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
		$query = 'select sum(`filesize`) from `tiki_files`';
		$bindvars = array();
		if (!empty($galleryId)) {
			$this->getGalleryIds( $bindvars, $galleryId, 'list' );
			$query .= 'where `galleryId` in ('.implode(',', array_fill(0, count($bindvars), '?')).')';
		}
		$size = $this->getOne($query, $bindvars);
		return $size;
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
			$query = 'select max(`quota`) from `tiki_file_galleries` where `galleryId` in ('.implode(',', array_fill(0, count($subtree), '?')).')';
			return $this->getOne($query, $subtree);
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
		foreach ($columns as $col) {// artificial size column unitl it is in the database
			if ($col != 'size') {
				$cols[] = $col;
			}
		}
		if (!in_array('galleryId', $cols)) $cols[] = 'galleryId';
		if (!in_array('parentId', $cols)) $cols[] = 'parentId';
		$query = 'select `'.implode($cols, '`, `').'` from `tiki_file_galleries`';
		$all = $this->fetchAll($query);
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
	// check a size in K can be added to a gallery
	function checkQuota($size, $galleryId, &$error) {
		global $prefs, $smarty;
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
		global $objectlib; include_once('lib/objectlib.php');
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
		$this->_deleteBacklinks($objectId);
		$query = 'insert into `tiki_file_backlinks` (`objectId`, `fileId`) values(?,?)';
		foreach ($fileIds as $fileId) {
			$this->query($query, array((int)$objectId, (int)$fileId));
		}
	}
	// delete backlinks associated to an object
	function deleteBacklinks($context, $fileId=null) {
		if (empty($fileId)) {
			global $objectlib; include_once('lib/objectlib.php');
			$objectId = $objectlib->get_object_id($context['type'], $context['object']);
			if (!empty($objectId)) {
				$this->_deleteBacklinks($objectId);
			}
		} else {
			$this->_deleteBacklinks(null, $fileId);
		}
	}
	function _deleteBacklinks($objectId, $fileId=null) {
		if (empty($fileId)) {
			$query = 'delete from `tiki_file_backlinks` where `objectId`=?';
			$this->query($query, array((int)$objectId));
		} else {
			$query = 'delete from `tiki_file_backlinks` where `fileId`=?';
			$this->query($query, array((int)$fileId));
		}
	}
	// get the backlinks of an object
	function getFileBacklinks($fileId, $sort='type_asc') {
		$query = 'select tob.* from `tiki_file_backlinks` tfb left join `tiki_objects` tob on (tob.`objectId`=tfb.`objectId`) where `fileId`=? ';
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
			$debug=1;
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
				if ($fileId = $this->getLinkFileId($match[1])) {
					$fileIds[] = $fileId;
				}
			}
		}
		if (preg_match_all('/<a[^>]*href=(\'|\")?([^>*])/Umi', $data, $matches)) {
			foreach ($matches as $match) {
				if ($fileId = $this->getLinkFileId($match[2])) {
					$fileIds[] = $fileId;
				}
			}
		}
		$fileIds = array_unique($fileIds);
		if (!empty($fileIds)) {echo '<pre>'; print_r($context); print_r($fileIds); echo '</pre>';}
		//$this->replaceBacklinks($context, $fileIds);
		return $fileIds;
	}
	function getLinkFileId($url) {
		if (preg_match('/^tiki-download_file.php\?.*fileId=([0-9]+)/', $url, $matches)) {
			return $matches[1];
		}
		if (preg_match('/^(dl|preview|thumbnail|thumb||display)([0-9]+)/', $url, $matches)) {
			return $matches[2];
		}
	}
	function refreshBacklinks() {
		$query = 'select `data`, `description`, `pageName` from `tiki_pages`';
		$result = $this->query($query, array());
		while ($res = $result->fetchRow()) {
			$this->syncParsedText($res['data'], array('type'=> 'wiki page', 'object'=> $res['pageName'], 'description'=> $res['description'], 'name'=>$res['pageName'], 'href'=>'tiki-index.php?page='.$res['pageName']));
		}

		$query = 'select `heading`, `body`, `articleId`, `title` from `tiki_articles`';
		$ret = $this->query($query, array());
		while ($res = $ret->fetchRow()) {
			$this->syncParsedText($res['body'].' '.$res['heading'], array('type'=>'article', 'object'=>$res['articleId'], 'description'=>substr($res['heading'], 0, 200), 'name'=>$res['title'], 'href'=>'tiki-read_article.php?articleId='.$res['articleId']));
		}
		$query = 'select `heading`, `body`, `subId`, `title` from `tiki_submissions`';
		$result = $this->query($query, array());
		while ($res = $result->fetchRow()) {
			$this->syncParsedText($res['heading'].' '.$res['body'], array('type'=>'submission', 'object'=>$res['subId'], 'description'=>substr($res['heading'], 0, 200), 'name'=>$res['title'], 'href'=>'tiki-edit_submission.php?subId='.$res['subId']));
		}
		/* history are ignored in the backlinks process */

		$query = 'select `blogId`, `heading`, `description`, `title` from `tiki_blogs`';
		$result = $this->query($query, array());
		while ($res = $result->fetchRow()) {
			$this->syncParsedText($res['heading'], array('type'=>'blog', 'object'=>$res['blogId'], 'description'=>$res['description'], 'name'=>$res['title'], 'href'=>'tiki-view_blog.php?blogId='.$res['blogId']));
		}
		$query = 'select `blogId`, `data`, `postId`, `title`  from `tiki_blog_posts`';
		$result = $this->query($query, array());
		while ($res = $result->fetchRow()) {
			$this->syncParsedText($res['data'], array('type'=>'blog post', 'object'=>$res['postId'], 'description'=>substr($res['data'], 0, 200), 'name'=>$res['title'], 'href'=>'tiki-view_blog_post.php?postId='.$res['postId']));
		}

		$query = 'select `objectType`, `object`, `threadId`,`title`, `data` from `tiki_comments`';
		$result = $this->query($query, array());
		include_once ('lib/commentslib.php');global $dbTiki; $commentslib = new Comments($dbTiki);
		while ($res = $result->fetchRow()) {
			if ($res['objectType'] == 'forum') {
				$type = 'forum post';
			} else {
				$type = $res['objectType'].' comment';
			}
			$this->syncParsedText($res['data'], array('type'=>$type, 'object'=>$res['threadId'], 'description'=>'', 'name'=>$res['title'], 'href'=>$commentslib->getHref($res['objectType'], $res['object'], $res['threadId'])));
		}

		$query = 'select `description`, `name`, `trackerId` from `tiki_trackers` where `descriptionIsParsed`=?';
		$result = $this->query($query, array('y'));
		while ($res = $result->fetchRow()) {
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
}
$filegallib = new FileGalLib;
