<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_themegenerator_list() {
	
	$themes = array('' => tra('Select...'));
	$list = @unserialize($prefs['themegenerator_theme_list']);
	sort($list);
	
	return array(
		'themegenerator_theme' => array(
			'name' => tra('Generated Theme'),
			'description' => tra(''),
			'type' => 'list',
			'options' => $themes,
			'dependencies' => 'feature_themegenerator',
		),
	);	
}
