<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists('mod_last_podcasts_help')) {
	function mod_last_podcasts_help() {
		return 'galleryId=id1:id2,nonums=y|n,mediaplayer=path/to/player_mp3.swf,verbose=n';
	}
}

// Module special params:
// - galleryId: list of file galleries IDs. If none, all file galleries will be scanned
// - width: width of player. Default: : width=190
// - height: height of player. Default: : width=190
// - link_url: Url for a link at bottom of module.
// - link_text: Text for link if link_url is set, otherwise 'More Podcasts'
// - verbose: (y/n) Display description of podcast below player if 'y', and on title mouseover if 'n'. Default is 'y'
// - mediaplayer: path to mp3 player. For instance media/player_mp3_maxi.swf if you downloaded player_mp3_maxi.swf 
// from http://flash-mp3-player.net/players/maxi/download/ to directory media/ (to be created)

global $smarty;
if (isset($module_params["galleryId"])) {
	if (strstr($module_params['galleryId'], ':')) {
		$mediafiles = $tikilib->get_files(0, $module_rows, 'created_desc', '', explode(':',$module_params['galleryId']));
	} else {
		$mediafiles = $tikilib->get_files(0, $module_rows, 'created_desc', '', $module_params["galleryId"]);
	}
} else {
	$mediafiles = $tikilib->list_files(0, $module_rows, 'created_desc', '');
}

$mediaplayer=(isset($module_params["mediaplayer"]) && is_readable($module_params["mediaplayer"]))?$module_params["mediaplayer"]:"";

$smarty->assign('modLastFiles', $mediafiles["data"]);
$smarty->assign('mediaplayer', $mediaplayer);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('verbose', isset($module_params["verbose"]) ? $module_params["verbose"] : 'y');
$smarty->assign('link_url', isset($module_params["link_url"]) ? $module_params["link_url"] : '');
$smarty->assign('link_text', isset($module_params["link_text"]) ? $module_params["link_text"] : 'More Podcasts');
$smarty->assign('player_width', isset($module_params["width"]) ? $module_params["width"] : '190');
$smarty->assign('player_height', isset($module_params["height"]) ? $module_params["height"] : '20');
$smarty->assign('module_rows', $module_rows);


