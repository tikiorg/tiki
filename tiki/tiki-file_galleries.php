<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-file_galleries.php,v 1.57.2.5 2008-02-27 15:18:36 nyloth Exp $

require_once('tiki-setup.php');
include_once('lib/filegals/filegallib.php');

$auto_query_args = array('galleryId','offset','find','sort_mode','filegals_manager','edit_mode','dup_mode','maxRecords');
	
if ( $prefs['feature_file_galleries'] != 'y' ) {
	$smarty->assign('msg', tra('This feature is disabled').': feature_file_galleries');
	$smarty->display('error.tpl');
	die;  
}

$gal_info = '';
if ( ! isset($_REQUEST['galleryId']) ) {
	$_REQUEST['galleryId'] = 0;
} else {
	$gal_info = $filegallib->get_file_gallery_info($_REQUEST['galleryId']);
}

$tikilib->get_perm_object($_REQUEST['galleryId'], 'file gallery', $gal_info, true);

if ( ( isset($tiki_p_list_file_galleries) && $tiki_p_list_file_galleries != 'y' ) || ( ! isset($tiki_p_view_file_galleries) && $tiki_p_view_file_gallery != 'y' ) ) {
	$smarty->assign('msg', tra('Permission denied you cannot view this section'));
	$smarty->display('error.tpl');
	die;
}

$smarty->assign('individual','n');
if ( $userlib->object_has_one_permission($_REQUEST['galleryId'], 'file gallery') ) {
	$smarty->assign('individual','y');
	if ( $tiki_p_admin != 'y' ) {
		// Now get all the permissions that are set for this type of permissions 'file gallery'
		$perms = $userlib->get_permissions(0,-1,'permName_desc','','file galleries');
		foreach ( $perms['data'] as $perm ) {
			$permName = $perm['permName'];
			if ( $userlib->object_has_permission($user, $_REQUEST['galleryId'], 'file gallery', $permName) ) {
				$$permName = 'y';
				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';
				$smarty->assign("$permName", 'n');
			}
		}
	}
} elseif ( $tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y' ) {
	global $categlib; include_once('lib/categories/categlib.php');
	$perms_array = $categlib->get_object_categories_perms($user, 'file gallery', $_REQUEST['galleryId']);
	if ( $perms_array ) {
		$is_categorized = TRUE;
		foreach ( $perms_array as $perm => $value ) {
			$$perm = $value;
		}
	} else {
		$is_categorized = FALSE;
	}
	if ( $is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y' ) {
		if ( ! isset($user) ) {
			$smarty->assign('msg', $smarty->fetch('modules/mod-login_box.tpl'));
			$smarty->assign('errortitle', tra('Please login'));
		} else {
			$smarty->assign('msg', tra('Permission denied you cannot view this page'));
		}
		$smarty->display('error.tpl');
		die;
	}
}
	
if ( isset($_REQUEST['find']) ) {
	$find = $_REQUEST['find'];  
} else {
	$find = ''; 
}
$smarty->assign('find', $find);
	
$smarty->assign('galleryId', $_REQUEST['galleryId']);
	
$foo = parse_url($_SERVER['REQUEST_URI']);
$foo['path'] = str_replace('tiki-file_galleries', 'tiki-list_file_gallery', $foo['path']);
$smarty->assign('url', $tikilib->httpPrefix().$foo['path']);
	
// Init smarty variables to blank values
//$smarty->assign('theme', '');
$smarty->assign('name', '');
$smarty->assign('description', '');
$smarty->assign('max_desc', 1024);
$smarty->assign('maxRows', 10);
$smarty->assign('public', 'n');
$smarty->assign('lockable', 'n');
$smarty->assign('archives', -1);
$smarty->assign('edited', 'n');
$smarty->assign('edit_mode', 'n');
$smarty->assign('dup_mode', 'n');
$smarty->assign('visible', 'y');
$smarty->assign('fgal_type', 'default');
$smarty->assign('parentId', -1);
$smarty->assign('creator', $user);
$smarty->assign('sortorder', 'created');
$smarty->assign('sortdirection', 'desc');
	
