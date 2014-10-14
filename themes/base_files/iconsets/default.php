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
		'icon_tag' => 'i', //The default html tag for the icons in the icon set. TODO: You can override this for each icon using the tag option
		'icon_type' => 'font', //TODO The default file type for the icons in the icon set. Possible values: svg, font, png, jpeg. You can override this for each icon using the type option. Type sequence: 1)svg 2)font 3)image, so icon function will first use what is in the path_svg, than path_font, than path_png
	),
	'save' => array( //This is the definition of an icon in the icon set. For this one icon all options are explained. The key must be unique, it is the "name" parameter at the icon function, so eg: {icon name="save"} will find 'save' in the array and apply the specified configuration
		'class' => 'fa fa-save', //this is the class used for a glyphicon or a font-awesome icon  
		'alt' => tr('Save'),  //alternate text for an image, if the image cannot be displayed (http://www.w3schools.com/tags/att_img_alt.asp)
		'svg_path' => '', //TODO Specify the URL for an svg definition. Optional, if not defined, the default defined at _settings is used.
		'font_path' => '', //TODO Specify the URL for font icon. Optional, if not defined, the default defined at _settings is used.
		'image_path' => '', //TODO Specify the URL of an image. Optional, if not defined, the default defined at _settings is used.
		'image_file_name' => '', //Specify the name of an image file, eg: 'disk.png' 
		'tag' => 'span', //TODO specify an html tag
	),
	'actions' => array( 
		'class' => 'fa fa-play-circle',
	),
	'add' => array( 
		'class' => 'fa fa-plus',
	),
	'clone' => array(
		'class' => 'fa fa-clipboard',
	),
	'comments' => array(
		'class' => 'fa fa-comments-o',
	),
	'copy' => array(
		'class' => 'fa fa-copy',
	),
	'create' => array(
		'class' => 'fa fa-plus',
	),
	'delete' => array(
		'class' => 'fa fa-times',
	),
	'edit' => array(
		'class' => 'fa fa-edit',
	),
	'envelope' => array(
		'class' => 'fa fa-envelope-o',
	),
	'error' => array(
		'class' => 'fa fa-exclamation-circle',
	),
	'events' => array(
		'class' => 'fa fa-clock-o',
	),	
	'export' => array( 
		'class' => 'fa fa-upload',
	),
	'external-link' => array(
		'class' => 'fa fa-external-link',
	),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o',
	),
	'group' => array( 
		'class' => 'fa fa-group',
	),
	'help' => array( 
		'class' => 'fa fa-question-circle',
	),
	'history' => array( 
		'class' => 'fa fa-history',
	),
	'import' => array( 
		'class' => 'fa fa-download',
	),
	'info' => array( 
		'class' => 'fa fa-info-circle',
	),
	'list' => array( 
		'class' => 'fa fa-list',
	),
	'menu' => array(
		'class' => 'fa fa-bars',
	),
	'permission' => array(
		'class' => 'fa fa-key',
	),
	'post' => array(
		'class' => 'fa fa-pencil',
	),
	'print' => array(
		'class' => 'fa fa-print',
	),
	'refresh' => array(
		'class' => 'fa fa-refresh',
	),
	'remove' => array(
		'class' => 'fa fa-times',
	),
	'rss' => array(
		'class' => 'fa fa-rss',
	),
	'search' => array( 
		'class' => 'fa fa-search',
	),
	'settings' => array( 
		'class' => 'fa fa-wrench',
	),
	'share' => array(
		'class' => 'fa fa-share',
	),
	'stop-watching' => array(
		'class' => 'fa fa-eye-slash',
	),
	'success' => array(
		'class' => 'fa fa-check-circle',
	),
	'tag' => array(
		'class' => 'fa fa-tag',
	),
	'trackers' => array(
		'class' => 'fa fa-database',
	),
	'trackerfields' => array(
		'class' => 'fa fa-th-list',
	),
	'trash' => array(
		'class' => 'fa fa-trash-o',
	),
	'view' => array(
		'class' => 'fa fa-search-plus',
	),
	'warning' => array(
		'class' => 'fa fa-exclamation-triangle',
	),
	'watch' => array(
		'class' => 'fa fa-eye',
	),
	'watch-group' => array(
		'class' => 'fa fa-group',
	),
);