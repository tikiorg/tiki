<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		'introduced' => 8.0,
		'prefs' => array('wikiplugin_carousel', 'feature_file_galleries', 'feature_jquery_carousel'),
		'icon' => 'img/icons/wand.png',
		'tags' => array( 'basic' ),		
		'params' => array(
			'fgalId' => array(
				'required' => true,
				'name' => tra('File Gallery ID'),
				'description' => tra('ID number of the file gallery that contains the images to be displayed'),
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort by database table field name, ascending or descending. Examples: fileId_asc or name_desc.'),
				'filter' => 'word',
				'accepted' => 'fieldname_asc or fieldname_desc with actual table field name in place of \'fieldname\'.',
				'default' => 'created_desc',
			),
			'transitionSpeed' => array(
				'required' => false,
				'name' => tra('Transition time'),
				'description' => tra('The time (in milliseconds) it will take to transition between two images.'),
				'filter' => 'digits',
				'accepted' => tra('number'),
				'default' => '1500',
			),
			'displayTime' => array(
				'required' => false,
				'name' => tra('Display time'),
				'description' => tra('The time (in milliseconds) to display each image.'),
				'filter' => 'digits',
				'accepted' => tra('number'),
				'default' => '6000',
			),
			'textholderHeight' => array(
				'required' => false,
				'name' => tra('Caption height'),
				'description' => tra('The height of the caption. This is a fraction of the height of the images.'),
				'filter' => 'text',
				'accepted' => tra('real between 0 and 1'),
				'default' => '.2',
			),
			'displayProgressBar' => array(
				'required' => false,
				'name' => tra('Display progress ring'),
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
			),
			'thumbnailType' => array(
				'required' => false,
				'name' => tra('Display thumbnails'),
				'description' => tra('Display thumbnails, number, count etc.'),
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
				'name' => tra('Thumbnail box width'),
				'description' => tra('Width of thumbnail box in CSS units (default "20px")'),
				'filter' => 'text',
				'default' => '20px',
			),
			'thumbnailHeight' => array(
				'required' => false,
				'name' => tra('Thumbnail box height'),
				'description' => tra('Height of thumbnail box in CSS units (default "20px")'),
				'filter' => 'text',
				'default' => '20px',
			),
			'autoPilot' => array(
				'required' => false,
				'name' => tra('Start automatically'),
				'description' => tra('Move the carousel automatically when the page loads (default Yes).'),
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
			),
			'displayThumbnails' => array(
				'required' => false,
				'name' => tra('Display thumbnails'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Display thumbnails.'),
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
				'name' => tra('Display place numbers in the thumbnail boxes'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Display place numbers in the thumbnail boxes.'),
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
				'name' => tra('Use corresponding image as background for a thumbnail box'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Use corresponding image as background for a thumbnail box.'),
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
				'name' => tra('Thumbnail box font size'),
				'description' => tra('Legacy v2 param:') . ' ' . tra('Font size of thumbnail box in CSS units (default ".7em").'),
				'filter' => 'text',
				'accepted' => tra('real between 0 and 1'),
				'default' => '.7em',
				'advanced' => true,
			),
			'displaySize' => array(
				'required' => false,
				'name' => tra('Size of picture'),
				'description' => tra('In case your picture is too large, you can specify a scale between 0 and 1 to reduce it'),
				'filter' => 'text',
				'default' => '1',
				'accepted' => tra('real between 0 and 1'),
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
		
	$filegallib = TikiLib::lib('filegal');
	$files = $filegallib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId']);
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

	unset(
		$params['displayProgressBar'],
		$params['displayThumbnails'],
		$params['displayThumbnailNumbers'],
		$params['displayThumbnailBackground'],
		$params['thumbnailWidth'],
		$params['thumbnailHeight'],
		$params['thumbnailFontSize']
	);
	
	TikiLib::lib('header')->add_jq_onready('setTimeout( function() { $("#' . $unique . '").tiki("carousel", "", '. json_encode($params).'); }, 1000);');

	$html = '<div id="'.$unique.'" class="clearfix carousel" style="width: 1px; height: 1px; overflow: hidden"><ul>';
	foreach ($files['data'] as $file) {
		$html .= '<li><img src="tiki-download_file.php?fileId='.$file['fileId'].'&amp;display';
		if (!empty($params['displaySize']) && $params['displaySize'] != 1)
			$html .= '&amp;scale='.$params['displaySize'];
		$html .= ' alt="'.htmlentities($file['description']).'" />';
			
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
			$caption = '<p>' . $caption . '</p>';
			TikiLib::lib('header')->add_css('.textholder { padding: .5em .8em; }');
		}
		$html .= $caption . '</li>';
	}
	$html .= '</ul></div>';
	return "~np~$html~/np~";
}

