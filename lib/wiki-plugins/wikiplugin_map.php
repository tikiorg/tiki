<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_map_info() {
	return array(
		'name' => tra('Map'),
		'format' => 'html',
		'documentation' => 'PluginMap',
		'description' => tra('Display a map'),
		'prefs' => array( 'wikiplugin_map' ),
		'icon' => 'pics/icons/map.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'scope' => array(
				'required' => false,
				'name' => tr('Scope'),
				'description' => tr('Display the geolocated items represented in the page. (all, center, or custom as a CSS selector)'),
				'filter' => 'striptags',
				'default' => 'center',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of the map in pixels'),
				'filter' => 'int',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of the map in pixels'),
				'filter' => 'int',
			),
			'mapfile' => array(
				'required' => false,
				'name' => tra('MapServer File'),
				'description' => tra('MapServer file identifier. Only fill this in if you are using MapServer.'),
				'filter' => 'url',
				'advanced' => true,
			),
			'extents' => array(
				'required' => false,
				'name' => tra('Extents'),
				'description' => tra('Extents'),
				'filter' => 'text',
				'advanced' => true,
			),
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of the map'),
				'filter' => 'int',
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_map($data, $params) {
	global $tikilib, $prefs;

	if (isset($params['mapfile'])) {
		return wp_map_mapserver($params);
	}

	require_once 'lib/smarty_tiki/modifier.escape.php';

	$width = '100%';
	if (isset($params['width'])) {
		$width = intval($params['width']) . 'px';
	}

	$height = '100%';
	if (isset($params['height'])) {
		$height = intval($params['height']) . 'px';
	}

	TikiLib::lib('header')->add_map();
	$scope = smarty_modifier_escape(wp_map_getscope($params));
	return "<div class=\"map-container\" data-marker-filter=\"$scope\" style=\"width: {$width}; height: {$height};\"></div>";
}

function wp_map_getscope($params)
{
	$scope = 'center';
	if (isset($params['scope'])) {
		$scope = $params['scope'];
	}

	switch ($scope) {
		case 'center':
			return '#tiki-center .geolocated';
		case 'all':
			return '.geolocated';
		default:
			return $scope;
	}
}

function wp_map_mapserver($params)
{
	global $prefs;

	if ($prefs['feature_maps'] != 'y') {
		return WikiParser_PluginOutput::disabled('map', array('feature_maps'));
	}

	extract ($params,EXTR_SKIP);
	$mapdata="";
	if (isset($mapfile)) {
		$mapdata='mapfile='.$mapfile.'&';
	}

	$extdata="";
	if (isset($extents)) {
		$dataext=explode("|",$extents);
		if (count($dataext)==4) {
			$minx=floatval($dataext[0]);
			$maxx=floatval($dataext[1]);
			$miny=floatval($dataext[2]);
			$maxy=floatval($dataext[3]);
			$extdata="minx=".$minx."&maxx=".$maxx."&miny=".$miny."&maxy=".$maxy."&zoom=1&";
		}
	}
	
	$sizedata="";
	if (isset($size)) {
		$sizedata="size=".intval($size)."&";
	}
	$widthdata="";
	if (isset($width)) {
		$widthdata='width="'.intval($width).'"';
	}
	$heightdata="";
	if (isset($height)) {
		$heightdata='height="'.intval($height).'"';
	}	
	if (@$prefs['feature_maps'] != 'y') {
		$map=tra("Feature disabled");
	} else {
		$map='<object border="0" hspace="0" vspace="0" type="text/html" data="tiki-map.php?'.$mapdata.$extdata.$sizedata.'maponly=frame" '.$widthdata.' '.$heightdata.'><a href="tiki-map.php?'.$mapdata.$extdata.$sizedata.'"><img src="tiki-map.php?'.$mapdata.$extdata.$sizedata.'maponly=yes"/></a></object>';

	}
	return $map;
}
