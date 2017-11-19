<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_wysiwyg_info()
{
	global $prefs;

	return [
		'name' => 'WYSIWYG',
		'documentation' => 'PluginWYSIWYG',
		'description' => tra('Use a WYSIWYG editor to edit a section of content'),
		'prefs' => ['wikiplugin_wysiwyg'],
		'iconname' => 'wysiwyg',
		'introduced' => 9,
		'tags' => [ 'experimental' ], // Several important bugs, notably serious #6476. Most bugs are probably not specific to the WYSIWYG *plugin*. Chealer 2017-11-02
		'filter' => 'purifier',			/* N.B. uses htmlpurifier to ensure only "clean" html gets in */
		'format' => 'html',
		'body' => tra('Content'),
		'extraparams' => true,
		'params' => [
			'width' => [
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Minimum width for DIV. Default:') . ' <code>500px</code>',
				'since' => '9.0',
				'filter' => 'text',
				'default' => '500px',
			],
			'height' => [
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Minimum height for DIV. Default:') . ' <code>300px</code>',
				'since' => '9.0',
				'filter' => 'text',
				'default' => '300px',
			],
			'use_html' => [
				'required' => false,
				'name' => tra('Use HTML'),
				'description' => tr('Override the %0wysiwyg_htmltowiki%1 preference if needed. Defaults to No (%0n%1)
					if wysiwyg_htmltowiki is set to Yes (%0y%1).', '<code>', '</code>'),
				'since' => '14.1',
				'filter' => 'alpha',
				'default' => $prefs['wysiwyg_htmltowiki'] == 'y' ? 'n' : 'y',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				]
			],
		],
	];
} // wikiplugin_wysiwyg_info()


function wikiplugin_wysiwyg($data, $params)
{
	// TODO refactor: defaults for plugins?
	$defaults = [];
	$plugininfo = wikiplugin_wysiwyg_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	global $tiki_p_edit, $page, $prefs, $user;
	static $execution = 0;

	global $wikiplugin_included_page;
	if (! empty($wikiplugin_included_page)) {
		$sourcepage = $wikiplugin_included_page;
	} else {
		$sourcepage = $page;
	}

	$contentIsHTML = ! ($params['use_html'] !== 'y');
	$html = TikiLib::lib('edit')->parseToWysiwyg($data, true, $contentIsHTML, ['page' => $sourcepage]);

	if (TikiLib::lib('tiki')->user_has_perm_on_object($user, $sourcepage, 'wiki page', 'tiki_p_edit')) {
		$class = "wp_wysiwyg";
		$exec_key = $class . '_' . ++ $execution;
		$style = " style='min-width:{$params['width']};min-height:{$params['height']}'";

		$params['section'] = empty($params['section']) ? 'wysiwyg_plugin' : $params['section'];
		$params['_wysiwyg'] = 'y';
		$params['is_html'] = $contentIsHTML;
		$params['_is_html'] = $contentIsHTML;    // needed for toolbars
		//$params['comments'] = true;
		$ckoption = TikiLib::lib('wysiwyg')->setUpEditor($contentIsHTML, $exec_key, $params, '');

		if ($prefs['namespace_enabled'] == 'y' && $prefs['namespace_force_links'] == 'y') {
			$namespace = TikiLib::lib('wiki')->get_namespace($sourcepage);
			if ($namespace) {
				$namespace .= $prefs['namespace_separator'];
			}
		} else {
			$namespace = '';
		}
		$namespace = htmlspecialchars($namespace);

		$access = TikiLib::lib('access');
		$access->checkAuthenticity();

		$html = "<div id='$exec_key' class='{$class}'$style data-initial='$namespace' data-html='{$params['use_html']}' data-ticket='" . $access->getTicket() . "'>" . $html . '</div>';

		$js = '$("#' . $exec_key . '").wysiwygPlugin("' . $execution . '", "' . $sourcepage . '", ' . $ckoption . ');';

		TikiLib::lib('header')
			->add_jsfile('lib/ckeditor_tiki/tiki-ckeditor.js')
			->add_jq_onready($js);
	}
	return $html;
}
