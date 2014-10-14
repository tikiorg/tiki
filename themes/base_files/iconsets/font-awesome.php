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
	'actions' => array( 
		'class' => 'fa fa-play-circle',
	),
	'add' => array( 
		'class' => 'fa fa-plus',
	),
    'comments' => array(
        'class' => 'fa fa-comments-o',
    ),
    'delete' => array(
        'class' => 'fa fa-times',
    ),
    'edit' => array(
        'class' => 'fa fa-edit',
    ),
	'error' => array(
        'class' => 'fa fa-exclamation-circle',
    ),
	'export' => array( 
		'class' => 'fa fa-upload',
	),
    'external-link' => array(
        'class' => 'fa fa-external-link',
    ),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o',
	),
	'group' => array( 
		'class' => 'fa fa-group',
	),
	'group-watch' => array( 
		'class' => 'fa fa-group',
	),
	'help' => array( 
		'class' => 'fa fa-question-circle',
	),
	'history' => array( 
		'class' => 'fa fa-history',
	),
	'import' => array( 
		'class' => 'fa fa-download',
	),
	'info' => array( 
		'class' => 'fa fa-info-circle',
	),
    'permission' => array(
        'class' => 'fa fa-key',
    ),
    'post' => array(
        'class' => 'fa fa-pencil',
    ),
    'print' => array(
        'class' => 'fa fa-print',
    ),
    'refresh' => array(
        'class' => 'fa fa-refresh',
    ),
    'remove' => array(
        'class' => 'fa fa-times',
    ),
    'rss' => array(
        'class' => 'fa fa-rss',
    ),
	'settings' => array( 
		'class' => 'fa fa-wrench',
	),
    'stop-watching' => array(
        'class' => 'fa fa-eye-slash',
    ),
	'success' => array(
        'class' => 'fa fa-check-circle',
    ),
    'tag' => array(
        'class' => 'fa fa-tag',
    ),
    'trash' => array(
        'class' => 'fa fa-trash-o',
    ),
	'view' => array(
        'class' => 'fa fa-search-plus',
    ),
	'warning' => array(
        'class' => 'fa fa-exclamation-triangle',
    ),
    'watch' => array(
        'class' => 'fa fa-eye',
    ),
    'watch-group' => array(
        'class' => 'fa fa-eye',
    ),
);