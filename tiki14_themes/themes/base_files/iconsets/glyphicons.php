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

function iconset_glyphicons()
{
	return array(
		'name' => tr('Glyphicons'),
		'description' => tr('Glyphicon focused iconset, see http://getbootstrap.com/components/'),
		'tag' => 'span',
		'prepend' => 'glyphicon glyphicon-',
		'append' => '',
		'icons' => array(
			'actions' => array(
				'id' => 'play-circle',
			),
			'add' => array(
				'id' => 'plus-sign',
			),
			'admin_i18n' => array(
				'id' => 'globe',
			),
			'attach' => array(
				'id' => 'paperclip',
			),
			'backward' => array(
				'id' => 'backward',
			),
			'backward_step' => array(
				'id' => 'step-backward',
			),
			'check' => array(
				'id' => 'ok-circle',
			),
			'columns' => array(
				'id' => 'th-large',
			),
			'comments' => array(
				'id' => 'comment',
			),
			'create' => array(
				'id' => 'plus',
			),
			'delete' => array(
				'id' => 'remove',
			),
			'disable' => array(
				'id' => 'minus-sign',
			),
			'documentation' => array(
				'id' => 'book',
			),
			'down' => array(
				'id' => 'arrow-down',
			),
			'edit' => array(
				'id' => 'edit',
			),
			'enable' => array(
				'id' => 'ok-sign',
			),
			'envelope' => array(
				'id' => 'envelope',
			),
			'error' => array(
				'id' => 'exclamation-sign',
			),
			'export' => array(
				'id' => 'export',
			),
			'forward' => array(
				'id' => 'forward',
			),
			'forward_step' => array(
				'id' => 'step-forward',
			),
			'file-archive' => array(
				'id' => 'folder',
			),
			'file-archive-open' => array(
				'id' => 'folder-open',
			),
			'filter' => array(
				'id' => 'filter',
			),
			'floppy' => array(
				'id' => 'floppy-save',
			),
			'help' => array(
				'id' => 'question-sign',
			),
			'history' => array(
				'id' => 'time',
			),
			'import' => array(
				'id' => 'import',
			),
			'information' => array(
				'id' => 'info-sign',
			),
			'link' => array(
				'id' => 'link',
			),
			'lock' => array(
				'id' => 'lock',
			),
			'menuitem' => array(
				'id' => 'chevron-right',
			),
			'merge' => array(
				'id' => 'random',
			),
			'minus' => array(
				'id' => 'minus',
			),
			'move' => array(
				'id' => 'move',
			),
			'next' => array(
				'id' => 'arrow-right',
			),
			'notification' => array(
				'id' => 'bell',
			),
			'ok' => array(
				'id' => 'ok-sign',
			),
			'off' => array(
				'id' => 'off',
			),
			'pdf' => array(
				'id' => 'file',
			),
			'permission' => array(
				'id' => 'lock',
			),
			'post' => array(
				'id' => 'pencil',
			),
			'previous' => array(
				'id' => 'arrow-left',
			),
			'print' => array(
				'id' => 'print',
			),
			'refresh' => array(
				'id' => 'refresh',
			),
			'remove' => array(
				'id' => 'remove',
			),
			'rss' => array(
				'id' => 'bullhorn',
			),
			'screencapture' => array(
				'id' => 'camera',
			),
			'search' => array(
				'id' => 'search',
			),
			'settings' => array(
				'id' => 'wrench',
			),
			'share' => array(
				'id' => 'share',
			),
			'sort' => array(
				'id' => 'sort',
			),
			'sort-up' => array(
				'id' => 'sort-by-alphabet',
			),
			'sort-down' => array(
				'id' => 'sort-by-alphabet-alt',
			),
			'stop-watching' => array(
				'id' => 'eye-close',
			),
			'success' => array(
				'id' => 'ok',
			),
			'tag' => array(
				'id' => 'tag',
			),
			'trash' => array(
				'id' => 'trash',
			),
			//use a better unlock icon when available
			'unlock' => array(
				'id' => 'folder-open',
			),
			'up' => array(
				'id' => 'arrow-up',
			),
			'user' => array(
				'id' => 'user',
			),
			'view' => array(
				'id' => 'zoom-in',
			),
			'warning' => array(
				'id' => 'warning-sign',
			),
			'watch' => array(
				'id' => 'eye-open',
			),
		),
	);
}