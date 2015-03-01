<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

function iconset_legacy()
{
	return array(
		'name' => tr('Legacy (pre Tiki14) icons'),
		'description' => tr('Legacy (pre Tiki14) icons, mainly using famfamfam images'),
		'tag' => 'img',
		'prepend' => 'img/icons/',
		'append' => '.png',
		'icons' => array(
			'actions' => array(
				'id' => 'application_form',
			),
			'add' => array(
				'id' => 'large/icon-configuration',
			),
			'admin_ads' => array(
				'id' => 'large/ads',
			),
			'admin_articles' => array(
				'id' => 'large/stock_bold',
			),
			'admin_blogs' => array(
				'id' => 'large/blogs',
			),
			'admin_calendar' => array(
				'id' => 'large/date',
			),
			'admin_category' => array(
				'id' => 'large/categories',
			),
			'admin_comments' => array(
				'id' => 'large/comments',
			),
			'admin_community' => array(
				'id' => 'large/users',
			),
			'admin_connect' => array(
				'id' => 'large/gnome-globe',
			),
			'admin_copyright' => array(
				'id' => 'large/copyright',
			),
			'admin_directory' => array(
				'id' => 'large/gnome-fs-server',
			),
			'admin_faqs' => array(
				'id' => 'large/stock_dialog_question',
			),
			'admin_features' => array(
				'id' => 'large/boot',
			),
			'admin_fgal' => array(
				'id' => 'large/file-manager',
			),
			'admin_forums' => array(
				'id' => 'large/stock_index',
			),
			'admin_freetags' => array(
				'id' => 'large/vcard',
			),
			'admin_gal' => array(
				'id' => 'large/stock_select-color',
			),
			'admin_general' => array(
				'id' => 'large/icon-configuration',
				'position' => '0px -15px;',
			),
			'admin_i18n' => array(
				'id' => 'large/i18n',
			),
			'admin_intertiki' => array(
				'id' => 'large/intertiki',
			),
			'admin_login' => array(
				'id' => 'large/stock_quit',
			),
			'admin_look' => array(
				'id' => 'large/gnome-settings-background',
			),
			'admin_maps' => array(
				'id' => 'large/maps',
			),
			'admin_messages' => array(
				'id' => 'large/messages',
			),
			'admin_metatags' => array(
				'id' => 'large/metatags',
			),
			'admin_module' => array(
				'id' => 'large/display-capplet',
			),
			'admin_payment' => array(
				'id' => 'large/payment',
			),
			'admin_performance' => array(
				'id' => 'large/performance',
			),
			'admin_polls' => array(
				'id' => 'large/stock_missing-image',
			),
			'admin_profiles' => array(
				'id' => 'large/profiles',
			),
			'admin_rating' => array(
				'id' => 'large/rating',
			),
			'admin_rss' => array(
				'id' => 'large/feed-icon',
			),
			'admin_score' => array(
				'id' => 'large/stock_about',
			),
			'admin_search' => array(
				'id' => 'large/xfce4-appfinder',
			),
			'admin_semantic' => array(
				'id' => 'large/semantic',
			),
			'admin_security' => array(
				'id' => 'large/gnome-lockscreen',
			),
			'admin_sefurl' => array(
				'id' => 'large/goto',
			),
			'admin_share' => array(
				'id' => 'large/stock_contact',
			),
			'admin_socialnetworks' => array(
				'id' => 'large/socialnetworks',
			),
			'admin_textarea' => array(
				'id' => 'large/editing',
			),
			'admin_trackers' => array(
				'id' => 'large/gnome-settings-font',
			),
			'admin_userfiles' => array(
				'id' => 'large/userfiles',
			),
			'admin_video' => array(
				'id' => 'large/gnome-camera-video-32',
			),
			'admin_webmail' => array(
				'id' => 'large/evolution',
			),
			'admin_webservices' => array(
				'id' => 'large/webservices',
			),
			'admin_wiki' => array(
				'id' => 'large/wikipages',
			),
			'admin_workspace' => array(
				'id' => 'large/areas',
			),
			'admin_wysiwyg' => array(
				'id' => 'large/wysiwyg',
			),
			'administer' => array(
				'id' => 'wrench',
			),
			'arrow-up' => array(
				'id' => 'arrow-up',
			),
			'attach' => array(
				'id' => 'attach',
			),
			'back' => array(
				'id' => 'arrow_left',
			),
			'backlink' => array(
				'id' => 'arrow_turn_left',
			),
			'backward' => array(
				'id' => 'control_rewind',
			),
			'backward_step' => array(
				'id' => 'control_start',
			),
			'bug' => array(
				'id' => 'bug',
			),
			'cache' => array(
				'id' => 'database_refresh',
			),
			'calendar' => array(
				'id' => 'calendar',
			),
			'caret-left' => array(
				'id' => 'resultset_previous',
			),
			'caret-right' => array(
				'id' => 'resultset_next',
			),
			'change' => array(
				'id' => 'pencil',
			),
			'check' => array(
				'id' => 'select',
				'append' => '.gif'
			),
			'columns' => array(
				'id' => 'text_columns',
			),
			'comments' => array(
				'id' => 'comments',
			),
			'copy' => array(
				'id' => 'page_copy',
			),
			'create' => array(
				'id' => 'add',
			),
			'delete' => array(
				'id' => 'cross',
			),
			'disable' => array(
				'id' => 'delete',
			),
			'documentation' => array(
				'id' => 'book_open',
			),
			'down' => array(
				'id' => 'resultset_down',
			),
			'edit' => array(
				'id' => 'page_edit',
			),
			'ellipsis' => array(
				'id' => 'resultset_down',
			),
			'enable' => array(
				'id' => 'accept',
			),
			'envelope' => array(
				'id' => 'email',
			),
			'error' => array(
				'id' => 'exclamation',
			),
			'export' => array(
				'id' => 'disk',
			),
			'file-archive' => array(
				'id' => 'folder',
			),
			'file-archive-open' => array(
				'id' => 'folder_go',
			),
			'filter' => array(
				'id' => 'find',
			),
			'floppy' => array(
				'id' => 'disk',
			),
			'forward' => array(
				'id' => 'control_fastforward',
			),
			'forward_step' => array(
				'id' => 'control_end',
			),
			'group' => array(
				'id' => 'group',
			),
			'group-watch' => array(
				'id' => 'eye_group',
			),
			'help' => array(
				'id' => 'help',
			),
			'history' => array(
				'id' => 'clock',
			),
			'import' => array(
				'id' => 'upload',
			),
			'index' => array(
				'id' => 'table_refresh',
			),
			'information' => array(
				'id' => 'information',
			),
			'link' => array(
				'id' => 'link',
			),
			'link-external' => array(
				'id' => 'external_link',
				'append' => '.gif'
			),
			'list' => array(
				'id' => 'application_view_list',
			),
			'lock' => array(
				'id' => 'lock_add',
			),
			'log' => array(
				'id' => 'book',
			),
			'menu' => array(
				'id' => 'application_side_tree',
			),
			'menuitem' => array(
				'id' => 'arrow_right',
			),
			'merge' => array(
				'id' => 'arrow_switch',
			),
			'minus' => array(
				'id' => 'delete',
			),
			'module' => array(
				'id' => 'module',
			),
			'move' => array(
				'id' => 'task_submitted',
			),
			'next' => array(
				'id' => 'arrow_right',
			),
			'notepad' => array(
				'id' => 'disk',
			),
			'notification' => array(
				'id' => 'announce',
			),
			'ok' => array(
				'id' => 'accept',
			),
			'pdf' => array(
				'id' => 'page_white_acrobat',
			),
			'permission' => array(
				'id' => 'key',
			),
			'plugin' => array(
				'id' => 'plugin',
			),
			'popup' => array(
				'id' => 'application_view_columns',
			),
			'post' => array(
				'id' => 'pencil_add',
			),
			'previous' => array(
				'id' => 'arrow_left',
			),
			'print' => array(
				'id' => 'printer',
			),
			'ranking' => array(
				'id' => 'star',
			),
			'refresh' => array(
				'id' => 'arrow_refresh',
			),
			'remove' => array(
				'id' => 'cross',
			),
			'rss' => array(
				'id' => 'feed',
			),
			'screencapture' => array(
				'id' => 'camera',
			),
			'search' => array(
				'id' => 'magnifier',
			),
			'settings' => array(
				'id' => 'wrench',
			),
			'share' => array(
				'id' => 'sharethis',
			),
			'sort' => array(
				'id' => 'resultset',
			),
			'sort-down' => array(
				'id' => 'resultset_down',
			),
			'sort-up' => array(
				'id' => 'resultset_up',
			),
			'stop-watching' => array(
				'id' => 'no_eye',
			),
			'structure' => array(
				'id' => 'chart_organisation',
			),
			'success' => array(
				'id' => 'tick',
			),
			'tag' => array(
				'id' => 'tag_blue',
			),
			'theme' => array(
				'id' => 'image',
			),
			'themegenerator' => array(
				'id' => 'palette',
			),
			'three-d' => array(
				'id' => 'application_side_tree',
			),
			'thumbs-up' => array(
				'id' => 'thumb_up',
			),
			'toggle-off' => array(
				'id' => 'accept',
			),
			'toggle-on' => array(
				'id' => 'delete',
			),
			'translate' => array(
				'id' => 'world_edit',
			),
			'trash' => array(
				'id' => 'bin',
			),
			'undo' => array(
				'id' => 'arrow_undo',
			),
			'unlock' => array(
				'id' => 'lock_open',
			),
			'up' => array(
				'id' => 'resultset_up',
			),
			'user' => array(
				'id' => 'user',
			),
			'view' => array(
				'id' => 'magnifier',
			),
			'warning' => array(
				'id' => 'sticky',
			),
			'watch' => array(
				'id' => 'eye',
			),
			'watch-group' => array(
				'id' => 'eye_group',
			),
			'wizard' => array(
				'id' => 'wizard16x16',
			),
		),
	);
}
