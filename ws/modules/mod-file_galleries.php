<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $dbTiki, $filegallib, $cachelib;
require_once('tiki-setup.php');
include_once ('lib/filegals/filegallib.php');
include_once('lib/cache/cachelib.php');

$cacheName = $filegallib->get_all_galleries_cache_name($user);
$cacheType = $filegallib->get_all_galleries_cache_type();
if (!$cachelib->isCached($cacheName, $cacheType)) {
	$all_galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user, '', -1, false, true, false, false,false,true, false );
	$cachelib->cacheItem($cacheName, serialize($all_galleries), $cacheType);
} else {
	$all_galleries = unserialize($cachelib->getCached($cacheName, $cacheType));
}
$smarty->assign_by_ref('all_galleries', $all_galleries['data']);

if ( isset($all_galleries) && is_array($all_galleries) && count($all_galleries) > 0 ) {
	$tree = array('name' => tra('File Galleries'), 'data' => array(), 'link' => 'tiki-list_file_gallery.php');
	$gallery_path = array();
	$expanded = array('1');

	$link = "tiki-list_file_gallery.php";
	$filegallib->add2tree($tree['data'], $all_galleries['data'], $galleryId, $gallery_path, $expanded, $link);
	
	$smarty->assign_by_ref('tree', $tree);
	$smarty->assign_by_ref('expanded', $expanded);
}

?>
