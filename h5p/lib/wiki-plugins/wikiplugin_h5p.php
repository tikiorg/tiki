<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_googleanalytics.php 57962 2016-03-17 20:02:39Z jonnybradley $

function wikiplugin_h5p_info()
{
	return array(
		'name' => tra('H5P'),
		'documentation' => 'PluginH5P',
		'description' => tra(''),
		'prefs' => array( 'wikiplugin_h5p' ),
		'iconname' => 'html',
		'format' => 'html',
		'introduced' => 16,
		'params' => array(
			'fileId' => array(
				'required' => true,
				'name' => tra('File ID'),
				'description' => tr('The H5P file in a file gallery'),
				'since' => '17.0',
				'filter' => 'text',
				'default' => ''
			),
		),
	);
}

function wikiplugin_h5p($data, $params)
{

	$ret = '';

	$h5p = new H5PTiki();


	return $ret;
}

