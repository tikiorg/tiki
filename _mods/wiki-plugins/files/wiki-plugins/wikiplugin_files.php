<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/files/wiki-plugins/wikiplugin_files.php,v 1.1 2008-01-18 17:31:09 sylvieg Exp $
/*	list files of galleries
 * galleryId
 * categId
 * 
*/
function wikiplugin_files_help() {
	return tra("List files in a file gallery (with a category) or in a category or a file gallery od this category.")
		."<br />~np~{FILES(galleryId=id,categId=id,sort=name_asc,showaction=n,showfind=n)}Title{FILES}~/np~";
}
function wikiplugin_files($data, $params) {
	global $feature_file_galleries, $tikilib, $feature_categories, $smarty, $tiki_p_admin, $tiki_p_admin_files_galleries, $user;
	if ($feature_file_galleries != 'y') {
		return('');
	}
	global $filegallib; include_once('lib/filegals/filegallib.php');

	extract($params, EXTR_SKIP);

	if ($feature_categories != 'y') {
		if (isset($categId))
			unset($categId);
	} else {
		global $categlib; include_once('lib/categories/categlib.php');
	}

	$files = array();
	if (!isset($sort))
		$sort = 'name_asc';
	if (isset($galleryId)) {
		$gal_info = $tikilib->get_file_gallery($galleryId);
		if ($tiki_p_admin != 'y' && $tiki_p_admin_files_galleries != 'y' && $gal_info['user'] != $user) {
			$p_view_file_gallery = $tikilib->user_has_perm_on_object($user, $galleryId, 'file gallery', 'tiki_p_view_file_gallery');
			if ($p_view_file_gallery != 'y')
				return;
			$p_download_files = $tikilib->user_has_perm_on_object($user, $galleryId, 'file gallery', 'tiki_p_download_files');
			$p_admin_file_galleries = $tikilib->user_has_perm_on_object($user, $galleryId, 'file gallery', 'tiki_p_admin_file_galleries');
			$p_edit_gallery_file = $tikilib->user_has_perm_on_object($user, $galleryId, 'file gallery', 'tiki_p_edit_gallery_file');
		} else {
			$p_download_files = 'y';
			$p_view_file_gallery = 'y';
			$p_admin_file_galleries = 'y';
			$p_edit_gallery_file = 'y';
		}
		$fs = $tikilib->get_files(0, -1, $sort, '', $galleryId);
		for ($i = 0; $i < $fs['cant']; ++$i) {
			if (isset($categId)) { // filter the files
				$cats = $categlib->get_object_categories('file', $fs['data'][$i]['fileId']);
				if (!in_array($categId, $cats))
					continue;	
			}
			$fs['data'][$i]['p_download_files'] = $p_download_files;
			$fs['data'][$i]['p_view_file_gallery'] = $p_view_file_gallery;
			$fs['data'][$i]['p_admin_file_galleries'] = $p_admin_file_galleries;
			$fs['data'][$i]['p_edit_gallery_file'] = $p_edit_gallery_file;
			$fs['data'][$i]['galleryType'] = $gal_info['type'];
			$fs['data'][$i]['lockable'] = $gal_info['lockable'];
			$files[] = $fs['data'][$i];
		}
	} elseif (isset($categId)) {
		// galls of this category
		$objects = $categlib->list_category_objects($categId, 0, -1, $sort, 'file gallery');
		// get the files of the gallery
		foreach ($objects['data'] as $og) {
			$gal_info = $tikilib->get_file_gallery($og['itemId']);
			if ($tiki_p_admin != 'y' && $tiki_p_admin_files_galleries != 'y' && $gal_info['user'] != $user) {
				$p_view_file_gallery = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery');
				if ($p_view_file_gallery != 'y')
					continue;
				$p_download_files = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_download_files');
				$p_admin_file_galleries = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_admin_file_galleries');
				$p_edit_gallery_file = $tikilib->user_has_perm_on_object($user, $gal_info['galleryId'], 'file gallery', 'tiki_p_edit_gallery_file');
			} else {
				$p_download_files = 'y';
				$p_view_file_gallery = 'y';
				$p_admin_file_galleries = 'y';
				$p_edit_gallery_file = 'y';
			}

			$fs = $tikilib->get_files(0, -1, $sort, '', $og['itemId']);
			if ($fs['cant']) {
				for ($i = 0; $i < $fs['cant']; ++$i) {
					$fs['data'][$i]['gallery'] = $gal_info['name'];
					$fs['data'][$i]['galleryId'] = $gal_info['galleryId'];
					$fs['data'][$i]['p_download_files'] = $p_download_files;
					$fs['data'][$i]['p_view_file_gallery'] = $p_view_file_gallery;
					$fs['data'][$i]['p_admin_file_galleries'] = $p_admin_file_galleries;
					$fs['data'][$i]['galleryType'] = $gal_info['type'];
					$fs['data'][$i]['lockable'] = $gal_info['lockable'];
					$fs['data'][$i]['p_edit_gallery_file'] = $p_edit_gallery_file;
				}
				$files = array_merge($files, $fs['data']);
			}
		}
		// files from this categ
		$objects = $categlib->list_category_objects($categId, 0, -1, $sort, 'file');
		foreach ($objects['data'] as $of) {
			$info = $filegallib->get_file_info($of['itemId']);
			$gal_info = $tikilib->get_file_gallery($info['galleryId']);
			if ($tiki_p_admin != 'y' && $tiki_p_admin_files_galleries != 'y' && $gal_info['user'] != $user) {
				$info['p_view_file_gallery'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_view_file_gallery');
				if ($info['p_view_file_gallery'] != 'y')
					continue;
				$info['p_download_files'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files');
				$info['p_admin_file_galleries'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_admin_file_galleries');
				$info['p_edit_gallery_file'] = $tikilib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_edit_gallery_file');
			} else {
				$info['p_download_files'] = 'y';
				$info['p_view_file_gallery'] = 'y';
				$info['p_admin_file_galleries'] = 'y';
				$info['p_edit_gallery_file'] = 'y';
			}
			$info['gallery'] = $gal_info['name'];
			$info['galleryType'] = $gal_info['type'];
			$info['lockable'] = $gal_info['lockable'];
			$files[] = $info;
		}
		$gal_info['name'] = '';
	}
	$smarty->assign_by_ref('files', $files);
	if (isset($data))
		$smarty->assign_by_ref('data', $data);
	else
		$smarty->assign('data', '');
	if (isset($gal_info))
		$smarty->assign_by_ref('gal_info', $gal_info);
	else
		$smarty->assign('gal_info', '');
	if (isset($categId)) {
		$category = $categlib->get_category_name($categId);
		$smarty->assign_by_ref('category', $category);
	} else
		$smarty->assign('category', '');
	if (!isset($showfind)) {
		$showfind = 'n';
	}
	$smarty->assign_by_ref('show_find', $showfind);
	if (!isset($showaction)) {
		$showaction = 'n';
	}
	$smarty->assign_by_ref('show_action', $showaction);
	return $smarty->fetch('list_file_gallery.tpl');
}
?>
