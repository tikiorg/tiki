<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/imagegals/imagegallib.php,v 1.97.2.4 2008-03-06 19:45:42 sampaioprimo Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class VideoGalsLib extends TikiLib {
	function VideoGalsLib($db) {
		global $prefs;

		$this->TikiLib($db);
		$exts=get_loaded_extensions();

		// Which GD Version do we have?
		if (in_array('gd',$exts)) {
			$this->havegd = true;

			if (function_exists("gd_info")) {
				$this->gdinfo = gd_info();

				preg_match("/[0-9]+\.[0-9]+/", $this->gdinfo["GD Version"], $gdversiontmp);
				$this->gdversion = $gdversiontmp[0];
			} else {
				//next try
				ob_start();

				phpinfo (INFO_MODULES);

				if (preg_match('/GD Version.*2.0/', ob_get_contents())) {
					$this->gdversion = "2.0";
				} else {
					// I have no experience ... maybe someone knows better
					$this->gdversion = "1.0";
				}

				$this->gdinfo["JPG Support"] = preg_match('/JPG Support.*enabled/', ob_get_contents());
				$this->gdinfo["PNG Support"] = preg_match('/PNG Support.*enabled/', ob_get_contents());
				$this->gdinfo["GIF Create Support"] = preg_match('/GIF Create Support.*enabled/', ob_get_contents());
				$this->gdinfo["WBMP Support"] = preg_match('/WBMP Support.*enabled/', ob_get_contents());
				$this->gdinfo["XBM Support"] = preg_match('/XBM Support.*enabled/', ob_get_contents());
				ob_end_clean();
			}
		} else {
			$this->havegd = false;
		}

		// Do we have the imagick PECL module?
		// Module can be downloaded at http://pecl.php.net/package/imagick
		// Also check on 'imagick_rotate' function because the first check may detect imagick 2.x which has a completely different API
		if ( in_array('imagick',$exts) && function_exists('imagick_rotate') ) {
			$this->haveimagick = true;
		} else {
			$this->haveimagick = false;
		}

		//what shall we use?

		//$this->uselib = "gd";
		$this->uselib = $prefs['gal_use_lib'];

		//Fallback to GD
		if ($this->uselib == "imagick" && $this->haveimagick == false) {
			$this->uselib = "gd";
			$this->set_preference('gal_use_lib', 'gd');
		}

		if ($this->uselib == "imagick") {
			$this->canrotate = true;
		} else {
			$this->canrotate = false;
		}


		// get variables to determine if we can upload and how many data
		// we can upload
		$this->file_uploads=ini_get('file_uploads');
		$this->upload_max_filesize=ini_get('upload_max_filesize');
		$this->post_max_size=ini_get('post_max_size');
		if($this->file_uploads==0) {
		   $this->max_img_upload_size=0;
		} else {

		   }

	}

	function max_img_upload_size() {
	   $this->upload_max_filesize=$this->return_bytes($this->upload_max_filesize);
	   $this->post_max_size=$this->return_bytes($this->post_max_size);
	   if($this->file_uploads==0) {
	      return(0);
	   } else {
	      return(($this->post_max_size > $this->upload_max_filesize) ? $this->post_max_size : $this->upload_max_filesize);
	   }
	}



	// from php manual. one of the rare circumstances where
	// a break in the switch-case is not needed
        function return_bytes($val) {
          $val = trim($val);
          $last = strtolower($val{strlen($val)-1});
          switch($last) {

          // The 'G' modifier is available since PHP 5.1.0
            case 'g':
              $val *= 1024;
            case 'm':
              $val *= 1024;
            case 'k':
              $val *= 1024;
          }

          return $val;
        }


	// Wrappers
	//
	function validhandle() {
		if (isset($this->imagehandle)) {
			if ($this->uselib == "imagick") {
				// seems not to work in imagick 0.95 aahrgh
				if (imagick_iserror($this->imagehandle))
					return false;

				return true;
			} else if ($this->uselib == "gd") {
				if (imagesx($this->imagehandle) == 0)
					return false;

				return true;
			}
		}

		return false;
	}

	function add_gallery_hit($id) {
		global $prefs, $user;


		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_galleries_video` set `hits`=`hits`+1 where `galleryId`=?";

			$result = $this->query($query,array((int) $id));
		}

		if ($prefs['feature_score'] == 'y') {
		    $this->score_event($user, 'igallery_see', $id);
		    $query = "select `user` from `tiki_galleries_video` where `galleryId`=?";
		    $owner = $this->getOne($query, array((int)$id));
		    $this->score_event($owner, 'igallery_seen', "$user:$id");
		}

		return true;
	}

	function edit_image($id, $name, $description, $lat=NULL, $lon=NULL, $file=NULL) {
		global $prefs;
		$name = strip_tags($name);

		$description = strip_tags($description);
		$query = "update `tiki_videos` set `name`=?, `description`=?, `lat`=?, `lon`=? where `imageId` = ?";
		$result = $this->query($query,array($name,$description,$lat,$lon,(int)$id));
		if (!empty($file) && !empty($file['name'])) {
			if (!is_uploaded_file($file['tmp_name']) || !($fp = fopen($file['tmp_name'], "rb")))
				return false;
			$data =  fread($fp, $file['size']);
			$etag = md5($data);
			fclose($fp);
			if ($prefs['gal_use_db'] == 'y') {
				$query = "update `tiki_videos_data` set `data`=?, `etag`=?, `filename`=? where `imageId` = ? and `type`=?";
				$result = $this->query($query,array($data, $etag, $file['name'], (int)$id, 'o'));
			} else {
				$query = "select `path` from `tiki_videos` where `imageId`=?";
				$path = $this->getOne($query, $id);
				if (!move_uploaded_file($file['tmp_name'], $prefs['gal_use_dir'].$path)) {
					return false;
				}
				$query = "update `tiki_videos_data` set `etag`=?,`filename`=? where `imageId` = ? and `type`=?";
				$result = $this->query($query,array($etag, $file['name'], (int)$id, 'o'));
			}
			$query = "delete from `tiki_videos_data` where `imageId`=? and `type`!=?";
			$result = $this->query($query, array((int)$id, 'o'));
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('images', $id);
		}

		return true;
	}

	function insert_image($galleryId, $name, $description, $filename, $filetype, &$data, $size, $xsize, $ysize, $user, $t_data, $t_type ,$lat=NULL, $lon=NULL, $gal_info=NULL) {
		global $prefs;

		$name = strip_tags($name);
		$description = strip_tags($description);
		if ($t_data && is_string($t_data)) {
			$t_data = array('data' => $t_data, 'xsize' => 0, 'ysize' => 0);
		}
		$path = '';

		if ($prefs['gal_use_db'] == 'y') {
			// Prepare to store data in database
		} else {
			// Store data in directory
			$fhash = md5(uniqid($filename));
			if (!($fw = fopen($prefs['gal_use_dir'] . $fhash, "wb"))) {
				return false;
			}
			fwrite($fw, $data);
			fclose ($fw);
			$data = '';

			if ($t_data) {
				if (!($fw = fopen($prefs['gal_use_dir'] . $fhash . '.thumb', "wb"))) {
					return false;
				}
				fwrite($fw, $t_data['data']);
				fclose ($fw);
				$t_data['data'] = '';
			}
			$path = $fhash;
		}

		$query = "insert into `tiki_videos`(`galleryId`,`name`,`description`,`user`,`created`,`hits`,`path`,`lat`,`lon`)
                          values(?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int)$galleryId,$name,$description,$user,(int)$this->now,0,$path,$lat,$lon));
		$query = "select max(`imageId`) from `tiki_videos` where `created`=?";
		$imageId = $this->getOne($query,array((int)$this->now));
		// insert data
		$this->blob_encode($data);
		$query = "insert into `tiki_videos_data`(`imageId`,`xsize`,`ysize`, `type`,`filesize`,`filetype`,`filename`,`data`)
                        values (?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int)$imageId,(int)$xsize,(int)$ysize,'o',(int)$size,$filetype,$filename,$data));

		// insert thumb
		if ($t_data) {
			$this->blob_encode($t_data['data']);
			$query = "insert into `tiki_videos_data`(`imageId`,`xsize`,`ysize`, `type`,`filesize`,`filetype`,`filename`,`data`)
                        values (?,?,?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$imageId,(int)$t_data['xsize'],(int)$t_data['ysize'],'t',(int)$size,$t_type,$filename,$t_data['data']));
		}

		$query = "update `tiki_galleries_video` set `lastModif`=? where `galleryId`=?";
		$result = $this->query($query,array((int)$this->now,(int)$galleryId));

		if ($prefs['feature_score'] == 'y') {
		    $this->score_event($user, 'igallery_new_img');
		}

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Uploaded', $galleryId, 'image gallery', 'imageId='.$imageId);
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('images', $imageId);
		}

		$this->notify($imageId, $galleryId, $name, $filename, $description, isset($gal_info['name'])?$gal_info['name']: '', $user);

		return $imageId;
	}

	function notify($imageId, $galleryId, $name, $filename, $description, $galleryName, $user) {
		global $prefs;
		if ($prefs['feature_user_watches'] == 'y') {
			include_once('lib/notifications/notificationemaillib.php');
			global $smarty, $tikilib;
			$nots = $this->get_event_watches('image_gallery_changed', $galleryId);
			$smarty->assign_by_ref('galleryId', $galleryId);
			$smarty->assign_by_ref('galleryName', $galleryName);
			$smarty->assign_by_ref('mail_date', date('U'));
			$smarty->assign_by_ref('author', $user);
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $tikilib->httpPrefix(). dirname( $foo["path"] );
			$smarty->assign_by_ref('mail_machine', $machine);
			$smarty->assign_by_ref('fname', $name);
			$smarty->assign_by_ref('filename', $filename);
			$smarty->assign_by_ref('description', $description);
			$smarty->assign_by_ref('imageId', $imageId);
			sendEmailNotification($nots, 'watch', 'user_watch_image_gallery_changed_subject.tpl', NULL, 'user_watch_image_gallery_upload.tpl');
		}
	}

	function remove_image($id) {
		global $prefs;

		$path = $this->getOne("select `path` from `tiki_videos` where `imageId`=?",array($id));

		if ($path) {
			@unlink ($prefs['gal_use_dir'] . $path);

			@unlink ($prefs['gal_use_dir'] . $path . '.thumb');
		  // remove scaled images
		  $query = "select i.`path`, d.`xsize`, d.`ysize` from `tiki_videos` i, `tiki_videos_data` d where i.`imageId`=d.`imageId` and i.`imageId`=? and d.`type`=?";
		  $result=$this->query($query,array($id,'s'));
		  while($res = $result->fetchRow()) {
                    @unlink ($prefs['gal_use_dir'] . $path . '.scaled_'.$res['xsize'].'x'.$res['ysize']);
		  }
		}

		$query = "delete from `tiki_videos` where `imageId`=?";
		$result = $this->query($query,array((int)$id));
		$query = "delete from `tiki_videos_data` where `imageId`=?";
		$result = $this->query($query,array((int)$id));
		$this->remove_object('image', $id);
		return true;
	}

	function get_videos($offset, $maxRecords, $sort_mode, $find, $galleryId = -1) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		if ($galleryId != -1 && is_numeric($galleryId)) {
			$mid .= " and i.`galleryId`=? ";
			$bindvars[]=(int) $galleryId;
		} else if ($galleryId == -1) {//don't show system gallery
			$mid .= 'and i.`galleryId`!=? ';
			$bindvars[] = 0;
		}
		$query_cant = "select count(*) from `tiki_videos` i  where 1 $mid";
		$cant = $this->getOne($query_cant, $bindvars);


		$query = "select i.`videoId` ,i.`entryId`,i.`user` from `tiki_videos` i where i.`galleryId`=? ";
		$bindvars[] = 'o';
		$result = $this->query($query,array((int)$galleryId,$bindvars,$maxRecords,$offset));
		$ret = array();

		while ($res = $result->fetchRow()) {

			$ret[] = $res;

		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

        function get_subgalleries($offset, $maxRecords, $sort_mode, $find, $galleryId = -1) {

		if ($sort_mode == '')
			$sort_mode = 'name_asc';
		else		//filesize is for listing images. equivalent is images
			$sort_mode=preg_replace('/(filesize_)/','videos_',$sort_mode);

                if ($find) {
                        $findesc = '%' . $find . '%';

                        $mid = " and (`name` like ? or `description` like ?)";
                        $bindvars=array($galleryId,$findesc,$findesc);
                } else {
                        $mid = "";
                        $bindvars=array($galleryId);
                }

                $query = "select g.`galleryId`,g.`name`,g.`description`,
	       		g.`created`,g.`lastModif`,g.`visible`,g.`theme`,g.`user`,
       			g.`hits`,g.`maxRows`,g.`rowImages`,g.`thumbSizeX`,
	 		g.`thumbSizeY`,g.`public`,g.`sortorder`,g.`sortdirection`,
			g.`parentgallery`,count(i.`videoId`) as videos
			from `tiki_galleries_video` g, `tiki_videos` i
			where i.`galleryId`=g.`galleryId` and
                 	`parentgallery`=? $mid group by
			g.`galleryId`, g.`name`,g.`description`,
			g.`created`,g.`lastModif`,g.`visible`,g.`theme`,g.`user`,
			g.`hits`,g.`maxRows`,g.`rowImages`,g.`thumbSizeX`,
			g.`thumbSizeY`,g.`public`,g.`sortorder`,g.`sortdirection`,
			g.`parentgallery`
                order by ".$this->convert_sortmode($sort_mode);

                $result = $this->query($query,$bindvars,$maxRecords,$offset);
                $ret = array();

                while ($res = $result->fetchRow()) {
			// get the number of the gallery representation image
			//$res['videoId']=$this->get_gallery_image($res['galleryId'],$res['galleryimage']);
                        $ret[] = $res;
                }

                $retval = array();
                $retval["data"] = $ret;
                $query_cant = "select count(*) from `tiki_galleries_video` where `parentgallery`=? $mid";
                $cant = $this->getOne($query_cant,$bindvars);
                $retval["cant"] = $cant;
                return $retval;
        }

	function get_gallery_videos($galleryId,$rule='',$sort_mode = '') {
		$query='select i.`videoId` from `tiki_videos` i
                 where i.`galleryId`=?';

   print "Code for Sorting the list not completed";

	if (!$sort_mode) {
    				// first image in default gallery sortorder
    	$query2='select `sortorder`,`sortdirection` from `tiki_galleries_video` where `galleryId`=?';
    	$result=$this->query($query2,(int)$galleryId);
    	$res = $result->fetchRow();
    	$sort_mode=$res['sortorder'].'_'.$res['sortdirection'];
	}


    $result=$this->query($query,(int)$galleryId);
    $videoId=array();

	while ($res = $result->fetchRow()) {
		$videoId[]=reset($res);
    }

			return($videoId);
	}



	function get_gallery_image($galleryId,$rule='',$sort_mode = '') {
		$query='select i.`imageId` from `tiki_videos` i, `tiki_videos_data` d
                 where i.`imageId`=d.`imageId` and i.`galleryId`=? and d.`type`=? order by ';
		/* if sort by filesize while browsing images it needs to be read from tiki_image_data table */

		if ($sort_mode == 'filesize_asc' || $sort_mode == 'filesize_desc') {
			$query.='d.';
		} else {
			$query.='i.';
		}
		$bindvars=array($galleryId,'o');
		switch($rule) {
			case 'firstu':
				// first uploaded
				$query.=$this->convert_sortmode('created_asc');
				$imageId=$this->getOne($query,$bindvars);
				break;
			case 'lastu':
				// last uploaded
				$query.=$this->convert_sortmode('created_desc');
				$imageId=$this->getOne($query,$bindvars);
				break;
			case 'all':
			case 'first':
			    if (!$sort_mode) {
    				// first image in default gallery sortorder
    				$query2='select `sortorder`,`sortdirection` from `tiki_galleries_video` where `galleryId`=?';
    				$result=$this->query($query2,$bindvars);
    				$res = $result->fetchRow();
    				$sort_mode=$res['sortorder'].'_'.$res['sortdirection'];
				}
				$query.=$this->convert_sortmode($sort_mode);
				if ($rule != 'all') {
    				$imageId=$this->getOne($query,$bindvars);
    				break;
				}
				$result=$this->query($query,$bindvars);
				$imageId=array();
    			while ($res = $result->fetchRow()) {
    				$imageId[]=reset($res);
    			}
				break;
			case 'last':
			    if ($sort_mode) {
			        $invsor = explode('_', $sort_mode);
			        $sort_mode = $invsor[0] . '_' . ($invsor[1] == 'asc' ? 'desc' : 'asc');
			    } else {
    				// last image in default gallery sortorder
    				$query2='select `sortorder`,`sortdirection` from `tiki_galleries_video` where `galleryId`=?';
    				$result=$this->query($query2,$bindvars);
    				$res = $result->fetchRow();
    				if($res['sortdirection'] == 'asc') {
    					$res['sortdirection']='desc';
    				} else {
    					$res['sortdirection']='asc';
    				}
    				$sort_mode=$res['sortorder'].'_'.$res['sortdirection'];
				}
				$query.=$this->convert_sortmode($sort_mode);
				$imageId=$this->getOne($query,$bindvars);
				break;
			case 'random':
				//random image of gallery
				$ret=$this->get_random_image($galleryId);
				$imageId=$ret['imageId'];
				break;
			case 'default':
				//check gallery settings and re-run this function
				$query='select `galleryimage` from `tiki_galleries_video` where `galleryId`=?';
				$rule=$this->getOne($query,array($galleryId));
				$imageId=$this->get_gallery_image($galleryId,$rule);
				break;
			default:
				// imageId is listed in gallery settings
				if (is_numeric($rule)) {
					$imageId=(int) $rule;
				} else {
					// unknown.
					$imageId=-1;
				}
				break;
			}
			return($imageId);
	}

	function get_prev_and_next_image($sort_mode, $find, $imageId, $galleryId = -1) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array('o',$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array('o');
		}

		$midcant = "";
		$cantvars=array();

		if ($galleryId != -1 && is_numeric($galleryId)) {
			$mid .= " and i.`galleryId`=? ";
			$bindvars[]=(int)$galleryId;
			$midcant = "where `galleryId`=? ";
			$cantvars[]=(int)$galleryId;
		}

		$query = "select i.`imageId`
                from `tiki_videos` i , `tiki_videos_data` d
                 where i.`imageId`=d.`imageId`
                 and d.`type`=?
                $mid
                order by ";
        /* if sort by filesize while browsing images it needs to be read from tiki_image_data table */
		if ($sort_mode == 'filesize_asc' || $sort_mode == 'filesize_desc') {
			$query.='d.';
		} else {
			$query.='i.';
		}
        $query .= $this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars);
		$prev=-1; $next=0; $tmpid=0;
		while ($res = $result->fetchRow()) {
		        if ($imageId == $res['imageId']) {
		                $prev=$tmpid;
		        } else if ($prev >= 0) { // $prev is set, so, this one is the next
		                $next=$res['imageId'];
		                break;
		        }
		        $tmpid=$res['imageId'];
		}
		return array('prev' => ($prev > 0 ? $prev : 0), 'next' => $next);
	}

	function get_first_image($sort_mode, $find, $galleryId = -1) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array('o',$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array('o');
		}

		$midcant = "";
		$cantvars=array();

		if ($galleryId != -1 && is_numeric($galleryId)) {
			$mid .= " and i.`galleryId`=? ";
			$bindvars[]=(int)$galleryId;
			$midcant = "where `galleryId`=? ";
			$cantvars[]=(int)$galleryId;
		}

		$query = "select i.`imageId`
                from `tiki_videos` i , `tiki_videos_data` d
                 where i.`imageId`=d.`imageId`
                 and d.`type`=?
                $mid
                order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,1,0);
		$res = $result->fetchRow();
		return $res['imageId'];
	}

	function get_last_image($sort_mode, $find, $galleryId = -1) {
		if (strstr($sort_mode, 'asc')) {
			$sort_mode = str_replace('asc', 'desc', $sort_mode);
		} else {
			$sort_mode = str_replace('desc', 'asc', $sort_mode);
		}

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars=array('o',$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array('o');
		}

		$midcant = "";
		$cantvars=array();

		if ($galleryId != -1 && is_numeric($galleryId)) {
                        $mid .= " and i.`galleryId`=? ";
                        $bindvars[]=(int)$galleryId;
                        $midcant = "where `galleryId`=? ";
                        $cantvars[]=(int)$galleryId;
		}

		$query = "select i.`imageId`
                from `tiki_videos` i , `tiki_videos_data` d
                 where i.`imageId`=d.`imageId`
                 and d.`type`=?
                $mid
                order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,1,0);
		$res = $result->fetchRow();
		return $res['imageId'];
	}

	function list_images($offset, $maxRecords, $sort_mode, $find, $galleryId = -1) {
		return $this->get_images($offset, $maxRecords, $sort_mode, $find, $galleryId);
	}

    function get_random_image($galleryId = -1) {
	$whgal = "";
	$bindvars = array();
	if (((int)$galleryId) != -1) {
	    $whgal = " where `galleryId`=? ";
	    $bindvars[] = (int) $galleryId;
	}

	$query = "select count(*) from `tiki_videos` $whgal";
	$cant = $this->getOne($query,$bindvars);
	$ret = array();

	if ($cant) {
	    $pick = rand(0, $cant - 1);

	    $query = "select `imageId` ,`description`, `galleryId`,`name` from `tiki_videos` $whgal";
	    $result = $this->query($query,$bindvars,1,$pick);
	    $res = $result->fetchRow();
	    $ret["galleryId"] = $res["galleryId"];
	    $ret["imageId"] = $res["imageId"];
	    $ret["name"] = $res["name"];
	    $ret["description"] = $res["description"];
	    $query = "select `name`  from `tiki_galleries_video` where `galleryId` = ?";
	    $ret["gallery"] = $this->getOne($query,array((int)$res["galleryId"]));
	} else {
	    $ret["galleryId"] = 0;

	    $ret["imageId"] = 0;
	    $ret["name"] = tra("No image yet, sorry.");
	}

	return ($ret);
    }

	function list_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find=false) {
	    // If $user is admin then get ALL galleries, if not only user galleries are shown
	    global $tiki_p_admin_galleries;



	    $old_sort_mode = '';

	    if (in_array($sort_mode, array(
			    'videos_desc',
			    'videos_asc'
			    ))) {
		$old_offset = $offset;

		$old_maxRecords = $maxRecords;
		$old_sort_mode = $sort_mode;
		$sort_mode = 'name_desc';
		$offset = 0;
		$maxRecords = -1;
	    }

	    // If the user is not admin then select `it` 's own galleries or public galleries
	    if (($tiki_p_admin_galleries == 'y') or ($user == 'admin')) {
		$whuser = "";
		$bindvars=array();
	    } else {
		$whuser = "where g.`user`=? or g.public=?";
		$bindvars=array($user,'y');
	    }

	    if ($find) {
		$findesc = '%' . $find . '%';

		if (empty($whuser)) {
		    $whuser = "where g.`name` like ? or g.`description` like ?";
		    $bindvars=array($findesc,$findesc);
		} else {
		    $whuser .= " and g.`name` like ? or g.`description` like ?";
		    $bindvars[]=$findesc;
		    $bindvars[]=$findesc;
		}
	    }

	    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
	    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
	    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil


	    $query = "select g.*, a.`name` as parentgalleryName from `tiki_galleries_video` g left join `tiki_galleries_video` a on g.`parentgallery` = a.`galleryId` $whuser order by ".$this->convert_sortmode($sort_mode);
	    $query_cant = "select count(*) from `tiki_galleries_video` g $whuser";

	    $result = $this->query($query,$bindvars,$maxRecords,$offset);
	    $cant = $this->getOne($query_cant,$bindvars);
	    $ret = array();

	    global $prefs, $userlib, $user, $tiki_p_admin;
	    while ($res = $result->fetchRow()) {
			$res['perms'] = $this->get_perm_object($res['galleryId'], 'image gallery', $res, false);
			if ($res['perms']['tiki_p_view_image_gallery'] == 'y') {
				$res['videos'] = $this->getOne("select count(*) from `tiki_videos` where `galleryId`=?",array($res['galleryId']));
				$ret[] = $res;
			}
	    }

	    if ($old_sort_mode == 'videos_asc') {
		usort($ret, 'compare_videos');
	    }

	    if ($old_sort_mode == 'videos_desc') {
		usort($ret, 'r_compare_videos');
	    }

	    if (in_array($old_sort_mode, array(
			    'videos_desc',
			    'videos_asc'
			    ))) {
		$ret = array_slice($ret, $old_offset, $old_maxRecords);
	    }

	    $retval = array();
	    $retval["data"] = $ret;
	    $retval["cant"] = $cant;
	    return $retval;
	}

	function list_visible_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find) {
	    global $tiki_p_admin_galleries, $tikilib;
	    // If $user is admin then get ALL galleries, if not only user galleries are shown

	    $old_sort_mode = '';

	    if (in_array($sort_mode, array(
			    'images desc',
			    'images asc'
			    ))) {
		$old_offset = $offset;

		$old_maxRecords = $maxRecords;
		$old_sort_mode = $sort_mode;
		$sort_mode = 'user desc';
		$offset = 0;
		$maxRecords = -1;
	    }

		$whuser = "";
		$bindvars=array('y');

	    if ($find) {
		$findesc = '%' . $find . '%';

		if (empty($whuser)) {
		    $whuser = " and (`name` like ? or `description` like ?)";
		    $bindvars=array('y',$findesc,$findesc);
		} else {
		    $whuser .= " and (`name` like ? or `description` like ?)";
		    $bindvars[]=$findesc;
		    $bindvars[]=$findesc;
		}
	    }

	    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
	    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
	    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
	    $query = "select * from `tiki_galleries_video` where `visible`=? $whuser order by ".$this->convert_sortmode($sort_mode);
	    $query_cant = "select count(*) from `tiki_galleries_video` where `visible`=? $whuser";
	    $result = $this->query($query,$bindvars,$maxRecords,$offset);
	    $cant = $this->getOne($query_cant,$bindvars);
	    $ret = array();

	    while ($res = $result->fetchRow()) {
		if (!$tikilib->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
			continue;
		}
		$aux = array();

		$aux["name"] = $res["name"];
		$gid = $res["galleryId"];
		$aux["visible"] = $res["visible"];
		$aux["id"] = $gid;
		$aux["galleryId"] = $res["galleryId"];
		$aux["description"] = $res["description"];
		$aux["created"] = $res["created"];
		$aux["lastModif"] = $res["lastModif"];
		$aux["user"] = $res["user"];
		$aux["hits"] = $res["hits"];
		$aux["public"] = $res["public"];
		$aux["theme"] = $res["theme"];
		$aux["videos"] = $this->getOne("select count(*) from `tiki_videos` where `galleryId`=?",array($gid));
		$ret[] = $aux;
	    }

	    if ($old_sort_mode == 'videos asc') {
		usort($ret, 'compare_videos');
	    }

	    if ($old_sort_mode == 'videos desc') {
		usort($ret, 'r_compare_videos');
	    }

	    if (in_array($old_sort_mode, array(
			    'videos desc',
			    'videos asc'
			    ))) {
		$ret = array_slice($ret, $old_offset, $old_maxRecords);
	    }

	    $retval = array();
	    $retval["data"] = $ret;
	    $retval["cant"] = $cant;
	    return $retval;
	}

    function get_gallery($id) {
	$query = "select * from `tiki_galleries_video` where `galleryId`=?";
	$result = $this->query($query,array((int) $id));
	$res = $result->fetchRow();
	return $res;
    }

	function get_gallery_owner($galleryId) {
		$query = "select `user` from `tiki_galleries_video` where `galleryId`=?";

		$user = $this->getOne($query,array((int)$galleryId));
		return $user;
	}

	function get_gallery_from_video($videoid) {
		$query = "select `galleryId` from `tiki_videos` where `videoId`=?";

		$galid = $this->getOne($query,array((int)$videoid));
		return $galid;
	}

	function move_video($vidId, $galId) {
		$query = "update `tiki_videos` set `galleryId`=? where `videoId`=?";

		$result = $this->query($query,array((int)$galId,(int)$vidId));
		return true;
	}

	function get_video_info($id) {


		$query = "select i.`entryId`
                 from `tiki_videos` i where
                     i.`videoId`=?";

		$result = $this->query($query,(int)$id);
		$res = $result->fetchRow();

		return $res;
	}



	// Add an option to establish Image size (x,y)
	function get_image($id, $itype = 'o', $xsize = 0, $ysize = 0) {
		global $prefs;

		$mid = "";

		if ($itype == 't') {
			$galid = $this->get_gallery_from_image($id);

			$galinfo = $this->get_gallery_info($galid);
			$xsize = $galinfo["thumbSizeX"];
			$ysize = $galinfo["thumbSizeY"];
		}

		if ($xsize != 0 && $ysize == 0) {
			// first parameter (xsize) represents a scale
			// so we select a bounding box
			$ysize=$xsize;
		}

		if ($xsize != 0 && $ysize != 0) {
			if ($ysize == $xsize) {
				// we don't know yet.
				$mid = "and (d.`xsize`=? or d.`ysize`=?) order by `xysize` desc ";
				$bindvars=array((int)$id,$itype,(int)$xsize,(int)$ysize);
			} else {
				//exact match
				$mid = "and d.`xsize`=? and d.`ysize`=? ";
				$bindvars=array((int)$id,$itype,(int)$xsize,(int)$ysize);
			}
		}



		if(!isset($bindvars) || !is_array($bindvars)) {
			$bindvars=array((int)$id,$itype);
		}

		$query = "select i.`imageId`, i.`galleryId`, i.`name`,
                     i.`description`, i.`created`, i.`user`,
                     i.`hits`, i.`path`,i.`lat`, i.`lon`,
                     d.`xsize`,d.`ysize`,d.`type`,d.`filesize`,
                     d.`filetype`,d.`filename`,d.`data`,
		     d.`xsize` * d.`ysize` as `xysize`, d.`etag`
                 from `tiki_videos` i, `tiki_videos_data` d where
                     i.`imageId`=? and d.`imageId`=i.`imageId`
                     and d.`type`=?
                     $mid";

		$result = $this->query($query,$bindvars,1);

		if ($result===false || $result===null) {
			die;
		}

		$res = $result->fetchRow();

		$this->imageId = $res["imageId"];
		$this->galleryId = $res["galleryId"];
		$this->name = $res["name"];
		$this->description = $res["description"];
		$this->lat = $res["lat"];
		$this->lon = $res["lon"];
		$this->created = $res["created"];
		$this->user = $res["user"];
		$this->hits = $res["hits"];
		$this->path = $res["path"];
		$this->xsize = $res["xsize"];
		$this->ysize = $res["ysize"];
		$this->type = $res["type"];
		$this->filesize = $res["filesize"];
		$this->filetype = $res["filetype"];
		$this->filename = $res["filename"];
		$this->etag= $res["etag"];

		# build scaled videos or thumb if not available
		if ($itype != 'o' && !isset($this->imageId)) {
			if ($newsize = $this->rebuild_image($id, $itype, $xsize, $ysize)) {
				// removed because this causes endless recursion
				//return $this->get_image($id, $itype, $newsize["xsize"], $newsize["ysize"]);

				// Since the rescaled image is the one we want, we have to switch to its path and data
				$res['path'] = $this->path;
				$res['data'] = $this->image;
			}
		}

		// get image data from fs
		if (strlen($res["data"]) < 3) { // this is needed by postgres, because it inserts '' in the data field
			switch ($itype) {
			case 't':
				$ext = ".thumb";

				break;

			case 's':
				$ext = ".scaled_" . $res["xsize"] . "x" . $res["ysize"];

				break;

			case 'b':
				// for future use
				$ext = ".backup";

				break;

			default:
				$ext = '';
			}

			// If the image was a .gif then the thumbnail has 0 bytes if the thumbnail
			// is empty then use the full image as thumbnail
			//  if($ext==".thumb" && filesize($prefs['gal_use_dir'].$res["path"].$ext)==0 ) {
			//   $ext='';
			//}
			$this->readimagefromfile($prefs['gal_use_dir'] . $res["path"] . $ext);
		} else {
			$this->image = $res["data"];
		}

		if (!isset($this->imagehandle))
			$this->readimagefromstring();

		// etag checks
		if($this->etag=='') {
		   $this->add_etag();
		}

		return $res;
	}

   function add_etag() {
      // stores md5 based etag in the tables
      // this function assumes that the $this->imageId and other
      // are loaded before (through $this->get_image() or similar)
      if(isset($this->image)) {//avoid broken videos through warning
        $etag=md5($this->image);
        $query='update `tiki_videos_data` set `etag`=? where `imageId`=? and `xsize`=? and `ysize`=? and `type`=?';
        $bindvars=array($etag,(int) $this->imageId,(int) $this->xsize,(int) $this->ysize,$this->type);
        $this->query($query,$bindvars);
      }

   }

   function get_etag($id, $itype = 'o', $xsize = 0, $ysize = 0) {
      // used to get the etag of a image. This function can be called
      // before we load the image into memory to check if the browser
      // has the image cached.
       $mid = "";

       if ($itype == 't') {
	       $galid = $this->get_gallery_from_image($id);

	       $galinfo = $this->get_gallery_info($galid);
	       $xsize = $galinfo["thumbSizeX"];
	       $ysize = $galinfo["thumbSizeY"];
       }

       if ($xsize != 0 && $ysize == 0) {
	       // first parameter (xsize) represents a scale
	       // so we select a bounding box
	       $ysize=$xsize;
       }

       if ($xsize != 0 && $ysize != 0) {
	       if ($ysize == $xsize) {
		       // we don't know yet.
		       $mid = "and (d.`xsize`=? or d.`ysize`=?) order by `xysize` desc ";
		       $bindvars=array((int)$id,$itype,(int)$xsize,(int)$ysize);
	       } else {
		       //exact match
		       $mid = "and d.`xsize`=? and d.`ysize`=? ";
		       $bindvars=array((int)$id,$itype,(int)$xsize,(int)$ysize);
	       }
       }

       if(!@is_array($bindvars)) {
	       $bindvars=array((int)$id,$itype);
       }

       $query = "select d.`xsize` * d.`ysize` as `xysize`, d.`etag`
	from `tiki_videos_data` d where d.`imageId`=? and d.`type`=?
	    $mid";

       $result = $this->query($query,$bindvars,1);

       if ($result===false || $result===null) {
	       return(false);
       }

       $res = $result->fetchRow();
       $this->etag=$res["etag"];

       return($res["etag"]);
    }


   function get_imageid_byname($name, $galleryId=0) {

		$bindvars=array($name);

		$query = "select `imageId` from `tiki_videos`
						where `name` like ?";
		if (!empty($galleryId)) {
			$query .= ' and galleryId=?';
			$bindvars[] = $galleryId;
		}

		$result = $this->query($query,$bindvars,1);
		$res = $result->fetchRow();

		return($res["imageId"]);

   }

	function get_image_thumb($id) {
		return $this->get_image($id, 't');
	}

	function replace_gallery($galleryId, $name, $description, $theme, $user, $maxRows, $rowImages, $thumbSizeX, $thumbSizeY, $public, $visible = 'y', $sortorder='created', $sortdirection='desc',
$parentgallery=-1,$showname='y',$showvideoid='n',$showdescription='n',$showcreated='n',$showuser='n',$showhits='y',$showcategories='n') {
		global $prefs;

		// if the user is admin or the user is the same user and the gallery exists then replace if not then
		// create the gallary if the name is unused.
		$name = strip_tags($name);

		$description = strip_tags($description);

		// check if the gallery already exists. if yes: do update, if no: update it
		if ($galleryId<1)
		$galleryId = $this->getOne("select `galleryId` from `tiki_galleries_video` where `name`=? and `parentgallery`=?",array($name,$parentgallery));

		if ($galleryId > 0) {
			//$res = $result->fetchRow();
			//if( ($user == 'admin') || ($res["user"]==$user) ) {
			$query = "update `tiki_galleries_video` set `name`=?,`visible`=?, `maxRows`=? , `rowImages`=?,
                `thumbSizeX`=?, `thumbSizeY`=?, `description`=?, `theme`=?,
                `lastModif`=?, `public`=?, `sortorder`=?, `sortdirection`=?,
		`parentgallery`=?,`showname`=?,`showvideoid`=?,`showcategories`=?,`showdescription`=?,
		`showcreated`=?,`showuser`=?,`showhits`=?, `user`=?
	       	where `galleryId`=?";

			$result =
$this->query($query,array($name,$visible,(int)$maxRows,(int)$rowImages,(int)$thumbSizeX,(int)$thumbSizeY,$description,$theme,(int)$this->now,$public,$sortorder,$sortdirection,(int)$parentgallery,$showname,$showvideoid,$showcategories,$showdescription,$showcreated,$showuser,$showhits,$user,(int)$galleryId));
		} else {
			// Create a new record
			$query = "insert into
`tiki_galleries_video`(`name`,`description`,`theme`,`created`,`user`,`lastModif`,`maxRows`,`rowImages`,`thumbSizeX`,`thumbSizeY`,`public`,`hits`,`visible`,`sortorder`,`sortdirection`,`parentgallery`,`showname`,`showvideoid`,`showdescription`,`showcategories`,`showcreated`,`showuser`,`showhits`)
values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($name,$description,$theme,(int) $this->now,$user,(int) $this->now,(int) $maxRows,(int) $rowImages,(int) $thumbSizeX,(int)
$thumbSizeY,$public,0,$visible,$sortorder,$sortdirection,(int)$parentgallery,$showname,$showvideoid,$showdescription,$showcategories,$showcreated,$showuser,$showhits);
			$result = $this->query($query,$bindvars);
			$galleryId = $this->getOne("select max(`galleryId`) from `tiki_galleries_video` where `name`=? and `created`=?",array($name,(int) $this->now));

			if ($prefs['feature_score'] == 'y') {
			    $this->score_event($user, 'igallery_new');
			}
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('galleries', $galleryId);
		}

		return $galleryId;
	}

	function add_gallery_scale($galleryId, $scale) {
	        $old_scale = $this->getOne("select scale from tiki_galleries_video_scales where galleryId = ? AND scale = ?", array((int)$galleryId, (int)$scale));
		if ($scale != $old_scale) {
		    $query = "insert into `tiki_galleries_video_scales`(`galleryId`,`scale`)
            values(?,?)";
		    $result = $this->query($query,array((int)$galleryId,(int)$scale));
		}
	}

	function remove_gallery_scale($galleryId, $scale= 0) {
                $mid = "";
                $bindvars=array((int) $galleryId);
                if ($scale != 0) {
                        $mid = " and `scale`=? ";
                        $bindvars[]=(int) $scale;
                }
                $query = "delete from `tiki_galleries_video_scales` where
            `galleryId`=? $mid";
                $result = $this->query($query,$bindvars);
        }

	function remove_gallery($id) {
		global $prefs;

		$query = "select `imageId`,path from `tiki_videos` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));

		while ($res = $result->fetchRow()) {
			$path = $res["path"];

			$query2 = "select `xsize`,`ysize`,`type` from `tiki_videos_data` where `imageId`=?";
			$result2 = $this->query($query2,array((int)$res["imageId"]));

			while ($res2 = $result2->fetchRow()) {
				switch ($res2["type"]) {
				case 't':
					$ext = ".thumb";

					break;

				case 's':
					$ext = ".scaled_" . $res2["xsize"] . "x" . $res2["ysize"];

					break;

				case 'b':
					// for future use
					$ext = ".backup";

					break;

				default:
					$ext = '';
				}

				if ($path) {
					@unlink ($prefs['gal_use_dir'] . $path . $ext);
				}
			}

			$query3 = "delete from `tiki_videos_data` where `imageId`=?";
			$result3 = $this->query($query3,array((int)$res["imageId"]));

			$this->remove_object('image', $res["imageId"]);
		}

		$query = "delete from `tiki_galleries_video` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));
		$query = "delete from `tiki_videos` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));
		$this->remove_gallery_scale($id);
		$this->remove_object('image gallery', $id);
		return true;
	}

	function get_gallery_info($id) {
		// alias for get_gallery
		return $this->get_gallery($id);
	}

	function get_gallery_scale_info($id) {
		$query = "select * from `tiki_galleries_video_scales` where `galleryId`=?
              order by `scale` asc";

		$result = $this->query($query,array((int) $id));
		$resa = array();

		while ($res = $result->fetchRow()) {
			$resa[] = $res;
		}

		return $resa;
	}

	function get_gallery_next_scale($id, $scale= 0) {
		$query = "select * from `tiki_galleries_video_scales` where `galleryId`=?
              and `scale` > ? order by `scale` asc";
		$result = $this->query($query,array((int) $id,(int) $scale));
		$res = $result->fetchRow();
		return $res;
	}

	function get_gallery_default_scale($id) {
		$query = "select `defaultscale` from `tiki_galleries_video` where `galleryId`=?";
		$ret=$this->getOne($query,array((int) $id));
		return $ret;
	}

	function get_gallery_prevnext_scale($id,$currentscale) {
		$ret=array();
		$bindvars=array((int) $id, (int) $currentscale);
		$query = 'select `scale` from `tiki_galleries_video_scales` where `galleryId`=? ';
		$query2 =$query.'and `scale`>? order by `scale` asc';
		$ret['nextscale']=$this->getOne($query2,$bindvars);
		$query2 =$query.'and `scale`<? order by `scale` desc';
		$ret['prevscale']=$this->getOne($query2,$bindvars);
		if($ret['nextscale']) {
			$ret['nexttype']='s';
		} else {
			$ret['nexttype']='o';
		}
		if($ret['prevscale']) {
			$ret['prevtype']='s';
		} else {
			$ret['prevtype']='o';
		}
		return($ret);
	}

	//Capture Images from wiki, blogs, ....
	function capture_videos($data) {
		global $prefs, $tikilib;
		if ($prefs['cachevideos'] != 'y')
			return $data;

		preg_match_all("/src=\"([^\"]+)\"/", $data, $reqs1);
		preg_match_all("/src=\'([^\']+)\'/", $data, $reqs2);
		preg_match_all("/src=([A-Za-z0-9\:\?\=\/\\\.\-\_]+)\}/", $data, $reqs3);
		preg_match_all("/src=([A-Za-z0-9\:\?\=\/\\\.\-\_]+) /", $data, $reqs4);
		$merge = array_merge($reqs1[1], $reqs2[1], $reqs3[1], $reqs4[1]);
		$merge = array_unique($merge);
		//print_r($merge);
		// Now for each element in the array capture the image and
		// if the capture was successful then change the reference to the
		// internal image
		$page_data = $data;

		foreach ($merge as $img) {
			// This prevents caching videos
			if (!strstr($img, "img/wiki_up") && !strstr($img, "show_image.php") && !strstr($img, "nocache") && @getimagesize($img)) {
				//print("Procesando: $img<br />");
				@$fp = fopen($img, "r");

				if ($fp) {
					$data = '';

					while (!feof($fp)) {
						$data .= fread($fp, 4096);
					}

					//print("Imagen leida:".strlen($data)." bytes<br />");
					fclose ($fp);

					if (strlen($data) > 0) {
						$url_info = parse_url($img);

						$pinfo = pathinfo($url_info["path"]);
						$type = "image/" . $pinfo["extension"];
						$name = $pinfo["basename"];
						$size = strlen($data);
						$url = $img;

						if (function_exists("ImageCreateFromString") && (!strstr($type, "gif"))) {
							$img = imagecreatefromstring($data);

							$size_x = imagesx($img);
							$size_y = imagesy($img);
							// Fix the ratio values for system gallery
							$gal_info["thumbSizeX"] = 90;
							$gal_info["thumbSizeY"] = 90;

							if ($size_x > $size_y)
								$tscale = ((int)$size_x / $gal_info["thumbSizeX"]);
							else
								$tscale = ((int)$size_y / $gal_info["thumbSizeY"]);

							$tw = ((int)($size_x / $tscale));
							$ty = ((int)($size_y / $tscale));

							if (chkgd2()) {
								$t = imagecreatetruecolor($tw, $ty);

								imagecopyresampled($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
							} else {
								$t = imagecreate($tw, $ty);

								$this->ImageCopyResampleBicubic($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
							}

							// CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
							//$tmpfname = 'temp.jpg';
							//$tmpfname = tempnam("/tmp", "img");
							$tmpfname = tempnam($prefs['tmpDir'], "img");
							imagejpeg($t, $tmpfname);
							// Now read the information
							$fp = fopen($tmpfname, "rb");
							$t_data = fread($fp, filesize($tmpfname));
							fclose ($fp);
							unlink ($tmpfname);
							$t_pinfo = pathinfo($tmpfname);
							$t_type = $t_pinfo["extension"];
							$t_type = 'image/' . $t_type;

							$imageId = $this->insert_image(0, '', '', $name, $type, $data, $size, $size_x, $size_y, 'admin', $t_data, $t_type, NULL, NULL, $gal_info);
						//print("Imagen generada en $imageId<br />");
						} else {
							//print("No GD detected generating image without thumbnail<br />");
							$imageId = $this->insert_image(0, '', '', $name, $type, $data, $size, 100, 100, 'admin', '', '', NULL, NULL, $gal_info);
						//print("Imagen en $imageId<br />");
						}

						// Now change it!
						//print("Changing $url to imageId: $imageId");
						$uri = parse_url($_SERVER["REQUEST_URI"]);
						$path = str_replace("tiki-editpage", "show_image", $uri["path"]);
						$path = str_replace("tiki-edit_article", "show_image", $path);
						$page_data = str_replace($url, $tikilib->httpPrefix(). $path . '?id=' . $imageId, $page_data);
					} // if strlen
				} // if $fp
			}
		} // foreach
		return $page_data;
	}
	function get_one_image_from_disk($userfile, $galleryId=0, $name='', $description='', $gal_info='') {
		global $prefs, $user;
		$ret = array();
		if (is_uploaded_file($_FILES[$userfile]['tmp_name'])) {
			$file_name = $_FILES[$userfile]['name'];
			$ret['filename'] = $file_name;
			if (!empty($prefs['gal_match_regex']) && !preg_match('/'.$prefs['gal_match_regex'].'/', $file_name, $reqs)) {
				$ret['msg'] = tra('Invalid imagename (using filters for filenames)');
				return $ret;
			}
			if (!empty($prefs['gal_nmatch_regex']) && preg_match('/'.$prefs['gal_nmatch_regex'].'/', $file_name, $reqs)) {
				$ret['msg'] = tra('Invalid imagename (using filters for filenames)');
				return $ret;
			}
			$type = $_FILES[$userfile]['type'];
			$size = $_FILES[$userfile]['size'];
			$file_tmp_name = $_FILES[$userfile]['tmp_name'];
			$tmp_dest = $prefs['tmpDir'] . '/' . $file_name.'.tmp'; // add .tmp to not overwrite existing files (like index.php)
			if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
				$ret['msg'] = tra('Errors detected');
				@unlink($tmp_dest);
				return $ret;
			}
			$fp = fopen($tmp_dest, "rb");
			$ret['data'] = fread($fp, filesize($tmp_dest));
			fclose ($fp);
			$imginfo = @getimagesize($tmp_dest);
			unlink($tmp_dest);
			if (!$ret['data'] || !$imginfo) {
				$ret['msg'] = tra('Errors detected');
				return $ret;
			}
			if (!$galleryId) { // was called just to get and check the file
				$ret['xsize'] = $imginfo[0];
				$ret['ysize'] = $imginfo[1];
				$ret['filetype'] = $type;
				$ret['filesize'] = $size;
				return $ret;
			}
			if ($name == '')
				$name = $file_name;
			if (($imageId = $this->insert_image($galleryId, $name, $description, $file_name, $type, $ret['data'], $size, 0, 0, $user, '', '', NULL, NULL, $gal_info))===false) {
				$ret['msg'] = tra('Upload was not successful');
			} else {
				$ret['imageId'] = $imageId;
			}
		} else {
			$ret['msg'] = tra('Upload was not successful');
			$ret['filename'] = $_FILES[$userfile]['tmp_name'];
		}
		unset($ret['data']);
		return $ret;
	}

  // function to move images from one store to another (fs to db or db to fs)
  function move_image_store($imageId,$direction='to_fs')
  {
    global $prefs;

		global $errstr;
    $this->clear_class_vars(); //cleanup

    if($direction!='to_fs' && $direction!='to_db') {
      return(false);
    }

		if ($direction == 'to_fs' and !is_dir($prefs['gal_use_dir'])) {
			$errstr = tra("unknown destination directory. Please set it up in <a href='tiki-admin.php?page=galleries'>tiki-admin.php?page=galleries</a>");
			return(0);
		}
    // get the storage location
    $query='select `path` from `tiki_videos` where `imageId`=?';
    $path=$this->getOne($query,array($imageId),false);
    if($path===false) { // imageId not found
      return(false);
    }

    if((empty($path) && $direction=='to_fs') || (!empty($path) && $direction=='to_db')) {
      // move image
      // load image
      $this->get_image($imageId);
      $query='update `tiki_videos` set `path`=? where `imageId`=?';
      if($direction=='to_fs') {
   $this->path=md5(uniqid($this->filename));
        // store_image data did already overwrite the "data" field in tiki_videos_data
        $this->query($query,array($this->path,$imageId));
      }
      // write image
      $this->store_image_data(true);
      if($direction=='to_db') {
        // remove image in fs
        if(!@unlink($prefs['gal_use_dir'].$this->path)) {
          $errstr = tra("unlink failed");
        }
        $this->query($query,array('',$imageId));
      }
      return(1);

    }
    return(0);
  }

  function move_gallery_store($galId,$direction='to_fs')
  {
    $met=ini_get('max_execution_time');
    $st=time();
    $n=0;
    $errors=0;
    $timeout=false;
    if($direction!='to_fs' && $direction!='to_db') {
      return(false);
    }

    // remove all scales. They will be rebuild on access
    $this->rebuild_scales($galId);

    // move images store
    if($galId==-1) {
      $query='select `imageId` from `tiki_videos`';
      $result=$this->query($query,array());
    } else {
      $query='select `imageId` from `tiki_videos` where `galleryId`=?';
      $result=$this->query($query,array($galId));
    }
    while ($res = $result->fetchRow()) {
      $r=$this->move_image_store($res['imageId'],$direction);
      if($r!==false) {
        $n+=$r;
      } else {
  $errors++;
      }
      if($met-time()+$st < 3) { // avoid timeouts so that we dont end with broken images
   $timeout=true;
   break;
      }
    }
    $resultarray=array('moved_images'=>$n,'timeout'=>$timeout,'errors'=>$errors);
    return($resultarray);
  }

  function clear_class_vars()
  { // function to clear loaded data. Usable for mass changes
     unset($this->imageId);
     unset($this->galleryId);
     unset($this->name);
     unset($this->description);
     unset($this->lat);
     unset($this->lon);
     unset($this->created);
     unset($this->user);
     unset($this->hits);
     unset($this->path);
     unset($this->xsize);
     unset($this->oldxsize);
     unset($this->ysize);
     unset($this->oldysize);
     unset($this->type);
     unset($this->filesize);
     unset($this->filetype);
     unset($this->filename);
     unset($this->etag);
     unset($this->image);
  }
  /* compute the ratio the image $xsize,$size must have to go in the box */
  function ratio($xsize, $ysize, $xbox=0, $ybox=0) {
	if (empty($xbox) && empty($ybox))
		return 1;
	if ($xsize <= $xbox && $ysize <= $ybox)
		return 1;
	if (empty($xbox)) {
		return $ybox/xsize;
	} else {
		$t = $xbox/$xsize;
		if (!empty($ybox)) {
			return $t;
		} else {
			return min($t, $ybox/$ysize);
		}
	}
	}
}
global $dbTiki;
global $videogallib;
$videogallib = new VideoGalsLib($dbTiki);

?>





