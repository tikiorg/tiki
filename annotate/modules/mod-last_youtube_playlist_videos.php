<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $smarty, $prefs;
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

// Module special params:
// - id: ID of the YouTube playlist to display (not the complete URL). Example: id=4DE69387D46DA2F6
// - width: width of video. Example: : width=190
// - height: height of video. Example: height=150
// - link_url: Url for a link at bottom of module. Example: link_url=http://www.youtube.com/CouncilofEurope#grid/user/E57D0D93BA3A56C8
// - link_text: Text for link if link_url is set, otherwise 'More Videos'
// - title: Module title. If not provided, the title of the playlist will be displayed
// - verbose: (y/n) Display description of video on title mouseover if 'y'. Default is 'y'

$data = array();

if ( !empty($module_params['id']) ) {
	$id = $module_params['id'];
	require_once('lib/wiki-plugins/wikiplugin_youtube.php');
	$feedUrl = 'http://gdata.youtube.com/feeds/api/playlists/' . $id . '?orderby=published';
	$yt = new Zend_Gdata_YouTube();
	$yt->setMajorProtocolVersion(2);
	$yt->setHttpClient($tikilib->get_http_client());

	try {
		$playlistVideoFeed = $yt->getPlaylistVideoFeed($feedUrl);
		$data[$id]['info']['title'] = $playlistVideoFeed->title->text;

		// Prepare params for video display
		$params = array();
		if ( isset($module_params['width']) ) $params['width'] = $module_params['width'];
		if ( isset($module_params['height']) ) $params['height'] = $module_params['height'];

		// Get information from all videos from playlist
		// Limit to $module_rows first videos if $module_rows is set
		$count_videos = 1;
		foreach ($playlistVideoFeed as $videoEntry) {
			$videoId = $videoEntry->getVideoId();
			$data[$id]['videos'][$videoId]['title'] = $videoEntry->getVideoTitle();
			$data[$id]['videos'][$videoId]['uploaded'] = $videoEntry->mediaGroup->uploaded->text;
			$data[$id]['videos'][$videoId]['description'] = $videoEntry->getVideoDescription();
			$params['movie'] = $videoId;
			$data[$id]['videos'][$videoId]['xhtml'] = wikiplugin_youtube('', $params);
			if ( ($module_rows > 0) && ($count_videos >= $module_rows) ) break;
			$count_videos++;
		}

	} catch (Exception $e) {
		$data[$id]['info']['title'] = tra('No Playlist found');
		$data[$id]['videos'][0]['title'] = $e->getMessage();
	}
} else {
	$id=0;
	$data[$id]['info']['title'] = tra('No Playlist found');
	$data[$id]['videos'][0]['title'] = tra('No Playlist ID was provided');
}

$smarty->assign('verbose', isset($module_params["verbose"]) ? $module_params["verbose"] : 'y');
$smarty->assign('link_url', isset($module_params["link_url"]) ? $module_params["link_url"] : '');
$smarty->assign('link_text', isset($module_params["link_text"]) ? $module_params["link_text"] : 'More Videos');
$smarty->assign_by_ref('data', $data[$id]);
