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
/*
 * Note on sizing:
 * Some ability to use larger legacy icons based on size parameter in smarty icon function
 * Indicate size of main id using size => X
 * Add paths to additional sizes (usually just size 3) by using sizes => array(3 => iconname)
 * Regular size is 16px by 16px, so size 2 is 32px by 32px and size 3 is 48px by 48px
 * See admin_fgal below for an example
 */


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
				'size' => 2,
				'sizes' => array(
					3 => array(
						'id' => 'large/fileopen48x48'
					)
				)
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
				'size' => 2,
				'sizes' => array(
					3 => array(
						'id' => 'large/wikipages48x48'
					)
				)
			),
			'admin_workspace' => array(
				'id' => 'large/areas',
			),
			'admin_wysiwyg' => array(
				'id' => 'large/wysiwyg',
			),
			'arrow-up' => array(
				'id' => 'arrow-up',
			),
			'attach' => array(
				'id' => 'attach',
			),
			'audio' => array(
				'id' => 'bell',
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
			'ban' => array(
				'id' => 'cancel',
			),
			//bug in defaults
			'caret-left' => array(
				'id' => 'resultset_previous',
			),
			'caret-right' => array(
				'id' => 'resultset_next',
			),
			'chart' => array(
				'id' => 'chart_curve',
			),
			'check' => array(
				'id' => 'select',
				'append' => '.gif'
			),
			'code_file' => array(
				'id' => 'page_white_code',
			),
			'cog' => array(
				'id' => 'wrench',
			),
			'collapsed' => array(
				'id' => 'bullet_toggle_plus',
			),
			'columns' => array(
				'id' => 'text_columns',
			),
			'comments' => array(
				'id' => 'comments',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'large/comments48x48'
					)
				)
			),
			'compose' => array(
				'id' => 'pencil',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'webmail/compose',
						'prepend' => 'img/',
						'append' => '.gif'
					)
				)
			),
			'contacts' => array(
				'id' => 'group',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'webmail/contact',
						'prepend' => 'img/',
						'append' => '.gif'
					)
				)
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
			'difference' => array(
				'id' => 'text_strikethrough',
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
			'envelope' => array(
				'id' => 'email',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'large/evolution48x48'
					)
				)
			),
			'error' => array(
				'id' => 'exclamation',
			),
			'excel' => array(
				'id' => 'mime/xls',
			),
			'expanded' => array(
				'id' => 'bullet_toggle_minus',
			),
			'export' => array(
				'id' => 'disk',
			),
			'facebook' => array(
				'id' => 'thumb_up',
				'size' => 1,
				'sizes' => array(
					2 => array(
						'id' => 'facebook-logo_32'
					)
				)
			),
			'file' => array(
				'id' => 'page',
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
			'flag' => array(
				'id' => 'flag_blue',
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
			//help in defaults
			'history' => array(
				'id' => 'clock',
			),
			'home' => array(
				'id' => 'house',
			),
			//html in defaults
			//image in defaults
			'import' => array(
				'id' => 'upload',
			),
			'index' => array(
				'id' => 'table_refresh',
			),
			//information in defaults
			//link in defaults
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
			'login' => array(
				'id' => 'task_received',
			),
			'logout' => array(
				'id' => 'task_submitted',
			),
			'mailbox' => array(
				'id' => 'email',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'webmail/mailbox',
						'prepend' => 'img/',
						'append' => '.gif'
					)
				)
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
			'more' => array(
				'id' => 'resultset_down',
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
			'off' => array(
				'id' => 'delete',
			),
			'ok' => array(
				'id' => 'accept',
			),
			'pause' => array(
				'id' => 'control_pause',
			),
			'pdf' => array(
				'id' => 'page_white_acrobat',
			),
			'permission' => array(
				'id' => 'key',
			),
			'play' => array(
				'id' => 'control_play',
			),
			//plugin in defaults
			'popup' => array(
				'id' => 'application_view_columns',
			),
			'post' => array(
				'id' => 'pencil_add',
			),
			'powerpoint' => array(
				'id' => 'mime/ppt',
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
			'repeat' => array(
				'id' => 'arrow_redo',
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
			'send' => array(
				'id' => 'email_go',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'messages48x48'
					)
				)
			),
			'settings' => array(
				'id' => 'wrench',
				'size' => 1,
				'sizes' => array(
					3 => array(
						'id' => 'webmail/settings',
						'prepend' => 'img/',
						'append' => '.gif'
					)
				)
			),
			'share' => array(
				'id' => 'share_link',
			),
			//sharethis in defaults
			'sort' => array(
				'id' => 'resultset',
			),
			'sort-down' => array(
				'id' => 'resultset_down',
			),
			'sort-up' => array(
				'id' => 'resultset_up',
			),
			//star in defaults
			'star-empty' => array(
				'id' => 'star_grey',
			),
			'star-empty-selected' => array(
				'id' => 'star_grey_selected',
			),
			'star-half' => array(
				'id' => 'star_half',
			),
			//don't use half star for rating.tpl since there is no selected half star
			'star-half-rating' => array(
				'id' => 'star_grey',
			),
			'star-half-selected' => array(
				'id' => 'star_grey_selected',
			),
			'star-selected' => array(
				'id' => 'star_selected',
			),
			'stop' => array(
				'id' => 'control_stop',
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
			'tags' => array(
				'id' => 'tag_blue',
			),
			'textfile' => array(
				'id' => 'page',
			),
			'th-list' => array(
				'id' => 'text_list_bullets',
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
			'trackers' => array(
				'id' => 'database',
			),
			'translate' => array(
				'id' => 'world_edit',
			),
			'trash' => array(
				'id' => 'bin',
			),
			'twitter' => array(
				'id' => 'twitter',
				'size' => 1,
				'sizes' => array(
					2 => array(
						'id' => 'twitter_t_logo_32'
					)
				)
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
			'video' => array(
				'id' => 'mime/mpg',
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
			'word' => array(
				'id' => 'mime/doc',
			),
			'wysiwyg' => array(
				'id' => 'text-dropcaps',
			),
			'zip' => array(
				'id' => 'mime/zip',
			),
		),
		'defaults' => array(
			'bug',
			'calendar',
			'group',
			'help',
			'html',
			'image',
			'information',
			'link',
			'pencil',
			'plugin',
			'sharethis',
			'star',
			'user',
			'wrench',
		)
	);
}
