<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		'prefs' => array( 'wikiplugin_map', 'feature_search' ),
		'iconname' => 'map',
		'introduced' => 1,
		'tags' => array( 'basic' ),
		'filter' => 'wikicontent',
		'body' => tr('Instructions to load content'),
		'params' => array(
			'scope' => array(
				'required' => false,
				'name' => tra('Scope'),
				'description' => tr('Display the geolocated items represented in the page (%0all%1, %0center%1, or
					%0custom%1 as a CSS selector). Default: %0center%1', '<code>', '</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => 'center',
			),
			'controls' => array(
				'required' => false,
				'name' => tra('Controls'),
				'description' => tr('Comma-separated list of map controls will be displayed on the map and around it'),
				'since' => '9.0',
				'filter' => 'word',
				'accepted' => 'controls, layers, search_location, levels, current_location, scale, streetview,
					navigation, coordinates, overview',
				'separator' => ',',
				'default' => wp_map_default_controls(),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of the map in pixels'),
				'since' => '1',
				'filter' => 'digits',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of the map in pixels'),
				'since' => '1',
				'filter' => 'digits',
			),
			'center' => array(
				'requied' => false,
				'name' => tra('Center'),
				'description' => tr('Format: %0x,y,zoom%1 where %0x%1 is the longitude, and %0y%1 is the latitude.
					%0zoom%1 is between %00%1 (view Earth) and %019%1.', '<code>', '</code>'),
				'since' => '9.0',
				'filter' => 'text',
			),
			'popupstyle' => array(
				'required' => false,
				'name' => tr('Popup Style'),
				'description' => tr('Alter the way the information is displayed when objects are loaded on the map.'),
				'since' => '10.0',
				'filter' => 'word',
				'default' => 'bubble',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tr('Bubble'), 'value' => 'bubble'),
					array('text' => tr('Dialog'), 'value' => 'dialog'),
				),
			),
			'mapfile' => array(
				'required' => false,
				'name' => tra('MapServer File'),
				'description' => tra('MapServer file identifier. Only fill this in if you are using MapServer.'),
				'since' => '1',
				'filter' => 'url',
				'advanced' => true,
			),
			'extents' => array(
				'required' => false,
				'name' => tra('Extents'),
				'description' => tra('Extents'),
				'since' => '1',
				'filter' => 'text',
				'advanced' => true,
			),
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of the map'),
				'since' => '1',
				'filter' => 'digits',
				'advanced' => true,
			),
			'tooltips' => array(
				'required' => false,
				'name' => tra('Tooltips'),
				'description' => tra('Show item name in a tooltip on hover'),
				'since' => '12.1',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_map($data, $params)
{
	global $tikilib, $prefs;

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

	if (! isset($params['popupstyle'])) {
		$params['popupstyle'] = 'bubble';
	}

	if (! empty($params['tooltips']) && $params['tooltips'] === 'y') {
		$tooltips = ' data-tooltips="1"';
	} else {
		$tooltips = '';
	}

	$popupStyle = smarty_modifier_escape($params['popupstyle']);

	$controls = array_intersect($params['controls'], wp_map_available_controls());
	$controls = array_intersect($params['controls'], wp_map_available_controls());
	$controls = implode(',', $controls);

	$center = null;
	$geolib = TikiLib::lib('geo');
	if (isset($params['center'])) {
		if ($coords = $geolib->parse_coordinates($params['center'])) {
			$center = ' data-geo-center="' . smarty_modifier_escape($geolib->build_location_string($coords)) . '" ';
		}
	} else {
		$center = $geolib->get_default_center();
	}

	TikiLib::lib('header')->add_map();
	$scope = smarty_modifier_escape(wp_map_getscope($params));

	$output = "<div class=\"map-container\" data-marker-filter=\"$scope\" data-map-controls=\"{$controls}\" data-popup-style=\"$popupStyle\" style=\"width: {$width}; height: {$height};\" $center{$tooltips}>";

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
			return '#col1 .geolocated';
		case 'all':
			return '.geolocated';
		default:
			return $scope;
	}
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
		'navigation',
		'coordinates',
		'overview',
	);
}

function wp_map_plugin_searchlayer($body, $args)
{
	$layer = $args->layer->text();
	$refresh = $args->refresh->int();
	$suffix = $args->suffix->word();
	$maxRecords = $args->maxRecords->digits();
	$sort_mode = $args->sort_mode->word();
	$load_delay = $args->load_delay->int();
	$popup_width = $args->popup_width->text();	// plain numeric xx for pixels or xx% for percentage (only on dialog popups)
	$popup_height = $args->popup_height->text();

	$args->replaceFilter('fields', 'word');
	$fields = $args->asArray('fields', ',');

	unset($args['layer']);
	unset($args['refresh']);
	unset($args['suffix']);
	unset($args['maxRecords']);
	unset($args['fields']);
	unset($args['sort_mode']);
	unset($args['load_delay']);
	unset($args['popup_width'], $args['popup_height']);

	$args->setDefaultFilter('text');

	TikiLib::lib('smarty')->loadPlugin('smarty_modifier_escape');

	$filters = '';
	foreach ($args as $key => $arg) {
		$filters .= '<input type="hidden" name="filter~' . $key . '" value="' . smarty_modifier_escape($arg) . '"/>';
	}

	if ($maxRecords) {
		$maxRecords = '<input type="hidden" name="maxRecords" value="' . intval($maxRecords) . '"/>';
	}

	if ($sort_mode) {
		$sort_mode = '<input type="hidden" name="sort_mode" value="' . $sort_mode . '"/>';
	}

	$fieldList = '';
	if (! empty($fields)) {
		$fieldList = '<input type="hidden" name="fields" value="' . smarty_modifier_escape(implode(',', $fields)) . '"/>';
	}

	$popup_config = array();
	if ($popup_width && preg_match('/\d+[%]?/', $popup_width)) {
		$popup_config['width'] = $popup_width;
	}
	if ($popup_height && preg_match('/\d+[%]?/', $popup_height)) {
		$popup_config['height'] = $popup_height;
	}
	if ($popup_config) {
		$popup_config = 'data-popup-config=\'' . json_encode($popup_config) . '\'';
	} else {
		$popup_config = '';
	}

	$escapedLayer = smarty_modifier_escape($layer);
	$escapedSuffix = smarty_modifier_escape($suffix);
	return <<<OUT
<form method="post" action="tiki-searchindex.php" class="search-box onload" style="display: none" data-result-refresh="$refresh" data-result-layer="$escapedLayer" data-result-suffix="$escapedSuffix" data-load-delay="$load_delay"{$popup_config}>
	<p>$maxRecords$sort_mode$fieldList$filters<input type="submit" class="btn btn-default btn-sm" /></p>

</form>
OUT;
}

