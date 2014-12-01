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
	'iconset_name' => tr('Legacy'),
	'iconset_description' => tr('Legacy (pre Tiki14) icons, mainly using famfamfam images'),
	'icon_tag' => 'img',
);

$icons = array(
	'actions' => array(
		'image_src' => 'img/icons/application_form.png',
	),
	'add' => array(
		'image_src' => 'img/icons/large/icon-configuration.png',
	),
	'admin_ads' => array(
		'image_src' => 'img/icons/large/ads.png',
	),
	'admin_articles' => array(
		'image_src' => 'img/icons/large/stock_bold.png',
	),
	'admin_blogs' => array(
		'image_src' => 'img/icons/large/blogs.png',
	),
	'admin_calendar' => array(
		'image_src' => 'img/icons/large/date.png',
	),
	'admin_category' => array(
		'image_src' => 'img/icons/large/categories.png',
	),
	'admin_comments' => array(
		'image_src' => 'img/icons/large/comments.png',
	),
	'admin_community' => array(
		'image_src' => 'img/icons/large/users.png',
	),
	'admin_connect' => array(
		'image_src' => 'img/icons/large/gnome-globe.png',
	),
	'admin_copyright' => array(
		'image_src' => 'img/icons/large/copyright.png',
	),
	'admin_directory' => array(
		'image_src' => 'img/icons/large/gnome-fs-server.png',
	),
	'admin_faqs' => array(
		'image_src' => 'img/icons/large/stock_dialog_question.png',
	),
	'admin_features' => array(
		'image_src' => 'img/icons/large/boot.png',
	),
	'admin_fgal' => array(
		'image_src' => 'img/icons/large/file-manager.png',
	),
	'admin_forums' => array(
		'image_src' => 'img/icons/large/stock_index.png',
	),
	'admin_freetags' => array(
		'image_src' => 'img/icons/large/vcard.png',
	),
	'admin_gal' => array(
		'image_src' => 'img/icons/large/stock_select-color.png',
	),
	'admin_general' => array(
		'image_src' => 'img/icons/large/icon-configuration.png',
		'position' => '0px -15px;',
	),
	'admin_i18n' => array(
		'image_src' => 'img/icons/large/i18n.png',
	),
	'admin_intertiki' => array(
		'image_src' => 'img/icons/large/intertiki.png',
	),
	'admin_login' => array(
		'image_src' => 'img/icons/large/stock_quit.png',
	),
	'admin_look' => array(
		'image_src' => 'img/icons/large/gnome-settings-background.png',
	),
	'admin_maps' => array(
		'image_src' => 'img/icons/large/maps.png',
	),
	'admin_messages' => array(
		'image_src' => 'img/icons/large/messages.png',
	),
	'admin_metatags' => array(
		'image_src' => 'img/icons/large/metatags.png',
	),
	'admin_module' => array(
		'image_src' => 'img/icons/large/display-capplet.png',
	),
	'admin_payment' => array(
		'image_src' => 'img/icons/large/payment.png',
	),
	'admin_performance' => array(
		'image_src' => 'img/icons/large/performance.png',
	),
	'admin_polls' => array(
		'image_src' => 'img/icons/large/stock_missing-image.png',
	),
	'admin_profiles' => array(
		'image_src' => 'img/icons/large/profiles.png',
	),
	'admin_rating' => array(
		'image_src' => 'img/icons/large/rating.png',
	),
	'admin_rss' => array(
		'image_src' => 'img/icons/large/feed-icon.png',
	),
	'admin_score' => array(
		'image_src' => 'img/icons/large/stock_about.png',
	),
	'admin_search' => array(
		'image_src' => 'img/icons/large/xfce4-appfinder.png',
	),
	'admin_semantic' => array(
		'image_src' => 'img/icons/large/semantic.png',
	),
	'admin_security' => array(
		'image_src' => 'img/icons/large/gnome-lockscreen.png',
	),
	'admin_sefurl' => array(
		'image_src' => 'img/icons/large/goto.png',
	),
	'admin_share' => array(
		'image_src' => 'img/icons/large/stock_contact.png',
	),
	'admin_socialnetworks' => array(
		'image_src' => 'img/icons/large/socialnetworks.png',
	),
	'admin_textarea' => array(
		'image_src' => 'img/icons/large/editing.png',
	),
	'admin_trackers' => array(
		'image_src' => 'img/icons/large/gnome-settings-font.png',
	),
	'admin_userfiles' => array(
		'image_src' => 'img/icons/large/userfiles.png',
	),
	'admin_video' => array(
		'image_src' => 'img/icons/large/gnome-camera-video-32.png',
	),
	'admin_webmail' => array(
		'image_src' => 'img/icons/large/evolution.png',
	),
	'admin_webservices' => array(
		'image_src' => 'img/icons/large/webservices.png',
	),
	'admin_wiki' => array(
		'image_src' => 'img/icons/large/wikipages.png',
	),
	'admin_workspace' => array(
		'image_src' => 'img/icons/large/areas.png',
	),
	'admin_wysiwyg' => array(
		'image_src' => 'img/icons/large/wysiwyg.png',
	),
	'administer' => array(
		'image_src' => 'img/icons/wrench.png',
	),
	'backlink' => array(
		'image_src' => 'img/icons/arrow_turn_left.png',
	),
	'bug' => array(
		'image_src' => 'img/icons/bug.png',
	),
	'cache' => array(
		'image_src' => 'img/icons/database_refresh.png',
	),
	'change' => array(
		'image_src' => 'img/icons/pencil.png',
	),
	'check' => array(
		'image_src' => 'img/icons/select.gif',
	),
	'comments' => array(
		'image_src' => 'img/icons/comments.png',
	),
	'copy' => array(
		'image_src' => 'img/icons/ico_copy.gif',
	),
	'create' => array(
		'image_src' => 'img/icons/add.png',
	),
	'delete' => array(
		'image_src' => 'img/icons/cross.png',
	),
	'disable' => array(
		'image_src' => 'img/icons/delete.png',
	),
	'documentation' => array(
		'image_src' => 'img/icons/book_open.png',
	),
	'edit' => array(
		'image_src' => 'img/icons/page_edit.png',
	),
	'enable' => array(
		'image_src' => 'img/icons/accept.png',
	),
	'envelope' => array(
		'image_src' => 'img/icons/email.png',
	),
	'error' => array(
		'image_src' => 'img/icons/exclamation.png',
	),
	'export' => array(
		'image_src' => 'img/icons/disk.png',
	),
	'file-archive' => array(
		'image_src' => 'img/icons/folder.png',
	),
	'group' => array(
		'image_src' => 'img/icons/group.png',
	),
	'group-watch' => array(
		'image_src' => 'img/icons/eye_group.png',
	),
	'help' => array(
		'image_src' => 'img/icons/help.png',
	),
	'history' => array(
		'image_src' => 'img/icons/database.png',
	),
	'import' => array(
		'image_src' => 'img/icons/upload.png',
	),
	'index' => array(
		'image_src' => 'img/icons/table_refresh.png',
	),
	'information' => array(
		'image_src' => 'img/icons/information.png',
	),
	'link' => array(
		'image_src' => 'img/icons/link.png',
	),
	'link-external' => array(
		'image_src' => 'img/icons/external_link.gif',
	),
	'list' => array(
		'image_src' => 'img/icons/application_view_list.png',
	),
	'lock' => array(
		'image_src' => 'img/icons/lock_add.png',
	),
	'log' => array(
		'image_src' => 'img/icons/book.png',
	),
	'menu' => array(
		'image_src' => 'img/icons/application_side_tree.png',
	),
	'menuitem' => array(
		'image_src' => 'img/icons/omo.png',
	),
	'module' => array(
		'image_src' => 'img/icons/module.png',
	),
	'move' => array(
		'image_src' => 'img/icons/task_submitted.png',
	),
	'next' => array(
		'image_src' => 'img/icons/arrow_right.png',
	),
	'notepad' => array(
		'image_src' => 'img/icons/disk.png',
	),
	'notification' => array(
		'image_src' => 'img/icons/announce.png',
	),
	'ok' => array(
		'image_src' => 'img/icons/accept.png',
	),
	'pdf' => array(
		'image_src' => 'img/icons/page_white_acrobat.png',
	),
	'permission' => array(
		'image_src' => 'img/icons/key.png',
	),
	'plugin' => array(
		'image_src' => 'img/icons/plugin.png',
	),
	'post' => array(
		'image_src' => 'img/icons/pencil_add.png',
	),
	'previous' => array(
		'image_src' => 'img/icons/arrow_left.png',
	),
	'print' => array(
		'image_src' => 'img/icons/printer.png',
	),
	'redo' => array(
		'image_src' => 'img/icons/arrow_redo.png',
	),
	'refresh' => array(
		'image_src' => 'img/icons/arrow_refresh.png',
	),
	'remove' => array(
		'image_src' => 'img/icons/cross.png',
	),
	'rss' => array(
		'image_src' => 'img/icons/feed.png',
	),
	'screencapture' => array(
		'image_src' => 'img/icons/camera.png',
	),
	'search' => array(
		'image_src' => 'img/icons/magnifier.png',
	),
	'settings' => array(
		'image_src' => 'img/icons/wrench.png',
	),
	'share' => array(
		'image_src' => 'img/icons/sharethis.png',
	),
	'sort' => array(
		'image_src' => 'img/icons/resultset.png',
	),
	'sort-down' => array(
		'image_src' => 'img/icons/resultset_down.png',
	),
	'sort-up' => array(
		'image_src' => 'img/icons/resultset_up.png',
	),
	'stop-watching' => array(
		'image_src' => 'img/icons/no_eye.png',
	),
	'success' => array(
		'image_src' => 'img/icons/tick.png',
	),
	'tag' => array(
		'image_src' => 'img/icons/tag_blue.png',
	),
	'theme' => array(
		'image_src' => 'img/icons/image.png',
	),
	'themegenerator' => array(
		'image_src' => 'img/icons/palette.png',
	),
	'translate' => array(
		'image_src' => 'img/icons/world_edit.png',
	),
	'trash' => array(
		'image_src' => 'img/icons/bin.png',
	),
	'undo' => array(
		'image_src' => 'img/icons/arrow_undo.png',
	),
	'unlock' => array(
		'image_src' => 'img/icons/lock_break.png',
	),
	'user' => array(
		'image_src' => 'img/icons/user.png',
	),
	'view' => array(
		'image_src' => 'img/icons/magnifier.png',
	),
	'warning' => array(
		'image_src' => 'img/icons/sticky.png',
	),
	'watch' => array(
		'image_src' => 'img/icons/eye.png',
	),
	'watch-group' => array(
		'image_src' => 'img/icons/eye_group.png',
	),
	'wizard' => array(
		'image_src' => 'img/icons/wizard16x16.png',
	),
);