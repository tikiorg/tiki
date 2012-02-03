<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_html_info()
{
	return array(
		'name' => tra('HTML'),
		'documentation' => 'PluginHTML',
		'description' => tra('Add HTML to a page'),
		'prefs' => array('wikiplugin_html'),
		'body' => tra('HTML code'),
		'validate' => 'all',
		'filter' => 'rawhtml_unsafe',
		'icon' => 'pics/icons/mime/html.png',
		'tags' => array( 'basic' ),	
		'params' => array(
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Syntax'),
				'description' => tra('Parse wiki syntax within the HTML code.'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('No'), 'value' => 0),
					array('text' => tra('Yes'), 'value' => 1),
				),
				'filter' => 'int',
				'default' => '0',
			),
			'inPageWysiwyg' => array(
				'required' => false,
				'name' => tra('WYSIWYG Editing'),
				'description' => tra('Experimental: In-page WYSIWYG editing.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => 0),
					array('text' => tra('Yes'), 'value' => 1),
				),
				'filter' => 'int',
				'default' => '0',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width for (used for WYSIWYG editing).'),
				'filter' => 'text',
				'default' => '400px',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height for (used for WYSIWYG editing).'),
				'filter' => 'text',
				'default' => '300px',
			),
		),
	);
}

function wikiplugin_html($data, $params)
{
	// TODO refactor: defaults for plugins?
	$defaults = array();
	$plugininfo = wikiplugin_html_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	// strip out sanitisation which may have occurred when using nested plugins
	$html = str_replace('<x>', '', $data);

	// parse using is_html if wiki param set, or just decode html entities
	if ( isset($params['wiki']) && $params['wiki'] === 1 ) {
		$html = TikiLib::lib('tiki')->parse_data($html, array('is_html' => true));
	} else {
		$html  = html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
	}
	global $tiki_p_edit;
	if (!empty($params['inPageWysiwyg'])) {
		static $execution = 0;
		$exec_key = 'html-execution-' . ++ $execution;
		$style = " style='width:{$params['width']};height:{$params['height']}'";

		$html = "<div id='#$exec_key' class='wikiplugin_html'$style>" . $html . '</div>';

		if ($tiki_p_edit === 'y') {
			$js = '

$(".wikiplugin_html").each(function(){
	var wp_bgcol = $(this).css("background-color");
	$(this).mouseover(function(){
		$(this).css({
			backgroundColor: "#ddd",
			cusor: "crosshair"
		});

	}).mouseout(function(){
		$(this).css({
			backgroundColor: wp_bgcol,
			cusor: "inherit"
		});
	}).click(function(){
		var $this = $(this);
		// TODO set modal somehow
		//$("body *:not(#" + $(this).attr("id") + ")").css({backgroundColor: "#ddd"});

		$this.ckeditor(function(){
			$("body *:not(#" + $(this).attr("id") + ")")
				.bind("click.ws_html", function(){
					var editor = CKEDITOR.instances[$this.attr("id")];
					if(typeof editor !== "undefined" && editor.checkDirty() ) {
						alert("Save not implemented yet");
					}
					editor.destroy();
					//$("body *:not(#" + $(this).attr("id") + ")").css({backgroundColor: ""});
					$("body *")
									.unbind("click.ws_html");
					return false;
				});

		});
	});
});
';
			// TODO refactor: again
			global $tikiroot;
			TikiLib::lib('header')
					->add_jq_onready($js)
					->add_js_config('window.CKEDITOR_BASEPATH = "'. $tikiroot . 'lib/ckeditor/";')
					->add_jsfile('lib/ckeditor/ckeditor.js', 0, true)
					->add_jsfile('lib/ckeditor/adapters/jquery.js', 0, true);

		}
		return '~np~' . $html . '~/np~';
	} else {
		return '~np~' . $html . '~/np~';
	}
}
