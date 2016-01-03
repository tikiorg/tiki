<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_appframe_info()
{
	return array(
		'name' => tra('Application Frame'),
		'description' => tra('Create a frame in which to assemble custom applications'),
		'prefs' => array('wikiplugin_appframe'),
		'format' => 'html',
		'documentation' => 'PluginAppFrame',
		'iconname' => 'merge',
		'filter' => 'wikicontent',
		'introduced' => 9,
		'body' => tr('Application layout'),
		'params' => array(
			'min' => array(
				'required' => false,
				'name' => tr('Minimal height'),
				'description' => tr('Prevent the frame from becoming any shorter than the specified size.'),
				'default' => 300,
				'filter' => 'int',
				'since' => '9.0',
			),
			'max' => array(
				'required' => false,
				'name' => tr('Maximal height'),
				'description' => tr('Prevent the frame from becoming any higher than the specified size.'),
				'default' => -1,
				'filter' => 'int',
				'since' => '10.0',
			),
			'hideleft' => array(
				'requred' => false,
				'name' => tr('Hide left column'),
				'description' => tr('Hide the left column when the application frame is in use to provide more space to the application.'),
				'default' => 'n',
				'since' => '9.0',
				'options' => array(
					array('value' => 'n', 'text' => tr('No')),
					array('value' => 'y', 'text' => tr('Yes')),
				),
			),
			'hideright' => array(
				'requred' => false,
				'name' => tr('Hide right column'),
				'description' => tr('Hide the right column when the application frame is in use to provide more space to the application.'),
				'default' => 'n',
				'since' => '9.0',
				'options' => array(
					array('value' => 'n', 'text' => tr('No')),
					array('value' => 'y', 'text' => tr('Yes')),
				),
			),
			'fullpage' => array(
				'required' => false,
				'name' => tr('Full page'),
				'description' => tr('Occupy the complete content area of the page.'),
				'default' => 'n',
				'since' => '9.0',
				'options' => array(
					array('value' => 'n', 'text' => tr('No')),
					array('value' => 'y', 'text' => tr('Yes')),
				),
			),
			'absolute' => array(
				'required' => false,
				'name' => tr('Absolute Position'),
				'description' => tr('Position the app frame to use absolute position and really use all available space.'),
				'default' => 'n',
				'since' => '9.0',
				'options' => array(
					array('value' => 'n', 'text' => tr('No')),
					array('value' => 'y', 'text' => tr('Yes')),
				),
			),
			'top' => array(
				'required' => false,
				'name' => tr('Top'),
				'description' => tr('When using absolute mode, leave some space for the header at the top.'),
				'default' => 0,
				'filter' => 'int',
				'since' => '9.0',
			),
			'fullscreen' => array(
				'required' => false,
				'name' => tr('Full screen'),
				'description' => tr('Occupy the complete page.'),
				'default' => 'n',
				'since' => '10.0',
				'options' => array(
					array('value' => 'n', 'text' => tr('No')),
					array('value' => 'y', 'text' => tr('Yes')),
				),
			),
		),
	);
}

