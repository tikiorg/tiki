<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id

function wikiplugin_towtruck_info()
{
	return array(
		'name' => tra('TowTruck'),
		'documentation' => 'PluginTowTruck',
		'description' => tra('Insert the TowTruck Widget'),
		'prefs' => array( 'wikiplugin_towtruck' ),
		'body' => tra('A service for your website that makes it surprisingly easy to collaborate in real-time. TowTruck lets users communicate, co-author, co-browse and guide each other. TowTruck is implemented in Javascript; no software or plugins to install, and it is friendly with existing web pages, while still letting developers customize the experience.')." ".tra("Note: TowTruck is alpha-quality software. We do not recommend using it in production at this time."),
		'params' => array(
			'buttonname' => array(
				'required' => false,
				'name' => tra('Button Name'),
				'description' => tra('Set the button name. Default is Cowrite with TowTruck'),
				'default' => 'Start TowTruck'
			),
		)
	);
}

function wikiplugin_towtruck($data, $params)
{

	extract($params, EXTR_SKIP);
	
	if (!isset($buttonname)) {
		$buttonname = "CoWrite with TowTruck";
	}
	$ret = "<script type=\"text/javascript\" src=\"https://towtruck.mozillalabs.com/towtruck.js\"></script>";
	$ret.= "<button onclick=\"TowTruck(this); return false;\">$buttonname</button>";
	
	return $ret;
}
