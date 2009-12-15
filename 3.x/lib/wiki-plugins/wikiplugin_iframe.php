<?php
/*
 *
 * IFRAME plugin. Creates an iframe and loads the specified page within the frame.
 *
 * Syntax:
 *
 *  {IFRAME(some parameters)}$data{IFRAME}
 *
 * Syntax:
 *
 * {IFRAME(name=>name, longdescription=>, width=>, height=>, align=>, frameborder=>, marginheight=> marginwidth=> scrolling=>)}source_URL{IFRAME}
 *
 */
function wikiplugin_iframe_help() {
	return tra("iframe").":~np~{IFRAME(name=xxx, width=100, height=100, align=top|middle|bottom|left|right, frameborder=1|0, marginheight=0, marginwidth=0, scrolling=auto)}".tra('URL')."{IFRAME}~/np~";
}

function wikiplugin_iframe_info() {
	return array(
		'name' => tra('Iframe'),
		'documentation' => 'PluginIframe',
		'description' => tra("Displays an iframe"),
		'prefs' => array( 'wikiplugin_iframe' ),
		'body' => tra('URL'),
		'validate' => 'all',
		'params' => array(
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('name'),
			),
			'title' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Frame title'),
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Pixels or %'),
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Pixels or %'),
			),
			'align' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Alignment'),
				'description' => 'top|middle|bottom|left|right',
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => 'frameborder',
				'description' => '1|0',
			),
			'marginheight' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Height'),
				'description' => tra('Pixels'),
			),
			'marginwidth' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Width'),
				'description' => tra('Pixels'),
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => 'yes|no|auto',
			),
			'src' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('URL'),
			),
		),
	);
}

function wikiplugin_iframe($data, $params) {

	extract ($params);
	$ret = '<iframe ';

	if (isset($name)) {
		$ret .= " name=\"$name\"";
	}
	if (isset($title)) {
		$ret .= " title=\"$title\"";
	}
	if (isset($width)) {
		$ret .= " width=\"$width\"";
	}
	if (isset($height)) {
		$ret .= " height=\"$height\"";
	}
	if (isset($align)) {
		$ret .= " align=\"$align\"";
	}
	if (isset($frameborder)) {
		$ret .= " frameborder=\"$frameborder\"";
	}
	if (isset($marginheight)) {
		$ret .= " marginheight=\"$marginheight\"";
	}
	if (isset($marginwidth)) {
		$ret .= " marginwidth=\"$marginwidth\"";
	}
	if (isset($scrolling)) {
		$ret .= " scrolling=\"$scrolling\"";
	}
	if (isset($src)) {
		$ret .= " src=\"$src\"";
	} elseif (!empty($data)) {
		$ret .= " src=\"$data\"";
	}
	$ret .= ">$data</iframe>";
	return $ret;
}
