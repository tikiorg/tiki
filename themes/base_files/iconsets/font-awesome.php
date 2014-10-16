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
		'class' => 'fa fa-play-circle fa-fw',
	),
	'add' => array( 
		'class' => 'fa fa-plus fa-fw',
	),
	'comments' => array(
		'class' => 'fa fa-comments-o fa-fw',
	),
	'create' => array( 
		'class' => 'fa fa-plus fa-fw',
	),
	'delete' => array(
		'class' => 'fa fa-times fa-fw',
	),
	'edit' => array(
		'class' => 'fa fa-edit fa-fw',
	),
	'error' => array(
		'class' => 'fa fa-exclamation-circle fa-fw',
	),
	'export' => array( 
		'class' => 'fa fa-upload fa-fw',
	),
	'external-link' => array(
		'class' => 'fa fa-external-link fa-fw',
	),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o fa-fw',
	),
	'group' => array( 
		'class' => 'fa fa-group fa-fw',
	),
	'group-watch' => array( 
		'class' => 'fa fa-group fa-fw',
	),
	'help' => array( 
		'class' => 'fa fa-question-circle fa-fw',
	),
	'history' => array( 
		'class' => 'fa fa-history fa-fw',
	),
	'import' => array( 
		'class' => 'fa fa-download fa-fw',
	),
	'info' => array( 
		'class' => 'fa fa-info-circle fa-fw',
	),
	'menu' => array(
		'class' => 'fa fa-bars fa-fw',
	),
	'menuitem' => array(
		'class' => 'fa fa-minus fa-fw',
	),
	'notification' => array(
		'class' => 'fa fa-bell-o fa-fw',
	),
	'permission' => array(
		'class' => 'fa fa-key fa-fw',
	),
	'post' => array(
		'class' => 'fa fa-pencil fa-fw',
	),
	'print' => array(
		'class' => 'fa fa-print fa-fw',
	),
	'refresh' => array(
		'class' => 'fa fa-refresh fa-fw',
	),
	'remove' => array(
		'class' => 'fa fa-times fa-fw',
	),
	'rss' => array(
		'class' => 'fa fa-rss fa-fw',
	),
	'settings' => array( 
		'class' => 'fa fa-wrench fa-fw',
	),
	'stop-watching' => array(
		'class' => 'fa fa-eye-slash fa-fw',
	),
	'success' => array(
		'class' => 'fa fa-check-circle fa-fw',
	),
	'tag' => array(
		'class' => 'fa fa-tag fa-fw',
	),
	'trash' => array(
		'class' => 'fa fa-trash-o fa-fw',
	),
	'view' => array(
		'class' => 'fa fa-search-plus fa-fw',
	),
	'warning' => array(
		'class' => 'fa fa-exclamation-triangle fa-fw',
	),
	'watch' => array(
		'class' => 'fa fa-eye fa-fw',
	),
	'watch-group' => array(
		'class' => 'fa fa-eye fa-fw',
	),
);