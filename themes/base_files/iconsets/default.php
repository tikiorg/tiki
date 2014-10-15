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

$iconset = array(
	'_settings' => array( //Icon set settings
		'iconset_name' => tr('Default'), //mandatory, this will be displayed as Icon set option in the Look&Feel admin UI
		'iconset_description' => tr('The default system icon set'), //this will be displayed as Icon set description in the Look&Feel admin UI
		'icon_path_svg' => '', //TODO The default path for svgs in the icon set so that you don't need to specify for each icon. You can override this for each icon using the path_svg option
		'icon_path_font' => '', //TODO The default path for font icons in the icon set so that you don't need to specify for each icon. You can override this for each icon using the path_font option
		'icon_path_image' => '' , //The default path for images in the icon set so that you don't need to specify for each icon. You can override this for each icon using the image_path option
		'icon_tag' => 'span', //The default html tag for the icons in the icon set. TODO: You can override this for each icon using the tag option
		'icon_type' => 'font', //TODO The default file type for the icons in the icon set. Possible values: svg, font, png, jpeg. You can override this for each icon using the type option. Type sequence: 1)svg 2)font 3)image, so icon function will first use what is in the path_svg, than path_font, than path_png
	),
	'save' => array( //This is the definition of an icon in the icon set. For this one icon all options are explained. The key must be unique, it is the "name" parameter at the icon function, so eg: {icon name="save"} will find 'save' in the array and apply the specified configuration
		'class' => 'fa fa-save fa-fw', //this is the class used for a glyphicon or a font-awesome icon  
		'alt' => tr('Save'),  //alternate text for an image, if the image cannot be displayed (http://www.w3schools.com/tags/att_img_alt.asp)
		'svg_path' => '', //TODO Specify the URL for an svg definition. Optional, if not defined, the default defined at _settings is used.
		'font_path' => '', //TODO Specify the URL for font icon. Optional, if not defined, the default defined at _settings is used.
		'image_path' => '', //TODO Specify the URL of an image. Optional, if not defined, the default defined at _settings is used.
		'image_file_name' => '', //Specify the name of an image file, eg: 'disk.png' 
		'tag' => 'span', //TODO specify an html tag
	),
	'actions' => array( 
		'class' => 'fa fa-play-circle fa-fw',
	),
	'add' => array( 
		'class' => 'fa fa-plus fa-fw',
	),
	'bell' => array(
		'class' => 'fa fa-bell-o fa-fw',
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
	'external-link' => array(
		'class' => 'fa fa-external-link fa-fw',
	),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o fa-fw',
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
	'info' => array( 
		'class' => 'fa fa-info-circle fa-fw',
	),
	'list' => array( 
		'class' => 'fa fa-list fa-fw',
	),
	'menu' => array(
		'class' => 'fa fa-bars fa-fw',
	),
	'permission' => array(
		'class' => 'fa fa-key fa-fw',
	),
	'post' => array(
		'class' => 'fa fa-pencil fa-fw',
	),
	'print' => array(
		'class' => 'fa fa-print fa-fw',
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
	'search' => array( 
		'class' => 'fa fa-search fa-fw',
	),
	'settings' => array( 
		'class' => 'fa fa-wrench fa-fw',
	),
	'share' => array(
		'class' => 'fa fa-share fa-fw',
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
	'trackers' => array(
		'class' => 'fa fa-database fa-fw',
	),
	'trackerfields' => array(
		'class' => 'fa fa-th-list fa-fw',
	),
	'trash' => array(
		'class' => 'fa fa-trash-o fa-fw',
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
);