<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_flash.php');

function wikiplugin_vimeo_info()
{
	global $prefs;

	return array(
		'name' => tra('Vimeo'),
		'documentation' => 'PluginVimeo',
		'description' => tra('Embed a Vimeo video'),
		'prefs' => array( 'wikiplugin_vimeo' ),
		'iconname' => 'vimeo',
		'introduced' => 6.1,
		'format' => 'html',
		'params' => array(
			'url' => array(
				'required' => $prefs['vimeo_upload'] !== 'y',
				'name' => tra('URL'),
				'description' => tra('Complete URL to the Vimeo video. Example:') . ' <code>http://vimeo.com/3319966</code>'
					.	($prefs['vimeo_upload'] === 'y' ? ' ' . tra('or leave blank to upload one.') : ''),
				'since' => '6.1',
				'filter' => 'url',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels'),
				'since' => '6.1',
				'filter' => 'text',
				'default' => 425,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels'),
				'since' => '6.1',
				'filter' => 'text',
				'default' => 350,
			),
			'quality' => array(
				'required' => false,
				'name' => tra('Quality'),
				'description' => tra('Quality of the video'),
				'since' => '6.1',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('High'), 'value' => 'high'),
					array('text' => tra('Medium'), 'value' => 'medium'),
					array('text' => tra('Low'), 'value' => 'low'),
				),
				'default' => 'high',
				'advanced' => true
			),
			'allowFullScreen' => array(
				'required' => false,
				'name' => tra('Full screen'),
				'description' => tra('Expand to full screen'),
				'since' => '6.1',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'true'),
					array('text' => tra('No'), 'value' => 'false'),
				),
				'default' => '',
				'advanced' => true
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'description' => tr('Numeric ID of a Vimeo file in a File Gallery (or list separated by commas or %0).',
					'<code>|</code>'),
				'since' => '12.0',
				'filter' => 'text',
				'default' => '',
				'advanced' => true
			),
			'fromFieldId' => array(
				'required' => false,
				'name' => tra('Field ID'),
				'description' => tra('Numeric ID of a Tracker Files field, using Vimeo displayMode.'),
				'since' => '12.0',
				'filter' => 'int',
				'default' => 0,
				'advanced' => true
			),
			'fromItemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('Numeric ID of a Tracker item, using Vimeo displayMode.'),
				'since' => '12.0',
				'filter' => 'int',
				'default' => 0,
				'advanced' => true
			),
			'galleryId' => array(
				'required' => false,
				'name' => tra('Gallery ID'),
				'description' => tra('Gallery ID to upload to.'),
				'since' => '12.0',
				'filter' => 'int',
				'advanced' => true
			),
			'useFroogaloopApi' => array(
                                'required' => false,
                                'name' => tra('Froogaloop API'),
                                'description' => tra('Use Vimeo Froogaloop API'),
				'since' => '14.0',
                                'filter' => 'alpha',
                                'options' => array(
                                        array('text' => '', 'value' => ''),
                                        array('text' => tra('Yes'), 'value' => 'true'),
                                        array('text' => tra('No'), 'value' => 'false'),
                                ),
                                'default' => '',
                                'advanced' => true
                        ),
		),
	);
}

function vimeo_iframe($data, $params) {
	if (!empty($params['height'])) {
		$height = $params['height'];
	} else {
		$height = '350';
	}	
	if (!empty($params['width'])) {
		$width = $params['width'];
	} else {
		$width = '425';
	}

	$urlparts = explode('/', $params['vimeo']);
	foreach ($urlparts as $urlpart) {
		if (ctype_digit($urlpart)) {
			$vimeoId = $urlpart;
		}
	}	
	if (!isset($vimeoId)) {
		return '';
	}
	$url = '//player.vimeo.com/video/' . $vimeoId; 

	$output = '<iframe data-fileid="' . $params['vimeo_fileId'] . '" id="' . $params['player_id'] . '" src="' . $url . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
	return $output;
}