// If we are editing an existing gallery prepare smarty variables
if ( isset($_REQUEST['edit_mode']) && $_REQUEST['edit_mode'] ) {
	// Get information about this galleryID and fill smarty variables
	$smarty->assign('edit_mode', 'y');
	$smarty->assign('edited', 'y');

	if ( $_REQUEST['galleryId'] > 0 ) {
		$smarty->assign_by_ref('name', $gal_info['name']);
		$smarty->assign_by_ref('description', $gal_info['description']);
		$smarty->assign_by_ref('maxRows', $gal_info['maxRows']);
		$smarty->assign_by_ref('public', $gal_info['public']);
		$smarty->assign_by_ref('lockable', $gal_info['lockable']);
		$smarty->assign_by_ref('archives', $gal_info['archives']);
		$smarty->assign_by_ref('visible', $gal_info['visible']);
		$smarty->assign_by_ref('parentId', $gal_info['parentId']);
		$smarty->assign_by_ref('creator', $gal_info['user']);
		$smarty->assign('max_desc', $gal_info['max_desc']);
		$smarty->assign('fgal_type', $gal_info['type']);

		if ( $gal_info['sort_mode'] && preg_match('/(.*)_(asc|desc)/', $gal_info['sort_mode'], $matches) ) {
			$smarty->assign('sortorder', $matches[1]);
			$smarty->assign('sortdirection', $matches[2]);
		} else {
			$smarty->assign('sortorder', 'created');
			$smarty->assign('sortdirection', 'desc');
		}
	}
} elseif ( ! empty($_REQUEST['dup_mode']) ) {
	$smarty->assign('dup_mode', 'y');
}
	
// Process the insertion or modification of a gallery here
if ( isset($_REQUEST['edit']) ) {
	check_ticket('fgal');

	// Saving information
	// If the user is not gallery admin
	if ( $tiki_p_admin_file_galleries != 'y' ) {
		if ( $tiki_p_create_file_galleries != 'y' ) {
			// If you can't create a gallery then you can't edit a gallery because you can't have a gallery
			$smarty->assign('msg', tra('Permission denied you cannot create galleries and so you cant edit them'));
			$smarty->display('error.tpl');
			die;
		}
		// If the user can create a gallery then check if he can edit THIS gallery
		if ( $_REQUEST['galleryId'] > 0 ) {
			if ( ! $user || $gal_info['user'] != $user ) {
				$smarty->assign('msg', tra('Permission denied you cannot edit this gallery'));
				$smarty->display('error.tpl');
				die;
			}
		}
	}

	// Everything is ok so we proceed to edit the gallery
	$smarty->assign('edit_mode', 'y');
	$smarty->assign_by_ref('name', $_REQUEST['name']);
	$smarty->assign_by_ref('description', $_REQUEST['description']);
	$smarty->assign('max_desc', $_REQUEST['max_desc']);
	$smarty->assign('fgal_type', isset($_REQUEST['type']) ? $_REQUEST['type'] : '');
	$smarty->assign_by_ref('maxRows', $_REQUEST['maxRows']);
	$smarty->assign_by_ref('rowImages', $_REQUEST['rowImages']);
	$smarty->assign_by_ref('thumbSizeX', $_REQUEST['thumbSizeX']);
	$smarty->assign_by_ref('thumbSizeY', $_REQUEST['thumbSizeY']);
	$smarty->assign_by_ref('parentId', $_REQUEST['parentId']);
	$smarty->assign_by_ref('creator', $_REQUEST['creator']);

	if ( isset($_REQUEST['visible']) && $_REQUEST['visible'] == 'on' ) {
		$smarty->assign('visible', 'y');
		$visible = 'y';
	} else {
		$visible = 'n';
	}

	$smarty->assign('visible', $visible);
	if ( isset($_REQUEST['public']) && $_REQUEST['public'] == 'on' ) {
		$smarty->assign('public', 'y');
		$public = 'y';
	} else {
		$public = 'n';
	}
	$smarty->assign('public', $public);

	if ( isset($_REQUEST['lockable']) && $_REQUEST['lockable'] == 'on') {
		$smarty->assign('lockable', 'y');
		$lockable = 'y';
	} else {
		$lockable = 'n';
	}
	$smarty->assign('lockable', $lockable);

	$_REQUEST['archives'] = isset($_REQUEST['archives']) ? $_REQUEST['archives'] : -1;
	$_REQUEST['user'] = isset($_REQUEST['user']) ? $_REQUEST['user'] : ( isset($gal_info['user']) ? $gal_info['user'] : $user );
	$_REQUEST['sortorder'] = isset($_REQUEST['sortorder']) ? $_REQUEST['sortorder'] : 'created';
	$_REQUEST['sortdirection'] = isset($_REQUEST['sortdirection']) && $_REQUEST['sortdirection'] == 'asc' ? 'asc' : 'desc';

	$fgid = $filegallib->replace_file_gallery(
		$_REQUEST['galleryId'],
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['user'],
		$_REQUEST['maxRows'],
		$public,
		$visible,
		$_REQUEST['fgal_list_id'],
		$_REQUEST['fgal_list_type'],
		$_REQUEST['fgal_list_name'],
		$_REQUEST['fgal_list_size'],
		$_REQUEST['fgal_list_description'],
		$_REQUEST['fgal_list_created'],
		$_REQUEST['fgal_list_hits'],
		$_REQUEST['max_desc'],
		$_REQUEST['fgal_type'],
		$_REQUEST['parentId'],
		$lockable,
		$_REQUEST['fgal_list_lockedby'],
		$_REQUEST['archives'],
		$_REQUEST['sortorder'].'_'.$_REQUEST['sortdirection'],
		$_REQUEST['fgal_list_lastmodif'],
		$_REQUEST['fgal_list_creator'],
		$_REQUEST['fgal_list_author'],
		$_REQUEST['subgal_conf'],
		$_REQUEST['fgal_list_user'],
		$_REQUEST['fgal_list_comment'],
		$_REQUEST['fgal_list_files']
	);
	  
	if ( $prefs['feature_categories'] == 'y' ) {
		$cat_type = 'file gallery';
		$cat_objid = $fgid;
		$cat_desc = substr($_REQUEST['description'], 0, $_REQUEST['max_desc']);
		$cat_name = $_REQUEST['name'];
		$cat_href = 'tiki-list_file_gallery.php?galleryId='.$cat_objid;
		include_once('categorize.php');
		$categlib->build_cache();
	}

	if ( isset($_REQUEST['viewitem']) ) {
		header('location: tiki-list_file_gallery.php?galleryId='.$fgid);
		die;
	}

	$smarty->assign('edit_mode','n');

}
	
