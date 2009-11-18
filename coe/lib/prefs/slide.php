<?php

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
