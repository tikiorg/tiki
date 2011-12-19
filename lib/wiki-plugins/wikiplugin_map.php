<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_map_info()
{
	return array(
		'name' => tra('Map'),
		'format' => 'html',
		'documentation' => 'PluginMap',
		'description' => tra('Display a map'),
		'prefs' => array( 'wikiplugin_map' ),
		'icon' => 'pics/icons/map.png',
		'tags' => array( 'basic' ),
		'filter' => 'wikicontent',
		'body' => tr('Instructions to load content'),
		'params' => array(
			'scope' => array(
				'required' => false,
				'name' => tr('Scope'),
				'description' => tr('Display the geolocated items represented in the page. (all, center, or custom as a CSS selector)'),
				'filter' => 'striptags',
				'default' => 'center',
			),
			'controls' => array(
				'required' => false,
				'name' => tr('Controls'),
				'description' => tr('Allows to specify which map controls will be displayed on the map and around it.'),
				'filter' => 'word',
				'separator' => ',',
				'default' => wp_map_default_controls(),
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
			'center' => array(
				'requied' => false,
				'name' => tr('Center'),
				'description' => tr('Center and zoom level of the map display.'),
				'filter' => 'text',
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

function wikiplugin_map($data, $params)
{
	global $tikilib, $prefs;

	if (isset($params['mapfile'])) {
		return wp_map_mapserver($params);
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_escape');

	$width = '100%';
	if (isset($params['width'])) {
		$width = intval($params['width']) . 'px';
	}

	$height = '100%';
	if (isset($params['height'])) {
		$height = intval($params['height']) . 'px';
	}

	if (! isset($params['controls'])) {
		$params['controls'] = wp_map_default_controls();
	}

	if (! is_array($params['controls'])) {
		$params['controls'] = explode(',', $params['controls']);
	}

	$controls = array_intersect($params['controls'], wp_map_available_controls());
	$controls = implode(',', $controls);

	$center = null;
	if (isset($params['center'])) {
		$geolib = TikiLib::lib('geo');
		if ($coords = $geolib->parse_coordinates($params['center'])) {
			$center = ' data-geo-center="' . smarty_modifier_escape($geolib->build_location_string($coords)) . '" ';
		}
	}

	TikiLib::lib('header')->add_map();
	$scope = smarty_modifier_escape(wp_map_getscope($params));

	$output = "<div class=\"map-container\" data-marker-filter=\"$scope\" data-map-controls=\"{$controls}\" style=\"width: {$width}; height: {$height};\" $center>";

	$argumentParser = new WikiParser_PluginArgumentParser;
	$matches = WikiParser_PluginMatcher::match($data);
	foreach ($matches as $match) {
		$name = $match->getName();
		$arguments = $argumentParser->parse($match->getArguments());

		$function = 'wp_map_plugin_' . $name;
		if (function_exists($function)) {
			$output .= $function($match->getBody(), new JitFilter($arguments));
		}
	}

	$output .= "</div>";

	return $output;
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

	extract($params, EXTR_SKIP);
	$mapdata="";
	if (isset($mapfile)) {
		$mapdata='mapfile='.$mapfile.'&';
	}

	$extdata="";
	if (isset($extents)) {
		$dataext=explode("|", $extents);
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

function wp_map_default_controls()
{
	return 'controls,layers,search_location';
}

function wp_map_available_controls()
{
	return array(
		'controls',
		'layers',
		'levels',
		'search_location',
		'current_location',
		'scale',
		'streetview',
	);
}

function wp_map_plugin_searchlayer($body, $args)
{
	$layer = $args->layer->text();
	$refresh = $args->refresh->int();
	unset($args['layer']);
	unset($args['refresh']);

	$args->setDefaultFilter('text');

	TikiLib::lib('smarty')->loadPlugin('smarty_modifier_escape');

	$filters = '';
	foreach ($args as $key => $arg) {
		$filters .= '<input type="hidden" name="filter~' . $key . '" value="' . smarty_modifier_escape($arg) . '"/>';
	}

	$escapedLayer = smarty_modifier_escape($layer);
	return <<<OUT
<form method="post" action="tiki-searchindex.php" class="search-box onload" style="display: none" data-result-refresh="$refresh" data-result-layer="$escapedLayer">
	<p>$filters<input type="submit"/></p>
</form>
OUT;
}

