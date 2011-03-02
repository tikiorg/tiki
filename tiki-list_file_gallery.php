<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'file_galleries';
require_once ('tiki-setup.php');
$access->check_feature(array('feature_file_galleries', 'feature_jquery_tooltips'));
include_once ('lib/filegals/filegallib.php');
include_once ('lib/stats/statslib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	include_once ('lib/categories/categlib.php');
}

if ($prefs['feature_file_galleries_templates'] == 'y') {
	global $templateslib;
	include_once ('lib/templates/templateslib.php');
}

if ($prefs['feature_groupalert'] == 'y') {
	include_once ('lib/groupalert/groupalertlib.php');
}

$auto_query_args = array( 'galleryId'
												, 'fileId'
												, 'offset'
												, 'find'
												, 'find_creator'
												, 'find_categId'
												, 'sort_mode'
												, 'edit_mode'
												, 'page'
												, 'filegals_manager'
												, 'maxRecords'
												, 'show_fgalexplorer'
												, 'dup_mode'
												, 'show_details'
												, 'view'
												);
$gal_info = '';

if ( empty($_REQUEST['galleryId']) && isset($_REQUEST['parentId']) ) {

	$tikilib->get_perm_object('', 'file gallery');
	$_REQUEST['galleryId'] = 0;

	// Initialize listing fields with default values (used for the main gallery listing)
	$gal_info = $filegallib->get_file_gallery();
	$gal_info['usedSize'] = 0;
	$gal_info['maxQuota'] = $filegallib->getQuota($_REQUEST['parentId'], true);

} else {
	if ( ! isset($_REQUEST['galleryId']) ) {
		$_REQUEST['galleryId'] = $prefs['fgal_root_id'];
	}

	if ( $gal_info = $filegallib->get_file_gallery($_REQUEST['galleryId']) ) {
		$tikilib->get_perm_object($_REQUEST['galleryId'], 'file gallery', $gal_info);
		if ($userlib->object_has_one_permission($_REQUEST['galleryId'], 'file gallery')) {
			$smarty->assign('individual', 'y');
		}
		$podCastGallery = $filegallib->isPodCastGallery($_REQUEST['galleryId'], $gal_info);
	} else {
		$smarty->assign('msg', tra('Non-existent gallery'));
		$smarty->display('error.tpl');
		die;
	}
	$gal_info['usedSize'] = $filegallib->getUsedSize($_REQUEST['galleryId']);
	$gal_info['maxQuota'] = $filegallib->getQuota($gal_info['parentId']);
	$gal_info['minQuota'] = $filegallib->getMaxQuotaDescendants($_REQUEST['galleryId']);
}

