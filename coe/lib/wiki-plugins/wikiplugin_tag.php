<?php
// $Id$

// \brief Wiki plugin to output something like <a_tag style=a_style>...</a_tag>
// ex: {TAG(tag=STRIKE, style=color:#FF0000)}toto{TAG}
//	would produce <STRIKE style="color:#FF0000">toto</STRIKE>

function wikiplugin_tag_help() {
	return tra("Displays text between an html tag").":<br />~np~{TAG(tag=a_tag, style=a_style)}text{TAG}~/np~";
}

function wikiplugin_tag_info() {
	return array(
		'name' => tra('Tag'),
		'documentation' => 'PluginTag',
		'description' => tra('Displays the text between an html tag'),
		'prefs' => array( 'wikiplugin_tag' ),
		'validate' => 'all',
		'body' => tra('text'),
		'params' => array(
			'tag' => array(
				'required' => false,
				'name' => tra('Tag Name'),
				'description' => tra('Any valid HTML tag, span by default.'),
			),
			'style' => array(
				'required' => false,
				'name' => tra('CSS Style'),
				'description' => tra('Equivalent of the style attribute on the HTML tag.'),
			),
		),
	);
}

function wikiplugin_tag($data, $params) {
	extract ($params,EXTR_SKIP);
	if (!isset($tag)) {
		$tag = 'span';
	} else {
		// remove eveyrything what's not a word to allow only tags without attributes
		$tag = preg_replace("/[^\w]/e", "", $tag);
	}
	
	if (isset($style)) {
		// trim quotes from the begin and end of style
		$style = ' style="'.trim($style,"\'\"").'"';
	} else {
		$style = '';
	}
	return "<$tag$style>$data</$tag>";
}
?>
