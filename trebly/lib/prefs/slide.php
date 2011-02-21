<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_slide_list() {
	global $tikilib;
	$slide_styles = array();

	$list_slide_styles = $tikilib->list_slide_styles();

	foreach ($list_slide_styles as $onestyle) {
		$slide_styles[$onestyle] = substr($onestyle, 0, strripos($onestyle, '.css'));
	}

	return array(
		'slide_style' => array(
			'name' => tra('Slideshow theme'),
			'type' => 'list',
			'options' => $slide_styles,
		),
	);
}
