<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_addfreetag.php 36968 2011-09-05 22:51:47Z nkoth $

function wikiplugin_appframe_info()
{
	return array(
		'name' => tra('Application Frame'),
		'description' => tra('Creates a frame to assemble custom applications in. Components in the frame will be various wiki pages and modules.'),
		'prefs' => array('wikiplugin_appframe'),
		'format' => 'html',
		'introduced' => 9,
		'documentation' => 'PluginAppFrame',
		'filter' => 'wikicontent',
		'params' => array(
			'min' => array(
				'required' => false,
				'name' => tr('Minimal height'),
				'description' => tr('Prevent the frame from becoming any shorter than the specified size.'),
				'default' => 300,
				'filter' => 'int',
			),
		),
	);
}

function wikiplugin_appframe($data, $params)
{
	$minHeight = isset($params['min']) ? (int) $params['min'] : 300;

	$headerlib = TikiLib::lib('header');

	$headerlib->add_js(<<<JS
$(window).resize(function () {
	var viewportHeight = $(window).height(), appframe = $('#appframe'), footerSize, centerHeader, surplus, target;
	appframe.height(0);

	centerHeader = $('#appframe').position().top - $('#tiki-center').position().top;
	surplus = $('#show-errors-button').height();
	footerSize = $('#footer').height() + $('#tiki-center').height() - centerHeader + surplus;
	target = viewportHeight - appframe.position().top - footerSize;

	var min = $minHeight;
	if (target < min) {
		target = min;
	}

	appframe.height(target);
	$('#appframe .tab').each(function () {
		$(this).data('available-height', $('#appframe').height() - $(this).position().top).addClass('height-size');
	});
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
$('#appframe .accordion').wrapAll('<div/>').parent().accordion({
	autoHeight: false
});

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
	$params = WikiParser_PluginArgumentParser::parse($plugin->getArguments());

	if (! in_array($name, array('tab', 'column', 'page', 'module'))) {
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

	$data = $modlib->execute_module(array(
		'name' => $moduleName,
		'params' => array(
			'nobox' => 'y',
			'notitle' => 'y',
		),
	));

	if (! $data) {
		return null;
	}

	return <<<MODULE
<h4 class="accordion">{$label}</h4>
<div class="accordion">
	$data
</div>
MODULE;
}

