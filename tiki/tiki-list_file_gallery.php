<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_file_gallery.php,v 1.50.2.3 2008-02-15 13:52:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}

include_once ('lib/filegals/filegallib.php');
include_once ('lib/stats/statslib.php');

$auto_query_args = array('galleryId','fileId','offset','find','sort_mode','edit_mode','file_sort_mode','file_find','file_offset','page','filegals_manager');

if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once('lib/categories/categlib.php');
}

if (empty($_REQUEST['galleryId']) || !($gal_info = $tikilib->get_file_gallery($_REQUEST['galleryId']))) {
	$smarty->assign('msg', tra("Non-existent gallery"));
	$smarty->display("error.tpl");
	die;
}

$podCastGallery = $filegallib->isPodCastGallery($_REQUEST['galleryId'], $gal_info);

$tikilib->get_perm_object($_REQUEST["galleryId"], 'file gallery', $gal_info, true);

if ($tiki_p_admin_file_galleries == 'y') {
	if (isset($_REQUEST['delsel_x'])) {
		check_ticket('list-fgal');
		foreach (array_values($_REQUEST['file'])as $file) {
			if ($_REQUEST['file'] > 0) {
				$info = $filegallib->get_file_info($file);
				$smarty->assign('fileId', $file);
				$smarty->assign('galleryId', $_REQUEST['galleryId']);
				$smarty->assign_by_ref('filename', $info['filename']);
				$smarty->assign_by_ref('fname', $info['name']);
				$smarty->assign_by_ref('fdescription', $info['description']);
			}
			$filegallib->remove_file($info, $user, $gal_info);
		}
	}

	if (isset($_REQUEST['movesel'])) {
		check_ticket('list-fgal');
		foreach (array_values($_REQUEST['file'])as $file) {
			// To move a topic you just have to change the object
			$filegallib->set_file_gallery($file, $_REQUEST['moveto']);
		}
	}
}

if ($tiki_p_view_file_gallery != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign_by_ref('gal_info', $gal_info);
if (!empty($gal_info['subgal_conf'])) {
	list($fgal_list_id, $fgal_list_name, $fgal_list_description, $fgal_list_type, $fgal_list_created, $fgal_list_lastmodif, $fgal_list_user, $fgal_list_files, $fgal_list_hits, $fgal_list_parent) = split(':',$gal_info['subgal_conf']);
	$smarty->assign('fgal_list_id', $fgal_list_id);
	$smarty->assign('fgal_list_name', $fgal_list_name);
	$smarty->assign('fgal_list_description', $fgal_list_description);
	$smarty->assign('fgal_list_type', $fgal_list_type);
	$smarty->assign('fgal_list_created', $fgal_list_created);
	$smarty->assign('fgal_list_lastmodif', $fgal_list_lastmodif);
	$smarty->assign('fgal_list_user', $fgal_list_user);
	$smarty->assign('fgal_list_files', $fgal_list_files);
	$smarty->assign('fgal_list_hits', $fgal_list_hits);
	$smarty->assign('fgal_list_parent', $fgal_list_parent);
}

$smarty->assign_by_ref('galleryId', $_REQUEST['galleryId']);

$tikilib->add_file_gallery_hit($_REQUEST["galleryId"]);

if ( isset($_REQUEST['lock']) && isset($_REQUEST['fileId']) && $_REQUEST['fileId'] > 0 ) {
	if (!$fileInfo = $filegallib->get_file_info($_REQUEST['fileId'])) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display("error.tpl");
		die;
	}

	$error_msg = '';
	if ( $_REQUEST['lock'] == 'n' && ! empty($fileInfo['lockedby']) ) {
		if ( $fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y' ) {
			$error_msg = tra('You do not have permission to do that');
		} else {
			$filegallib->unlock_file($_REQUEST['fileId']);
		}
	} elseif ( $_REQUEST['lock'] == 'y' ) {
		if ( ! empty($fileInfo['lockedby']) ) {
			$error_msg = sprintf(tra('The file is already locked by %s'), $fileInfo['lockedby']);
		} elseif ( $tiki_p_edit_gallery_file != 'y' ) {
			$error_msg = tra('You do not have permission to do that');
		} else {
			$filegallib->lock_file($_REQUEST['fileId'], $user);
		}
	}
	if ( $error_msg != '' ) {
		$smarty->assign('msg', $error_msg);
		$smarty->display('error.tpl');
		die;
	}
}