$galleryId = $_REQUEST['galleryId'];
if (($galleryId != 0 || $tiki_p_list_file_galleries != 'y') && ($galleryId == 0 || $tiki_p_view_file_gallery != 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to view this section'));
	$smarty->display('error.tpl');
	die;
}

// Init smarty variables to blank values
$smarty->assign('fname', '');
$smarty->assign('fdescription', '');
$smarty->assign('max_desc', 1024);
$smarty->assign('maxRows', $maxRecords);
$smarty->assign('edited', 'n');
$smarty->assign('edit_mode', 'n');
$smarty->assign('dup_mode', 'n');
$smarty->assign('parentId', isset($_REQUEST['parentId']) ? (int)$_REQUEST['parentId'] : (isset($gal_info['parentId']) ? $gal_info['parentId'] : -1));
$smarty->assign('creator', $user);
$smarty->assign('sortorder', 'created');
$smarty->assign('sortdirection', 'desc');
$smarty->assign_by_ref('name', $gal_info['name']);
$smarty->assign_by_ref('galleryId', $_REQUEST['galleryId']);
$smarty->assign('reindex_file_id', -1);

// Execute batch actions
if ($tiki_p_admin_file_galleries == 'y') {
	if (isset($_REQUEST['delsel_x'])) {
		check_ticket('fgal');
		if (isset($_REQUEST['file'])) {
			foreach(array_values($_REQUEST['file']) as $file) {
				if ($info = $filegallib->get_file_info($file)) {
					$filegallib->remove_file($info, $gal_info);
				}
			}
		}

		if (isset($_REQUEST['subgal'])) {
			foreach(array_values($_REQUEST['subgal']) as $subgal) {
				$filegallib->remove_file_gallery($subgal, $galleryId);
			}
		}
	}
	
	if (isset($_REQUEST['movesel'])) {
		check_ticket('fgal');
		if (isset($_REQUEST['file'])) {
			foreach(array_values($_REQUEST['file']) as $file) {
				$filegallib->set_file_gallery($file, $_REQUEST['moveto']);
			}
		}
		if (isset($_REQUEST['subgal'])) {
			foreach(array_values($_REQUEST['subgal']) as $subgal) {
				$filegallib->move_file_gallery($subgal, $_REQUEST['moveto']);
			}
		}
	}
	
	if (isset($_REQUEST['defaultsel_x'])) {
		check_ticket('fgal');
		if (!empty($_REQUEST['subgal'])) {
			$filegallib->setDefault(array_values($_REQUEST['subgal']));
		} else if (!empty($_REQUEST['galleryId'])) {
			$filegallib->setDefault(array((int)$_REQUEST['galleryId']));
		}
		unset($_REQUEST['view']);
	}
}

if (isset($_REQUEST['zipsel_x']) && $tiki_p_upload_files == 'y') {
	check_ticket('fgal');
	$href = array();
	if (isset($_REQUEST['file'])) {
		foreach(array_values($_REQUEST['file']) as $file) {
			$href[] = "fileId[]=$file";
		}
	}
	if (isset($_REQUEST['subgal'])) {
		foreach(array_values($_REQUEST['subgal']) as $subgal) {
			$href[] = "galId[]=$subgal";
		}
	}
	header("Location: tiki-download_file.php?" . implode('&', $href));
	die;
}

if (isset($_REQUEST['permsel_x']) && $tiki_p_assign_perm_file_gallery == 'y') {
	$perms = $userlib->get_permissions(0, -1, 'permName_asc', '', 'file galleries');
	$smarty->assign_by_ref('perms', $perms['data']);
	$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$smarty->assign_by_ref('groups', $groups['data']);
}

if (isset($_REQUEST['permsel']) && $tiki_p_assign_perm_file_gallery == 'y' && isset($_REQUEST['subgal'])) {
	check_ticket('fgal');
	foreach($_REQUEST['subgal'] as $id) {
		foreach($_REQUEST['perms'] as $perm) {
			if (empty($_REQUEST['groups']) && empty($perm)) {
				$userlib->assign_object_permission('', $id, 'file gallery', '');
				continue;
			}
			foreach($_REQUEST['groups'] as $group) {
				$userlib->assign_object_permission($group, $id, 'file gallery', $perm);
			}
		}
	}
}

// Lock a file
if (isset($_REQUEST['lock']) && isset($_REQUEST['fileId']) && $_REQUEST['fileId'] > 0) {
	if (!$fileInfo = $filegallib->get_file_info($_REQUEST['fileId'])) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display("error.tpl");
		die;
	}
	$error_msg = '';
	if ($_REQUEST['lock'] == 'n' && !empty($fileInfo['lockedby'])) {
		if ($fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y') {
			$smarty->assign('errortype', 401);
			$error_msg = sprintf(tra('The file is already locked by %s'), $fileInfo['lockedby']);
		} else {
			if ($fileInfo['lockedby'] != $user) {
				$access->check_authenticity(sprintf(tra('The file is already locked by %s'), $fileInfo['lockedby']));
				$filegallib->unlock_file($_REQUEST['fileId']);
			} else {
				$filegallib->unlock_file($_REQUEST['fileId']);
			}
		}
	} elseif ($_REQUEST['lock'] == 'y') {
		if (!empty($fileInfo['lockedby']) && $fileInfo['lockedby'] != $user) {
			$error_msg = sprintf(tra('The file is already locked by %s'), $fileInfo['lockedby']);
		} elseif ($gal_info['lockable'] != 'y') {
			$smarty->assign('errortype', 401);
			$error_msg = tra('You do not have permission to do that');
		} else {
			$filegallib->lock_file($_REQUEST['fileId'], $user);
		}
	}
	if ($error_msg != '') {
		$smarty->assign('msg', $error_msg);
		$smarty->display('error.tpl');
		die;
	}
}

// Validate a draft
if (!empty($_REQUEST['validate']) && $prefs['feature_file_galleries_save_draft'] == 'y') {
	// To validate a draft the user must be the owner or the file or the gallery or admin
	if (!$info = $filegallib->get_file_info($_REQUEST['validate'])) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}
	if ($tiki_p_admin_file_galleries != 'y' && (!$user || $user != $gal_info['user'])) {
		if ($user != $info['user']) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('Permission denied you cannot validate files from this gallery'));
			$smarty->display('error.tpl');
			die;
		}
	}

	$access->check_authenticity(tra('Validate draft: ') . (!empty($info['name']) ? htmlspecialchars($info['name']) . ' - ' : '') . $info['filename']);
	$filegallib->validate_draft($info['fileId']);
}

if ( ! empty($_REQUEST['remove']) ) {
	$filegallib->actionHandler(
		'removeFile',
		array(
			'fileId' => $_REQUEST['remove'],
			'draft' => ( ! empty($_REQUEST['draft']) )
		)
	);
}

