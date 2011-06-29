<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_carousel.php 34433 2011-05-15 18:54:24Z chealer $

function wikiplugin_carousel_info()
{
	return array(
		'name' => tra('Carousel'),
		'documentation' => 'PluginCarousel',
		'description' => tra('Create a link that shows/hides initially hidden content'),
		'prefs' => array('wikiplugin_carousel', 'feature_file_galleries', 'feature_jquery_carousel'),
		'body' => tra('Wiki syntax containing the content that can be hidden or shown.'),
		'filter' => 'wikicontent',
		'icon' => 'pics/icons/wand.png',
		'params' => array(
			'fgalId' => array(
				'required' => true,
				'name' => tra('File Gallery ID'),
				'description' => tra('ID number of the file gallery that contains the images to be displayed'),
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
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
				'description' => tra('The height of the caption. This is a fraction of the height of the images. '),
				'filter' => 'striptags',
				'accepted' => tra('real'),
				'default' => '.2',
			),
			'displayProgressBar' => array(
				'required' => false,
				'name' => tra('Display progress bar'),
				'description' => tra('Display progress bar.'),
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
				'description' => tra('Display thumbnails.'),
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
				'default' => '1',
			),
			'displayThumbnailNumbers' => array(
				'required' => false,
				'name' => tra('Display place numbers in the thumbnail boxes'),
				'description' => tra('Display place numbers in the thumbnail boxes.'),
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
				'default' => '1',
			),
			'displayThumbnailBackground' => array(
				'required' => false,
				'name' => tra('Use corresponding image as background for a thumbnail box'),
				'description' => tra('Use corresponding image as background for a thumbnail box.'),
				'filter' => 'digits',
				'options' => array(
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
				'default' => '1',
			),
			'thumbnailWidth' => array(
				'required' => false,
				'name' => tra('Width of thumbnail box'),
				'description' => tra('Width of thumbnail box in pixels'),
				'filter' => 'digits',
				'default' => '20',
			),
			'thumbnailHeight' => array(
				'required' => false,
				'name' => tra('Height of thumbanil box'),
				'description' => tra('Height of thumbnail box in pixels'),
				'filter' => 'digits',
				'accepted' => tra(''),
				'default' => '20',
			),
			'thumbnailFontSize' => array(
				'required' => false,
				'name' => tra('Font size thumbanil box'),
				'description' => tra('Font size thumbanil box in em.'),
				'filter' => 'striptags',
				'default' => '.7',
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
	$files = $filegallib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId'], false, false, false, true, false, false, false, false, '', true, false, false, $filter);
	if (empty($files['cant'])) {
		return '';
	}
	$jqparams = array();
	foreach ($params as $param=>$value) {
		$jqparams[] = "$param : $value";
	}
	$jq = '
				$(document).ready(function(){
					$(\'#' . $unique . '\').infiniteCarousel({'. implode(', ', $jqparams).'});
				});';
	global $headerlib;
	$headerlib->add_jq_onready($jq);
	$html = '<div id="'.$unique.'" class="clearfix"><ul>';
	foreach ($files['data'] as $file) {
		echo $file['fileId'].' '.$unique;
		$html .= '<li><img src="tiki-download_file.php?fileId='.$file['fileId'].'&display" alt="'.$file['description'].'" /></li>';
	}
	$html .= '</ul></div>';
	return "~np~$html~/np~";
}