if (!empty($_REQUEST['remove'])) {
	// To remove an image the user must be the owner or the file or the gallery or admin
  if (!$info = $filegallib->get_file_info($_REQUEST['remove'])) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display("error.tpl");
		die;		
	}
	if ($tiki_p_admin_file_galleries != 'y'  && (!$user || $user != $gal_info['user'])) {
		if ($user != $info['user']) {
			$smarty->assign('msg', tra("Permission denied you cannot remove files from this gallery"));
			$smarty->display("error.tpl");
			die;
		}
	}
	$area = 'delfile';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
	  key_check($area);

//Watches
		$smarty->assign('fileId', $_REQUEST['remove']);
		$smarty->assign('galleryId', $_REQUEST['galleryId']);
		$smarty->assign_by_ref('filename', $info['filename']);
		$smarty->assign_by_ref('fname', $info['name']);
		$smarty->assign_by_ref('fdescription', $info['description']);
		$filegallib->remove_file($info, $user, $gal_info);

  } else {
	  key_get($area, tra('Remove file: ').(!empty($info['name'])?$info['name'].' - ':'').$info['filename']);
  }
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$smarty->assign('url', $tikilib->httpPrefix(). $foo["path"]);

// Init smarty variables to blank values
$smarty->assign('fname', '');
$smarty->assign('fdescription', '');

if (isset($_REQUEST["edit_mode"])and ($_REQUEST['edit_mode'])) {
	$smarty->assign('edit_mode', 'y');

	$smarty->assign('edited', 'y');

	if ($_REQUEST['fileId'] > 0) {
		$info = $filegallib->get_file_info($_REQUEST['fileId']);

		$smarty->assign('fileId', $_REQUEST['fileId']);
		$smarty->assign('galleryId', $_REQUEST['galleryId']);
		$smarty->assign_by_ref('filename', $info['filename']);
		$smarty->assign_by_ref('fname', $info['name']);
		$smarty->assign_by_ref('fdescription', $info['description']);
	}
}

if (isset($_REQUEST['edit'])) {
		check_ticket('list-fgal');
	if ($tiki_p_admin_file_galleries != 'y') {
		if ($tiki_p_upload_files != 'y') {
			// If you can't upload files then you can't edit a file you can't have a file
			$smarty->assign('msg', tra("Permission denied you can't upload files so you can't edit them"));

			$smarty->display("error.tpl");
			die;
		}

		// If the user can upload a file then check if he can edit THIS file
		if ($_REQUEST["fileId"] > 0) {
			$info = $filegallib->get_file_info($_REQUEST["fileId"]);

			if (!$user || $info["user"] != $user) {
				$smarty->assign('msg', tra("Permission denied you cannot edit this file"));

				$smarty->display("error.tpl");
				die;
			}
		}
	}

	// Everything is ok so we proceed to edit the file
	$smarty->assign('edit_mode', 'y');
	$smarty->assign_by_ref('fname', $_REQUEST["fname"]);
	$smarty->assign_by_ref('fdescription', $_REQUEST["fdescription"]);

	$fid = $filegallib->replace_file($_REQUEST["fileId"], $_REQUEST["fname"], $_REQUEST["fdescription"], $info['filename'], $info['data'], $info['filesize'], $info['filetype'], $info['user'], $info['path'], $info['galleryId']);

	/*
	  $cat_type='file gallery';
	  $cat_objid = $fgid;
	  $cat_desc = substr($_REQUEST["description"],0,200);
	  $cat_name = $_REQUEST["name"];
	  $cat_href="tiki-list_file_gallery.php?galleryId=".$cat_objid;
	  include_once("categorize.php");
	*/
	$smarty->assign('edit_mode', 'n');
}

if (!isset($gal_info["maxRows"]))
	$gal_info["maxRows"] = 10;

if ($gal_info["maxRows"] == 0)
	$gal_info["maxRows"] = 10;

$maxRecords = $gal_info["maxRows"];
$smarty->assign('maxRecords', $maxRecords);
$smarty->assign_by_ref('name', $gal_info["name"]);
$smarty->assign_by_ref('description', $gal_info["description"]);

if (!isset($_REQUEST["file_sort_mode"])) {
	$file_sort_mode = $gal_info['sort_mode']? $gal_info['sort_mode']: 'created_desc';

	$_REQUEST["file_sort_mode"] = $file_sort_mode;
} else {
	$file_sort_mode = $_REQUEST["file_sort_mode"];
}

$smarty->assign_by_ref('file_sort_mode', $file_sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["file_offset"])) {
	$file_offset = 0;

	$_REQUEST["file_offset"] = 0;
} else {
	$file_offset = $_REQUEST["file_offset"];
}

$smarty->assign_by_ref('file_offset', $file_offset);