$foo = parse_url($_SERVER['REQUEST_URI']);
$smarty->assign('url', $tikilib->httpPrefix() . $foo['path']);
// Edit mode
if (isset($_REQUEST['edit_mode']) and $_REQUEST['edit_mode']) {
	$smarty->assign('edit_mode', 'y');
	$smarty->assign('edited', 'y');
	if ($prefs['feature_categories'] == 'y') {
		$cat_type = 'file gallery';
		$cat_objid = $galleryId;
		include_once ('categorize_list.php');
	}
	
	if ($prefs['feature_groupalert'] == 'y') {
		$smarty->assign('groupforAlert', isset($_REQUEST['groupforAlert']) ? $_REQUEST['groupforAlert'] : '');
		$all_groups = $userlib->list_all_groups();
		$groupselected = $groupalertlib->GetGroup('file gallery', $_REQUEST['galleryId']);
		if (is_array($all_groups)) {
			foreach($all_groups as $g) {
				$groupforAlertList[$g] = ($g == $groupselected) ? 'selected' : '';
			}
		}
		$smarty->assign_by_ref('groupforAlert', $groupselected);
		$showeachuser = $groupalertlib->GetShowEachUser('file gallery', $_REQUEST['galleryId'], $groupselected);
		$smarty->assign_by_ref('showeachuser', $showeachuser);
		$smarty->assign_by_ref('groupforAlertList', $groupforAlertList);
	}
	// Edit a file
	if (isset($_REQUEST['fileId']) && $_REQUEST['fileId'] > 0) {
		if ($tiki_p_edit_gallery_file != 'y') {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('Permission denied'));
			$smarty->display('error.tpl');
			die;
		}
		$info = $filegallib->get_file_info($_REQUEST['fileId']);
		$smarty->assign('fileId', $_REQUEST['fileId']);
		$smarty->assign_by_ref('filename', $info['filename']);
		$smarty->assign_by_ref('fname', $info['name']);
		$smarty->assign_by_ref('fdescription', $info['description']);
	} elseif ($galleryId > 0) {
		// Edit a gallery
		$smarty->assign_by_ref('maxRows', $gal_info['maxRows']);
		$smarty->assign_by_ref('parentId', $gal_info['parentId']);
		$smarty->assign_by_ref('creator', $gal_info['user']);
		$smarty->assign('max_desc', $gal_info['max_desc']);


		if (isset($gal_info['sort_mode']) && preg_match('/(.*)_(asc|desc)/', $gal_info['sort_mode'], $matches)) {
			$smarty->assign('sortorder', $matches[1]);
			$smarty->assign('sortdirection', $matches[2]);
		} else {
			$smarty->assign('sortorder', 'created');
			$smarty->assign('sortdirection', 'desc');
		}
	} elseif ($tiki_p_create_file_galleries != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
	}
	// Duplicate mode
} elseif (!empty($_REQUEST['dup_mode'])) {
	$smarty->assign('dup_mode', 'y');
}

