<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/filegals/filegallib.php,v 1.57 2006-12-15 21:57:54 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class FileGalLib extends TikiLib {
	function FileGalLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to FilegalLib constructor");
		}

		$this->db = $db;
	}

	function isPodCastGallery($galleryId, $gal_info=null) {
		if (empty($gal_info))
			$gal_info = $this->get_file_gallery_info((int)$galleryId);
		if (($gal_info["type"]=="podcast") || ($gal_info["type"]=="vidcast")) {
			return true;
		} else {
			return false;
		}
	}

	function remove_file($fileInfo, $user, $galInfo='') {
		global $fgal_use_dir, $fgal_podcast_dir, $smarty;

		if ($podCastGallery = $this->isPodCastGallery($galleryId, $galInfo)) {
			$savedir=$fgal_podcast_dir;
		} else {
			$savedir=$fgal_use_dir;
		}
		
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
		$this->notify($fileInfo['galleryId'], $fileInfo['name'], $fileInfo['filename'], '', 'remove file', $user);
		return true;
	}

	function insert_file($galleryId, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='', $author, $created='', $lockedby=NULL) {
	  global $fgal_use_db, $fgal_use_dir, $fgal_podcast_dir, $tikilib, $fgal_allow_duplicates, $smarty, $user;

		$name = strip_tags($name);

		if ($podCastGallery = $this->isPodCastGallery($galleryId)) {
			$savedir=$fgal_podcast_dir;
		} else {
			$savedir=$fgal_use_dir;
		}

		if (($fgal_use_db == 'n') || ($podCastGallery)) {
			if (function_exists('md5_file')) {
				$checksum = md5_file($savedir . $path);
			} else {
				$checksum = md5(implode('', file($savedir . $path)));
			}
		} else {
			$checksum = md5($data);
		}

		$description = strip_tags($description);
		$now = date("U");

		if ($fgal_allow_duplicates != 'y' &&
		    $this->getOne("select count(*) from `tiki_files` where `hash`=?",array($checksum)))
			return false;

		$search_data = '';
		if ($tikilib->get_preference('fgal_enable_auto_indexing','y') != 'n') {
			$search_data = $this->get_search_text_for_data($data,$path,$type, $galleryId);
			if ($search_data === false)
				return false;
		}
		if (empty($created))
			$created = $now;
		
		$query = "insert into `tiki_files`(`galleryId`,`name`,`description`,`filename`,`filesize`,`filetype`,`data`,`user`,`created`,`downloads`,`path`,`hash`,`search_data`,`lastModif`,`lastModifUser`, `comment`, `author`, `lockedby`)
                          values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array($galleryId,$name,$description,$filename,$size,$type,$data,$creator,$created,0,$path,$checksum,$search_data,(int)$now,$user,$comment, $author, $lockedby));
		$query = "update `tiki_file_galleries` set `lastModif`=? where `galleryId`=?";
		$result = $this->query($query,array((int) $now,$galleryId));
		$query = "select max(`fileId`) from `tiki_files` where `created`=?";
		$fileId = $this->getOne($query,array((int) $created));

		global $feature_score;
		if ($feature_score == 'y') {
		    $this->score_event($user, 'fgallery_new_file');
		}
		global $feature_actionlog;
		if ($feature_actionlog == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Uploaded', $galleryId, 'file gallery', "fileId=$fileId&amp;add=$size");
		}

		//Watches
		$smarty->assign('galleryId', $galleryId);
                $smarty->assign('fname', $name);
                $smarty->assign('filename', $filename);
                $smarty->assign('fdescription', $description);

		$this->notify($galleryId, $name, $filename, $description, 'upload file', $user);

		return $fileId;
	}

	function list_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find='', $parentId=0) {
		global $tiki_p_admin_file_galleries;

		// If $user is admin then get ALL galleries, if not only user galleries are shown
		$old_sort_mode = '';

		if (in_array($sort_mode, array(
			'files desc',
			'files asc'
		))) {
			$old_offset = $offset;

			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user_desc';
			$offset = 0;
			$maxRecords = -1;
		}

		// If the user is not admin then select it's own galleries or public galleries
		if (($tiki_p_admin_file_galleries == 'y') or ($user == 'admin')) {
			$whuser = "";
			$bindvars=array();
		} elseif (!$parentId) {
			$whuser = "where tfg.`user`=? or tfg.`public`=?";
			$bindvars=array($user,'y');
		}

		if ($find) {
			$find = '%' . $find . '%';

			if (empty($whuser)) {
				$whuser = "where tfg.`name` like ? or tfg.`description` like ?";
				$bindvars=array($find,$find);
			} else {
				$whuser .= " and tfg.`name` like ? or tfg.`description` like ?";
				$bindvars[]=$find;
				$bindvars[]=$find;
			}
		}
		if ($parentId) {
			$whuser .= empty($whuser)? 'where ':' and ';
			$whuser .= 'tfg.`parentId` = ?';
			$bindvars[] = $parentId;
		}

		$query = "select tfg.*, tfgp.`name` as `parentName`
			from `tiki_file_galleries` tfg
			left join `tiki_file_galleries` tfgp on (tfg.`parentId` = tfgp.`galleryId`)
			$whuser
			order by tfg.".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_file_galleries` tfg $whuser";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {

		    $add = TRUE;
		    global $feature_categories;
		    global $userlib;
		    global $user;
		    global $tiki_p_admin;

		    if ($tiki_p_admin != 'y' && $userlib->object_has_one_permission($res['galleryId'], 'file gallery')) {
		    // gallery permissions override category permissions
				if (!$userlib->object_has_permission($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				    $add = FALSE;
				}
		    } elseif ($tiki_p_admin != 'y' && $feature_categories == 'y') {
		    	// no forum permissions so now we check category permissions
		    	global $categlib;
				if (!is_object($categlib)) {
					include_once('lib/categories/categlib.php');
				}
		    	unset($tiki_p_view_categories); // unset this var in case it was set previously
		    	$perms_array = $categlib->get_object_categories_perms($user, 'file gallery', $res['galleryId']);
		    	if ($perms_array) {
		    		$is_categorized = TRUE;
			    	foreach ($perms_array as $perm => $value) {
			    		$$perm = $value;
			    	}
		    	} else {
		    		$is_categorized = FALSE;
		    	}

		    	if ($is_categorized && isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y') {
		    		$add = FALSE;
		    	}
		    }

			if ($add) {
				$aux = array();

				$aux["name"] = $res["name"];
				$gid = $res["galleryId"];
				$aux["id"] = $gid;
				$aux["visible"] = $res["visible"];
				$aux["galleryId"] = $res["galleryId"];
				$aux["description"] = $res["description"];
				$aux["created"] = $res["created"];
				$aux["lastModif"] = $res["lastModif"];
				$aux["user"] = $res["user"];
				$aux["hits"] = $res["hits"];
				$aux["public"] = $res["public"];
				$aux["type"] = $res["type"];
				$aux['parentId'] = $res['parentId'];
				$aux['parentName'] = $res['parentName'];
// Only get the file count when necessary. Otherwise there are many excess db queries. GG
				if ($maxRecords > -1) {
				$aux["files"] = $this->getOne("select count(*) from `tiki_files` where `galleryId`=?",array($gid));
				}

				$ret[] = $aux;
			}
		}

		if ($old_sort_mode == 'files_asc') {
			usort($ret, 'compare_files');
		}

		if ($old_sort_mode == 'files_desc') {
			usort($ret, 'r_compare_files');
		}

		if (in_array($old_sort_mode, array(
			'files_desc',
			'files_asc'
		))) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function set_file_gallery($file, $gallery) {
		$query = "update `tiki_files` set `galleryId`=? where `fileId`=?";

		$this->query($query,array($gallery,$file));
	}

	function remove_file_gallery($id, $galleryId=0) {
		global $fgal_use_dir, $fgal_podcast_dir;

		if (empty($galleryId)) {
			$info = $this->get_file_info($id);
			$galleryId = $info['galleryId'];
		}

		if ($podCastGallery = $this->isPodCastGallery($galleryId)) {
			$savedir=$fgal_podcast_dir;
		} else {
			$savedir=$fgal_use_dir;
		}

		$query = "select `path` from `tiki_files` where `galleryId`=?";
		$result = $this->query($query,array($id));

		while ($res = $result->fetchRow()) {
			$path = $res["path"];

			if ($path) {
				@unlink ($savedir . $path);
			}
		}

		$query = "delete from `tiki_file_galleries` where `galleryId`=?";
		$result = $this->query($query,array($id));
		$query = "delete from `tiki_files` where `galleryId`=?";
		$result = $this->query($query,array($id));
		$this->remove_object('file gallery', $id);
		return true;
	}

	function get_file_gallery_info($id) {
		$query = "select * from `tiki_file_galleries` where `galleryId`=?";

		$result = $this->query($query,array((int) $id));
		$res = $result->fetchRow();
		return $res;
	}

	function replace_file_gallery($galleryId, $name, $description, $user, $maxRows, $public, $visible = 'y', $show_id, $show_icon,
								  $show_name, $show_size, $show_description, $show_created, $show_dl, $max_desc, $fgal_type='default', $parentId=-1, $lockable='n', $show_lockedby='y', $archives=-1, $sort_mode='', $show_modified='n', $show_creator='y', $show_author='n') {
		// if the user is admin or the user is the same user and the gallery exists then replace if not then
		// create the gallary if the name is unused.
		$name = strip_tags($name);

		$description = strip_tags($description);
		$now = date("U");
		if ($sort_mode == 'created_desc') {
			$sort_mode = null;
		}

		if ($galleryId > 0) {
			$query = "update `tiki_file_galleries` set `name`=?, `maxRows`=?, `description`=?,`lastModif`=?, `public`=?, `visible`=?,`show_icon`=?,`show_id`=?,`show_name`=?,`show_description`=?,`show_size`=?,`show_created`=?,`show_dl`=?,`max_desc`=?,`type`=?,`parentId`=?,`user`=?,`lockable`=?,`show_lockedby`=?, `archives`=?, `sort_mode`=?, `show_modified`=?, `show_creator`=?, `show_author`=? where `galleryId`=?";
			$bindvars=array($name,(int) $maxRows,$description,(int) $now,$public,$visible,$show_icon,$show_id,$show_name,$show_description,$show_size,$show_created,$show_dl,(int) $max_desc, $fgal_type, $parentId, $user, $lockable, $show_lockedby, $archives, $sort_mode, $show_modified, $show_creator, $show_author,(int)$galleryId);

			$result = $this->query($query,$bindvars);

			$query = "update `tiki_objects` set `name`=?, `description`=? where `type`=? and `itemId`=?";
			$bindvars = array($name,$description,'file gallery',(int)$galleryId);
			$this->query($query,$bindvars);
		} else {
			// Create a new record
			$query = "insert into `tiki_file_galleries`(`name`,`description`,`created`,`user`,`lastModif`,`maxRows`,`public`,`hits`,`visible`,`show_id`,`show_icon`,`show_name`,`show_description`,`show_created`,`show_dl`,`max_desc`,`type`, `parentId`, `lockable`, `show_lockedby`, `archives`, `sort_mode`, `show_modified`, `show_creator`, `show_author`)
                                    values (?,?,?,?,?,?,?,?,?,
                                    ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($name,$description,(int) $now,$user,(int) $now,(int) $maxRows,$public,0,$visible,
							$show_id,$show_icon,$show_name,$show_description,$show_created,$show_dl,(int) $max_desc, $fgal_type, $parentId, $lockable, $show_lockedby, $archives, $sort_mode, $show_modified, $show_creator, $show_author);

			$result = $this->query($query,$bindvars);
			$galleryId
				= $this->getOne("select max(`galleryId`) from `tiki_file_galleries` where `name`=? and `lastModif`=?",array($name,(int) $now));
			global $feature_score;
			if ($feature_score == 'y') {
			    $this->score_event($user, 'fgallery_new');
			}
		}

		return $galleryId;
	}

	function process_batch_file_upload($galleryId, $file, $user, $description) {
		global $fgal_match_regex;

		global $fgal_nmatch_regex;
		global $fgal_use_db;
		global $fgal_use_dir;
		global $fgal_podcast_dir;
		include_once ('lib/pclzip.lib.php');
		include_once ('lib/mime/mimelib.php');
		$archive = new PclZip($file);
		$archive->extract('temp');
		$files = array();
		$h = opendir("temp");
		$gal_info = $this->get_file_gallery_info($galleryId);
		if ($podCastGallery = $this->isPodCastGallery($galleryId, $gal_info)) {
			$savedir=$fgal_podcast_dir;
		} else {
			$savedir=$fgal_use_dir;
		}

		while (($file = readdir($h)) !== false) {
			if ($file != '.' && $file != '..' && is_file("temp/$file") && $file != 'license.txt') {
				$files[] = $file;

				// check filters
				$upl = 1;

				if (!empty($fgal_match_regex)) {
					if (!preg_match("/$fgal_match_regex/", $file, $reqs))
						$upl = 0;
				}

				if (!empty($fgal_nmatch_regex)) {
					if (preg_match("/$fgal_nmatch_regex/", $file, $reqs))
						$upl = 0;
				}

				$fp = fopen('temp/' . $file, "rb");
				$data = '';
				$fhash = '';

				if (($fgal_use_db == 'n') || ($podCastGallery)) {
					$fhash = md5($name = $file);

					@$fw = fopen($savedir . $fhash, "wb");

					if (!$fw) {
						$smarty->assign('msg', tra('Cannot write to this file:'). $fhash);

						$smarty->display("error.tpl");
						die;
					}
				}

				while (!feof($fp)) {
					if (($fgal_use_db == 'y') && (!$podCastGallery)) {
						$data .= fread($fp, 8192 * 16);
					} else {
						$data = fread($fp, 8192 * 16);

						fwrite($fw, $data);
					}
				}

				fclose ($fp);

				if (($fgal_use_db == 'n') || ($podCastGallery)) {
					fclose ($fw);

					$data = '';
				}

				$size = filesize('temp/' . $file);
				$name = $file;
				$type = tiki_get_mime('temp/' . $file);
				$fileId = $this->insert_file($galleryId, $name, $description, $name, $data, $size, $type, $user, $fhash);
				unlink ('temp/' . $file);
			}
		}

		closedir ($h);
	}

	// Added by LeChuck, May 2, 2003
	function get_file_info($id) {
		$query = "select * from `tiki_files` where `fileId`=?";

		$result = $this->query($query,array($id));
		$res = $result->fetchRow();
		return $res;
	}

	function update_file($id, $name, $description,$user) {

		// Update the fields in the database
		$name = strip_tags($name);

		$now = date("U");

		$description = strip_tags($description);
		$query = "update `tiki_files` set `name`=?, `description`=?, `lastModif`=?, `lastModifUser`=? where `fileId`=?";
		$result = $this->query($query,array($name,$description,(int)$now,$user,$id));

		// Get the gallery id for the file and update the last modified field
		$now = date("U");
		$galleryId = $this->getOne("select `galleryId` from `tiki_files` where `fileId`=?",array($id));

		if ($galleryId) {
			$query = "update `tiki_file_galleries` set `lastModif`=? where `galleryId`=?";

			$this->query($query,array($now,$galleryId));
		}

		return $result;
	}

	function replace_file($id, $name, $description, $filename, $data, $size, $type, $creator, $path, $comment='', $gal_info, $didFileReplace, $author='', $created='', $lockedby=NULL) {
	  global $fgal_use_db, $fgal_use_dir, $fgal_podcast_dir, $tikilib, $feature_categories, $feature_search, $user;

		// Update the fields in the database
		$name = strip_tags($name);

		if ($podCastGallery = $this->isPodCastGallery($gal_info['galleryId'], $gal_info)) {
			$savedir=$fgal_podcast_dir;
		} else {
			$savedir=$fgal_use_dir;
		}

		if (($fgal_use_db == 'n') || ($podCastGallery)) {
			if (function_exists('md5_file')) {
				$checksum = md5_file($savedir . $path);
			} else {
				$checksum = md5(implode('', file($savedir . $path)));
			}
		} else {
			$checksum = md5($data);
		}

		$description = strip_tags($description);
		$now = date("U");

		$search_data = '';
		if ($tikilib->get_preference("fgal_enable_auto_indexing") != 'n') {
			$search_data = $this->get_search_text_for_data($data,$path,$type, $gal_info['galleryId']);
			if ($search_data === false)
				return false;
		}
		$oldPath = $this->getOne("select `path` from `tiki_files` where `fileId`=?",array($id));

		if ($gal_info['archives'] == -1 || !$didFileReplace) { // no archive
			$query = "update `tiki_files` set `name`=?, `description`=?, `filename`=?, `filesize`=?, `filetype`=?, `data`=?, `lastModifUser`=?, `lastModif`=?, `path`=?, `hash`=?, `search_data`=?, `author`=?, `user`=?, `lockedby`=?  where `fileId`=?";
			if (!($result = $this->query($query,array($name,$description,$filename,$size,$type,$data,$user,(int)$now,$path,$checksum,$search_data,$author,$creator,$lockedby, $id))))
				return false;
			
			if ($didFileReplace && !empty($oldPath)) {
				unlink($savedir . $oldPath);
			}
		} else { //archive the old file : change archive_id, take away from indexation and categorization
		  $idNew = $this->insert_file($gal_info['galleryId'], $name, $description, $filename, $data, $size, $type, $creator, $path, $comment, $author, $created, $lockedby);
			if ($gal_info['archives'] > 0) {
				$archives = $this->get_archives($id, 0, -1, 'created_asc');
				if ($archives['cant'] >= $gal_info['archives']) {
					$nb = $archives['cant'] - $gal_info['archives'] + 1;
					$query = "delete from `tiki_files` where `fileId`in (".implode(',', array_fill(0, $nb, '?')).")";
					for ($i = 0; $i < $nb; ++$i) {
						$bindvars[] = $archives['data'][$i]['fileId'];
						if ($archives['data'][$i]['path'])
							unlink ($savedir . $archives['data'][$i]['path']);
					}
					$this->query($query, $bindvars);
				}
			}
			$query = "update `tiki_files` set `archiveId`=?, `search_data`=?,`user`=?, `lockedby`=? where `archiveId`=? or `fileId`=?";
			$this->query($query,array($idNew, '',$creator,NULL, $id, $id));
			if ($feature_categories == 'y') {
				global $categlib; require_once('lib/categories/categlib.php');
				$categlib->uncategorize_object('file', $id);
			}
			if ($feature_search == 'y') {
				include_once('lib/search/refresh-functions.php');
				$words = array();
				insert_index($words, 'file', $id);
			}
			$id = $idNew;
		}		

		if ($gal_info['galleryId']) {
			$query = "update `tiki_file_galleries` set `lastModif`=? where `galleryId`=?";

			$this->query($query,array($now,$gal_info['galleryId']));
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
		if ($mime_type == 'default')
			return false;
			
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
		refresh_index_files();
	}

	function get_search_text_for_data($data,$path,$type, $galleryId) {
		global $fgal_use_dir, $fgal_podcast_dir;
		
		if (!isset($data) && !isset($path)) {
			return false;
		}

		if ($podCastGallery = $this->isPodCastGallery($galleryId)) {
			$savedir=$fgal_podcast_dir;
		} else {
			$savedir=$fgal_use_dir;
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

	function convert_error_to_string($error) {
		switch($error) {
		case 0: //no error; possible file attack!
			return tra("There was a problem with your upload.");
		case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
		case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			return tra("The file you are trying to upload is too big.");
		case 3: //uploaded file was only partially uploaded
			return tra("The file you are trying to upload was only partially uploaded.");
		case 4: //no file was uploaded
			return tra("You must select a file for upload.");
		default: //a default error, just in case!  :)
			return tra("There was a problem with your upload.");
		}
	}

	function notify ($galleryId, $name, $filename, $description, $action, $user) {
		global $feature_user_watches;
                if ($feature_user_watches == 'y') {
                        //  Deal with mail notifications.
                        include_once('lib/notifications/notificationemaillib.php');
                        $foo = parse_url($_SERVER["REQUEST_URI"]);
                        $machine = $this->httpPrefix(). dirname( $foo["path"]);
			$galleryName = $this->getOne("select `name` from `tiki_file_galleries` where `galleryId`=?",array($galleryId));

                        sendFileGalleryEmailNotification('file_gallery_changed', $galleryId, $galleryName, $name, $filename, $description, $action, $user);
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
		$mid = array();
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid[] = "(upper(`name`) like upper(?) or upper(`filename`) like upper(?) or upper(`description`) like upper(?) or upper(`comment`) like upper(?))";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}		
		$mid[] = '`archiveId`=?';
		$bindvars[] = (int)$fileId;

		$mid = implode(' AND ', $mid);
		$query = "select * from `tiki_files` where $mid
			order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$query_cant = "select count(*) from `tiki_files` where $mid";
		$cant = $this->getOne($query_cant, $bindvars);
		return array('cant'=>$cant, 'data'=>$ret);
	}
}
global $dbTiki;
$filegallib = new FileGalLib($dbTiki);

?>
