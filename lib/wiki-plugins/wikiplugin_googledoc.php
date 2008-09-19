<?php
/*
 *
 * Google Docs plugin. Creates an iframe and loads the Google Doc within the frame.
 *
 * MatWho 13/09/08
 */


function wikiplugin_googledoc_help() {
	return tra("googledoc").":~np~{GOOGLEDOC(key=XXXXX name=xxx, width=100, height=100, align=top|middle|bottom|left|right, frameborder=1|0, marginheight=0, marginwidth=0, scrolling=yes|no|auto, editLink=top|bottom|both)}{GOOGLEDOC}~/np~";
}

function wikiplugin_googledoc_info() {
	return array(
		'name' => tra('googledoc'),
		'documentation' => 'PluginGoogleDoc',
		'description' => tra("Displays a Google document"),
//		'prefs' => array( 'wikiplugin_googleDoc' ),
		'body' => tra('none'),
		'validate' => 'all',
		'params' => array(
			'key' => array(
					'safe' => true,
					'required' => true,
					'name' => tra('key'),
					'description' => tra('Google doc key - for example pXsHENf1bGGY92X1iEeJJI'),
				),
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name of iframe'),
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
			'editLink' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('editLink'),
				'description' => 'top|bottom|both',
			),
		),
	);
}

function wikiplugin_googledoc($data, $params) {

	extract ($params);

	$ret = "";
	
	if (isset($name)) {
		$frameName=$name;
	} else {
		$frameName="Frame".$key;
	}
	if ($editLink== 'both' or $editLink== 'top') {
		$ret .= " <P><A HREF=\"http://spreadsheets.google.com/ccc?key=$key\" Target=\"$frameName\">Edit this Google Document</A></P>";
	}

	$ret .= '<iframe ';
	$ret .= " name=\"$frameName\"";
	
	if (isset($width)) {
		$ret .= " width=\"$width\"";
	} else {
		$ret .=  " width=\"800\"";
	}
	if (isset($height)) {
		$ret .= " height=\"$height\"";
	} else {
		$ret .= " height=\"400\"";
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
	if (isset($key)) {
		$ret .= " src=\"http://spreadsheets.google.com/pub?key=$key &output=html&widget=true\"></iframe>";
	}
	if ($editLink== 'both' or $editLink== 'bottom') {
		$ret .= " <P><A HREF=\"http://spreadsheets.google.com/ccc?key=$key\" Target=\"$frameName\">Edit this Google Document</A></P>";
	}

	$ret .= "";
	return $ret;
}
