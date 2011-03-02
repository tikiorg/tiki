<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
function wikiplugin_banner_help() {
	return tra("Insert a banner").":<br />~np~{BANNER(zone=zone, target=target) /}~/np~";
}
function wikiplugin_banner_info() {
	return array(
		'name' => tra('Banner'),
		'documentation' => 'PluginBanner',
		'description' => tra('Add a banner'),
		'prefs' => array('wikiplugin_banner'),
		'icon' => 'pics/icons/page_lightning.png',
		'params' => array(
			'zone' => array(
				'required' => true,
				'name' => tra('Zone'),
				'description' => tra('Name of the zone created in Admin > Banners'),
				'default' => '',
			),
			'target' => array(
				'required' => false,
				'name' => tra('Target'),
				'description' => tra('Determines the browser behavior once the banner is clicked'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Blank'), 'value' => '_blank'), 
					array('text' => tra('Display'), 'value' => 'display')
				)
			)
		)
	);
}

function wikiplugin_banner($data, $params)
{
    global $tikilib, $prefs;
	if ($prefs['feature_banners'] != 'y') {
		return;
	}
    global $bannerlib;include_once('lib/banners/bannerlib.php');

	extract ($params, EXTR_SKIP);
		
    if (empty($zone)) {
        return tra('missing parameter');
    }
	if (empty($target)) {
		$banner = $bannerlib->select_banner($zone);
	} else {
		$banner = $bannerlib->select_banner($zone, $target);
	}
    return '~np~'.$banner.'~/np~';
}