if ( ! empty($_REQUEST['duplicate']) && ! empty($_REQUEST['name']) && ! empty($_REQUEST['galleryId']) ) {
	$newGalleryId = $filegallib->duplicate_file_gallery(
		$_REQUEST['galleryId'],
		$_REQUEST['name'],
		isset($_REQUEST['description']) ? $_REQUEST['description'] : ''
	);
	if ( isset($_REQUEST['dupCateg']) && $_REQUEST['dupCateg'] == 'on' && $prefs['feature_categories'] == 'y' ) {
		global $categlib; include_once('lib/categories/categlib.php');
		$cats = $categlib->get_object_categories('file gallery', $_REQUEST['galleryId']);
		$catObjectId = $categlib->add_categorized_object(
			'file gallery',
			$newGalleryId,
			( isset($_REQUEST['description']) ? $_REQUEST['description'] : '' ),
			$_REQUEST['name'],
			'tiki-list_file_gallery.php?galleryId='.$newGalleryId
		);
		foreach ( $cats as $cat ) {
			$categlib->categorize($catObjectId, $cat);
		}
	}
	if ( isset($_REQUEST['dupPerms']) && $_REQUEST['dupPerms'] == 'on' ) {
		global $userlib; include_once('lib/userslib.php');
		$userlib->copy_object_permissions($_REQUEST['galleryId'], $newGalleryId, 'file gallery');
	}
	$_REQUEST['galleryId'] = $newGalleryId;
}

if ( ! empty($_REQUEST['removegal']) ) {
	if ( ! ( $gal_info = $filegallib->get_file_gallery_info($_REQUEST['removegal']) ) ) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}
	if ( $tiki_p_admin_file_galleries != 'y' && ( ! $user || $gal_info['user'] != $user ) ) {
		$smarty->assign('msg', tra('Permission denied you cannot remove this gallery'));
		$smarty->display('error.tpl');
		die;
	}
	$area = 'delfilegal';
	if ( $prefs['feature_ticketlib2'] != 'y' or ( isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]) ) ) {
		key_check($area);
		$filegallib->remove_file_gallery($_REQUEST['removegal'], $_REQUEST['galleryId']);
	} else {
		key_get($area, tra('Remove file gallery: ').' '.$gal_info['name']);
	}
}

if ( isset($_REQUEST['batchaction']) &&  $_REQUEST['batchaction'] == 'delsel_x' && isset($_REQUEST['checked']) ) {
	check_ticket('fgal');
	if ( $tiki_p_admin_file_galleries != 'y' ) {
		$smarty->assign('msg', tra('Permission denied you cannot remove this gallery'));
		$smarty->display('error.tpl');
		die;  
	}
	foreach ( $_REQUEST['checked'] as $id ) {
		$filegallib->remove_file_gallery($id);
	}
}

