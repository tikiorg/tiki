<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_together_info()
{
	return array(
		'name' => tra('Together'),
		'documentation' => 'PluginTogether',
		'description' => tra('Insert the TogetherJS Widget'),
		'prefs' => array( 'wikiplugin_together' ),
		'body' => tra('A service for your website that makes it surprisingly easy to collaborate in real-time. TogetherJS lets users communicate, co-author, co-browse and guide each other. TogetherJS is implemented in Javascript; no software or plugins to install, and it is friendly with existing web pages, while still letting developers customize the experience.')." ".tra("Note: TogetherJS is alpha-quality software. We do not recommend using it in production at this time."),
		'params' => array(
			'buttonname' => array(
				'required' => false,
				'name' => tra('Button Name'),
				'description' => tra('Set the button name. Default is CoWrite with TogetherJS'),
				'default' => 'CoWrite with TogetherJS'
			),
		)
	);
}

function wikiplugin_together($data, $params)
{

	extract($params, EXTR_SKIP);
	
	if (!isset($buttonname)) {
		$buttonname = "CoWrite with TogetherJS";
	}
	$ret = "<script type=\"text/javascript\" src=\"https://togetherjs.com/togetherjs-min.js\"></script>";
	$ret.= "<button onclick=\"TogetherJS(this); return false;\">$buttonname</button>";
	
	return $ret;
}
