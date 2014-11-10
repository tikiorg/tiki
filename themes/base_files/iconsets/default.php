<?php 
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//The default icon set associates icon names to icon fonts. It is used as the fallback for all other icon sets.


// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$settings = array( //Settings for the icon set
	'iconset_name' => tr('Default (Font-awesome)'), //Mandatory, will be displayed as Icon set option in the Look&Feel admin UI
	'iconset_description' => tr('The default system icon set usind Font-awesome fonts'), //TODO display as Icon set description in the Look&Feel admin UI
	'icon_tag' => 'span', //The default html tag for the icons in the icon set. TODO: Override for each icon using the tag option
);

$icons = array( //Icons of the icon set
	'save' => array( //This is the definition of an icon in the icon set. For this one icon all options are explained. The key must be unique, it is the "name" parameter at the icon function, so eg: {icon name="save"} will find 'save' in the array and apply the specified configuration
		'class' => 'fa fa-save fa-fw', //Class used for a font icon (font-awesome, glyphicon, etc)
		'image_src' => '', //For images: specify the path inside your Tiki installation including the file name, eg: '/img/icons/disk.png'
	),
	'actions' => array( 
		'class' => 'fa fa-play-circle fa-fw',
	),
	'add' => array( 
		'class' => 'fa fa-plus fa-fw',
	),
	'admin_ads' => array( 
		'class' => 'fa fa-film fa-fw',
	),
	'admin_articles' => array( 
		'class' => 'fa fa-font fa-fw',
	),
	'admin_blogs' => array( 
		'class' => 'fa fa-bold fa-fw',
	),
	'admin_calendar' => array( 
		'class' => 'fa fa-calendar fa-fw',
	),
	'admin_category' => array( 
		'class' => 'fa fa-sitemap fa-fw',
	),
	'admin_comments' => array( 
		'class' => 'fa fa-comment fa-fw',
	),
	'admin_community' => array( 
		'class' => 'fa fa-group fa-fw',
	),
	'admin_connect' => array( 
		'class' => 'fa fa-link fa-fw',
	),
	'admin_copyright' => array( 
		'class' => 'fa fa-copyright fa-fw',
	),
	'admin_directory' => array( 
		'class' => 'fa fa-folder-o fa-fw',
	),
	'admin_faqs' => array( 
		'class' => 'fa fa-question fa-fw',
	),
	'admin_features' => array( 
		'class' => 'fa fa-power-off fa-fw',
	),
	'admin_fgal' => array( 
		'class' => 'fa fa-folder-open fa-fw',
	),
	'admin_forums' => array( 
		'class' => 'fa fa-comments fa-fw',
	),
	'admin_freetags' => array( 
		'class' => 'fa fa-tags fa-fw',
	),
	'admin_gal' => array( 
		'class' => 'fa fa-file-image-o fa-fw',
	),
	'admin_general' => array( 
		'class' => 'fa fa-cog fa-fw',
	),
	'admin_i18n' => array( 
		'class' => 'fa fa-globe fa-fw',
	),
	'admin_intertiki' => array( 
		'class' => 'fa fa-exchange fa-fw',
	),
	'admin_login' => array( 
		'class' => 'fa fa-sign-in fa-fw',
	),
	'admin_look' => array( 
		'class' => 'fa fa-image fa-fw',
	),
	'admin_maps' => array( 
		'class' => 'fa fa-map-marker fa-fw',
	),
	'admin_messages' => array( 
		'class' => 'fa fa-envelope-o fa-fw',
	),
	'admin_metatags' => array( 
		'class' => 'fa fa-tag fa-fw',
	),
	'admin_module' => array( 
		'class' => 'fa fa-cogs fa-fw',
	),
	'admin_payment' => array( 
		'class' => 'fa fa-credit-card fa-fw',
	),
	'admin_performance' => array( 
		'class' => 'fa fa-tachometer fa-fw',
	),
	'admin_polls' => array( 
		'class' => 'fa fa-tasks fa-fw',
	),
	'admin_profiles' => array( 
		'class' => 'fa fa-cubes fa-fw',
	),
	'admin_rating' => array( 
		'class' => 'fa fa-check-square fa-fw',
	),
	'admin_rss' => array( 
		'class' => 'fa fa-rss fa-fw',
	),
	'admin_score' => array( 
		'class' => 'fa fa-trophy fa-fw',
	),
	'admin_search' => array( 
		'class' => 'fa fa-search fa-fw',
	),
	'admin_semantic' => array( 
		'class' => 'fa fa-arrows-h fa-fw',
	),
	'admin_security' => array( 
		'class' => 'fa fa-lock fa-fw',
	),
	'admin_sefurl' => array( 
		'class' => 'fa fa-search-plus fa-fw',
	),
	'admin_share' => array( 
		'class' => 'fa fa-share-alt fa-fw',
	),
	'admin_socialnetworks' => array( 
		'class' => 'fa fa-thumbs-up fa-fw',
	),
	'admin_textarea' => array( 
		'class' => 'fa fa-edit fa-fw',
	),
	'admin_trackers' => array( 
		'class' => 'fa fa-database fa-fw',
	),
	'admin_userfiles' => array( 
		'class' => 'fa fa-cog fa-fw',
	),
	'admin_video' => array( 
		'class' => 'fa fa-video-camera fa-fw',
	),
	'admin_webmail' => array( 
		'class' => 'fa fa-inbox fa-fw',
	),
	'admin_webservices' => array( 
		'class' => 'fa fa-cog fa-fw',
	),
	'admin_wiki' => array( 
		'class' => 'fa fa-file-text-o fa-fw',
	),
	'admin_workspace' => array( 
		'class' => 'fa fa-desktop fa-fw',
	),
	'admin_wysiwyg' => array( 
		'class' => 'fa fa-align-center fa-fw',
	),
	'administer' => array( 
		'class' => 'fa fa-cog fa-fw',
	),
	'backlink' => array(
		'class' => 'fa fa-reply fa-fw',
	),
	'bug' => array(
		'class' => 'fa fa-bug fa-fw',
	),
	'cache' => array(
		'class' => 'fa fa-trash-o fa-fw',
	),
	'check' => array(
		'class' => 'fa fa-check-square-o fa-fw',
	),
	'clone' => array(
		'class' => 'fa fa-clipboard fa-fw',
	),
	'comments' => array(
		'class' => 'fa fa-comments-o fa-fw',
	),
	'copy' => array(
		'class' => 'fa fa-copy fa-fw',
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
	'envelope' => array(
		'class' => 'fa fa-envelope-o fa-fw',
	),
	'error' => array(
		'class' => 'fa fa-exclamation-circle fa-fw',
	),
	'events' => array(
		'class' => 'fa fa-clock-o fa-fw',
	),	
	'export' => array( 
		'class' => 'fa fa-upload fa-fw',
	),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o fa-fw',
	),
	'filter' => array( 
		'class' => 'fa fa-filter fa-fw',
	),
	'group' => array( 
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
	'inbox' => array( 
		'class' => 'fa fa-inbox fa-fw',
	),
	'index' => array( 
		'class' => 'fa fa-spinner fa-fw',
	),
	'info' => array( 
		'class' => 'fa fa-info-circle fa-fw',
	),
	'list' => array( 
		'class' => 'fa fa-list fa-fw',
	),
	'link' => array( 
		'class' => 'fa fa-link fa-fw',
	),
	'link-external' => array(
		'class' => 'fa fa-external-link fa-fw',
	),
	'log' => array(
		'class' => 'fa fa-history fa-fw',
	),
	'menu' => array(
		'class' => 'fa fa-bars fa-fw',
	),
	'menuitem' => array(
		'class' => 'fa fa-minus fa-fw',
	),
	'module' => array(
		'class' => 'fa fa-cogs fa-fw',
	),
	'notepad' => array(
		'class' => 'fa fa-file-text-o fa-fw',
	),
	'notification' => array(
		'class' => 'fa fa-bell-o fa-fw',
	),
	'off' => array(
		'class' => 'fa fa-power-off fa-fw',
	),
	'ok' => array(
		'class' => 'fa fa-check-circle fa-fw',
	),
	'pdf' => array(
		'class' => 'fa fa-file-pdf-o fa-fw',
	),
	'permission' => array(
		'class' => 'fa fa-key fa-fw',
	),
	'permission-active' => array(
		'class' => 'fa fa-key fa-fw',
	),
	'plugin' => array(
		'class' => 'fa fa-puzzle-piece fa-fw',
	),
	'post' => array(
		'class' => 'fa fa-pencil fa-fw',
	),
	'print' => array(
		'class' => 'fa fa-print fa-fw',
	),
	'redo' => array(
		'class' => 'fa fa-share fa-fw',
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
	'screencapture' => array( 
		'class' => 'fa fa-camera fa-fw',
	),
	'search' => array( 
		'class' => 'fa fa-search fa-fw',
	),
	'settings' => array( 
		'class' => 'fa fa-wrench fa-fw',
	),
	'share' => array(
		'class' => 'fa fa-share fa-fw',
	),
	'sort' => array(
		'class' => 'fa fa-sort fa-fw',
	),
	'sort-down' => array(
		'class' => 'fa fa-sort-desc fa-fw',
	),
	'sort-up' => array(
		'class' => 'fa fa-sort-asc fa-fw',
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
	'theme' => array(
		'class' => 'fa fa-image fa-fw',
	),
	'themegenerator' => array(
		'class' => 'fa fa-paint-brush fa-fw',
	),
	'trackers' => array(
		'class' => 'fa fa-database fa-fw',
	),
	'trackerfields' => array(
		'class' => 'fa fa-th-list fa-fw',
	),
	'translate' => array(
		'class' => 'fa fa-flag fa-fw',
	),
	'trash' => array(
		'class' => 'fa fa-trash-o fa-fw',
	),
	'undo' => array(
		'class' => 'fa fa-reply fa-fw',
	),
	'user' => array(
		'class' => 'fa fa-user fa-fw',
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
		'class' => 'fa fa-group fa-fw',
	),
	'wizard' => array(
		'class' => 'fa fa-magic fa-fw',
	),
);