if ( isset($_REQUEST['batchaction']) && $_REQUEST['batchaction'] != 'delsel_x' && isset($_REQUEST['checked']) && isset($_REQUEST['groups']) ) {
	check_ticket('fgal');
	if ( $tiki_p_admin_file_galleries != 'y' && $tiki_p_assign_perm_file_gallery != 'y' ) {
		$smarty->assign('msg', tra('Permission denied you cannot assign permissions for this object'));
		$smarty->display('error.tpl');
		die;
	}
	$perms = $userlib->get_permissions(0, -1, 'permName_asc', '', 'file galleries');
	foreach ( $perms['data'] as $perm ) {
		if ( $_REQUEST['batchaction'] == 'assign_'.$perm['permName'] ) {
			foreach ( $_REQUEST['checked'] as $id ) {
				foreach ( $_REQUEST['groups'] as $group ) {
					$userlib->assign_object_permission($group, $id, 'file gallery', $perm['permName']);
				}
			}
		}
	}
}

if ( isset($_REQUEST['sort_mode']) ) {
	$sort_mode = $_REQUEST['sort_mode'];
} elseif ( ! empty($prefs['fgal_sort_mode']) ) {
	$sort_mode = $prefs['fgal_sort_mode'];
} else {
	$sort_mode = ( $prefs['fgal_list_name'] == 'f' ? 'filename_asc' : 'name_asc' );
} 
$smarty->assign_by_ref('sort_mode',$sort_mode);

if ( isset($_REQUEST['maxRecords']) && $_REQUEST['maxRecords'] > 0 ) {
	$maxRecords = $_REQUEST['maxRecords'];
} else {
	$maxRecords = $prefs['maxRecords'];
}
$smarty->assign_by_ref('maxRecords', $maxRecords);

if ( isset($_REQUEST['offset']) ) {
	$offset = (int)$_REQUEST['offset']; 
} else {
	$offset = 0;
}
$smarty->assign_by_ref('offset', $offset);

$all_galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user);
$smarty->assign_by_ref('all_galleries', $all_galleries['data']);

$files = $tikilib->get_files($offset, $maxRecords, $sort_mode, $find, 0, true, true);
$smarty->assign_by_ref('cant', $files['cant']);
$smarty->assign_by_ref('files', $files['data']);

if ( $tiki_p_admin_file_galleries == 'y' ) {
	$users = $tikilib->list_users(0, -1, 'login_asc', '', false);
	$smarty->assign_by_ref('users', $users['data']);
}
if ( $tiki_p_admin_file_galleries == 'y' || $tiki_p_assign_perm_file_gallery == 'y' ) {
	if ( ! isset($perms) ) {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'file galleries');
	}
	$smarty->assign_by_ref('perms', $perms['data']);
	$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$smarty->assign_by_ref('groups', $groups['data']);
}

$options_sortorder = array(
	tra('Creation Date') => 'created',
	tra('Name') => 'name',
	tra('Filename') => 'filename',
	tra('Size') => 'filesize',
	tra('Owner') => 'user',
	tra('Hits') => 'hits',
	tra('ID') => 'fileId'
);
$smarty->assign_by_ref('options_sortorder', $options_sortorder);
	
$cat_type = 'file gallery';
$cat_objid = $_REQUEST['galleryId'];
include_once('categorize_list.php');
	
$section = 'file_galleries';
include_once('tiki-section_options.php');

// Initialize listing fields with default values
if ( $gal_info == '' || ( ! isset($_REQUEST['edit']) && ! isset($_REQUEST['edit_mode']) && ! isset($_REQUEST['duplicate']) ) ) {
	$gal_info = array(
		'name' => 'File Galleries',
		'show_id' => $prefs['fgal_list_id'],
		'show_icon' => $prefs['fgal_list_type'],
		'show_name' => 'f', //$prefs['fgal_list_name'],
		'show_description' => $prefs['fgal_list_description'],
		'show_size' => $prefs['fgal_list_size'],
		'show_created' => $prefs['fgal_list_created'],
		'show_modified' => $prefs['fgal_list_lastmodif'],
		'show_creator' => $prefs['fgal_list_creator'],
		'show_author' => $prefs['fgal_list_author'],
		'show_last_user' => $prefs['fgal_list_last_user'],
		'show_comment' => $prefs['fgal_list_comment'],
		'show_files' => $prefs['fgal_list_files'],
		'show_hits' => $prefs['fgal_list_hits'],
		'show_lockedby' => $prefs['fgal_list_lockedby'],
		'show_checked' => 'y',
		'show_userlink' => 'y'
	);
}
$smarty->assign_by_ref('gal_info', $gal_info);
include_once('fgal_listing_conf.php');
	
ask_ticket('fgal');

$smarty->assign('mid', 'tiki-file_galleries.tpl');

// Display the template
if ( isset($_REQUEST['filegals_manager']) ) {
	$smarty->assign('filegals_manager', 'y');
	$smarty->display('tiki_full.tpl');
} else {
	$smarty->display('tiki.tpl');
}
?>
