<?php 
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$settings = array(
	'iconset_name' => tr('Glyphicons'),
	'iconset_description' => tr('Glyphicon focused iconset, see http://getbootstrap.com/components/'),
	'icon_tag' => 'span',
);

$icons = array(
	'actions' => array(
		'class' => 'glyphicon glyphicon-play-circle',
	),
	'add' => array(
		'class' => 'glyphicon glyphicon-plus',
	),
	'check' => array(
		'class' => 'glyphicon glyphicon-ok-circle',
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
	'disable' => array(
		'class' => 'glyphicon glyphicon-minus-sign',
	),
	'documentation' => array(
		'class' => 'glyphicon glyphicon-book',
	),
	'edit' => array(
		'class' => 'glyphicon glyphicon-edit',
	),
	'enable' => array(
		'class' => 'glyphicon glyphicon-ok-sign',
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
	'information' => array(
		'class' => 'glyphicon glyphicon-info-sign',
	),
	'link' => array(
		'class' => 'glyphicon glyphicon-link',
	),
	'lock' => array(
		'class' => 'glyphicon glyphicon-lock',
	),
	'menuitem' => array(
		'class' => 'glyphicon glyphicon-minus',
	),
	'move' => array(
		'class' => 'glyphicon glyphicon-move',
	),
	'next' => array(
		'class' => 'glyphicon glyphicon-arrow-right',
	),
	'notification' => array(
		'class' => 'glyphicon glyphicon-bell',
	),
	'ok' => array(
		'class' => 'glyphicon glyphicon-ok-sign',
	),
	'off' => array(
		'class' => 'glyphicon glyphicon-off',
	),
	'permission' => array(
		'class' => 'glyphicon glyphicon-lock',
	),
	'post' => array(
		'class' => 'glyphicon glyphicon-pencil',
	),
	'previous' => array(
		'class' => 'glyphicon glyphicon-arrow-left',
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
	'screencapture' => array(
		'class' => 'glyphicon glyphicon-camera',
	),
	'search' => array(
		'class' => 'glyphicon glyphicon-search',
	),
	'settings' => array(
		'class' => 'glyphicon glyphicon-wrench',
	),
	'share' => array(
		'class' => 'glyphicon glyphicon-share',
	),
	'sort-up' => array(
		'class' => 'glyphicon glyphicon-chevron-up',
	),
	'sort-down' => array(
		'class' => 'glyphicon glyphicon-chevron-down',
	),
	'stop-watching' => array(
		'class' => 'glyphicon glyphicon-eye-close',
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
	//use a better unlock icon when available
	'unlock' => array(
		'class' => 'glyphicon glyphicon-folder-open',
	),
	'user' => array(
		'class' => 'glyphicon glyphicon-user',
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