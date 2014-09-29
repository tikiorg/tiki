<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_localfiles_info()
{
	return array(
		'name' => tra('Local Files'),
		'documentation' => 'PluginLocalFiles',
		'description' => tra('Displays links to local files or directories (in IE only).'),
		'prefs' => array('wikiplugin_localfiles'),
		'icon' => 'img/icons/mime/default.png',
		'tags' => array( 'experimental' ),
		'format' => 'html',
		'validate' => 'all',
		'params' => array(
			'path' => array(
				'required' => false,
				'name' => tra('Path'),
				'description' => tra('Local file or directory path'),
				'default' => '',
				'filter' => 'text',
				//'separator' => ',',	TODO?
			),
			'list' => array(
				'required' => false,
				'name' => tra('List Directory'),
				'description' => tra('If the path above is a directory then list the contents.'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'icons' => array(
				'required' => false,
				'name' => tra('Show Icons'),
				'description' => tra('Show mime-type icons.'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
} // wikiplugin_localfiles_info()


function wikiplugin_localfiles($data, $params)
{
	// TODO refactor: defaults for plugins?
	$defaults = array();
	$plugininfo = wikiplugin_localfiles_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults[$key] = $param['default'];
	}
	$params = array_merge($defaults, $params);
	$files = array();
	if (!is_array( $params['path'])) {
		if ($params['list'] === 'y' && file_exists($params['path']) && is_dir($params['path'])) {
			$params['path'] = scandir(dir($params['path']));
		} else {
			$params['path'] =  array($params['path']);
		}
	}
	foreach($params['path'] as $path) {
		$info = pathinfo($path);
		if (!$info || $info['basename'] === $path) {	// windows file but non-windows server
			preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$path,$m);
			if($m[1]) $info['dirname'] = $m[1];
			if($m[2]) $info['basename'] = $m[2];
			if($m[5]) $info['extension'] = $m[5];
			if($m[3]) $info['filename'] = $m[3];
			// thanks http://www.php.net/manual/en/function.pathinfo.php#107461
		}
		if ($params['icons'] === 'y') {
			$icon = "img/icons/mime/{$info['extension']}.png";
			if (!file_exists($icon)) {
				$icon = "img/icons/mime/default.png";
			}
		} else {
			$icon = '';
		}

		$files[] = array(
			'path' => $path,
			'name' => $info['basename'],
			'icon' => $icon,
		);
	}

	$smartylib = TikiLib::lib('smarty');
	$smartylib->assign('files', $files);
	$smartylib->assign('isIE', strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false);

	return $smartylib->fetch('wiki-plugins/wikiplugin_localfiles.tpl');
}