// Process the insertion or modification request
if (isset($_REQUEST['edit'])) {
	check_ticket('fgal');
	// Saving information
	// Handle files
	if (isset($_REQUEST['fileId'])) {
		if ($tiki_p_admin_file_galleries != 'y') {
			// Check file upload rights
			if ($tiki_p_upload_files != 'y') {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra("You do not have permission to upload files so you cannot edit them"));
				$smarty->display('error.tpl');
				die;
			}
			// Check THIS file edit rights
			if ($_REQUEST['fileId'] > 0) {
				$info = $filegallib->get_file_info($_REQUEST["fileId"]);
				if (!$user || $info['user'] != $user) {
					$smarty->assign('errortype', 401);
					$smarty->assign('msg', tra('You do not have permission to edit this file'));
					$smarty->display('error.tpl');
					die;
				}
			}
		}
	} else {
		// Handle galleries
		if ($tiki_p_admin_file_galleries != 'y') {
			// Check gallery creation rights
			if ($tiki_p_create_file_galleries != 'y') {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra('You do not have permission to create galleries and so you cannot edit them'));
				$smarty->display('error.tpl');
				die;
			}
			// Check THIS gallery modification rights
			if ($galleryId > 0) {
				if (!$user || $gal_info['user'] != $user) {
					$smarty->assign('errortype', 401);
					$smarty->assign('msg', tra('You do not have permission to edit this gallery'));
					$smarty->display('error.tpl');
					die;
				}
			}
		}
	}
	// Everything is ok so we proceed to edit the file or gallery
	$request_vars = array('name'
											, 'fname'
											, 'description'
											, 'fdescription'
											, 'max_desc'
											, 'fgal_type'
											, 'maxRows'
											, 'rowImages'
											, 'thumbSizeX'
											, 'thumbSizeY'
											, 'parentId'
											, 'creator'
											, 'quota'
											, 'image_max_size_x'
											, 'image_max_size_y'
											, 'wiki_syntax'
											);
	foreach($request_vars as $v) {
		if (isset($_REQUEST[$v])) {
			$smarty->assign_by_ref($v, $_REQUEST[$v]);
		}
	}
	$request_toggles = array('visible', 'public', 'lockable');
	foreach($request_toggles as $t) {
		$$t = (isset($_REQUEST[$t]) && $_REQUEST[$t] == 'on') ? 'y' : 'n';
		$smarty->assign($t, $$t);
	}
	$_REQUEST['archives'] = isset($_REQUEST['archives']) ? $_REQUEST['archives'] : 0;
	$_REQUEST['user'] = isset($_REQUEST['user']) ? $_REQUEST['user'] : (isset($gal_info['user']) ? $gal_info['user'] : $user);
	$_REQUEST['sortorder'] = isset($_REQUEST['sortorder']) ? $_REQUEST['sortorder'] : 'created';
	$_REQUEST['sortdirection'] = isset($_REQUEST['sortdirection']) && $_REQUEST['sortdirection'] == 'asc' ? 'asc' : 'desc';
	if (isset($_REQUEST['fileId'])) {
		$fid = $filegallib->replace_file( $_REQUEST['fileId']
																		, $_REQUEST['fname']
																		, $_REQUEST['fdescription']
																		, $info['filename']
																		, $info['data']
																		, $info['filesize']
																		, $info['filetype']
																		, $info['user']
																		, $info['path']
																		, $info['galleryId']
																		);
		$smarty->assign('edit_mode', 'n');
	} else {
		if ($prefs['fgal_quota_per_fgal'] != 'y') {
			$_REQUEST['quota'] = 0;
		}

		if ($test = $filegallib->checkQuotaSetting($_REQUEST['quota'], $galleryId, $_REQUEST['parentId'])) {
			$smarty->assign('msg', ($test > 0)?tra('Quota too big'):tra('Quota too small'));
			$smarty->display('error.tpl');
			die;
		}
		$old_gal_info = $filegallib->get_file_gallery_info($galleryId);
		$gal_info = array('galleryId'					=> $galleryId,
											'name'							=> $_REQUEST['name'],
											'description'				=> $_REQUEST['description'],
											'user'							=> $_REQUEST['user'],
											'maxRows'						=> $_REQUEST['maxRows'],
											'public'						=> $public,
											'visible'						=> $visible,
											'show_id'						=> $_REQUEST['fgal_list_id'],
											'show_icon'					=> $_REQUEST['fgal_list_type'],
											'show_name'					=> $_REQUEST['fgal_list_name'],
											'show_size'					=> $_REQUEST['fgal_list_size'],
											'show_description'	=> $_REQUEST['fgal_list_description'],
											'show_created'			=> $_REQUEST['fgal_list_created'],
											'show_hits'					=> $_REQUEST['fgal_list_hits'],
											'show_lastDownload' => $_REQUEST['fgal_list_lastDownload'],
											'max_desc'					=> $_REQUEST['max_desc'],
											'type'							=> $_REQUEST['fgal_type'],
											'parentId'					=> empty($_REQUEST['parentId']) ? $old_gal_info['parentId'] : $_REQUEST['parentId'],
											'lockable'					=> $lockable,
											'show_lockedby'			=> $_REQUEST['fgal_list_lockedby'],
											'archives'					=> $_REQUEST['archives'],
											'sort_mode'					=> $_REQUEST['sortorder'] . '_' . $_REQUEST['sortdirection'],
											'show_modified'			=> $_REQUEST['fgal_list_lastModif'],
											'show_creator'			=> $_REQUEST['fgal_list_creator'],
											'show_deleteAfter'		=> $_REQUEST['fgal_list_deleteAfter'],
											'show_checked'			=> $_REQUEST['fgal_show_checked'],
											'show_share'			=> $_REQUEST['fgal_list_share'],
											'show_author'				=> $_REQUEST['fgal_list_author'],
											'subgal_conf'				=> $_REQUEST['subgal_conf'],
											'show_last_user'		=> $_REQUEST['fgal_list_last_user'],
											'show_comment'			=> $_REQUEST['fgal_list_comment'],
											'show_files'				=> $_REQUEST['fgal_list_files'],
											'show_explorer'			=> (isset($_REQUEST['fgal_show_explorer']) ? 'y' : 'n'),
											'show_path'					=> (isset($_REQUEST['fgal_show_path']) ? 'y' : 'n'),
											'show_slideshow'		=> (isset($_REQUEST['fgal_show_slideshow']) ? 'y' : 'n'),
											'default_view'			=> $_REQUEST['fgal_default_view'],
											'quota'							=> $_REQUEST['quota'],
											'image_max_size_x'	=> $_REQUEST['image_max_size_x'],
											'image_max_size_y'	=> $_REQUEST['image_max_size_y'],
											'backlinkPerms'			=> isset($_REQUEST['backlinkPerms'])? 'y': 'n',
											'show_backlinks'		=> $_REQUEST['fgal_list_backlinks'],
											'wiki_syntax'			=> $_REQUEST['wiki_syntax']
										);

		if ($prefs['feature_file_galleries_templates'] == 'y' && isset($_REQUEST['fgal_template']) && !empty($_REQUEST['fgal_template'])) {
			// Override with template parameters
			$template = $templateslib->get_parsed_template($_REQUEST['fgal_template']);

			if ($template) {
				$gal_info = array_merge($gal_info, $template['content']);
				$gal_info['template'] = $_REQUEST['fgal_template'];
			}
		}

		if ($prefs['fgal_show_slideshow'] != 'y') {
			$gal_info['show_slideshow'] = $old_gal_info['show_slideshow'];
		}
		
		if ($prefs['fgal_show_explorer'] != 'y') {
			$gal_info['show_explorer'] = $old_gal_info['show_explorer'];
		}
		
		if ($prefs['fgal_show_path'] != 'y') {
			$gal_info['show_path'] = $old_gal_info['show_path'];
		}
		
		if ($prefs['fgal_show_checked'] != 'y') {
			$gal_info['show_checked'] = $old_gal_info['show_checked'];
		}
		
		$fgal_diff = array_diff_assoc($gal_info, $old_gal_info);
		unset($fgal_diff['created']);
		unset($fgal_diff['lastModif']);
		unset($fgal_diff['votes']);
		unset($fgal_diff['points']);
		unset($fgal_diff['hits']);
		$smarty->assign('fgal_diff', $fgal_diff);

		$fgid = $filegallib->replace_file_gallery($gal_info);
		if ($prefs['feature_groupalert'] == 'y') {
			$groupalertlib->AddGroup('file gallery', $galleryId, $_REQUEST['groupforAlert'], $_REQUEST['showeachuser']);
		}
		
		if ($prefs['feature_categories'] == 'y') {
			$cat_type = 'file gallery';
			$cat_objid = $fgid;
			$cat_desc = substr($_REQUEST['description'], 0, $_REQUEST['max_desc']);
			$cat_name = $_REQUEST['name'];
			$cat_href = 'tiki-list_file_gallery.php?galleryId=' . $cat_objid;
			include_once ('categorize.php');
			$categlib->build_cache();
		}
		
		if (isset($_REQUEST['viewitem'])) {
			header('Location: tiki-list_file_gallery.php?galleryId='
							. $fgid
							. (!empty($_REQUEST['filegals_manager'])?'&filegals_manager='.$_REQUEST['filegals_manager']:'')
						);
			die;
		}
	$smarty->assign('edit_mode', 'y');
	}
}

