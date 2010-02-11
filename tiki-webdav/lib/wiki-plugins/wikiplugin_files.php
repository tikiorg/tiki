<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/files/wiki-plugins/wikiplugin_files.php,v 1.2 2008/01/18 22:00:48 sylvieg Exp $
/*	list files of galleries
 * galleryId
 * categId
 * 
*/
function wikiplugin_files_help() {
	return tra("List files in a file gallery (with a category) or in a category or a file gallery od this category.")
		."<br />~np~{FILES(galleryId=id,categId=id,sort=name_asc,showaction=n,showfind=n,slideshow=n)}Title{FILES}~/np~";
}
function wikiplugin_files_info() {
	return array(
		'name' => tra('Files'),
		'documentation' => 'PluginFiles',
		'description' => tra("Displays a list of files from the File Gallery"),
		'prefs' => array( 'feature_file_galleries', 'wikiplugin_files' ),
		'body' => tra('Title'),
		'params' => array(
			'galleryId' => array(
				'required' => false,
				'name' => tra('Gallery ID'),
				'description' => tra('Gallery ID'),
			),
			'categId' => array(
				'required' => false,
				'name' => tra('Category ID'),
				'description' => tra('Category ID').':'.tra('Category ID'),
			),
			'sort' => array(
				'required' => false,
				'name' => tra('sort'),
				'description' => tra('name_asc'),
			),
			'showaction' => array(
				'required' => false,
				'name' => tra('sort'),
				'description' => 'y|n',
			),
			'showfind' => array(
				'required' => false,
				'name' => tra('find'),
				'description' => 'y|n',
			),
			'showid' => array(
				'required' => false,
				'name' => tra('Shows ID'),
				'description' => 'y|n',
			),
			'showicon' => array(
				'required' => false,
				'name' => tra('Shows Icon'),
				'description' => 'y|n',
			),
			'showname' => array(
				'required' => false,
				'name' => tra('Shows Name'),
				'description' => 'y|n',
			),
			'showfilename' => array(
				'required' => false,
				'name' => tra('Shows Filename'),
				'description' => 'y|n',
			),
			'showsize' => array(
				'required' => false,
				'name' => tra('Shows Size'),
				'description' => 'y|n',
			),
			'showdescription' => array(
				'required' => false,
				'name' => tra('Shows Description'),
				'description' => 'y|n',
			),
			'showcreated' => array(
				'required' => false,
				'name' => tra('Shows Creation Date'),
				'description' => 'y|n',
			),
			'showhits' => array(
				'required' => false,
				'name' => tra('Shows Hits'),
				'description' => 'y|n',
			),
			'showlockedby' => array(
				'required' => false,
				'name' => tra('Shows Locked by'),
				'description' => 'y|n',
			),
			'showlmodified' => array(
				'required' => false,
				'name' => tra('Shows Modification Date'),
				'description' => 'y|n',
			),
			'showauthor' => array(
				'required' => false,
				'name' => tra('Shows Author'),
				'description' => 'y|n',
			),
			'showcreator' => array(
				'required' => false,
				'name' => tra('Shows Creator'),
				'description' => 'y|n',
			),
			'showgallery' => array(
				'required' => false,
				'name' => tra('Shows Parent Gallery Name'),
				'description' => 'y|n',
			),
			'showfiles' => array(
				'required' => false,
				'name' => tra('Shows Number of Files'),
				'description' => 'y|n',
			),
			'slideshow' => array(
				'required' => false,
				'name' => tra('Shows the slideshow of a gallery'),
				'description' => 'y|n',
			),
	  )
	 );
}
function wikiplugin_files($data, $params) {
	global $prefs, $tikilib, $smarty, $tiki_p_admin, $tiki_p_admin_files_galleries, $user;
	if ($prefs['feature_file_galleries'] != 'y') {
		return('');
	}
	global $filegallib; include_once('lib/filegals/filegallib.php');

	extract($params, EXTR_SKIP);

	if ($prefs['feature_categories'] != 'y') {
		if (isset($categId))
			unset($categId);
	} else {
		global $categlib; include_once('lib/categories/categlib.php');
	}

	$files = array();
	if (isset($categId) && strstr($categId, ':')) {
		$categId = explode(':', $categId);
	}
	static $iplugin = 0;
	++$iplugin;
	if (isset($_REQUEST["wp_files_sort_mode$iplugin"])) {
		$sort = $_REQUEST["wp_files_sort_mode$iplugin"];
	}
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
		if (!empty($slideshow) && $slideshow == 'y') {
			if ($prefs['javascript_enabled'] != 'y') return;
			if (empty($data)) $data = 'Slideshow';
			return "~np~<a onclick=\"javascript:window.open('tiki-list_file_gallery.php?galleryId=$galleryId&amp;slideshow','','menubar=no,width=600,height=500,resizable=yes');\" href=\"#\">".tra($data).'</a>~/np~';
		}
		$find = isset($_REQUEST['find'])?  $_REQUEST['find']: '';
		$fs = $tikilib->get_files(0, -1, $sort, $find, $galleryId, false, true);
		if (isset($categId)) {
			$objects = $categlib->list_category_objects($categId, 0, -1, 'itemId_asc', 'file');
			$objects_in_categs = array();
			foreach($objects['data'] as $o) {
				$objects_in_categs[] = $o['itemId'];
			}
		}
		for ($i = 0; $i < $fs['cant']; ++$i) {
			if (isset($categId)) { // filter the files
				if (!in_array($fs['data'][$i]['fileId'], $objects_in_categs)) {
					continue;
				}
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
		$objects = $categlib->list_category_objects($categId, 0, -1, 'itemId_asc', 'file gallery');
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

			$fs = $tikilib->get_files(0, -1, $sort, '', $og['itemId'], false, true, false, true, false, true);
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
		$objects = $categlib->list_category_objects($categId, 0, -1, 'itemId_asc', 'file');
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
	include_once('fgal_listing_conf.php');
	$gal_info['show_checked' ] = 'n'; // the multiple action will not work
	if (!empty($showid)) $gal_info['show_id'] = $showid;
	if (!empty($showicon)) $gal_info['show_icon'] = $showicon;
	if (!empty($showsize)) $gal_info['show_size'] = $showsize;
	if (!empty($showdescription)) $gal_info['show_description'] = $showdescription;
	if (!empty($showcreated)) $gal_info['show_created'] = $showcreated;
	if (!empty($showcreator)) $gal_info['show_creator'] = $showcreator;
	if (!empty($showauthor)) $gal_info['show_author'] = $showauthor;
	if (!empty($showmodified)) {$gal_info['show_lastmodif'] = $gal_info['show_modified'] = $showmodified;}
	if (!empty($showlockedby)) $gal_info['show_lockedby'] = $showlockedby;
	if (!empty($showhits)) $gal_info['show_hits'] = $showhits;
	if (!empty($showfiles)) $gal_info['show_files'] = $showfiles;
	if (!empty($showaction)) $gal_info['show_action'] = $showaction;
	if (!empty($showname) && $showname == 'y' && !empty($showfilename) && $showfilename == 'y') $gal_info['show_name'] = 'a';
	if (!empty($showname) && $showname == 'y' && !empty($showfilename) && $showfilename == 'n') $gal_info['show_name'] = 'n';
	if (!empty($showname) && $showname == 'n' && !empty($showfilename) && $showfilename == 'y') $gal_info['show_name'] = 'f';

	$smarty->assign_by_ref('gal_info', $gal_info);

	if (empty($showgallery)) {
		$show_parentName = empty($galleryId)? 'y': 'n';
	} else {
		$show_parentName = $showgallery;
	}
	$smarty->assign('show_parentName', $show_parentName);

	if (isset($categId)) {
		if (is_array($categId)) {
			foreach ($categId as $cat) {
				$category[] = $categlib->get_category_name($cat);
			}
		} else {
			$category = $categlib->get_category_name($categId);
		}
		$smarty->assign_by_ref('category', $category);
	} else
		$smarty->assign('category', '');
	if (!isset($showfind)) {
		$showfind = 'n';
	}
	$smarty->assign_by_ref('show_find', $showfind);
	$smarty->assign('sort_arg', "wp_files_sort_mode$iplugin");
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_files.tpl').'~/np~';
}