function wikiplugin_appframe($data, $params)
{
	$minHeight = isset($params['min']) ? (int) $params['min'] : 300;
	$maxHeight = isset($params['max']) ? (int) $params['max'] : -1;
	$fullPage = 0;
	if (isset($params['fullpage']) && $params['fullpage'] == 'y') {
		$fullPage = 1;
	}
	$fullscreen = 0;
	if (isset($params['fullscreen']) && $params['fullscreen'] == 'y') {
		$fullscreen = 1;
	}

	$absolute = intval(isset($params['absolute']) ? $params['absolute'] == 'y' : false);
	$top = isset($params['top']) ? $params['top'] : 0;

	$headerlib = TikiLib::lib('header');

	if (isset($params['hideleft']) && $params['hideleft'] == 'y') {
		$headerlib->add_js(
<<<JS
hideCol('col2','left', 'col1');
JS
);
	}

	if (isset($params['hideright']) && $params['hideright'] == 'y') {
		$headerlib->add_js(
<<<JS
hideCol('col3','right', 'col1');
JS
);
	}

	$headerlib->add_js(
<<<JS
$(window).resize(function () {
	var viewportHeight = $(window).height(), appframe = $('#appframe'), footerSize, centerHeader, surplus, target;

	if ($absolute) {
		$('#appframe')
			.css('position', 'absolute')
			.css('top', $top)
			.css('left', 0)
			.css('bottom', 0)
			.css('right', 0)
			;
	} else {
		appframe.height(0);

		centerHeader = $('#appframe').position().top - $('#tiki-center').position().top;
		surplus = $('#show-errors-button').height();
		footerSize = $('#footer').height() + $('#tiki-center').height() - centerHeader + surplus;
		target = viewportHeight - appframe.position().top - footerSize;

		var min = $minHeight;
		if (target < min) {
			target = min;
		}

		var max = $maxHeight;
		if ((max != -1) && (target > max)) {
			target = max;
		}

		appframe.height(target);
	}

	$('#appframe .tab').each(function () {
		$(this).data('available-height', $('#appframe').height() - $(this).position().top).addClass('height-size');
	});

	$('#appframe .anchor-container')
		.css('z-index', 100000)
		.css('position', 'absolute')
		.css('top', 150)
		.css('right', 0)
		;
});
$('#appframe .tab').parent().each(function () {
	var tabs = $(this).children('.tab').wrapAll('<div class="tabs" style="height: 100%;"/>');
	var list = $('<ul/>');
	tabs.parent().prepend(list);
	tabs.each(function () {
		var link = $('<a/>').attr('href', '#' + $(this).attr('id')).text($(this).data('label'));
		list.append($('<li/>').append(link));
	});
	tabs.parent().tabs();
});
$('#appframe .accordion').parent().each(function () {
	$('.accordion', this).wrapAll('<div/>').parent().accordion({
		heightStyle: "content"
	});
});
$('#appframe .anchor').wrapAll('<div/>').parent()
	.addClass('anchor-container')
	;

$('#appframe .anchor').each(function () {
	var anchor = this;
	$('.anchor-head, .anchor-content', anchor)
		.css('text-align', 'right')
		;

	$('.anchor-toggle', anchor).click(function () {
		$('.anchor-head .label', anchor).toggle('fast');
		$('.anchor-content', anchor).toggle('fast');
		return false;
	});

	if (location.hash == "#" + $("img", anchor).attr("alt")) {
		setTimeout( function() { $('.anchor-toggle', anchor).click(); }, 2000);
	}
});

if ($fullPage) {
	$('#role_main').append($('#appframe'));
	$('#role_main').children().not($('#appframe')).remove();
}

if ($fullscreen) {
	$('.header_outer').hide();
	$('#topbar_modules').hide();
	$('#footer').hide();
	$('#error_report').hide();
	$('.share').hide();
	$('.tellafriend').hide();
}

$(window).resize();
JS
);

	$matches = WikiParser_PluginMatcher::match($data);
	foreach ($matches as $plugin) {
		if ($output = wikiplugin_appframe_execute($plugin)) {
			$plugin->replaceWith($output);
		}
	}

	$data = $matches->getText();

	return <<<HTML
<div id="appframe">$data</div>
HTML;
}

function wikiplugin_appframe_execute($plugin)
{
	$name = $plugin->getName();
	$body = $plugin->getBody();
	$argumentParger = new WikiParser_PluginArgumentParser();
	$params = $argumentParger->parse($plugin->getArguments());

	if (! in_array($name, array('tab', 'column', 'page', 'module', 'cond', 'anchor', 'overlay', 'template', 'hidden', 'mapcontrol'))) {
		return null;
	}

	$function = 'wikiplugin_appframe_' . $name;
	return $function($body, new JitFilter($params), $plugin->getStart());
}

function wikiplugin_appframe_tab($data, $params, $start)
{
	return <<<TAB
<div id="apptab-$start" class="tab" data-label="{$params->label->text()}" style="height: 100%;">$data</div>
TAB;
}

function wikiplugin_appframe_anchor($data, $params, $start)
{
	return <<<TAB
<div id="appanchor-$start" class="anchor">
	<h3 class="anchor-head">
		<a class="anchor-toggle" href="#"><img src="{$params->icon->text()}" alt="{$params->label->text()}"/></a>
		<span class="label" style="display: none;">{$params->label->text()}</span>
	</h3>
	<div class="anchor-content" style="display: none;">
		<div style="text-align: left;">$data</div>
	</div>
</div>
TAB;
}
function wikiplugin_appframe_column($data, $params, $start)
{
	$width = $params->width->int() . '%';
	return <<<COLUMN
<div style="width: {$width}; float: left; height: 100%;">$data</div>
COLUMN;
}

function wikiplugin_appframe_page($data, $params, $start)
{
	$tikilib = TikiLib::lib('tiki');
	$info = $tikilib->get_page_info($params->name->pagename());

	if (! $info) {
		return null;
	}

	$perms = Perms::get('wiki page', $info['pageName']);

	if (! $perms->view) {
		return null;
	}

	$keys = array();
	$replacements = array();
	foreach ($params as $key => $value) {
		$keys[] = "{{{$key}}}";
		$replacements[] = $value;
	}
	$info['data'] = str_replace($keys, $replacements, $info['data']);

	return "~/np~{$info['data']}~np~";
}

