<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @return array
 */
function module_last_youtube_playlist_videos_info()
{
	return array(
		'name' => tra('YouTube Playlist'),
		'description' => tra('Display a YouTube playlist'),
		'prefs' => array('wikiplugin_youtube'),
		'documentation' => 'Module last_youtube_playlist_videos',
	'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Playlist ID'),
				'description' => tra('ID of the YouTube playlist to display (not the complete URL). Example: id=4DE69387D46DA2F6'),
				'filter' => 'striptags',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of each video in pixels'),
				'default' => 425,
				'filter' => 'striptags',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of each video in pixels'),
				'default' => 350,
				'filter' => 'striptags',
			),
			'allowFullScreen' => array(
				'required' => false,
				'name' => tra('Allow FullScreen'),
				'description' => tra('Enlarge video to full screen size'),
				'default' => true,
				'filter' => 'alpha',
				'options' => array(
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'true'),
					array('text' => tra('No'), 'value' => 'false'),
				),
			),
			'link_url' => array(
				'required' => false,
				'name' => tra('Bottom Link'),
				'description' => tra('Url for a link at bottom of module. Example: link_url=http://www.youtube.com/CouncilofEurope#grid/user/E57D0D93BA3A56C8'),
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'link_text' => array(
				'required' => false,
				'name' => tra('Bottom Link Text'),
				'description' => tra('Text for link if link_url is set, otherwise \'More Videos\''),
				'default' => 'More Videos',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'verbose' => array(
				'required' => false,
				'name' => tra('Video Descriptions'),
				'description' => tra('Display description of video on title mouseover if \'y\'. Default is \'y\''),
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
				'options' => array(
					array('text' => tra(''), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
				),
			),
			'orderby' => array(
				'required' => false,
				'name' => tra('Order by'),
				'description' => tra('Criteria to order by the videos in the list. Default is \'position\''),
				'default' => 'position',
				'filter' => 'striptags',
				'options' => array(
					array('text' => tra(''), 'value' => ''), 
					array('text' => tra('position'), 'value' => 'position'), 
					array('text' => tra('commentCount'), 'value' => 'commentCount'), 
					array('text' => tra('duration'), 'value' => 'duration'), 
					array('text' => tra('published'), 'value' => 'published'), 
					array('text' => tra('reversedPosition'), 'value' => 'reversedPosition'), 
					array('text' => tra('title'), 'value' => 'title'), 
					array('text' => tra('viewCount'), 'value' => 'viewCount'), 
				),
			),
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_youtube_playlist_videos($mod_reference, $module_params)
{
	global $prefs; 
	$tikilib = TikiLib::lib('tiki');
	$data = array();
	$smarty = TikiLib::lib('smarty');
	if (!empty($module_params['id'])) {
		$id = $module_params['id'];

		require_once('lib/wiki-plugins/wikiplugin_youtube.php');

		$client = new Google_Client();
		$client->setScopes('https://www.googleapis.com/auth/youtube');

		// TODO: Add "dev_key" to documentation, without key API uses public API call limit
		// and it is usually exceeded
		if (!empty($module_params['dev_key'])) {
			$client->setDeveloperKey($module_params['dev_key']);
		}

		// Define an object that will be used to make all API requests.
		$youtube = new Google_Service_YouTube($client);

		/*
		 * TODO: orderby is not supported by V3 API, probably sort locally
		if (!empty($module_params['orderby'])) {
			$orderby = $module_params['orderby'];
			$feedUrl = 'http://gdata.youtube.com/feeds/api/playlists/' . $id . '?orderby='. $orderby;
		} else {
			$feedUrl = 'http://gdata.youtube.com/feeds/api/playlists/' . $id . '?orderby=position';
		}
		*/
	
		try {
			// Get playlist information
			// DOC: https://developers.google.com/youtube/v3/docs/playlists/list
			$playlists = $youtube->playlists->listPlaylists("snippet", array(
				'id' => $module_params['id'],
				'hl' => $prefs['language']
			));

			// Get playlist content
			// DOC: https://developers.google.com/youtube/v3/docs/playlistItems/list
			$playlistItems = $youtube->playlistItems->listPlaylistItems('snippet', array(
				'playlistId' => $module_params['id'],
				'maxResults' => 50 // What if more items available?
			));

			$data[$id]['info']['title'] = $playlists[0]["snippet"]["localized"]['title'];
	
			// Prepare params for video display
			$params = array();
			$params['width'] = isset($module_params['width']) ? $module_params['width'] : 425;
			$params['height'] = isset($module_params['height']) ? $module_params['height'] : 350;
			$params['allowFullScreen'] = isset($module_params['allowFullScreen']) ? $module_params['allowFullScreen'] : true;
				
			// Get information from all videos from playlist
			// Limit to $module_rows first videos if $module_rows is set
			$count_videos = 1;
			foreach ($playlistItems as $videoEntry) {
				$videoId = $videoEntry['snippet']['resourceId']['videoId'];
				$data[$id]['videos'][$videoId]['title'] = $videoEntry['snippet']['title'];
				$data[$id]['videos'][$videoId]['uploaded'] = $videoEntry['snippet']['publishedAt'];
				$data[$id]['videos'][$videoId]['description'] = $videoEntry['snippet']['description'];
				$params['movie'] = $videoId;
				$pluginstr = wikiplugin_youtube('', $params);
				$len = strlen($pluginstr);
				//need to take off the ~np~ and ~/np~ at the beginning and end of the string returned by wikiplugin_youtube
				$data[$id]['videos'][$videoId]['xhtml'] = substr($pluginstr, 4, $len - 4 - 5);
				if ((isset($module_rows) && $module_rows > 0) && ($count_videos >= $module_rows)) break;
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
	
	$smarty->assign('verbose', isset($module_params['verbose']) ? $module_params['verbose'] : 'y');
	$smarty->assign('link_url', isset($module_params['link_url']) ? $module_params['link_url'] : '');
	$smarty->assign('link_text', isset($module_params['link_text']) ? $module_params['link_text'] : 'More Videos');
	$smarty->assign_by_ref('data', $data[$id]);	
}

