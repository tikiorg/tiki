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
		'iconset_name' => tr('Glyphicons'),
		'iconset_description' => tr('Glyphicon focused iconset, see http://getbootstrap.com/components/'),
		'icon_tag' => 'span',
	),
	'file-archive' => array( 
		'class' => 'glyphicon glyphicon-floppy-save',
	),
	'trash' => array( 
		'class' => 'glyphicon glyphicon-trash',
	),
    'print' => array(
        'class' => 'glyphicon glyphicon-print',
    ),
);