function wikiplugin_vimeo($data, $params)
{
	global $prefs;
	static $instance = 0;
	$instance++;

	if ($params['useFroogaloopApi']) { 
		TikiLib::lib('header')->add_jsfile('vendor_extra/vimeo/froogaloop.min.js', true);
		TikiLib::lib('header')->add_jsfile('vendor_extra/vimeo/vimeo.js');
	}

	if (isset($params['url'])) {
		$params['vimeo'] = $params['url'];
		$params['player_id'] = "pid_".uniqid(); 
		$params['vimeo_fileId'] = 0;
		unset($params['url']);
		if ($params['useFroogaloopApi']) {
			$params['vimeo'] .= "?api=1&player_id=".$params['player_id'];
		}
		return vimeo_iframe($data, $params);
	} elseif (isset($params['fileId'])) {
		$fileIds = preg_split('/\D+/', $params['fileId'], -1, PREG_SPLIT_NO_EMPTY);
		unset($params['fileId']);

		$out = '';
		foreach ($fileIds as $fileId) {
		$attributelib = TikiLib::lib('attribute');
			$attributes = $attributelib->get_attributes('file', $fileId);
			if (!empty($attributes['tiki.content.url'])) {
				$params['vimeo'] = $attributes['tiki.content.url'];
				$params['player_id'] = "pid_".uniqid(); 
				$params['vimeo_fileId'] = $fileId;
				if ($params['useFroogaloopApi']) {
					$params['vimeo'] .= "?api=1&player_id=".$params['player_id'];
				}
				$out .= vimeo_iframe($data, $params);
			} else {
				TikiLib::lib('errorreport')->report(tr('Vimeo video not found for file #%0', $fileId));
			}
		}

		return $out;
	} else {

		global $page;
		$smarty = TikiLib::lib('smarty');
		if ($prefs['vimeo_upload'] !== 'y') {
			$smarty->loadPlugin('smarty_block_remarksbox');
			$repeat = false;
			return smarty_block_remarksbox(
				array('type' => 'error', 'title' => tra('Feature required')),
				tra('Feature "vimeo_upload" is required to be able to add videos here.'), $smarty, $repeat
			);
		}

		// old perms access to get "special" gallery perms to handle user gals etc
		$perms = TikiLib::lib('tiki')->get_perm_object(
			!empty($params['galleryId']) ? $params['galleryId'] : $prefs['vimeo_default_gallery'],
			'file gallery',
			TikiLib::lib('filegal')->get_file_gallery_info($prefs['vimeo_default_gallery']),
			false
		);
		if ($perms['tiki_p_upload_files'] !== 'y' ) {

			return '';		//$permMessage = tra('You do not have permsission to add files here.');

		} else if (!empty($params['fromFieldId'])) {

			$fieldInfo = TikiLib::lib('trk')->get_tracker_field($params['fromFieldId']);
			if (empty($params['fromItemId'])) {
				$item = Tracker_Item::newItem($fieldInfo['trackerId']);
			} else {
				$item = Tracker_Item::fromId($params['fromItemId']);
			}
			if (!$item->canModify()) {
				return '';		//$permMessage = tra('You do not have permsission modify this tracker item.');
			}
		} else if ($page) {
			$pagePerms = Perms::get(array( 'type' => 'wiki page', 'object' => $page ))->edit;
			if (!$pagePerms) {
				return '';		//$permMessage = tra('You do not have permsission modify this page.');
			}
		}

		// set up for an upload
		$smarty->loadPlugin('smarty_function_button');
		$smarty->loadPlugin('smarty_function_service');
		$html = smarty_function_button(
			array(
				'_keepall' => 'y',
				'_class' => 'vimeo dialog',
				'href' => smarty_function_service(
					array(
						'controller' => 'vimeo',
						'action' => 'upload',
					),
					$smarty
				),
				'_text' => tra('Upload Video'),
			), $smarty
		);

		$js = '
$(".vimeo.dialog").click(function () {
	var link = this;
	$(this).serviceDialog({
		title: tr("Upload Video"),
		data: {
			controller: "vimeo",
			action: "upload"' .
			(!empty($params['galleryId']) ? ',galleryId:' . $params['galleryId'] : '') .
			(!empty($params['fromFieldId']) ? ',fieldId:' . $params['fromFieldId'] : '') .
			(!empty($params['fromItemId']) ? ',itemId:' . $params['fromItemId'] : '') . '
		},
		load: function(data) {
			var $dialog = $(".vimeo_upload").parents(".ui-dialog-content");		// odd its the content, not the outer div
			$(".vimeo_upload").on("vimeo_uploaded", function(event, data) {';

		if (!empty($page) && empty($params['fromFieldId'])) {
			$js .= '
				var params = {
					page: ' . json_encode($page) . ',
					content: "",
					index: ' . $instance . ',
					type: "vimeo",
					params: {
						url: data.url
					}
				};
				$.post("tiki-wikiplugin_edit.php", params, function() {
					$("input[type=file]", $dialog).val("");		// webkit reloads the dialog as it destroys it for some reason
					$dialog.dialog("destroy").remove();
					$.get($.service("wiki", "get_page", {page:' . json_encode($page) . '}), function (data) {
						if (data) {
							$("#page-data").html(data);
						}
					});
				});';
		} else {
			$js .= '
				$dialog.dialog("destroy").remove();
				handleVimeoFile(link, data);
';
		}

		$js .= '	});
		}
	});
	return false;
});';
		TikiLib::lib('header')->add_jq_onready($js);

		return $html;
	}

}