function wp_map_plugin_colorpicker($body, $args)
{
	$headerlib = TikiLib::lib('header');
	static $counter = 0;

	$args->replaceFilter('colors', 'word');
	$colors = array_map('wp_map_color_filter', $args->asArray('colors', ','));

	if (count($colors)) {
		$size = '25px';
		$json = json_encode($colors);
		$methods = <<<METHOD
function setColor(color) {
	$(dialog).find('.current')
		.css('background', color);
	feature.attributes.color = color;
}
function init() {
	$(dialog)
		.dialog({
			autoOpen: false,
			width: 200,
			title: $(dialog).data('title'),
			close: function (e) {
				$.each(container.map.getControlsByClass('OpenLayers.Control.ModifyFeature'), function (k, control) {
					if (feature && control) {
						control.unselectFeature(feature);
					}
				});
				$.each(container.map.getControlsByClass('OpenLayers.Control.SelectFeature'), function (k, control) {
					if (feature && control) {
						control.unselect(feature);
					}
				});
			}
		})
		.append($('<div class="current" style="height: $size;"/>'));

	$.each($json, function (k, color) {
		$(dialog).append(
			$('<div style="float: left; width: $size; height: $size;"/>')
				.css('background', color)
				.click(function () {
					setColor(color);
					vlayer.redraw();
					if (feature.executor) {
						feature.executor();
					}
				})
		);
	});
}
METHOD;
	} else {
		$headerlib->add_jsfile('vendor/jquery/plugins/colorpicker/js/colorpicker.js');
		$headerlib->add_cssfile('vendor/jquery/plugins/colorpicker/css/colorpicker.css');
		$methods = <<<METHOD
function setColor(color) {
	$(dialog).ColorPickerSetColor(color);
}
function init() {
	$(dialog)
		.dialog({
			autoOpen: false,
			width: 400,
			title: $(dialog).data('title'),
			close: function (e) {
				$.each(container.map.getControlsByClass('OpenLayers.Control.ModifyFeature'), function (k, control) {
					if (feature && control) {
						control.unselectFeature(feature);
					}
				});
				$.each(container.map.getControlsByClass('OpenLayers.Control.SelectFeature'), function (k, control) {
					if (feature && control) {
						control.unselect(feature);
					}
				});
			}
		})
		.ColorPicker({
			flat: true,
			onChange: function (hsb, hex) {
				feature.attributes.color = '#' + hex;
				vlayer.redraw();
				if (feature.executor) {
					feature.executor();
				}
			}
		});
}
METHOD;
	}

	$target = 'map-colorpicker-' . ++$counter;

	$full = <<<FULL
$("#$target").closest('.map-container').bind('initialized', function () {
	var container = this
		, vlayer
		, feature
		, dialog = '#$target'
		, defaultRules
		;

	$methods

	vlayer = container.vectors;

	vlayer.events.on({
		featureselected: function (ev) {
			var active = false;

			feature = ev.feature;

			$.each(container.map.getControlsByClass('OpenLayers.Control.ModifyFeature'), function (k, control) {
				active = active || control.active;
				if (active) {
					control.selectFeature(feature);
				}
			});

			if (active && feature.attributes.intent !== 'marker') {
				setColor(feature.attributes.color);
				vlayer.redraw();
				$(dialog).dialog('open');
			}
		},
		featureunselected: function (ev) {
			feature = null;
			$(dialog).dialog('close');

			vlayer.styleMap = container.defaultStyleMap;
			$.each(container.map.getControlsByClass('OpenLayers.Control.ModifyFeature'), function (k, control) {
				if (ev.feature && control.active) {
					control.unselectFeature(ev.feature);
				}
			});
		},
		beforefeaturemodified: function (ev) {
			defaultRules = this.styleMap.styles["default"].rules;
			this.styleMap.styles["default"].rules = [];
		},
		afterfeaturemodified: function (ev) {
			this.styleMap.styles["default"].rules = defaultRules;
			this.redraw();
		}
	});

	init();
});
FULL;

	$headerlib->add_js($full);

	$title = tr('Color Picker');
	return "<div id=\"$target\" data-title=\"$title\"></div>";
}

function wp_map_color_filter ($color)
{
	$color = strtolower($color);
	if (preg_match('/^[0-9a-f]{3}([0-9a-f]{3})?$/', $color)) {
		return "#$color";
	} else {
		return $color;
	}
}