// Process duplication of a gallery
if (!empty($_REQUEST['duplicate']) && !empty($_REQUEST['name']) && !empty($_REQUEST['galleryId'])) {
	check_ticket('fgal');
	$newGalleryId = $filegallib->duplicate_file_gallery($galleryId
																										, $_REQUEST['name']
																										, isset($_REQUEST['description']) ? $_REQUEST['description'] : ''
																										);

	if (isset($_REQUEST['dupCateg']) && $_REQUEST['dupCateg'] == 'on' && $prefs['feature_categories'] == 'y') {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$cats = $categlib->get_object_categories('file gallery', $galleryId);
		$catObjectId = $categlib->add_categorized_object('file gallery'
																										, $newGalleryId
																										, (isset($_REQUEST['description']) ? $_REQUEST['description'] : '')
																										, $_REQUEST['name']
																										, 'tiki-list_file_gallery.php?galleryId=' . $newGalleryId
																									);
		foreach($cats as $cat) {
			$categlib->categorize($catObjectId, $cat);
		}
	}
	if (isset($_REQUEST['dupPerms']) && $_REQUEST['dupPerms'] == 'on') {
		$userlib->copy_object_permissions($galleryId, $newGalleryId, 'file gallery');
	}
	header('Location: tiki-list_file_gallery.php?galleryId='.$newGalleryId);
	die;
}

// Process removal of a gallery
if (!empty($_REQUEST['removegal'])) {
	check_ticket('fgal');
	if (!($gal_info = $filegallib->get_file_gallery_info($_REQUEST['removegal']))) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}

	if ($tiki_p_admin_file_galleries != 'y' && (!$user || $gal_info['user'] != $user)) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('You do not have permission to remove this gallery'));
		$smarty->display('error.tpl');
		die;
	}
	$access->check_authenticity(tra('Remove file gallery: ') . ' ' . htmlspecialchars($gal_info['name']));
	$filegallib->remove_file_gallery($_REQUEST['removegal'], $_REQUEST['removegal']);
}

// Process upload of a file version
if (!empty($_FILES)) {
	check_ticket('fgal');
	if ($tiki_p_upload_files != 'y' && $tiki_p_admin_file_galleries != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('You have permission to upload files but not to this file gallery'));
		$smarty->display('error.tpl');
		die;
	}

	foreach($_FILES as $k => $v) {
		$result = $filegallib->handle_file_upload($k, $v);

		if (isset($result['error'])) {
			$smarty->assign('msg', $result['error']);
			$smarty->display('error.tpl');
			exit;
		}

		$fileId = $filegallib->replace_file($fileInfo['fileId']
											, $fileInfo['name']
											, $fileInfo['description']
											, $result['name']
											, $result['data']
											, $result['size']
											, $type['type']
											, $user
											, $result['fhash']
											, $fileInfo['comment']
											, $gal_info
											, true		// replace file
											, $fileInfo['author']
											, $fileInfo['lastModif']
											, $fileInfo['lockedby']
											);

		if (!$fileId) {
			// If insert failed and stored on disk
			if ($result['fhash']) {
				@unlink($savedir . $result['fhash']);
			}
			$smarty->assign('msg', tra('Upload was not successful. Duplicate file content') . ': ' . $v['name']);
			$smarty->display('error.tpl');
			die;
		}
		$smarty->assign('fileId', $fileId);
		$smarty->assign('fileChangedMessage', tra('File update was successful') . ': ' . $v['name']);
		if (isset($_REQUEST['fast']) && $prefs['fgal_asynchronous_indexing'] == 'y') {
			$smarty->assign('reindex_file_id', $fileId);
		}
	}
}

