<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_file.php 25177 2010-02-13 17:34:48Z changi67 $

function wikiplugin_filelink_info()
{
	return array(
		'name' => tra( 'File link' ),
//		'documentation' => 'PluginFileLink',
		'description' => tra("Displays a link to download a file from a file gallery."),
		'prefs' => array( 'feature_file_galleries', 'wikiplugin_filelink' ),
		'inline' => true,
		'icon' => 'pics/large/file-manager.png',
		'params' => array(
			'fileId' => array(
				'required' => true,
				'name' => tra('File'),
				'type' => 'fileId',
				'area' => 'fgal_picker_id',
				'description' => tra('Integer identifying a file in the file galleries'),
				'filter' => 'digits',
			),
 			'label' => array(
				'required' => true,
				'name' => tra('Label'),
				'description' => tra('Text showing on the link'),
			),
		),
	);
}

function wikiplugin_filelink( $data, $params )
{
	return "[tiki-download_file.php?fileId=" . $params['fileId'] . "|" . $params['label'] . "]";
}
