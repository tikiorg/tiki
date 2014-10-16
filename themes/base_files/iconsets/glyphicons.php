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
	'actions' => array(
		'class' => 'glyphicon glyphicon-play-circle',
	),
	'add' => array(
		'class' => 'glyphicon glyphicon-plus',
	),
	'comments' => array(
		'class' => 'glyphicon glyphicon-comment',
	),
	'create' => array(
		'class' => 'glyphicon glyphicon-plus',
	),
	'delete' => array(
		'class' => 'glyphicon glyphicon-remove',
	),
	'edit' => array(
		'class' => 'glyphicon glyphicon-edit',
	),
	'envelope' => array(
		'class' => 'glyphicon glyphicon-envelope',
	),
	'error' => array( 
		'class' => 'glyphicon glyphicon-fire',
	),
	'export' => array(
		'class' => 'glyphicon glyphicon-export',
	),
	'external-link' => array(
		'class' => 'fa fa-external-link',
	),
	'file-archive' => array( 
		'class' => 'glyphicon glyphicon-floppy-save',
	),
	'help' => array( 
		'class' => 'glyphicon glyphicon-question-sign',
	),
	'history' => array( 
		'class' => 'glyphicon glyphicon-time',
	),
	'import' => array( 
		'class' => 'glyphicon glyphicon-import',
	),
	'info' => array( 
		'class' => 'glyphicon glyphicon-info-sign',
	),
	'menuitem' => array( 
		'class' => 'glyphicon glyphicon-minus',
	),
	'notification' => array( 
		'class' => 'glyphicon glyphicon-bell',
	),
	'permission' => array(
		'class' => 'glyphicon glyphicon-key',
	),
	'post' => array(
		'class' => 'glyphicon glyphicon-pencil',
	),
	'print' => array(
		'class' => 'glyphicon glyphicon-print',
	),
	'refresh' => array(
		'class' => 'glyphicon glyphicon-refresh',
	),
	'remove' => array(
		'class' => 'glyphicon glyphicon-remove',
	),
	'rss' => array(
		'class' => 'glyphicon glyphicon-bullhorn',
	),
	'settings' => array(
		'class' => 'glyphicon glyphicon-cog',
	),
	'share' => array(
		'class' => 'glyphicon glyphicon-share',
	),
	'stop-watching' => array(
		'class' => 'glyphicon glyphicon-close',
	),
	'success' => array(
		'class' => 'glyphicon glyphicon-ok',
	),
	'tag' => array(
		'class' => 'glyphicon glyphicon-tag',
	),
	'trash' => array(
		'class' => 'glyphicon glyphicon-trash',
	),
	'view' => array(
		'class' => 'glyphicon glyphicon-zoom-in',
	),
	'warning' => array(
		'class' => 'glyphicon glyphicon-warning-sign',
	),
	'watch' => array(
		'class' => 'glyphicon glyphicon-eye-open',
	),
);