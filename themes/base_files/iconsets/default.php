<?php 
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//The default iconset associates icon names to icon fonts. It is used as the fallback for all other iconsets.


// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$iconset = array(
	'_settings' => array( //Icon set settings
		'iconset_name' => tr('Default'), //mandatory, this will be displayed as Iconset option in the Look&Feel admin UI
		'iconset_description' => tr('The default system iconset'), //this will be displayed as Iconset description in the Look&Feel admin UI
		'icon_path_svg' => '', //TODO The default path for svgs in the iconset so that you dont need to specify for each icon. You can override this for each icon using the path_svg option
		'icon_path_font' => '', //TODO The default path for font icons in the iconset so that you dont need to specify for each icon. You can override this for each icon using the path_font option
		'icon_path_image' => '' , //The default path for images in the iconset so that you dont need to specify for each icon. You can override this for each icon using the image_path option
		'icon_tag' => 'i', //The default html tag for the icons in the iconset. TODO: You can override this for each icon using the tag option
		'icon_type' => 'font', //TODO The default file type for the icons in the iconset. Possible values: svg, font, png, jpeg. You can override this for each icon using the type option. Type sequence: 1)svg 2)font 3)image, so icon function will first use what is in the path_svg, than path_font, than path_png
	),
	'save' => array( //This is the definition of an icon in the iconset. For this one icon all options are explained. The key must be unique, it is the "name" parameter at the icon function, so eg: {icon name="save"} will find 'save' in the array and apply the specified configuration
		'alias_for' => '', //allows to have alias for another, enables to take configuration from another icon. maybe specify separately for svg, font and png?
		'class' => 'fa fa-save', //this is the class used for a glyphicon or a font-awesome icon  
		'alt' => tr('Save'),  //alternate text for an image, if the image cannot be displayed (http://www.w3schools.com/tags/att_img_alt.asp)
		'svg_path' => '', //TODO Specify the URL for an svg definition. Optional, if not defined, the default defined at _settings is used.
		'font_path' => '', //TODO Specify the URL for font icon. Optional, if not defined, the default defined at _settings is used.
		'image_path' => '', //TODO Specify the URL of an image. Optional, if not defined, the default defined at _settings is used.
		'image_file_name' => '', //TODO Specify the name of an image file, eg: 'disk.png' 
		'tag' => 'span', //TODO specify an html tag
	),
	'add' => array( 
		'class' => 'fa fa-plus',
	),
    'comments' => array(
        'class' => 'fa fa-comments-o',
    ),
    'delete' => array(
        'class' => 'fa fa-times',
    ),
    'edit' => array(
        'class' => 'fa fa-edit',
    ),
	'export' => array( 
		'class' => 'fa fa-upload',
	),
	'file-archive' => array( 
		'class' => 'fa fa-file-archive-o',
	),
	'group' => array( 
		'class' => 'fa fa-group',
	),
	'group-watch' => array( 
		'class' => 'fa fa-group',
	),
	'help' => array( 
		'class' => 'fa fa-question',
	),
	'history' => array( 
		'class' => 'fa fa-history',
	),
	'import' => array( 
		'class' => 'fa fa-download',
	),
	'list' => array( 
		'class' => 'fa fa-list',
	),
    'post' => array(
        'class' => 'fa fa-pencil',
    ),
	'print' => array(
		'class' => 'fa fa-print',
	),
	'remove' => array( 
		'class' => 'fa fa-remove',
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
    'stop-watching' => array(
        'class' => 'fa fa-eye-slash',
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
    'watch' => array(
        'class' => 'fa fa-eye',
    ),
);