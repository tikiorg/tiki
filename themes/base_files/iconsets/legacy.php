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
	return [
		'name' => tr('Legacy (pre Tiki14) icons'),
		'description' => tr('Legacy (pre Tiki14) icons, mainly using famfamfam images'),
		'tag' => 'img',
		'prepend' => 'img/icons/',
		'append' => '.png',
		'icons' => [
			'actions' => [
				'id' => 'application_form',
			],
			//add in defaults
			'admin' => [
					'id' => 'wrench',
			],
			'admin_ads' => [
				'id' => 'large/ads',
			],
			'admin_articles' => [
				'id' => 'large/stock_bold',
			],
			'admin_blogs' => [
				'id' => 'large/blogs',
			],
			'admin_calendar' => [
				'id' => 'large/date',
			],
			'admin_category' => [
				'id' => 'large/categories',
			],
			'admin_comments' => [
				'id' => 'large/comments',
			],
			'admin_community' => [
				'id' => 'large/users',
			],
			'admin_connect' => [
				'id' => 'large/gnome-globe',
			],
			'admin_copyright' => [
				'id' => 'large/copyright',
			],
			'admin_directory' => [
				'id' => 'large/gnome-fs-server',
			],
			'admin_faqs' => [
				'id' => 'large/stock_dialog_question',
			],
			'admin_features' => [
				'id' => 'large/boot',
			],
			'admin_fgal' => [
				'id' => 'large/file-manager',
				'size' => 2,
				'sizes' => [
					3 => [
						'id' => 'large/fileopen48x48'
					]
				]
			],
			'admin_forums' => [
				'id' => 'large/stock_index',
			],
			'admin_freetags' => [
				'id' => 'large/vcard',
			],
			'admin_gal' => [
				'id' => 'large/stock_select-color',
			],
			'admin_general' => [
				'id' => 'large/icon-configuration',
				'position' => '0px -15px;',
			],
			'admin_i18n' => [
				'id' => 'large/i18n',
			],
			'admin_intertiki' => [
				'id' => 'large/intertiki',
			],
			'admin_login' => [
				'id' => 'large/stock_quit',
			],
			'admin_look' => [
				'id' => 'large/gnome-settings-background',
			],
			'admin_maps' => [
				'id' => 'large/maps',
			],
			'admin_messages' => [
				'id' => 'large/messages',
			],
			'admin_metatags' => [
				'id' => 'large/metatags',
			],
			'admin_module' => [
				'id' => 'large/display-capplet',
			],
			'admin_payment' => [
				'id' => 'large/payment',
			],
			'admin_performance' => [
				'id' => 'large/performance',
			],
			'admin_polls' => [
				'id' => 'large/stock_missing-image',
			],
			'admin_profiles' => [
				'id' => 'large/profiles',
			],
			'admin_rating' => [
				'id' => 'large/rating',
			],
			'admin_rss' => [
				'id' => 'large/feed-icon',
			],
			'admin_score' => [
				'id' => 'large/stock_about',
			],
			'admin_search' => [
				'id' => 'large/xfce4-appfinder',
			],
			'admin_semantic' => [
				'id' => 'large/semantic',
			],
			'admin_security' => [
				'id' => 'large/gnome-lockscreen',
			],
			'admin_sefurl' => [
				'id' => 'large/goto',
			],
			'admin_share' => [
				'id' => 'large/stock_contact',
			],
			'admin_socialnetworks' => [
				'id' => 'large/socialnetworks',
			],
			'admin_stats' => [
				'id' => 'large/stats48x48',
			],
			'admin_textarea' => [
				'id' => 'large/editing',
			],
			'admin_trackers' => [
				'id' => 'large/gnome-settings-font',
			],
			'admin_userfiles' => [
				'id' => 'large/userfiles',
			],
			'admin_video' => [
				'id' => 'large/gnome-camera-video-32',
			],
			'admin_webmail' => [
				'id' => 'large/evolution',
			],
			'admin_webservices' => [
				'id' => 'large/webservices',
			],
			'admin_wiki' => [
				'id' => 'large/wikipages',
				'size' => 2,
				'sizes' => [
					3 => [
						'id' => 'large/wikipages48x48'
					]
				]
			],
			'admin_workspace' => [
				'id' => 'large/areas',
			],
			'admin_wysiwyg' => [
				'id' => 'large/wysiwyg',
			],
			'align-center' => [
				'id' => 'text_align_center',
			],
			'align-justify' => [
				'id' => 'text_align_justify',
			],
			'align-left' => [
				'id' => 'text_align_left',
			],
			'align-right' => [
				'id' => 'text_align_right',
			],
			//anchor in defaults
			'arrow-up' => [
				'id' => 'arrow-up',
			],
			'articles' => [
				'id' => 'newspaper_go',
			],
			'attach' => [
				'id' => 'attach',
			],
			'audio' => [
				'id' => 'bell',
			],
			'back' => [
				'id' => 'arrow_left',
			],
			'background-color' => [
				'id' => 'palette_bg',
			],
			'backlink' => [
				'id' => 'arrow_turn_left',
			],
			'backward' => [
				'id' => 'control_rewind',
			],
			'backward_step' => [
				'id' => 'control_start',
			],
			'ban' => [
				'id' => 'ban_remove',
			],
			'bold' => [
				'id' => 'text_bold',
			],
			//book in defaults
			'bookmark' => [
				'id' => 'book',
			],
			'box' => [
				'id' => 'layout_header',
			],
			//bug in defaults
			'bullhorn' => [
				'id' => 'announce',
			],
			//calendar in defaults
			'caret-left' => [
				'id' => 'resultset_previous',
			],
			'caret-right' => [
				'id' => 'resultset_next',
			],
			'cart' => [
				'id' => 'cart_add',
			],
			'chart' => [
				'id' => 'chart_curve',
			],
			'check' => [
				'id' => 'accept',
//				'append' => '.gif'
			],
			'code' => [
				'id' => 'page_white_code',
			],
			'code_file' => [
				'id' => 'page_white_code',
			],
			'cog' => [
				'id' => 'wrench',
			],
			'collapsed' => [
				'id' => 'bullet_toggle_plus',
			],
			'columns' => [
				'id' => 'text_columns',
			],
			'comment' => [
				'id' => 'comment_add',
			],
			'comments' => [
				'id' => 'comments',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'large/comments48x48'
					]
				]
			],
			'compose' => [
				'id' => 'pencil',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'webmail/compose',
						'prepend' => 'img/',
						'append' => '.gif'
					]
				]
			],
			//computer in defaults
			'contacts' => [
				'id' => 'group',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'webmail/contact',
						'prepend' => 'img/',
						'append' => '.gif'
					]
				]
			],
			'copy' => [
				'id' => 'page_copy',
			],
			'copyright' => [
				'id' => 'shield',
			],
			'create' => [
				'id' => 'add',
			],
			'dashboard' => [
				'id' => 'application_view_columns',
			],
			//database in defaults
			'delete' => [
				'id' => 'cross',
			],
			'difference' => [
				'id' => 'text_strikethrough',
			],
			'disable' => [
				'id' => 'delete',
			],
			'documentation' => [
				'id' => 'book_open',
			],
			'down' => [
				'id' => 'resultset_down',
			],
			'edit' => [
				'id' => 'page_edit',
			],
			'education' => [
				'id' => 'text_signature',
			],
			'envelope' => [
				'id' => 'email',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'large/evolution48x48'
					]
				]
			],
			'error' => [
				'id' => 'exclamation',
			],
			'erase' => [
					'id' => 'page_edit',
			],
			'excel' => [
				'id' => 'mime/xls',
			],
			'expanded' => [
				'id' => 'bullet_toggle_minus',
			],
			'export' => [
				'id' => 'disk',
			],
			'facebook' => [
				'id' => 'thumb_up',
				'size' => 1,
				'sizes' => [
					2 => [
						'id' => 'facebook-logo_32'
					]
				]
			],
			'file' => [
				'id' => 'page',
			],
			'file-archive' => [
				'id' => 'folder',
			],
			'file-archive-open' => [
				'id' => 'folder_go',
			],
			'filter' => [
				'id' => 'find',
			],
			'flag' => [
				'id' => 'flag_blue',
			],
			'floppy' => [
				'id' => 'disk',
			],
			'font-color' => [
				'id' => 'font',
			],
			'forward' => [
				'id' => 'control_fastforward',
			],
			'forward_step' => [
				'id' => 'control_end',
			],
			'fullscreen' => [
				'id' => 'application_get',
			],
			//google in defaults
			//group in defaults
			'h1' => [
				'id' => 'text_heading_1',
			],
			'h2' => [
				'id' => 'text_heading_2',
			],
			'h3' => [
				'id' => 'text_heading_3',
			],
			//help in defaults
			'history' => [
				'id' => 'clock',
			],
			'home' => [
				'id' => 'house',
			],
			'horizontal-rule' => [
				'id' => 'text_horizontalrule',
			],
			//html in defaults
			//image in defaults
			'import' => [
				'id' => 'upload',
			],
			'indent' => [
				'id' => 'text_indent',
			],
			'index' => [
				'id' => 'table_refresh',
			],
			//information in defaults
			'italic' => [
				'id' => 'text_italic',
			],
			'language' => [
				'id' => 'world',
			],
			//link in defaults
			'link-external' => [
				'id' => 'external_link',
				'append' => '.gif'
			],
			'link-external-alt' => [
				'id' => 'page_link',
			],
			'list' => [
				'id' => 'application_view_list',
			],
			'list-numbered' => [
				'id' => 'text_list_numbers',
			],
			'lock' => [
				'id' => 'lock',
			],
			'log' => [
				'id' => 'book',
			],
			'login' => [
				'id' => 'task_received',
			],
			'logout' => [
				'id' => 'task_submitted',
			],
			'mailbox' => [
				'id' => 'email',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'webmail/mailbox',
						'prepend' => 'img/',
						'append' => '.gif'
					]
				]
			],
			//map in defaults
			'menu' => [
				'id' => 'application_side_tree',
			],
			'menu-extra' => [
				'id' => 'resultset_down',
			],
			'menuitem' => [
				'id' => 'arrow_right',
			],
			'merge' => [
				'id' => 'arrow_merge',
			],
			'minimize' => [
				'id' => 'arrow_in',
			],
			'minus' => [
				'id' => 'delete',
			],
			//module in defaults
			//money in defaults
			'more' => [
				'id' => 'resultset_down',
			],
			'move' => [
				'id' => 'task_submitted',
			],
			'music' => [
				'id' => '/mime/mp3',
			],
			'next' => [
				'id' => 'arrow_right',
			],
			'notepad' => [
				'id' => 'disk',
			],
			'notification' => [
				'id' => 'announce',
			],
			'off' => [
				'id' => 'delete',
			],
			'ok' => [
				'id' => 'accept',
			],
			'outdent' => [
				'id' => 'text_indent_remove',
			],
			'page-break' => [
				'id' => 'page_break',
			],
			'paste' => [
				'id' => 'control_pause',
			],
			'pause' => [
				'id' => 'control_pause',
			],
			'paypal' => [
				'id' => 'money',
			],
			'pdf' => [
				'id' => 'page_white_acrobat',
			],
			//pencil in defaults
			'permission' => [
				'id' => 'key',
			],
			'play' => [
				'id' => 'control_play',
			],
			//plugin in defaults
			'popup' => [
				'id' => 'application_view_columns',
			],
			'post' => [
				'id' => 'pencil_add',
			],
			'powerpoint' => [
				'id' => 'mime/ppt',
			],
			'previous' => [
				'id' => 'arrow_left',
			],
			'print' => [
				'id' => 'printer',
			],
			//quotes in defaults
			'ranking' => [
				'id' => 'star',
			],
			'refresh' => [
				'id' => 'arrow_refresh',
			],
			'remove' => [
				'id' => 'cross',
			],
			'repeat' => [
				'id' => 'arrow_redo',
			],
			'rss' => [
				'id' => 'feed',
			],
			'scissors' => [
					'id' => 'cut',
			],
			'screencapture' => [
				'id' => 'camera',
			],
			'search' => [
				'id' => 'magnifier',
			],
			'selectall' => [
				'id' => 'page-lightning',
			],
			'send' => [
				'id' => 'email_go',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'messages48x48'
					]
				]
			],
			'settings' => [
				'id' => 'wrench',
				'size' => 1,
				'sizes' => [
					3 => [
						'id' => 'webmail/settings',
						'prepend' => 'img/',
						'append' => '.gif'
					]
				]
			],
			'share' => [
				'id' => 'share_link',
			],
			//sharethis in defaults
			//skype in defaults
			'smile' => [
				'prepend' => 'img/smiles/',
				'id' => 'icon_smile',
				'append' => '.png'
			],
			'sort' => [
				'id' => 'resultset',
			],
			'sort-down' => [
				'id' => 'resultset_down',
			],
			'sort-up' => [
				'id' => 'resultset_up',
			],
			//star in defaults
			'star-empty' => [
				'id' => 'star_grey',
			],
			'star-empty-selected' => [
				'id' => 'star_grey_selected',
			],
			'star-half' => [
				'id' => 'star_half',
			],
			//don't use half star for rating.tpl since there is no selected half star
			'star-half-rating' => [
				'id' => 'star_grey',
			],
			'star-half-selected' => [
				'id' => 'star_grey_selected',
			],
			'star-selected' => [
				'id' => 'star_selected',
			],
			'status-open' => [
				'id' => 'status_open',
				'append' => '.gif'
			],
			'status-pending' => [
				'id' => 'status_pending',
				'append' => '.gif'
			],
			'status-closed' => [
				'id' => 'status_closed',
				'append' => '.gif'
			],
			'stop' => [
				'id' => 'control_stop',
			],
			'stop-watching' => [
				'id' => 'no_eye',
			],
			'strikethrough' => [
				'id' => 'text_strikethrough',
			],
			'structure' => [
				'id' => 'chart_organisation',
			],
			'subscript' => [
				'id' => 'text_subscript',
			],
			'success' => [
				'id' => 'tick',
			],
			'superscript' => [
				'id' => 'text_superscript',
			],
			//table in defaults
			'tag' => [
				'id' => 'tag_blue',
			],
			'tags' => [
				'id' => 'tag_blue',
			],
			'textfile' => [
				'id' => 'page',
			],
			'th-large' => [
				'id' => 'application_view_columns',
			],
			'th-list' => [
				'id' => 'text_list_bullets',
			],
			'three-d' => [
				'id' => 'application_side_tree',
			],
			'thumbs-down' => [
				'prepend' => 'vendor_bundled/vendor/ckeditor/ckeditor/plugins/smiley/images/',
				'id' => 'thumbs_down',
			],
			'thumbs-up' => [
				'prepend' => 'vendor_bundled/vendor/ckeditor/ckeditor/plugins/smiley/images/',
				'id' => 'thumbs_up',
			],
			'title' => [
				'id' => 'text_padding_top',
			],
			'toggle-off' => [
				'id' => 'accept',
			],
			'toggle-on' => [
				'id' => 'delete',
			],
			'toggle-left' => [
				'id' => 'resultset_first',
			],
			'toggle-right' => [
				'id' => 'resultset_last',
			],
			'trackers' => [
				'id' => 'database',
			],
			'translate' => [
				'id' => 'world_edit',
			],
			'trash' => [
				'id' => 'bin',
			],
			'tv' => [
				'id' => 'television',
			],
			'twitter' => [
				'id' => 'twitter',
				'size' => 1,
				'sizes' => [
					2 => [
						'id' => 'twitter_t_logo_32'
					]
				]
			],
			'underline' => [
				'id' => 'text_underline',
			],
			'undo' => [
				'id' => 'arrow_undo',
			],
			'unlink' => [
				'id' => 'lock_delete',
			],
			'unlock' => [
				'id' => 'lock_open',
			],
			'up' => [
				'id' => 'resultset_up',
			],
			'video' => [
				'id' => 'mime/mpg',
			],
			'video_file' => [
				'id' => 'mime/mpg',
			],
			//vimeo in defaults
			'view' => [
				'id' => 'magnifier',
			],
			'warning' => [
				'id' => 'sticky',
			],
			'watch' => [
				'id' => 'eye',
			],
			'watch-group' => [
				'id' => 'eye_group',
			],
			'wizard' => [
				'id' => 'wizard16x16',
			],
			'word' => [
				'id' => 'mime/doc',
			],
			'wysiwyg' => [
				'id' => 'text_dropcaps',
			],
			//youtube in defaults
			'zip' => [
				'id' => 'mime/zip',
			],
		],
		'defaults' => [
			'add',
			'anchor',
			'book',
			'bug',
			'calendar',
			'computer',
			'cut',
			'database',
			'font',
			'google',
			'group',
			'help',
			'html',
			'image',
			'information',
			'link',
			'map',
			'module',
			'money',
			'pencil',
			'plugin',
			'quotes',
			'sharethis',
			'skype',
			'star',
			'table',
			'user',
			'vimeo',
			'wrench',
			'youtube',
		]
	];
}
