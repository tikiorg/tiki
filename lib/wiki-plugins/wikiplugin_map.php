<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_map_info() {
	return array(
		'name' => tra('Map'),
		'documentation' => 'PluginMap',
		'description' => tra('Display a map created using the Maps feature'),
		'prefs' => array( 'feature_maps', 'wikiplugin_map' ),
		'icon' => 'pics/icons/map.png',
		'params' => array(
			'mapfile' => array(
				'required' => true,
				'name' => tra('Map File'),
				'description' => tra('Map file identifier'),
			),
			'extents' => array(
				'required' => false,
				'name' => tra('Extents'),
				'description' => tra('Extents'),
			),
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of the map'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of the map'),
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of the map'),
			),
		),
	);
}

function wikiplugin_map($data, $params) {
	global $tikilib, $prefs;

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
	if(@$prefs['feature_maps'] != 'y') {
		$map=tra("Feature disabled");
	} else {
		$map='<object border="0" hspace="0" vspace="0" type="text/html" data="tiki-map.php?'.$mapdata.$extdata.$sizedata.'maponly=frame" '.$widthdata.' '.$heightdata.'><a href="tiki-map.php?'.$mapdata.$extdata.$sizedata.'"><img src="tiki-map.php?'.$mapdata.$extdata.$sizedata.'maponly=yes"/></a></object>';

	}
	return $map;
}
