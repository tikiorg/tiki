<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_kaltura_info()
{
	global $prefs;
	if ($prefs['feature_kaltura'] === 'y') {
		global $kalturaadminlib; require_once 'lib/videogals/kalturalib.php';

		$players = array(array('value' => '', 'text' => tra('Default')));
		if (is_object($kalturaadminlib) && !empty($kalturaadminlib->session)) {
			$players1 = $kalturaadminlib->getPlayersUiConfs();
			foreach ($players1 as & $pl) {
				$players[] = array('value' => $pl['id'], 'text' => tra($pl['name']));
			}
			unset($players1);
		}
	}

	return array(
		'name' => tra('Kaltura Video'),
		'documentation' => 'PluginKaltura',
		'description' => tra('Display a video created through the Kaltura feature'),
		'prefs' => array('wikiplugin_kaltura', 'feature_kaltura'),
		'format' => 'html',
		'icon' => 'img/icons/film_edit.png',
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Kaltura Entry ID'),
				'description' => tra('Kaltura ID of the video to be displayed, or leave empty to show a button to allow users to add a new one.'),
				'tags' => array('basic'),
				'area' => 'kaltura_uploader_id',
				'type' => 'kaltura',
				'icon' => 'img/icons/film_add.png',
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
			'align' => array(
				'required' => false,
				'name' => tra('Align'),
				'description' => tra('Alignment of the player'),
				'default' => '',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('Not set'), 'value' => ''),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Centre'), 'value' => 'center'),
					array('text' => tra('Right'), 'value' => 'right'),
				),
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Alignment of the player using CSS float'),
				'default' => '',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('Not set'), 'value' => ''),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Right'), 'value' => 'right'),
				),
			),
			'add_button_label' => array(
				'required' => false,
				'name' => tra('Add Media Button Label'),
				'description' => tra('Text to display on button for adding new media.'),
				'default' => tra('Add media'),
			),
		),
	);
}

function wikiplugin_kaltura($data, $params)
{
	global $prefs, $kalturalib, $tiki_p_upload_videos, $user, $page;

	static $instance = 0;

	$instance++;

	$defaults = array();
	$plugininfo = wikiplugin_kaltura_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults[$key] = $param['default'];
	}

	if (empty($params['id'])) {

		if ($tiki_p_upload_videos === 'y') {
			$smarty = TikiLib::lib('smarty');
			$smarty->loadPlugin('smarty_function_button');

			TikiLib::lib('header')->add_jq_onready(
				'
$("#kaltura_upload_btn' . $instance . ' a").live("click", function() {
	openMediaUploader("<input type=\"hidden\" name=\"from\" value=\"plugin\" />" +
					"<input type=\"hidden\" name=\"content\" value=\"\" />" +
					"<input type=\"hidden\" name=\"type\" value=\"kaltura\" />" +
					"<input type=\"hidden\" name=\"page\" value=\"'.$page.'\" />" +
					"<input type=\"hidden\" name=\"index\" value=\"'.$instance.'\" />",
				"tiki-wikiplugin_edit.php");
	return false;
});
			'
			);

			$html = smarty_function_button(
				array(	// default for add_button_label already tra but not merged yet
					'_text' => !empty($params['add_button_label']) ? tra($params['add_button_label']) : $defaults['add_button_label'],
					'_id' => 'kaltura_upload_btn' . $instance,
				),
				$smarty
			);

		} else if (!empty($user)) {
			$html = '<span class="error">' . tra('Media id or permission to upload video is required') . '</span>';
		} else {
			$html = '<span class="error">' . tra('Log in to upload video') . '</span>';
		}

		return $html;
	} else {
		$id = $params['id'];
	}

	if (empty($params['player_id'])) {
		$params['player_id'] = $prefs['kaltura_kdpUIConf'];
	}

	global $kalturaadminlib; require_once 'lib/videogals/kalturalib.php';

	if ($kalturaadminlib && $kalturaadminlib->session && (empty($params['width']) || empty($params['height']))) {
		$player = $kalturaadminlib->getPlayersUiConf($params['player_id']);
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
	$params['session'] = $kalturalib->session;

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('kaltura', $params);
	$code = $smarty->fetch('wiki-plugins/wikiplugin_kaltura.tpl');

	$style = '';
	if (!empty($params['align'])) {
		$style .= "text-align:{$params['align']};";
	}
	if (!empty($params['float'])) {
		$style .= "float:{$params['float']};";
	}
	if (!empty($style)) {
		$code = "<div style=\"$style\">$code</div>";
	}

	return $code;
}
