<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once('tiki-setup.php');
include_once ('lib/filegals/filegallib.php');

global $dbTiki;
$filegal = new FileGalLib($dbTiki);

function mod_add2tree(&$tree, &$galleries, &$gallery_id, &$gallery_path, &$expanded, $cur_id = -1) {
	static $total = 1;
	static $nb_galleries = 0;
	$i = 0;
	$current_path = array();
	$path_found = false;

	if ( $nb_galleries == 0 ) $nb_galleries = count($galleries);
	for ( $gk = 0 ; $gk < $nb_galleries ; $gk++ ) {
		$gv =& $galleries[$gk];
		if ( $gv['parentId'] == $cur_id && $gv['id'] != $cur_id ) {
			$tree[$i] = &$galleries[$gk];
			$tree[$i]['link'] = 'tiki-list_file_gallery.php?galleryId='.$gv['id'];
			$tree[$i]['pos'] = $total++;
			mod_add2tree($tree[$i]['data'], $galleries, $gallery_id, $gallery_path, $expanded, $gv['id']);
			if ( ! $path_found && $gv['id'] == $gallery_id ) {
				if ( $_REQUEST['galleryId'] == $gv['id'] ) $tree[$i]['current'] = 1;
				array_unshift($gallery_path, array($gallery_id, $gv['name']));
				$expanded[] = $tree[$i]['pos'] + 1;
				$gallery_id = $cur_id;
				$path_found = true;
			}
			$i++;
		}
	}
}

if ($prefs['fgal_show_explorer'] == 'y' || $prefs['fgal_show_path'] == 'y' || isset($_REQUEST['movesel'])) {
	global $cachelib; include_once('lib/cache/cachelib.php');
	$cacheName = $filegal->get_all_galleries_cache_name($user);
	$cacheType = $filegal->get_all_galleries_cache_type();
	if (!$cachelib->isCached($cacheName, $cacheType)) {
		$all_galleries = $filegal->list_file_galleries(0, -1, 'name_asc', $user, '', -1, false, true, false, false,false,true, false );
		$cachelib->cacheItem($cacheName, serialize($all_galleries), $cacheType);
	} else {
		$all_galleries = unserialize($cachelib->getCached($cacheName, $cacheType));
	}
	$smarty->assign_by_ref('all_galleries', $all_galleries['data']);

	if ( isset($all_galleries) && is_array($all_galleries) && count($all_galleries) > 0 ) {
		$tree = array('name' => tra('File Galleries'), 'data' => array(), 'link' => 'tiki-list_file_gallery.php');
		$gallery_path = array();
		$expanded = array('1');

		mod_add2tree($tree['data'], $all_galleries['data'], $galleryId, $gallery_path, $expanded);
		if ($prefs['fgal_show_path'] == 'y') {
			array_unshift($gallery_path, array(0, $tree['name']));
			$gallery_path_str = '';
			foreach ( $gallery_path as $dir_id ) {
				if ( $gallery_path_str != '' ) $gallery_path_str .= ' &nbsp;&gt;&nbsp;';
				$gallery_path_str .= '<a href="tiki-list_file_gallery.php?galleryId='.$dir_id[0].( ( isset($_REQUEST['filegals_manager']) && $_REQUEST['filegals_manager'] != '' ) ? '&amp;filegals_manager='.urlencode($_REQUEST['filegals_manager']) : '').'">'.$dir_id[1].'</a>';
			}
			$smarty->assign('gallery_path', $gallery_path_str);
		}
			
		$smarty->assign_by_ref('tree', $tree);
		$smarty->assign_by_ref('expanded', $expanded);
	}
}

?>