if (isset($_REQUEST['file_find'])) {
	$file_find = $_REQUEST['file_find'];
} else {
	$file_find = '';

	$_REQUEST['file_find'] = '';
}

$smarty->assign('file_find', $file_find);

$files = $tikilib->get_files($file_offset, $maxRecords, $file_sort_mode, $file_find, $_REQUEST["galleryId"], true);
$smarty->assign_by_ref('file_cant', $files['cant']);
$smarty->assign('file_actual_page', 1 + ($file_offset / $maxRecords));

if ($files["cant"] > ($file_offset + $maxRecords)) {
	$smarty->assign('file_next_offset', $file_offset + $maxRecords);
} else {
	$smarty->assign('file_next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($file_offset > 0) {
	$smarty->assign('file_prev_offset', $file_offset - $maxRecords);
} else {
	$smarty->assign('file_prev_offset', -1);
}

$smarty->assign_by_ref('files', $files['data']);

if ($prefs['feature_file_galleries_comments'] == 'y') {
	$comments_per_page = $prefs['file_galleries_comments_per_page'];

	$thread_sort_mode = $prefs['file_galleries_comments_default_ordering'];
	$comments_vars = array('galleryId', 'offset', 'sort_mode', 'find', 'file_offset', 'file_sort_mode', 'file_find');

	$comments_prefix_var = 'file gallery:';
	$comments_object_var = 'galleryId';
	include_once ("comments.php");
}

// sub galleries
if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;
if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'name_asc';
if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';
$galleries = $filegallib->list_file_galleries($_REQUEST['offset'], $maxRecords, $_REQUEST['sort_mode'], $user, $_REQUEST['find'], $_REQUEST["galleryId"]);

$smarty->assign('cant', $galleries['cant']);
$smarty->assign_by_ref('galleries', $galleries['data']);
$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);
$smarty->assign_by_ref('find', $_REQUEST['find']);
$smarty->assign_by_ref('offset', $_REQUEST['offset']);

$section = 'file_galleries';
include_once ('tiki-section_options.php');

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'file gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include ('tiki-tc.php');
}

// Watches
$galleryId = $_REQUEST["galleryId"];
                                
if (!isset($_REQUEST["galleryName"])) {
        $galleryName = '';
} else {
        $galleryName = $_REQUEST["galleryName"];
}

if($prefs['feature_user_watches'] == 'y') {
    if($user && isset($_REQUEST['watch_event'])) {
        check_ticket('index');
        if($_REQUEST['watch_action']=='add') {
            $tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'File Gallery',$galleryName,"tiki-list_file_gallery.php?galleryId=$galleryId");
        } else {
            $tikilib->remove_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object']);
        }   
    }
    $smarty->assign('user_watching_file_gallery','n');
    if($user && $tikilib->user_watches($user,'file_gallery_changed',$galleryId, 'File Gallery')) {
        $smarty->assign('user_watching_file_gallery','y');
    }
    // Check, if the user is watching this file gallery by a category.    
	if ($prefs['feature_categories'] == 'y') { 		  
	    $watching_categories_temp=$categlib->get_watching_categories($galleryId,'file gallery',$user);	    
	    $smarty->assign('category_watched','n');
	 	if (count($watching_categories_temp) > 0) {
	 		$smarty->assign('category_watched','y');
	 		$watching_categories=array();	 			 	
	 		foreach ($watching_categories_temp as $wct ) {								 	
	 			$watching_categories[]=array("categId"=>$wct,"name"=>$categlib->get_category_name($wct));
	 		}		 		 	
	 		$smarty->assign('watching_categories', $watching_categories);
	 	}    
	} 	        
}


if ($tiki_p_admin_file_galleries == 'y' && isset($_REQUEST['movesel_x'])) {
	$all_galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user, '');
	$smarty->assign('all_galleries', $all_galleries['data']);
 }
if ($podCastGallery) {
	$smarty->assign('download_path', $prefs['fgal_podcast_dir']);
} else {
	$smarty->assign('download_path', $prefs['fgal_use_dir']);
}
ask_ticket('list-fgal');

//add a hit
$statslib->stats_hit($gal_info["name"],"file gallery",$galleryId);
if ($prefs['feature_actionlog'] == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Viewed', $galleryId, 'file gallery');
}
if (isset($_GET['filegals_manager'])) {
  $smarty->assign('filegals_manager','y');
  $smarty->assign('mid','tiki-list_file_gallery.tpl');
  $smarty->display("tiki_full.tpl");
} else {
// Display the template
$smarty->assign('mid', 'tiki-list_file_gallery.tpl');
$smarty->display("tiki.tpl");
}

?>
