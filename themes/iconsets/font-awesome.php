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
		'iconset_name' => tr('Font-awesome'),
		'iconset_description' => tr('Font-awesome focused iconset, see http://fortawesome.github.io/Font-Awesome/icons/'),
		'icon_tag' => 'i', 
	),
    'comments' => array(
        'class' => 'fa fa-comments-o',
    ),
    'edit' => array(
        'class' => 'fa fa-edit',
    ),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o',
	),
    'post' => array(
        'class' => 'fa fa-pencil',
    ),
    'print' => array(
        'class' => 'fa fa-print',
    ),
    'rss' => array(
        'class' => 'fa fa-rss-square',
    ),
    'stop-watching' => array(
        'class' => 'fa fa-eye-slash',
    ),
    'trash' => array(
        'class' => 'fa fa-trash-o',
    ),
    'watch' => array(
        'class' => 'fa fa-eye-o',
    ),
);