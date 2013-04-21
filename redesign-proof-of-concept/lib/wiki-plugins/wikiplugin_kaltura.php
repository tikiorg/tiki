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
		$kalturaadminlib = TikiLib::lib('kalturaadmin');

		$playerList = $kalturaadminlib->getPlayersUiConfs();
		$players = array();
		foreach ($playerList as $pl) {
			$players[] = array('value' => $pl['id'], 'text' => tra($pl['name']));
		}

		if (count($players)) {
			array_unshift($players, array('value' => '', 'text' => tra('Default')));
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
			'type' => array(
				'required' => false,
				'name' => tra('Player type'),
				'description' => tra('"kdp" or "html5"'),
				'default' => 'kdp',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('KDP'), 'value' => 'kdp'),
					array('text' => tra('HTML5'), 'value' => 'html5'),
				),
			),
		),
	);
}

function wikiplugin_kaltura($data, $params)
{
	global $prefs, $tiki_p_upload_videos, $user, $page;

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

			$json_page = json_encode($page);
			$json_instance = json_encode($instance);
			$json_title = json_encode(tr('Upload Media'));
			TikiLib::lib('header')->add_jq_onready(
<<<REG
$("#kaltura_upload_btn$instance a").on("click", function() {
	$(this).serviceDialog({
		title: $json_title,
		width: 710,
		height: 450,
		hideButtons: true,
		success: function (data) {
			if (data.entries) {
				$.post('tiki-wikiplugin_edit.php', {
					content: '',
					type: 'kaltura',
					page: {$json_page},
					index: {$json_instance},
					params: {
						id: data.entries[0]
					}
				}, function () {
					document.location.reload();
				});
			}
		}
	});
	return false;
});
REG
			);

			$html = smarty_function_button(
				array(	// default for add_button_label already tra but not merged yet
					'_text' => !empty($params['add_button_label']) ? tra($params['add_button_label']) : $defaults['add_button_label'],
					'_id' => 'kaltura_upload_btn' . $instance,
					'href' => TikiLib::lib('service')->getUrl(array(
						'controller' => 'kaltura',
						'action' => 'upload'
					)),
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

	if (empty($params['width']) || empty($params['height'])) {
		$kalturaadminlib = TikiLib::lib('kalturaadmin');
		$player = $kalturaadminlib->getPlayersUiConf($params['player_id']);
		if (! empty($player)) {
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

	$kalturalib = TikiLib::lib('kalturauser');
	$params = array_merge($defaults, $params);
	$params['session'] = $kalturalib->getSessionKey();
	$params['media_url'] = $kalturalib->getMediaUrl($params['id'], $params['player_id']);

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('kaltura', $params);
	$style = '';
	if (!empty($params['align'])) {
		$style .= "text-align:{$params['align']};";
	}
	if (!empty($params['float'])) {
		$style .= "float:{$params['float']};";
	}
	if ($params['type'] === 'html5') {
		$embedIframeJs = '/embedIframeJs';	// TODO add as params?
		$leadWithHTML5 = 'true';
		$autoPlay = 'false';

		TikiLib::lib('header')
			->add_jsfile("{$prefs['kaltura_kServiceUrl']}/p/{$prefs['kaltura_partnerId']}/sp/{$prefs['kaltura_partnerId']}00{$embedIframeJs}/uiconf_id/{$params['player_id']}/partner_id/{$prefs['kaltura_partnerId']}")
			->add_jq_onready("
mw.setConfig('Kaltura.LeadWithHTML5', $leadWithHTML5);

kWidget.embed({
	targetId: 'kaltura_player$instance',
	wid: '_{$prefs['kaltura_partnerId']}',
	uiconf_id: '{$params['player_id']}',
	entry_id: '{$params['id']}',
	flashvars: { // flashvars allows you to set runtime uiVar configuration overrides.
		//autoPlay: $autoPlay
	},
	params: { // params allows you to set flash embed params such as wmode, allowFullScreen etc
		wmode: 'transparent'
	},
	readyCallback: function (playerId) {
		\$ = \$jq;	// restore our jQuery after Kaltura has finished with it
		console.log('Player:' + playerId + ' is ready ');
	}
});");
		return "<div id='kaltura_player$instance' style='width:{$params['width']}px;height:{$params['height']}px;$style'></div>";

	} elseif ($params['type'] === 'kdp') {
		$code = $smarty->fetch('wiki-plugins/wikiplugin_kaltura.tpl');
		if (!empty($style)) {
			$code = "<div style='$style'>$code</div>";
		}
		return $code;

	} else {
		TikiLib::lib('erroreport')->report(tra('Kaltura player: unsupported type.'));
		return '';
	}

}