// Update a file comment
if (isset($_REQUEST['comment']) && $_REQUEST['comment'] != '' && isset($_REQUEST['fileId']) && $_REQUEST['fileId'] > 0) {
	$msg = '';
	if (!$fileInfo = $filegallib->get_file_info($_REQUEST['fileId'])) {
		$msg = tra('Incorrect param');
	} elseif ($_REQUEST['galleryId'] != $fileInfo['galleryId']) {
		$msg = tra('Could not find the file requested');
	} elseif ((!empty($fileInfo['lockedby']) && $fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y') || $tiki_p_edit_gallery_file != 'y') {
		$smarty->assign('errortype', 401);
		$msg = tra('You do not have permission to do that');
	} else {
		$filegallib->update_file($fileInfo['fileId'], $fileInfo['name'], $fileInfo['description'], $user, $_REQUEST['comment'], false);
	}
	if ($msg != '') {
		$smarty->assign('msg', $error_msg);
		$smarty->display('error.tpl');
		die;
	}
}

// load categories for find
if ($prefs['feature_categories'] == 'y' && !isset($_REQUEST['edit_mode'])) {
	global $categlib;
	include_once ('lib/categories/categlib.php');
	$categories = $categlib->get_all_categories_respect_perms(null, 'view_category');
	$smarty->assign_by_ref('categories', $categories);
	$smarty->assign('cat_tree', $categlib->generate_cat_tree($categories, true, empty($_REQUEST['cat_categories'])? array(): $_REQUEST['cat_categories']));
}

// Set display config
if (!isset($_REQUEST['maxRecords']) || $_REQUEST['maxRecords'] <= 0) {
	if (isset($gal_info['maxRows']) && $gal_info['maxRows'] > 0) {
		$_REQUEST['maxRecords'] = $gal_info['maxRows'];
	} else {
		$_REQUEST['maxRecords'] = $prefs['maxRecords'];
	}
}

$smarty->assign_by_ref('maxRecords', $_REQUEST['maxRecords']);
if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;
$smarty->assign_by_ref('offset', $_REQUEST['offset']);

if (empty($_REQUEST['sort_mode'])) {
	if ($gal_info['sort_mode'] == 'name_asc' && $gal_info['show_name'] == 'f') {
		$_REQUEST['sort_mode'] = 'filename_asc';
	} else {
		$_REQUEST['sort_mode'] = $gal_info['sort_mode'];
	}
}

$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);

$find = array();
if (!isset($_REQUEST['find_creator'])) {
	$smarty->assign('find_creator', '');
} else {
	$find['creator'] = $_REQUEST['find_creator'];
	$smarty->assign('find_creator', $_REQUEST['find_creator']);
}
if (!empty($_REQUEST['find_lastModif']) && !empty($_REQUEST['find_lastModif_unit'])) {
	$find['lastModif'] = $tikilib->now - ($_REQUEST['find_lastModif'] * $_REQUEST['find_lastModif_unit']);
}
if (!empty($_REQUEST['find_lastDownload']) && !empty($_REQUEST['find_lastDownload_unit']) ) {
	$find['lastDownload'] = $tikilib->now - ($_REQUEST['find_lastDownload'] * $_REQUEST['find_lastDownload_unit']);
}

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';
$smarty->assign_by_ref('find', $_REQUEST['find']);

if (isset($_REQUEST['fileId'])) {
	$smarty->assign('fileId', $_REQUEST['fileId']);
}
if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['cat_categories'])) {
	$find['categId'] = $_REQUEST['cat_categories'];
	if (count($_REQUEST['cat_categories']) > 1) {
		$smarty->assign('find_cat_categories', $_REQUEST['cat_categories']);
		unset($_REQUEST['categId']);
	} else {
		$_REQUEST['categId'] = $_REQUEST['cat_categories'][0];
		unset($_REQUEST['cat_categories']);
	}
} else {
	$_REQUEST['cat_categories'] = array();
}
if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['categId'])) {
	$find['categId'] = $_REQUEST['categId'];
	$smarty->assign('find_categId', $_REQUEST['categId']);
}
if (!empty($_REQUEST['find_orphans']) && ($_REQUEST['find_orphans'] == 'on' || $_REQUEST['find_orphans'] == 'y')) {
	$find['orphan'] = 'y';
	$smarty->assign('find_orphans', 'y');
}
if (!empty($_REQUEST['find_sub']) && ($_REQUEST['find_sub'] == 'on' || $_REQUEST['find_sub'] == 'y')) {
	$find_sub = true;
	$smarty->assign('find_sub', 'y');
} else {
	$find_sub = false;
}

