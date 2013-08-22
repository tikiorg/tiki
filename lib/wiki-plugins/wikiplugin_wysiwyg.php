<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_wysiwyg_info()
{
	return array(
		'name' => 'WYSIWYG',
		'documentation' => 'PluginWYSIWYG',
		'description' => tra('Permits to have a WYSIWYG section for part of a page.'),
		'prefs' => array('wikiplugin_wysiwyg'),
		'icon' => 'img/icons/mime/default.png',
		'tags' => array( 'experimental' ),
		'filter' => 'purifier',			/* N.B. uses htmlpurifier to ensure only "clean" html gets in */
		'format' => 'html',
		'body' => tra('Content'),
		'extraparams' => true,
		'params' => array(
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Minimum width for DIV. Default:500px'),
				'filter' => 'text',
				'default' => '500px',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Minimum height for DIV. Default:300px.'),
				'filter' => 'text',
				'default' => '300px',
			),
		),
	);
} // wikiplugin_wysiwyg_info()


function wikiplugin_wysiwyg($data, $params)
{
	// TODO refactor: defaults for plugins?
	$defaults = array();
	$plugininfo = wikiplugin_wysiwyg_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	$html = TikiLib::lib('tiki')->parse_data($data, array('is_html' => true));

	global $tiki_p_edit, $page, $prefs;
	static $execution = 0;

	if ($tiki_p_edit === 'y') {
		$class = "wp_wysiwyg";
		$exec_key = $class . '_' . ++ $execution;
		$style = " style='min-width:{$params['width']};min-height:{$params['height']}'";

		$params['section'] = empty($params['section']) ? 'wysiwyg_plugin' : $params['section'];
		$params['_wysiwyg'] = 'y';
		$params['is_html'] = true;
		//$params['comments'] = true;
		$ckoption = TikiLib::lib('wysiwyg')->setUpEditor(true, $exec_key, $params, '', false);

		$html = "<div id='$exec_key' class='{$class}'$style>" . $html . '</div>';

		$js = '$("#' . $exec_key . '").wysiwygPlugin("' . $execution . '", "' . $page . '", ' . $ckoption . ')';

		TikiLib::lib('header')
			->add_jsfile('lib/ckeditor_tiki/tiki-ckeditor.js')
			->add_jq_onready($js);
	}
	return $html;

}

