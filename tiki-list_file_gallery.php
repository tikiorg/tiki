<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_file_gallery.php,v 1.50.2.5 2008-02-27 15:54:12 sylvieg Exp $

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

$auto_query_args = array('galleryId','fileId','offset','find','sort_mode','edit_mode','page','filegals_manager','maxRecords');

if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once('lib/categories/categlib.php');
}

if ( empty($_REQUEST['galleryId']) || !($gal_info = $tikilib->get_file_gallery($_REQUEST['galleryId'])) ) {
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

if ( isset($_REQUEST["edit_mode"]) and ($_REQUEST['edit_mode']) ) {
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

	$smarty->assign('edit_mode', 'n');
}

if ( ! isset($_REQUEST['maxRecords']) || $_REQUEST['maxRecords'] <= 0 ) {
	if ( isset($gal_info['maxRows']) && $gal_info['maxRows'] > 0 ) {
		$_REQUEST['maxRecords'] = $gal_info['maxRows'];
	} else {
		$_REQUEST['maxRecords'] = $prefs['maxRecords'];
	}
}
$smarty->assign_by_ref('maxRecords', $_REQUEST['maxRecords']);

if ( ! isset($_REQUEST['offset']) ) $_REQUEST['offset'] = 0;
$smarty->assign_by_ref('offset', $_REQUEST['offset']);

if ( ! isset($_REQUEST['sort_mode']) ) {
	$_REQUEST['sort_mode'] = ( $gal_info['show_name'] == 'f' ? 'filename_asc' : 'name_asc' );
}
$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);

if ( ! isset($_REQUEST['find']) ) $_REQUEST['find'] = '';
$smarty->assign_by_ref('find', $_REQUEST['find']);

$smarty->assign_by_ref('name', $gal_info["name"]);
$smarty->assign_by_ref('description', $gal_info["description"]);

$files = $tikilib->get_files($_REQUEST['offset'], $_REQUEST['maxRecords'], $_REQUEST['sort_mode'], $_REQUEST['find'], $_REQUEST['galleryId'], true, true);
$smarty->assign_by_ref('files', $files['data']);
$smarty->assign('cant', $files['cant']);

if ($prefs['feature_file_galleries_comments'] == 'y') {
	$comments_per_page = $prefs['file_galleries_comments_per_page'];

	$thread_sort_mode = $prefs['file_galleries_comments_default_ordering'];
	$comments_vars = array('galleryId', 'offset', 'sort_mode', 'find');

	$comments_prefix_var = 'file gallery:';
	$comments_object_var = 'galleryId';
	include_once ("comments.php");
}



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

$all_galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user);

// Build galleries browsing tree and current gallery path array
//
function add2tree(&$tree, &$galleries, &$gallery_id, &$gallery_path, $cur_id = -1) {
	$i = 0;
	$current_path = array();
	$path_found = false;
	foreach ( $galleries as $gk => $gv ) {
		if ( $gv['parentId'] == $cur_id && $gv['id'] != $cur_id ) {
			$tree[$i] = &$galleries[$gk];
			$tree[$i]['link_var'] = 'galleryId';
			$tree[$i]['link_id'] = $gv['id'];
			add2tree($tree[$i]['data'], $galleries, $gallery_id, $gallery_path, $gv['id']);
			if ( ! $path_found && $gv['id'] == $gallery_id ) {
				if ( $_REQUEST['galleryId'] == $gv['id'] ) $tree[$i]['current'] = 1;
				array_unshift($gallery_path, array($gallery_id, $gv['name']));
				$gallery_id = $cur_id;
				$path_found = true;
			}
			$i++;
		}
	}
}

if ( is_array($all_galleries) && count($all_galleries) > 0 ) {
	$tree = array('name' => tra('File Galleries'), 'data' => array());
	$gallery_path = array();

	add2tree($tree['data'], $all_galleries['data'], $galleryId, $gallery_path);

	array_unshift($gallery_path, array(0, $tree['name']));
	$gallery_path_str = '';
	foreach ( $gallery_path as $dir_id ) {
		if ( $gallery_path_str != '' ) $gallery_path_str .= ' &nbsp;&gt;&nbsp;';
		$gallery_path_str .= ( $dir_id[0] > 0 ? '<a href="tiki-list_file_gallery.php?galleryId='.$dir_id[0].( isset($_REQUEST['filegals_manager']) ? '&amp;filegals_manager' : '').'">'.$dir_id[1].'</a>' : $dir_id[1]);
	}
}

$smarty->assign('gallery_path', $gallery_path_str);
$smarty->assign_by_ref('tree', $tree);

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

include_once('fgal_listing_conf.php');

$smarty->assign('mid','tiki-list_file_gallery.tpl');

// Display the template
if ( isset($_REQUEST['filegals_manager']) ) {
	$smarty->assign('filegals_manager','y');
	$smarty->display('tiki_full.tpl');
} else {
	$smarty->display('tiki.tpl');
}

?>
