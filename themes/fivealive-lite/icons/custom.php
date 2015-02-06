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

$settings = array( //only these settings will be applied
	'iconset_name' => tr('Custom icons'),
	'iconset_description' => tr('Custom icons for the theme'),
	'icon_tag' => 'span', //the default html tag to sorround the icon
);

$icons = array(
	'information' => array(
		'class' => 'fa fa-info-circle fa-fw', //This is a sample icon, change it to have a customized fa icon or change/add parameters to have an image or a glyphicon. See http://dev.tiki.org/Icons for more details
	),
);