if (isset($_GET['slideshow'])) {
	$_REQUEST['maxRecords'] = $maxRecords = - 1;
	$offset = 0;
	$files = $filegallib->get_files( 0
															, -1
															, $_REQUEST['sort_mode']
															, $_REQUEST['find']
															, $_REQUEST['galleryId']
															, false
															, false
															, false
															, true
															, false
															, false
															, false
															, true
															, ''
															, false
															, false
															, false
															, $find
														);
	$smarty->assign('cant', $files['cant']);
	$smarty->assign_by_ref('file', $files['data']);

	$smarty->assign('show_find', 'n');
	$smarty->assign('direct_pagination', 'y');
	if (isset($_REQUEST['slideshow_noclose'])) {
		$smarty->assign('slideshow_noclose', 'y');
	}
	$smarty->display('file_gallery_slideshow.tpl');
	die();
} else {
	if (!isset($_REQUEST["edit_mode"]) && !isset($_REQUEST["edit"])) {
		$recursive = (isset($_REQUEST['view']) && $_REQUEST['view'] == 'admin') || $find_sub;
		$with_subgals = !((isset($_REQUEST['view']) && $_REQUEST['view'] == 'admin') || $find_sub);
		if (!empty($_REQUEST['filegals_manager'])) {	// get wiki syntax if needed
			$syntax = $filegallib->getWikiSyntax($_REQUEST['galleryId']);
		} else {
			$syntax = '';
		}
		$with_archive = ( isset($gal_info['archives']) && $gal_info['archives'] == '-1') ? false : true;
		// Get list of files in the gallery
		$files = $filegallib->get_files( $_REQUEST['offset']
																, $_REQUEST['maxRecords']
																, $_REQUEST['sort_mode']
																, $_REQUEST['find']
																, $_REQUEST['galleryId']
																, $with_archive
																, $with_subgals
																, true
																, true
																, false
																, false
																, true
																, $recursive
																, ''
																, true
																, false
																, ($gal_info['show_backlinks']!='n')
																, $find
																, $syntax
																);
		$smarty->assign_by_ref('files', $files['data']);
		$smarty->assign('cant', $files['cant']);
	}
	$smarty->assign('mid', 'tiki-list_file_gallery.tpl');
}

// Browse view
$smarty->assign('thumbnail_size', $prefs['fgal_thumb_max_size']);
$smarty->assign('show_details', isset($_REQUEST['show_details']) ? $_REQUEST['show_details'] : 'n');
// Set comments config
if ($prefs['feature_file_galleries_comments'] == 'y') {
	$comments_per_page = $prefs['file_galleries_comments_per_page'];
	$thread_sort_mode = $prefs['file_galleries_comments_default_ordering'];
	$comments_vars = array('galleryId', 'offset', 'sort_mode', 'find');
	$comments_prefix_var = 'file gallery:';
	$comments_object_var = 'galleryId';
	include_once ('comments.php');
}

$options_sortorder = array( tra('Creation Date') => 'created'
													, tra('Name') => 'name'
													, tra('Last modification date') => 'lastModif'
													, tra('Hits') => 'hits'
													, tra('Owner') => 'user'
													, tra('Description') => 'description'
													, tra('ID') => 'id'
												);
$smarty->assign_by_ref('options_sortorder', $options_sortorder);
// Set section config
include_once ('tiki-section_options.php');

// Theme control
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'file gallery';
	$cat_objid = $_REQUEST['galleryId'];
	include ('tiki-tc.php');
}

// Watches
if ($prefs['feature_user_watches'] == 'y') {
	if (!isset($_REQUEST['fileId'])) {
		if ($user && isset($_REQUEST['watch_event'])) {
			check_ticket('index');
			if ($_REQUEST['watch_action'] == 'add') {
				$tikilib->add_user_watch( $user
																, $_REQUEST['watch_event']
																, $_REQUEST['watch_object']
																, 'File Gallery'
																, (isset($_REQUEST['galleryName']) ? $_REQUEST['galleryName'] : '')
																, "tiki-list_file_gallery.php?galleryId=$galleryId"
															);
			} else {
				$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'File Gallery');
			}
		}
		$smarty->assign('user_watching_file_gallery', 'n');
		if ($user && $tikilib->user_watches($user, 'file_gallery_changed', $galleryId, 'File Gallery')) {
			$smarty->assign('user_watching_file_gallery', 'y');
		}
		
		// Check, if the user is watching this file gallery by a category.
		if ($prefs['feature_categories'] == 'y') {
			$watching_categories_temp = $categlib->get_watching_categories($galleryId, 'file gallery', $user);
			$smarty->assign('category_watched', 'n');
			if (count($watching_categories_temp) > 0) {
				$smarty->assign('category_watched', 'y');
				$watching_categories = array();
				foreach($watching_categories_temp as $wct) {
					$watching_categories[] = array('categId' => $wct, 'name' => $categlib->get_category_name($wct));
				}
				$smarty->assign('watching_categories', $watching_categories);
			}
		}
	}
}

