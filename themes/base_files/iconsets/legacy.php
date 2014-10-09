<?php 
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//The default iconset associates icon names to icon fonts. It is used as the fallback for all other iconsets.


// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$iconset = array(
	'_settings' => array(
		'iconset_name' => tr('Legacy'),
		'iconset_description' => tr('Legacy (pre Tiki14) icons, mainly using famfamfam images'),
		'icon_path_image' => 'img/icons',
		'icon_tag' => 'img',
	),
    'comments' => array(
        'image_file_name' => 'comments.png',
    ),
    'delete' => array(
        'image_file_name' => 'cross.png',
    ),
    'edit' => array(
        'image_file_name' => 'page_edit.png',
    ),
	'export' => array( 
		'image_file_name' => 'disk.png',
	),
	'file-archive' => array( 
		'image_file_name' => 'folder.png',
	),
	'history' => array( 
		'image_file_name' => 'database.png',
	),
	'history' => array( 
		'image_file_name' => 'database.png',
	),
	'group' => array( 
		'image_file_name' => 'group.png',
	),
	'group-watch' => array( 
		'image_file_name' => 'eye_group.png',
	),
	'help' => array( 
		'image_file_name' => 'help.png',
	),
	'import' => array( 
		'image_file_name' => 'upload.png',
	),
    'permission' => array(
        'image_file_name' => 'key.png',
    ),
    'post' => array(
        'image_file_name' => 'pencil_add.png',
    ),
    'print' => array(
        'image_file_name' => 'printer.png',
    ),
    'rss' => array(
        'image_file_name' => 'feed.png',
    ),
	'settings' => array(
        'image_file_name' => 'wrench.png',
    ),
    'stop-watching' => array(
        'image_file_name' => 'no-eye.png',
    ),
    'trash' => array(
        'image_file_name' => 'bin.png',
    ),
    'watch' => array(
        'image_file_name' => 'eye.png',
    ),
);