<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	return [
		'name' => tr('Glyphicons'),
		'description' => tr('Glyphicon focused iconset, see http://getbootstrap.com/components/'),
		'tag' => 'span',
		'prepend' => 'glyphicon glyphicon-',
		'append' => '',
		'icons' => [
			'actions' => [
				'id' => 'play-circle',
			],
			'add' => [
				'id' => 'plus-sign',
			],
			'admin' => [
					'id' => 'cog',
			],
			//align-center in defaults
			//align-justify in defaults
			//align-left in defaults
			//align-right in defaults
			'anchor' => [
				'id' => 'flag',
			],
			//arrow-up in defaults
			'articles' => [
				'id' => 'text-background',
			],
			'attach' => [
				'id' => 'paperclip',
			],
			'audio' => [
				'id' => 'volume-up',
			],
			'back' => [
				'id' => 'arrow-left',
			],
			'background-color' => [
				'id' => 'text-background',
			],
			'backlink' => [
				'id' => 'new-window',
			],
			//backward in defaults
			'backward_step' => [
				'id' => 'step-backward',
			],
			'ban' => [
				'id' => 'ban-circle',
			],
			//book in defaults
			//bookmark in defaults
			'box' => [
				'id' => 'text-background',
			],
			'bug' => [
				'id' => 'alert',
			],
			//bullhorn in defaults
			//calendar in defaults
			'caret-left' => [
				'id' => 'triangle-left',
			],
			'caret-right' => [
				'id' => 'triangle-right',
			],
			'cart' => [
				'id' => 'shopping-cart',
			],
			'chart' => [
				'id' => 'object-align-bottom',
			],
			'check' => [
				'id' => 'ok',
			],
			'code' => [
				'id' => 'console',
			],
			'code_file' => [
				'id' => 'file',
			],
			'collapsed' => [
				'id' => 'expand',
			],
			'columns' => [
				'id' => 'th-large',
			],
			//comment in defaults
			'comments' => [
				'id' => 'comment',
			],
			'compose' => [
				'id' => 'pencil',
			],
			'computer' => [
				'id' => 'modal-window',
			],
			'contacts' => [
				'id' => 'user',
			],
			'copy' => [
				'id' => 'duplicate',
			],
			'copyright' => [
				'id' => 'copyright-mark',
			],
			'create' => [
				'id' => 'plus',
			],
			'database' => [
					'id' => 'tasks',
			],
			'delete' => [
				'id' => 'remove',
			],
			//dashboard in defaults
			'difference' => [
				'id' => 'text-color',
			],
			'disable' => [
				'id' => 'minus-sign',
			],
			'documentation' => [
				'id' => 'book',
			],
			'down' => [
				'id' => 'arrow-down',
			],
			//edit in defaults
			//education in defaults
			//envelope in defaults
			//erase in defaults
			'error' => [
				'id' => 'exclamation-sign',
			],
			'excel' => [
				'id' => 'file',
			],
			'expanded' => [
				'id' => 'collapse-down',
			],
			//export in defaults
			'facebook' => [
				'id' => 'thumbs-up',
			],
			//file in defaults
			'file-archive' => [
				'id' => 'folder-close',
			],
			'file-archive-open' => [
				'id' => 'folder-open',
			],
			//filter in defaults
			//flag in defaults
			'floppy' => [
				'id' => 'floppy-save',
			],
			'font-color' => [
				'id' => 'font',
				'style' => 'color:red',
			],
			//forward in defaults
			'forward_step' => [
				'id' => 'step-forward',
			],
			//fullscreen in defaults
			//used for google doc plugin
			'google' => [
				'id' => 'file',
			],
			'group' => [
				'id' => 'user',
			],
			'h1' => [
				'id' => 'header',
			],
			'h2' => [
				'id' => 'header',
				'size' => '.9'
			],
			'h3' => [
				'id' => 'header',
				'size' => '.8'
			],
			'help' => [
				'id' => 'question-sign',
			],
			'history' => [
				'id' => 'time',
			],
			'horizontal-rule' => [
				'id' => 'minus',
			],
			'html' => [
				'id' => 'console',
			],
			'image' => [
				'id' => 'picture',
			],
			//import in defaults
			'indent' => [
					'id' => 'indent-left',
			],
			'index' => [
				'id' => 'refresh',
			],
			'information' => [
				'id' => 'info-sign',
			],
			'language' => [
				'id' => 'globe',
			],
			//link in defaults
			'link-external' => [
				'id' => 'share',
			],
			'link-external-alt' => [
				'id' => 'share',
			],
			//list in defaults
			'list-numbered' => [
				'id' => 'list-alt',
			],
			//lock in defaults
			'log' => [
				'id' => 'list-alt',
			],
			'login' => [
				'id' => 'log-in',
			],
			'logout' => [
				'id' => 'log-out',
			],
			'mailbox' => [
				'id' => 'inbox',
			],
			'map' => [
				'id' => 'map-marker',
			],
			'menu' => [
				'id' => 'menu-hamburger',
			],
			'menu-extra' => [
				'id' => 'chevron-down',
			],
			'menuitem' => [
				'id' => 'chevron-right',
			],
			'merge' => [
				'id' => 'random',
			],
			'minimize' => [
				'id' => 'resize-small',
			],
			//minus in defaults
			'module' => [
				'id' => 'cog',
			],
			'money' => [
				'id' => 'piggy-bank',
			],
			'more' => [
				'id' => 'option-horizontal',
			],
			//move in defaults
			//music in defaults
			'next' => [
				'id' => 'arrow-right',
			],
			'notepad' => [
				'id' => 'file',
			],
			'notification' => [
				'id' => 'bell',
			],
			//off in defaults
			'ok' => [
				'id' => 'ok-sign',
			],
			'outdent' => [
				'id' => 'indent-right',
			],
			'page-break' => [
				'id' => 'scissors',
			],
			//paste in defaults
			//pause in defaults
			'paypal' => [
				'id' => 'credit-card',
			],
			'pdf' => [
				'id' => 'file',
			],
			//pencil in defaults
			'permission' => [
				'id' => 'lock',
			],
			//play in defaults
			'plugin' => [
				'id' => 'briefcase',
			],
			'popup' => [
				'id' => 'list-alt',
			],
			'post' => [
				'id' => 'pencil',
			],
			'powerpoint' => [
				'id' => 'blackboard',
			],
			'previous' => [
				'id' => 'arrow-left',
			],
			//print in defaults
			'quotes' => [
				'id' => 'comment',
			],
			'ranking' => [
				'id' => 'sort-by-order',
			],
			//refresh in defaults
			//remove in defaults
			//repeat in defaults
			'rss' => [
				'id' => 'bullhorn',
			],
			//scissors in defaults
			'screencapture' => [
				'id' => 'camera',
			],
			//search in defaults
			'selectall' => [
				'id' => 'open-file',
			],
			//send in defaults
			'settings' => [
				'id' => 'wrench',
			],
			//share in defaults
			'sharethis' => [
				'id' => 'share-alt',
			],
			'skype' => [
				'id' => 'share',
			],
			'smile' => [
				'id' => 'sunglasses',
			],
			//sort in defaults
			'sort-down' => [
				'id' => 'sort-by-alphabet-alt',
			],
			'sort-up' => [
				'id' => 'sort-by-alphabet',
			],
			//star in defaults
			//star-empty in defaults
			'star-empty-selected' => [
				'id' => 'star-empty',
				'class' => 'text-success'
			],
			'star-half-rating' => [
				'id' => 'star-empty',
			],
			'star-half-selected' => [
				'id' => 'star-empty',
				'class' => 'text-success'
			],
			'star-selected' => [
				'id' => 'star',
				'class' => 'text-success'
			],
			'status-open' => [
				'id' => 'ok-sign',
				'class' => 'text-success'
			],
			'status-pending' => [
				'id' => 'question-sign',
				'class' => 'text-warning'
			],
			'status-closed' => [
				'id' => 'remove-sign',
				'class' => 'text-muted'
			],
			//stop in defaults
			'stop-watching' => [
				'id' => 'eye-close',
			],
			'structure' => [
				'id' => 'tree-conifer',
			],
			'success' => [
				'id' => 'ok',
			],
			//subscript in defaults
			//superscript in defaults
			'table' => [
				'id' => 'list-alt',
			],
			//tag in defaults
			//tags in defaults
			'textfile' => [
				'id' => 'file',
			],
			//th-large in defaults
			//th-list in defaults
			'three-d' => [
				'id' => 'road',
			],
			//thumbs-down in defaults
			//thumbs-up in defaults
			'title' => [
				'id' => 'text-color',
			],
			'toggle-off' => [
				'id' => 'stop',
			],
			'toggle-on' => [
				'id' => 'play',
			],
			'toggle-left' => [
				'id' => 'step-backward',
			],
			'toggle-right' => [
				'id' => 'step-forward',
			],
			'trackers' => [
				'id' => 'tasks',
			],
			'translate' => [
				'id' => 'globe',
			],
			//trash in defaults
			'tv' => [
				'id' => 'film',
			],
			'twitter' => [
				'id' => 'retweet',
			],
			'undo' => [
				'id' => 'arrow-left',
			],
			//use a better unlock icon when available
			'unlink' => [
					'id' => 'flash',
			],
			'unlock' => [
				'id' => 'folder-open',
			],
			'up' => [
				'id' => 'arrow-up',
			],
			'video' => [
				'id' => 'facetime-video',
			],
			'video_file' => [
				'id' => 'facetime-video',
			],
			'view' => [
				'id' => 'zoom-in',
			],
			'vimeo' => [
				'id' => 'facetime-video',
			],
			'warning' => [
				'id' => 'warning-sign',
			],
			'watch' => [
				'id' => 'eye-open',
			],
			'watch-group' => [
				'id' => 'eye-open',
			],
			'wizard' => [
				'id' => 'flash',
			],
			'word' => [
				'id' => 'file',
			],
			'wysiwyg' => [
				'id' => 'text-background',
			],
			'youtube' => [
				'id' => 'play',
			],
			'zip' => [
				'id' => 'compressed',
			],
		],
		'defaults' => [
			'adjust',
			'alert',
			'align-bottom',
			'align-center',
			'align-horizontal',
			'align-justify',
			'align-left',
			'align-right',
			'align-top',
			'align-vertical',
			'apple',
			'arrow-down',
			'arrow-left',
			'arrow-right',
			'arrow-up',
			'asterisk',
			'baby-formula',
			'backward',
			'ban-circle',
			'barcode',
			'bed',
			'bell',
			'bishop',
			'bitcoin',
			'blackboard',
			'bold',
			'book',
			'bookmark',
			'briefcase',
			'btc',
			'bullhorn',
			'calendar',
			'camera',
			'cd',
			'certificate',
//			'check',
			'chevron-down',
			'chevron-left',
			'chevron-right',
			'chevron-up',
			'circle-arrow-down',
			'circle-arrow-left',
			'circle-arrow-right',
			'circle-arrow-up',
			'cloud-download',
			'cloud-upload',
			'cloud',
			'cog',
			'collapse-down',
			'collapse-up',
			'comment',
			'compressed',
			'console',
			'copy',
			'copyright-mark',
			'credit-card',
			'cutlery',
			'dashboard',
			'download-alt',
			'download',
			'duplicate',
			'earphone',
			'edit',
			'education',
			'eject',
			'envelope',
			'equalizer',
			'erase',
			'euro',
			'exclamation-sign',
			'expand',
			'export',
			'eye-close',
			'eye-open',
			'facetime-video',
			'fast-backward',
			'fast-forward',
			'file',
			'film',
			'filter',
			'fire',
			'flag',
			'flash',
			'floppy-disk',
			'floppy-open',
			'floppy-remove',
			'floppy-save',
			'floppy-saved',
			'folder-close',
			'folder-open',
			'font',
			'forward',
			'fullscreen',
			'gbp',
			'gift',
			'glass',
			'globe',
			'grain',
			'hand-down',
			'hand-left',
			'hand-right',
			'hand-up',
			'hd-video',
			'hdd',
			'header',
			'headphones',
			'heart-empty',
			'heart',
			'home',
			'hourglass',
			'ice-lolly',
			'ice-lolly-tasted',
			'import',
			'inbox',
			'indent-left',
			'indent-right',
			'info-sign',
			'italic',
			'jpy',
			'king',
			'knight',
			'lamp',
			'leaf',
			'level-up',
			'link',
			'list-alt',
			'list',
			'lock',
			'log-in',
			'log-out',
			'magnet',
			'map-marker',
			'menu-down',
			'menu-hamburger',
			'menu-left',
			'menu-right',
			'menu-up',
			'minus-sign',
			'minus',
			'modal-window',
			'move',
			'music',
			'new-window',
			'off',
			'oil',
			'ok-circle',
			'ok-sign',
			'ok',
			'open',
			'open-file',
			'option-horizontal',
			'option-vertical',
			'paperclip',
			'paste',
			'pause',
			'pawn',
			'pencil',
			'phone-alt',
			'phone',
			'picture',
			'piggy-bank',
			'plane',
			'play-circle',
			'play',
			'plus-sign',
			'plus',
			'print',
			'pushpin',
			'qrcode',
			'queen',
			'question-sign',
			'random',
			'record',
			'refresh',
			'registration-mark',
			'remove-circle',
			'remove-sign',
			'remove',
			'repeat',
			'resize-full',
			'resize-horizontal',
			'resize-small',
			'resize-vertical',
			'retweet',
			'road',
			'rub',
			'ruble',
			'save',
			'save-file',
			'saved',
			'scale',
			'scissors',
			'screenshot',
			'sd-video',
			'search',
			'send',
			'share-alt',
			'share',
			'shopping-cart',
			'signal',
			'sort-by-alphabet-alt',
			'sort-by-alphabet',
			'sort-by-attributes-alt',
			'sort-by-attributes',
			'sort-by-order-alt',
			'sort-by-order',
			'sort',
			'sound-5-1',
			'sound-6-1',
			'sound-7-1',
			'sound-dolby',
			'sound-stereo',
			'star-empty',
			'star',
			'stats',
			'step-backward',
			'step-forward',
			'stop',
			'subscript',
			'subtitles',
			'sunglasses',
			'superscript',
			'tag',
			'tags',
			'tasks',
			'tent',
			'text-background',
			'text-color',
			'text-height',
			'text-size',
			'text-width',
			'th-large',
			'th-list',
			'th',
			'thumbs-down',
			'thumbs-up',
			'time',
			'tint',
			'tower',
			'transfer',
			'trash',
			'tree-conifer',
			'tree-deciduous',
			'triangle-bottom',
			'triangle-left',
			'triangle-right',
			'triangle-top',
			'unchecked',
			'upload',
			'usd',
			'user',
			'volume-down',
			'volume-off',
			'volume-up',
			'warning-sign',
			'wrench',
			'xbt',
			'yen',
			'zoom-in',
			'zoom-out',
		],
	];
}
