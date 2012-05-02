<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_kaltura_info()
{
	global $kalturaadminlib; require_once 'lib/videogals/kalturalib.php';

	$players = array(array('value' => '', 'text' => tra('Default')));
	if (is_object($kalturaadminlib) && !empty($kalturaadminlib->session)) {
		$players1 = $kalturaadminlib->getPlayersUiConfs();
		foreach($players1 as & $pl) {
			$players[] = array('value' => $pl['id'], 'text' => tra($pl['name']));
		}
		unset($players1)
;	}

	return array(
		'name' => tra('Kaltura Video'),
		'documentation' => 'PluginKaltura',
		'description' => tra('Display a video created through the Kaltura feature'),
		'prefs' => array('wikiplugin_kaltura', 'feature_kaltura'),
		'extraparams' => true,
		'icon' => 'img/icons/film_edit.png',
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Kaltura Entry ID'),
				'description' => tra('Kaltura entry ID of the video to be displayed'),
			),
			'player_id' => array(
				'name' => tra('Kaltura Video Player ID'),
				'description' => tra('Kaltura Dynamic Player (KDP) user interface configuration ID'),
				'type' => empty($players) ? 'text' : 'list',
				'options' => $players,
				'size' => 20,
				'default' => '',
				'tags' => array('basic'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of the player in pixels'),
				'default' => 595,
				'filter' => 'int',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of the player in pixels'),
				'default' => 365,
				'filter' => 'int',
			),
		),
	);
}

function wikiplugin_kaltura($data, $params)
{
	global $prefs, $kalturalib;

	if (empty($params['id'])) {
		return '<span class="error">' . tra('Media id is required') . '</span>';
	} else {
		$id = $params['id'];
	}

	if (empty($params['player_id'])) {
		$playerId = $prefs['kaltura_kdpUIConf'];
	} else {
		$playerId = $params['player_id'];
	}

	global $kalturaadminlib; require_once 'lib/videogals/kalturalib.php';

	$defaults = array();
	$plugininfo = wikiplugin_kaltura_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	if ($kalturaadminlib && (empty($params['width']) || empty($params['height']))) {
		$player = $kalturaadminlib->getPlayersUiConf($playerId);
		if (!empty($player)) {
			if (empty($params['width'])) {
				$params['width'] = $player['width'];
			}
			if (empty($params['height'])) {
				$params['height'] = $player['height'];
			}
		} else {
			return '<span class="error">' . tra('Player not found') . '</span>';
		}
	}
	$params = array_merge($defaults, $params);


	$code ='<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" allowScriptAccess="always" ' .
			'allowNetworking="all" allowFullScreen="true" height="'.$params['height'].'" width="'.$params['width'].'" data="'.$prefs['kaltura_kServiceUrl'] .
			'index.php/kwidget/wid/_'.$prefs['kaltura_partnerId'].'/uiconf_id/'. $playerId .'/entry_id/'.urlencode($id).'">' .
					'<param name="allowScriptAccess" value="always" />' .
					'<param name="allowNetworking" value="all" />' .
					'<param name="allowFullScreen" value="true" />' .
					'<param name="movie" value="'.$prefs['kaltura_kServiceUrl'].'index.php/kwidget/wid/_' .
						$prefs['kaltura_partnerId'].'/uiconf_id/'. $playerId .'/entry_id/'.urlencode($id).'"/>' .
					' <param name="flashVars" value="entry_id='.htmlspecialchars($id).'&ks='.$kalturalib->session.'"/>' .
					'<param name="wmode" value="opaque"/>' .
				'</object>';

     return '~np~'.$code.'~/np~';
}
