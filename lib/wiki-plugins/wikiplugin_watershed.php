<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_watershed_info() {
	return array(
		'name' => tra('Watershed'),
		'documentation' => 'PluginWatershed',
		'description' => tra('Viewer for UStream Watershed Embed.'),
		'format' => 'html',
		'prefs' => array( 'wikiplugin_watershed', 'feature_watershed' ),
		'icon' => 'pics/icons/transmit_blue.png',
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Specify archive, broadcaster, chat or viewer'),
				'filter' => 'text',
				'default' => 'viewer',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Archive'), 'value' => 'archive'), 
					array('text' => tra('Broadcaster'), 'value' => 'broadcaster'), 
					array('text' => tra('Chat'), 'value' => 'chat'), 
					array('text' => tra('Viewer'), 'value' => 'viewer'), 
				)
					
			),
			'channelCode' => array(
				'required' => true,
				'name' => tra('Channel code'),
				'description' => tra('Specify the channel code to search in the tracker for a channel to use'),
				'filter' => 'text',
				'default' => '',
			),
			'brandId' => array(
				'required' => false,
				'name' => tra('Brand ID'),
				'description' => tra('Specify the brand id to search in the tracker for a channel to use'),
				'filter' => 'text',
				'default' => '',
			),
			'brandPrefix' => array(
				'required' => false,
				'name' => tra('Brand prefix'),
				'description' => tra('Copy the prefix from the chat embed, e.g. Name-of-Brand-'),
				'filter' => 'text',
				'default' => '',
			),
			'videoId' => array(
				'required' => false,
				'name' => tra('Video ID'),
				'description' => tra('Video ID of archive which can be found in the tracker'),
				'filter' => 'text',
				'default' => '',
			),
			'locale' => array(
				'required' => false,
				'name' => tra('Locale'),
				'description' => tra('Locale specified in viewer embed, default is en_US'),
				'default' => 'en_US',
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_watershed( $data, $params ) {
	global $smarty, $prefs, $user, $tikilib;
	global $watershedlib; require_once 'lib/videogals/watershedlib.php';
	
	if (!empty($params['locale'])) {
		$locale = $params['locale'];
	} else {
		$locale = 'en_US';
	}
	if (!empty($params['brandId'])) {
		$brandId = $params['brandId'];
	} else {
		$brandId = '';
	}
	$channels = $watershedlib->getAllViewableChannels( $params['channelCode'], $brandId );
	if ($channels) {
		if ($params['type'] == 'broadcaster') {
			$channels = $watershedlib->filterChannels( $channels, 'broadcaster' );
		} else {
			$channels = $watershedlib->filterChannels( $channels, 'viewer' );
		}
	}
	if (!$channels) {
		return '';	
	} else if (count($channels) > 1 && $params["type"] != 'archive') {
		return tra("More than one channel found for specified criteria. Please be more specific.");	
	}
	
	if ($params["type"] == 'archive') {
		if (isset($params["videoId"])) {
			// check archive whether viewable by status or channel
			if (!$watershedlib->checkArchiveViewable($params["videoId"], $channels)) {
				return '';
			}
		} else {
			return tra("No videoId specified for archive.");
		}
	}
	
	if (!$user) {
		$sessionId = md5('watershedpublicsession' . $tikilib->now . rand(100000,999999));
	} else {
		$sessionId = $watershedlib->getSessionId($user);
		if (!$sessionId) {
			$sessionId = md5($tikilib->now . $user);
			$watershedlib->storeSessionId($user, $sessionId);
		}
	}

	$smarty->assign('wsd_locale', $locale);
	if (isset($params['brandPrefix'])) {
		$smarty->assign('wsd_prefix', $params['brandPrefix']);
	}
	// generate random embed ids and names
	if ($params['type'] == 'chat') {
		$objectId = 'chat_' . rand(100000,999999);
		$embedName =  'chat_' . rand(100000,999999);
	} else {
		$objectId = 'utv' . rand(100000,999999);
		$embedName =  'utv_n_' . rand(100000,999999);
	}
	
	$smarty->assign('wsd_objectId', $objectId);
	$smarty->assign('wsd_embedName', $embedName);
	$smarty->assign('wsd_sessionId', $sessionId);
	$smarty->assign('wsd_brandId', $channels[0]["brandId"]);
	$smarty->assign('wsd_channelCode', $channels[0]["channelCode"]);
	if ($params['type'] == 'broadcaster') {
		return $smarty->fetch( 'wiki-plugins/wikiplugin_watershedbroadcaster.tpl' );		
	} else if ($params['type'] == 'chat') {
		return $smarty->fetch( 'wiki-plugins/wikiplugin_watershedchat.tpl' );
	} else if ($params['type'] == 'archive') {
		$smarty->assign('wsd_videoId', $params["videoId"]);
		return $smarty->fetch( 'wiki-plugins/wikiplugin_watershedarchive.tpl' );
	} else {
		return $smarty->fetch( 'wiki-plugins/wikiplugin_watershedviewer.tpl' );
	}
}

