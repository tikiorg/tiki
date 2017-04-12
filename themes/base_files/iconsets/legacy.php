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
			//add in defaults
			'admin' => array(
					'id' => 'wrench',
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
			'admin_stats' => array(
				'id' => 'large/stats48x48',
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
			'align-center' => array(
				'id' => 'text_align_center',
			),
			'align-justify' => array(
				'id' => 'text_align_justify',
			),
			'align-left' => array(
				'id' => 'text_align_left',
			),
			'align-right' => array(
				'id' => 'text_align_right',
			),
			//anchor in defaults
			'arrow-up' => array(
				'id' => 'arrow-up',
			),
			'articles' => array(
				'id' => 'newspaper_go',
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
			'background-color' => array(
				'id' => 'palette_bg',
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
				'id' => 'ban_remove',
			),
			'bold' => array(
				'id' => 'text_bold',
			),
			//book in defaults
			'bookmark' => array(
				'id' => 'book',
			),
			'box' => array(
				'id' => 'layout_header',
			),
			//bug in defaults
			'bullhorn' => array(
				'id' => 'announce',
			),
			//calendar in defaults
			'caret-left' => array(
				'id' => 'resultset_previous',
			),
			'caret-right' => array(
				'id' => 'resultset_next',
			),
			'cart' => array(
				'id' => 'cart_add',
			),
			'chart' => array(
				'id' => 'chart_curve',
			),
			'check' => array(
				'id' => 'select',
				'append' => '.gif'
			),
			'code' => array(
				'id' => 'page_white_code',
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
			'comment' => array(
				'id' => 'comment_add',
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
			//computer in defaults
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
			'copyright' => array(
				'id' => 'shield',
			),
			'create' => array(
				'id' => 'add',
			),
			'dashboard' => array(
				'id' => 'application_view_columns',
			),
			//database in defaults
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
			'education' => array(
				'id' => 'text_signature',
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
			'erase' => array(
					'id' => 'page_edit',
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
			'font-color' => array(
				'id' => 'font',
			),
			'forward' => array(
				'id' => 'control_fastforward',
			),
			'forward_step' => array(
				'id' => 'control_end',
			),
			'fullscreen' => array(
				'id' => 'application_get',
			),
			//google in defaults
			//group in defaults
			'h1' => array(
				'id' => 'text_heading_1',
			),
			'h2' => array(
				'id' => 'text_heading_2',
			),
			'h3' => array(
				'id' => 'text_heading_3',
			),
			//help in defaults
			'history' => array(
				'id' => 'clock',
			),
			'home' => array(
				'id' => 'house',
			),
			'horizontal-rule' => array(
				'id' => 'text_horizontalrule',
			),
			//html in defaults
			//image in defaults
			'import' => array(
				'id' => 'upload',
			),
			'indent' => array(
				'id' => 'text_indent',
			),
			'index' => array(
				'id' => 'table_refresh',
			),
			//information in defaults
			'italic' => array(
				'id' => 'text_italic',
			),
			'language' => array(
				'id' => 'world',
			),
			//link in defaults
			'link-external' => array(
				'id' => 'external_link',
				'append' => '.gif'
			),
			'link-external-alt' => array(
				'id' => 'page_link',
			),
			'list' => array(
				'id' => 'application_view_list',
			),
			'list-numbered' => array(
				'id' => 'text_list_numbers',
			),
			'lock' => array(
				'id' => 'lock',
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
			//map in defaults
			'menu' => array(
				'id' => 'application_side_tree',
			),
			'menu-extra' => array(
				'id' => 'resultset_down',
			),
			'menuitem' => array(
				'id' => 'arrow_right',
			),
			'merge' => array(
				'id' => 'arrow_merge',
			),
			'minimize' => array(
				'id' => 'arrow_in',
			),
			'minus' => array(
				'id' => 'delete',
			),
			//module in defaults
			//money in defaults
			'more' => array(
				'id' => 'resultset_down',
			),
			'move' => array(
				'id' => 'task_submitted',
			),
			'music' => array(
				'id' => '/mime/mp3',
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
			'outdent' => array(
				'id' => 'text_indent_remove',
			),
			'page-break' => array(
				'id' => 'page_break',
			),
			'paste' => array(
				'id' => 'control_pause',
			),
			'pause' => array(
				'id' => 'control_pause',
			),
			'paypal' => array(
				'id' => 'money',
			),
			'pdf' => array(
				'id' => 'page_white_acrobat',
			),
			//pencil in defaults
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
			//quotes in defaults
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
			'scissors' => array(
					'id' => 'cut',
			),
			'screencapture' => array(
				'id' => 'camera',
			),
			'search' => array(
				'id' => 'magnifier',
			),
			'selectall' => array(
				'id' => 'page-lightning',
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
			//skype in defaults
			'smile' => array(
				'prepend' => 'img/smiles/',
				'id' => 'icon_smile',
				'append' => '.gif'
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
			'status-open' => array(
				'id' => 'status_open',
				'append' => '.gif'
			),
			'status-pending' => array(
				'id' => 'status_pending',
				'append' => '.gif'
			),
			'status-closed' => array(
				'id' => 'status_closed',
				'append' => '.gif'
			),
			'stop' => array(
				'id' => 'control_stop',
			),
			'stop-watching' => array(
				'id' => 'no_eye',
			),
			'strikethrough' => array(
				'id' => 'text_strikethrough',
			),
			'structure' => array(
				'id' => 'chart_organisation',
			),
			'subscript' => array(
				'id' => 'text_subscript',
			),
			'success' => array(
				'id' => 'tick',
			),
			'superscript' => array(
				'id' => 'text_superscript',
			),
			//table in defaults
			'tag' => array(
				'id' => 'tag_blue',
			),
			'tags' => array(
				'id' => 'tag_blue',
			),
			'textfile' => array(
				'id' => 'page',
			),
			'th-large' => array(
				'id' => 'application_view_columns',
			),
			'th-list' => array(
				'id' => 'text_list_bullets',
			),
			'three-d' => array(
				'id' => 'application_side_tree',
			),
			'thumbs-down' => array(
				'prepend' => 'vendor/ckeditor/ckeditor/plugins/smiley/images/',
				'id' => 'thumbs_down',
			),
			'thumbs-up' => array(
				'prepend' => 'vendor/ckeditor/ckeditor/plugins/smiley/images/',
				'id' => 'thumbs_up',
			),
			'title' => array(
				'id' => 'text_padding_top',
			),
			'toggle-off' => array(
				'id' => 'accept',
			),
			'toggle-on' => array(
				'id' => 'delete',
			),
            'toggle-left' => array(
                'id' => 'resultset_first',
            ),
            'toggle-right' => array(
                'id' => 'resultset_last',
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
			'tv' => array(
				'id' => 'television',
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
			'underline' => array(
				'id' => 'text_underline',
			),
			'undo' => array(
				'id' => 'arrow_undo',
			),
			'unlink' => array(
				'id' => 'lock_delete',
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
			'video_file' => array(
				'id' => 'mime/mpg',
			),
			//vimeo in defaults
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
				'id' => 'text_dropcaps',
			),
			//youtube in defaults
			'zip' => array(
				'id' => 'mime/zip',
			),
		),
		'defaults' => array(
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
		)
	);
}
