<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/videogals/videogallib.php,v 1.97.2.4 2008-03-06 19:45:42 sampaioprimo Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ("includes.php");

class VideoGalsLib extends TikiLib {



	function VideoGalsLib($db) {
		global $prefs;

		$this->TikiLib($db);
		$exts=get_loaded_extensions();

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

	function edit_video($id, $name, $description, $tags) {
		global $prefs;

		$entry = new KalturaEntry();
		$entry->name= $name;
		$entry->description = $description;
		$entry->tags = $tags;

        $entry_id = $this->get_entry_from_video($id);

		$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = "123";
		$kaltura_client = new KalturaClient($kaltura_conf);

		$kres = $kaltura_client->start($kuser, $kaltura_conf->secret);

		$kres= $kaltura_client->updateEntry($kuser , $entry_id, $entry);

		return true;
	}

	function insert_image($galleryId, $name, $description, $filename, $filetype, &$data, $size, $xsize, $ysize, $user, $t_data, $t_type ,$lat=NULL, $lon=NULL, $gal_info=NULL) {
		global $prefs;

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

	function remove_video($id) {
		global $prefs;

		$query = "delete from `tiki_videos` where `videoId`=?";
		$result = $this->query($query,array((int)$id));
		$this->remove_object('video', $id);
		return true;
	}

	function get_videos($offset, $maxRecords, $sort_mode, $find, $galleryId = -1) {

		print $sort_mode.": code for the sorting the data not completed";

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

		$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = "123";
		$kaltura_client = new KalturaClient($kaltura_conf);
		$kres =$kaltura_client->start($user, $kaltura_conf->secret);

		while ($res = $result->fetchRow()) {

			$kres = $kaltura_client->getEntry ( $kuser , $res['entryId']);
			$ret[]= array_merge($res,$kres['result']['entry']);


		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

        function get_subgalleries($offset, $maxRecords, $sort_mode, $find, $galleryId = -1) {

		if ($sort_mode == '')
			$sort_mode = 'name_asc';

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
       			g.`hits`,g.`maxRows`,g.`rowVideos`,g.`thumbSizeX`,
	 		g.`thumbSizeY`,g.`public`,g.`sortorder`,g.`sortdirection`,
			g.`parentgallery`,count(i.`videoId`) as videos
			from `tiki_galleries_video` g, `tiki_videos` i
			where i.`galleryId`=g.`galleryId` and
                 	`parentgallery`=? $mid group by
			g.`galleryId`, g.`name`,g.`description`,
			g.`created`,g.`lastModif`,g.`visible`,g.`theme`,g.`user`,
			g.`hits`,g.`maxRows`,g.`rowVideos`,g.`thumbSizeX`,
			g.`thumbSizeY`,g.`public`,g.`sortorder`,g.`sortdirection`,
			g.`parentgallery`
                order by ".$this->convert_sortmode($sort_mode);

                $result = $this->query($query,$bindvars,$maxRecords,$offset);
                $ret = array();

                while ($res = $result->fetchRow()) {
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

	function get_entry_from_video($videoid) {
		$query = "select `entryId` from `tiki_videos` where `videoId`=?";

		$entid = $this->getOne($query,array((int)$videoid));
		return $entid;
	}
	function move_video($vidId, $galId) {
		$query = "update `tiki_videos` set `galleryId`=? where `videoId`=?";

		$result = $this->query($query,array((int)$galId,(int)$vidId));
		return true;
	}

	function get_video_info($id) {


		$query = "select `entryId`,`galleryId`
                 from `tiki_videos` where
                     `videoId`=?";

		$result = $this->query($query,(int)$id);
		$res = $result->fetchRow();

		$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = "123";
		$kaltura_client = new KalturaClient($kaltura_conf);

		$kres = $kaltura_client->start($kuser, $kaltura_conf->secret);

		$kres= $kaltura_client->getEntry ( $kuser , $res[entryId],1);

		$res = array_merge($res,$kres['result']['entry'] );
		return $res;
	}

	function replace_gallery($galleryId, $name, $description, $theme, $user, $maxRows, $rowVideos, $thumbSizeX, $thumbSizeY, $public, $visible = 'y', $sortorder='created', $sortdirection='desc',
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
			$query = "update `tiki_galleries_video` set `name`=?,`visible`=?, `maxRows`=? , `rowVideos`=?,
                `thumbSizeX`=?, `thumbSizeY`=?, `description`=?, `theme`=?,
                `lastModif`=?, `public`=?, `sortorder`=?, `sortdirection`=?,
		`parentgallery`=?,`showname`=?,`showvideoid`=?,`showcategories`=?,`showdescription`=?,
		`showcreated`=?,`showuser`=?,`showhits`=?, `user`=?
	       	where `galleryId`=?";

			$result =
$this->query($query,array($name,$visible,(int)$maxRows,(int)$rowVideos,(int)$thumbSizeX,(int)$thumbSizeY,$description,$theme,(int)$this->now,$public,$sortorder,$sortdirection,(int)$parentgallery,$showname,$showvideoid,$showcategories,$showdescription,$showcreated,$showuser,$showhits,$user,(int)$galleryId));
		} else {
			// Create a new record
			$query = "insert into
`tiki_galleries_video`(`name`,`description`,`theme`,`created`,`user`,`lastModif`,`maxRows`,`rowVideos`,`thumbSizeX`,`thumbSizeY`,`public`,`hits`,`visible`,`sortorder`,`sortdirection`,`parentgallery`,`showname`,`showvideoid`,`showdescription`,`showcategories`,`showcreated`,`showuser`,`showhits`)
values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($name,$description,$theme,(int) $this->now,$user,(int) $this->now,(int) $maxRows,(int) $rowVideos,(int) $thumbSizeX,(int)
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

	function remove_gallery($id) {
		global $prefs;

		$query = "delete from `tiki_galleries_video` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));
		$query = "delete from `tiki_videos` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));

		$this->remove_object('video gallery', $id);
		return true;
	}

	function get_gallery_info($id) {
		// alias for get_gallery
		return $this->get_gallery($id);
	}



  function clear_class_vars()
  { // function to clear loaded data. Usable for mass changes
     unset($this->videoId);
     unset($this->galleryId);
     unset($this->name);
     unset($this->description);
     unset($this->created);
     unset($this->user);
     unset($this->hits);
     unset($this->video);
  }
  /* compute the ratio the image $xsize,$size must have to go in the box */

}
global $dbTiki;
global $videogallib;
$videogallib = new VideoGalsLib($dbTiki);

?>