function wikiplugin_appframe_module($data, $params, $start)
{
	$modlib = TikiLib::lib('mod');
	$moduleName = $params->name->word();
	$label = $params->label->text();

	if (! $label) {
		$info = $modlib->get_module_info($moduleName);

		if (! $info ) {
			return null;
		}

		$label = $info['name'];
	}

	$data = $modlib->execute_module(
		array(
			'name' => $moduleName,
			'params' => array_merge($params->none(), array('nobox' => 'y', 'notitle' => 'y')),
		)
	);

	if (! $data) {
		return null;
	}

	$class = null;
	if ($params->accordion->int()) {
		$class = ' class="accordion"';
	}

	return <<<MODULE
<h4$class>{$label}</h4>
<div$class>
	$data
</div>
MODULE;
}

function wikiplugin_appframe_cond($data, $params, $start)
{
	if (isset($params['notempty']) && $params->notempty->text()) {
		return $data;
	}

	if (isset($params['empty']) && ! $params->{'empty'}->text()) {
		return $data;
	}

	return ' ';
}

function wikiplugin_appframe_overlay($data, $params, $start)
{
	$position = array();

	foreach (array('top', 'bottom', 'left', 'right') as $pos) {
		if (isset($params[$pos])) {
			$value = $params->$pos->int();
			$position[] = "$pos: {$value}px;";
		}
	}

	$position = implode(' ', $position);

	return <<<OVERLAY
<div class="overlay {$params->class->word()}" style="position: absolute; z-index: 999; $position">
	$data
</div>
OVERLAY;
}

function wikiplugin_appframe_hidden($data, $params, $start)
{
	return <<<OVERLAY
<div style="display: none;">
	$data
</div>
OVERLAY;
}

function wikiplugin_appframe_template($data, $params, $start)
{
	$smarty = TikiLib::lib('smarty');
	$file = $params->file->url();

	try {
		$data = array_map(
			function ($value)
			{
				return preg_replace('/\{\{\w+\}\}/', '', $value);
			},
			$params->text()
		);

		$smarty->assign('input', $data);
		return $smarty->fetch($file);
	} catch (SmartyException $e) {
		return tr('Template file not found: %0', $file);
	}
}

function wikiplugin_appframe_mapcontrol($data, $params, $start)
{
	static $counter = 0;
	$function = null;
	$control = null;
	$label = null;
	$mode = null;

	switch ($name = $params->type->word()) {
	case 'pan_zoom':
		$label = tr('Pan/Zoom');
		$mode = tr('Default');
		break;
	case 'mode_enable':
		$mode = $params->mode->text();
		$label = $mode;

		if (! $mode) {
			return false;
		}
		break;
	case 'select_feature':
		$control = 'new OpenLayers.Control.SelectFeature(vlayer)';
		$label = tr('Select');
		break;
	case 'modify_feature':
		$control = 'new OpenLayers.Control.ModifyFeature(vlayer, {
			mode: OpenLayers.Control.ModifyFeature.DRAG | OpenLayers.Control.ModifyFeature.RESHAPE,
			standalone: true,
			virtualStyle: drawStyle,
			vertexRenderIntent: "vertex"
		}), new OpenLayers.Control.SelectFeature(vlayer)';
		$label = tr('Select/Modify');
		break;
	case 'draw_polygon':
		$control = 'new OpenLayers.Control.DrawFeature(vlayer, OpenLayers.Handler.Polygon, {handlerOptions:{style:drawStyle}})';
		$label = tr('Draw Polygon');
		break;
	case 'draw_path':
		$control = 'new OpenLayers.Control.DrawFeature(vlayer, OpenLayers.Handler.Path, {handlerOptions:{style:drawStyle}})';
		$label = tr('Draw Path');
		break;
	case 'reset_zoom':
		$function = 'container.resetPosition();';
		$label = tr('Reset Zoom');
		break;
	default:
		return false;
	}

	if (! $icon = $params->icon->url()) {
		$icon = 'mapcontrol_' . $name;
	}

	if ($specifiedLabel = $params->label->text()) {
		$label = $specifiedLabel;
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign(
		'mapcontrol', array(
			'id' => 'mapcontrol-' . ++$counter,
			'control' => $control,
			'icon' => $icon,
			'label' => $label,
			'mode' => $mode,
			'function' => $function,
			'navigation' => $params->navigation->int(),
			'class' => $params->class->text() ? $params->class->text() : 'icon',
		)
	);
	return $smarty->fetch('wiki-plugins/wikiplugin_appframe_mapcontrol.tpl');
}