if ($prefs['feature_file_galleries_templates'] == 'y') {
	$all_templates = $templateslib->list_templates('file_galleries', 0, -1, 'name_asc', '');
	$templates = array();
	foreach ($all_templates['data'] as $template) {
		$templates[] = array('label' => $template['name'], 'id' => $template['templateId']);
	}
	sort($templates);
	$smarty->assign_by_ref('all_templates', $templates);
}

$subGalleries = $filegallib->getSubGalleries( ( isset($_REQUEST['parentId']) && $galleryId == 0 ) ? $_REQUEST['parentId'] : $galleryId );
$smarty->assign('treeRootId', $subGalleries['parentId']);

if ($prefs['fgal_show_explorer'] == 'y' || $prefs['fgal_show_path'] == 'y' || isset($_REQUEST['movesel_x']) || isset($_REQUEST["edit_mode"])) {
	$gals = array();
	foreach ($subGalleries['data'] as $gal) {
		if ($gal['id'] != $galleryId) {
			$gals[] = array('label' => $gal['parentName'] . ' > ' . $gal['name'], 'id' => $gal['id']);
		}
	}
	sort($gals);
	$smarty->assign_by_ref('all_galleries', $gals);

	if ( ! empty($subGalleries) && is_array($subGalleries) && $subGalleries['cant'] > 0) {
		$phplayersTreeData = $filegallib->getFilegalsTreePhplayers( $galleryId );

		if ( $prefs['fgal_show_path'] == 'y' ) {
			$smarty->assign('gallery_path', $phplayersTreeData['path']);
		}
	
		if ($prefs['javascript_enabled'] != 'n') {
			$tree_array = array('data' => $subGalleries['data'],
				'name' => $phplayersTreeData['tree']['name'],
				'link' => $phplayersTreeData['tree']['link'],
				'id' => $phplayersTreeData['tree']['id']
			);
			$smarty->assign_by_ref('tree', $tree_array);
			$smarty->assign('expanded', '');
		}
	}
}

ask_ticket('fgal');
if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'browse') {
	foreach($files['data'] as $file) {
		$_SESSION['allowed'][$file['fileId']] = true;
	}
}

if ($_REQUEST['galleryId'] == 0) {
	$smarty->assign('download_path', ((isset($podCastGallery) && $podCastGallery) ? $prefs['fgal_podcast_dir'] : $prefs['fgal_use_dir']));
	// Add a file hit
	$statslib->stats_hit($gal_info['name'], 'file gallery', $galleryId);
	if ($prefs['feature_actionlog'] == 'y') {
		$logslib->add_action('Viewed', $galleryId, 'file gallery');
	}
} else {
	if (!isset($_REQUEST['fileId'])) {
		// Add a gallery hit
		$filegallib->add_file_gallery_hit($_REQUEST['galleryId']);
	}
}

// Get listing display config
include_once ('fgal_listing_conf.php');

$find_durations = array();
if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'admin') {
	$find_durations[] = array('label' => tra('Not modified since')
													, 'prefix' => 'find_lastModif'
													, 'default' => empty($_REQUEST['find_lastModif'])?'':$_REQUEST['find_lastModif']
													, 'default_unit' => empty($_REQUEST['find_lastModif_unit']) ? 'week' : $_REQUEST['find_lastModif_unit']
													);
	$find_durations[] = array('label' => tra('Not downloaded since')
													, 'prefix' => 'find_lastDownload'
													, 'default' => empty($_REQUEST['find_lastDownload']) ? '' : $_REQUEST['find_lastDownload']
													, 'default_unit' => empty($_REQUEST['find_lastDownload_unit']) ? 'week' : $_REQUEST['find_lastDownload_unit']
													);
	foreach ($fgal_listing_conf as $k => $v) {
		if ( $k == 'type' )
			$show_k = 'icon';
		elseif ( $k == 'lastModif' )
			$show_k = 'modified';
		else $show_k = $k;
			$gal_info['show_'.$show_k] = $prefs['fgal_list_'.$k.'_admin'];
	}
	$smarty->assign('show_find_orphans', 'y');
}
$smarty->assign_by_ref('find_durations', $find_durations);
$smarty->assign_by_ref('gal_info', $gal_info);

$smarty->assign('view', isset($_REQUEST['view']) ? $_REQUEST['view'] : $fgal_options['default_view']['value']);

// Display the template
if (!empty($_REQUEST['filegals_manager'])) {
	$smarty->assign('filegals_manager', $_REQUEST['filegals_manager']);
	$smarty->display('tiki_full.tpl');
} else {
	$smarty->display('tiki.tpl');
}
