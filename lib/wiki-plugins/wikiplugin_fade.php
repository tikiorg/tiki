<?php

function wikiplugin_fade_info()
{
	return array(
		'name' => tra('Fade'),
		'description' => tra('Displays a label. On click, the block of content will fade in and fade out.'),
		'prefs' => array('wikiplugin_fade'),
		'body' => tra('Wiki syntax containing the text to display.'),
		'params' => array(
			'label' => array(
				'required' => true,
				'name' => tra('Label'),
				'description' => tra('Label to display on first display'),
			),
		),
	);
}

function wikiplugin_fade( $body, $params )
{
	static $id = 0;
	global $tikilib;

	$unique = 'wpfade-' . ++$id;

	$body = trim($body);
	$body = $tikilib->parse_data( $body );
	return "~np~<a href=\"javascript:toggle('$unique')\">{$params['label']}</a><div id=\"$unique\" style=\"display:none\">$body</div>~/np~";
}

?>
