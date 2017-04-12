<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_carousel_info()
{
	return array(
		'name' => tra('Carousel'),
		'documentation' => 'PluginCarousel',
		'description' => tra('Display images in a self-advancing carousel'),
		'introduced' => 8,
		'prefs' => array('wikiplugin_carousel', 'feature_file_galleries', 'feature_jquery_carousel'),
		'iconname' => 'wizard',
		'tags' => array( 'basic' ),
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Images Source'),
				'description' => tra('Choose where to get your images from'),
				'default' => 'fgalId',
				'options' => array(
					array('text' => tra('Select an option'), 'value' => ''),
					array('text' => tra('All the images in a file gallery'), 'value' => 'fgalId'),
					array('text' => tra('A list of file IDs'), 'value' => 'fileIds'),
				),
			),
			'fgalId' => array(
				'required' => true,
				'name' => tra('File Gallery ID'),
				'description' => tra('ID number of the file gallery that contains the images to be displayed'),
				'since' => '8.0',
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'fgalId'),
				'profile_reference' => 'file_gallery',
			),
			'fileIds' => array(
				'required' => true,
				'name' => tra('File IDs'),
				'description' => tra('List of IDs of images from the File Galleries separated by commas.'),
				'filter' => 'striptags',
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'fileIds'),
				'profile_reference' => 'file',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tr('Sort by database table field name, ascending or descending. Examples:
					%0 or %1.', '<code>fileId_asc</code>', '<code>name_desc</code>'),
				'since' => '8.0',
				'filter' => 'word',
				'accepted' => tr('%0 or %1 with actual database field name in place of
					%2.', '<code>fieldname_asc</code>', '<code>fieldname_desc</code>', '<code>fieldname</code>'),
				'default' => 'created_desc',
			),
			'transitionSpeed' => array(
				'required' => false,
				'name' => tra('Transition Time'),
				'description' => tra('The time (in milliseconds) for the transition between two images.'),
				'since' => '8.0',
				'filter' => 'digits',
				'accepted' => tra('number'),
				'default' => '1500',
			),
			'displayTime' => array(
				'required' => false,
				'name' => tra('Display Time'),
				'description' => tra('The time (in milliseconds) to display each image.'),
				'since' => '8.0',
				'filter' => 'digits',
				'accepted' => tra('number'),
				'default' => '6000',
			),
			'textholderHeight' => array(
				'required' => false,
				'name' => tra('Caption Height'),
				'description' => tra('The height of the caption. This is a fraction of the height of the images.'),
				'since' => '8.0',
				'filter' => 'text',
				'accepted' => tra('real between 0 and 1'),
				'default' => '.2',
				'advanced' => true,
			),
			'displayProgressBar' => array(
				'required' => false,
				'name' => tra('Display Progress Bar'),
				'since' => '8.0',
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
				'advanced' => true,
			),
			'thumbnailType' => array(
				'required' => false,
				'name' => tra('Display Thumbnails'),
				'description' => tra('Display thumbnails, number, count etc.'),
				'since' => '8.0',
				'filter' => 'text',
				'options' => array(
					array('text' => tra('None'), 'value' => 'none'),
					array('text' => tra('Buttons'), 'value' => 'buttons'),
					array('text' => tra('Images'), 'value' => 'images'),
					array('text' => tra('Numbers'), 'value' => 'numbers'),
					array('text' => tra('Count'), 'value' => 'count'),
				),
				'default' => 'none',
			),
			'thumbnailWidth' => array(
				'required' => false,
				'name' => tra('Thumbnail Box Width'),
				'description' => tr('Width of thumbnail box in CSS units (default %0)', '<code>20px</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '20px',
				'advanced' => true,
			),
			'thumbnailHeight' => array(
				'required' => false,
				'name' => tra('Thumbnail Box Height'),
				'description' => tr('Height of thumbnail box in CSS units (default %0)', '<code>20px</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '20px',
				'advanced' => true,
			),
			'autoPilot' => array(
				'required' => false,
				'name' => tra('Start Automatically'),
				'description' => tra('Start the carousel automatically when the page loads (default: Yes).'),
				'since' => '8.0',
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
			),
			'displayThumbnails' => array(
				'required' => false,
				'name' => tra('Thumbnails'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Display thumbnails.'),
				'since' => '8.0',
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
				'advanced' => true,
			),
			'displayThumbnailNumbers' => array(
				'required' => false,
				'name' => tra('Thumbnail Numbers'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Display place numbers in the thumbnail boxes.'),
				'since' => '8.0',
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
				'advanced' => true,
			),
			'displayThumbnailBackground' => array(
				'required' => false,
				'name' => tra('Thumbnail Background'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Use corresponding image as background for a thumbnail box.'),
				'since' => '8.0',
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
				'advanced' => true,
			),
			'thumbnailFontSize' => array(
				'required' => false,
				'name' => tra('Thumbnail Font Size'),
				'description' => tra('Legacy v2 param:') . ' ' . tr('Font size of thumbnail box in CSS units
					(default %0).', '<code>7em</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'accepted' => tra('CSS units'),
				'default' => '.7em',
				'advanced' => true,
			),
			'displaySize' => array(
				'required' => false,
				'name' => tra('Image Size'),
				'description' => tra('Scale image between 0 and 1 to reduce it, or set a maximum size in pixels'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '1',
				'accepted' => tra('Real between 0 and 1, or integer over 10'),
				'advanced' => true,
			),
			'clickable' => array(
				'required' => false,
				'name' => tra('Makes images clickable'),
				'description' => tra('In case there are URLs in the image description, the image is made clickable and links to the first URL found'),
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'default' => '0',
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_carousel( $body, $params )
{
	static $id = 0;
	$plugininfo = wikiplugin_carousel_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	$params = array_merge($default, $params);

	$unique = 'wpcarousel-' . ++$id;
	$uniqueId = 'carousel'.$id;

	$filegallib = TikiLib::lib('filegal');

	if ($params['type'] == 'fgalId') {
		$files = $filegallib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId']);
	} else if ($params['type'] == 'fileIds') {
		$params['fileIds'] = explode(',', $params['fileIds']);

		foreach ($params['fileIds'] as $fileId) {
			$file = $filegallib->get_file($fileId);

			if (!is_null($file)) {
				$files['data'][] = $file;
			}
		}

		$files['cant'] = count($files['data']);
	}

	if (empty($files['cant'])) {
		return '';
	}

	unset($params['fgalId'], $params['sort_mode']);

	$params['displayProgressRing'] = ($params['displayProgressBar'] == 1);

	if (empty($params['thumbnailType'])) {
		$displayThumbnails = ($params['displayThumbnails'] == 1);
		$displayThumbnailNumbers = ($params['displayThumbnailNumbers'] == 1);
		$displayThumbnailBackground = ($params['displayThumbnailBackground'] == 1);

		if ($displayThumbnailNumbers) {
			$params['thumbnailType']= 'numbers';
		}
		if ($displayThumbnails) {
			$params['thumbnailType']= 'images';
		}
	}

	foreach ( $params as &$param) {
		if (is_numeric($param)) {
			$param = (float) $param;	// seems to leave ints as ints
		}
	}

 	if ($params['thumbnailType'] === 'images') {
		TikiLib::lib('header')->add_css(".ic_button { float: left; width: {$params['thumbnailWidth']}; height: {$params['thumbnailHeight']};}");
	}

	if (isset($params['clickable']) && $params['clickable'] == 1 ) {
		$jq_clickable = '
function carousel_callback(){
	$("#' . $uniqueId . ' div.ic_tray div.ic_caption").each(function(){
		var $this = $(this);
		//console.log("JML" + $this.text());
		var source = $this.text();
		var regexToken = /https?:\/\/[\-\w@:%_\+.~#?,&\/\/=]+/;
		var matchArray;
		if ( (matchArray = regexToken.exec( source )) !== null) {
			//console.log("JJJ" + matchArray[0]);
			$this.parent().click(function(){
				window.location = matchArray[0];
			}).css("cursor", "pointer");
		};
	});
};
	';
	} else {
		$jq_clickable = '
function carousel_callback(){ };
	';
	}
	TikiLib::lib('header')->add_jq_onready($jq_clickable);

	unset(
		$params['displayProgressBar'],
		$params['displayThumbnails'],
		$params['displayThumbnailNumbers'],
		$params['displayThumbnailBackground'],
		$params['thumbnailWidth'],
		$params['thumbnailHeight'],
		$params['thumbnailFontSize']
	);

	TikiLib::lib('header')->add_jq_onready('setTimeout( function() { $("#' . $unique . '").tiki("carousel", "", '. json_encode($params).'); carousel_callback();}, 1000);');
	$size = '';
	if (empty($params['displaySize'])) {
		$size = 'width: 1px; height: 1px;';
	} else if ($params['displaySize'] > 10) {
		$size = "width: {$params['displaySize']}px; height: {$params['displaySize']}px;";
	}
	$html = '<div id="'.$uniqueId.'" ><div id="'.$unique. '" class="clearfix carousel" style="' . $size . ' overflow: hidden"><ul>';
	foreach ($files['data'] as $file) {
		$html .= '<li><img src="tiki-download_file.php?fileId='.$file['fileId'].'&amp;display';
		if (!empty($params['displaySize'])) {
			if ($params['displaySize'] > 10) {
				$html .= '&amp;max='.$params['displaySize'];
			} elseif ( $params['displaySize'] <= 1) {
				$html .= '&amp;scale='.$params['displaySize'];
			}
		}

		$html .= '" alt="'.htmlentities($file['description']).'" />';

		$caption = '';
		if (!empty($file['name'])) {
			$caption .= '<strong>'.htmlentities($file['name']).'</strong>';
			if (!empty($file['description'])) {
					$caption .= '<br />';
			}
		}
		if (!empty($file['description'])) {
			$caption .= htmlentities($file['description']);
		}
		if (!empty($caption)) {
			$caption = '<p style="display:none;">' . $caption . '</p>';
			TikiLib::lib('header')->add_css('.textholder { padding: .5em .8em; }');
		}
		$html .= $caption . '</li>';
	}
	$html .= '</ul></div></div>';

	return "~np~$html~/np~";
}

