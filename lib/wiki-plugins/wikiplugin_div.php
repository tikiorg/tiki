<?php
/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_div.php,v 1.10 2007-07-19 21:02:35 ricks99 Exp $
 *
 * DIV plugin. Creates a division block for the content. Forces the content 
 * to be aligned (left by default).
 * 
 * Syntax:
 * 
 *  {DIV([align=>left|right|center|justify][, bg=color][, width=>num[%]][, float=>left|right])}
 *   some content
 *  {DIV}
 * 
 */
function wikiplugin_div_help() {
	return tra("Insert a division block on wiki page").":<br />~np~{DIV(class=>class, id=>id, type=>div|span|pre|i|b|tt|blockquote, align=>left|right|center|justify, bg=>color, width=>num[%], float=>left|right])}".tra("text")."{DIV}~/np~";
}

function wikiplugin_div_info() {
	return array(
		'name' => tra('Div'),
		'documentation' => 'PluginDiv',
		'description' => tra("Insert a division block on wiki page"),
		'prefs' => array('wikiplugin_div'),
		'body' => tra('text'),
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('div|span|pre|b|i|tt|p|blockquote'),
				'filter' => 'alpha',
			),
			'bg' => array(
				'required' => false,
				'name' => tra('Background color'),
				'description' => tra('As defined by CSS, name or Hex code.'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Box width'),
				'description' => tra('In pixels or percentage. Default value is 100%.'),
			),
			'align' => array(
				'required' => false,
				'name' => tra('Text Alignment'),
				'description' => tra('left|right|center|justify'),
				'filter' => 'alpha',
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float Position'),
				'description' => tra('left|right, for box with width lesser than 100%, make text wrap around the box.'),
				'filter' => 'alpha',
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the div.'),
				'filter' => 'text',
			),
			'id' => array(
				'required' => false,
				'name' => tra('HTML id'),
				'description' => tra('Sets the div\'s id attribute, as defined by HTML.'),
			),
		),
	);
}

function wikiplugin_div($data, $params) {

	extract ($params,EXTR_SKIP);
	$possibletypes = array('div','span','pre','b','i','tt','p','blockquote');
	$t    = (isset($type) and in_array($type,$possibletypes)) ? "$type"  : "div";
	$c    = (isset($class)) ? " class='$class'"  : "";
	$id    = (isset($id)) ? " id='$id'"  : "";
	$w    = (isset($width)) ? " width: $width;"  : "";
	$bg   = (isset($bg))    ? " background: $bg;" : "";
	$al   = (isset($align) && ($align == 'right' || $align == "center" || $align == "justify")) ? " text-align: $align;" : " text-align: left;";
	$fl   = (isset($float) && ($float == 'left' || $float == 'right')) ? " float: $float;"  : " float: none;";
	$cl   = (isset($clear) && ($clear == 'left' || $clear == 'right' || $clear == 'both')) ? " clear: $clear;"  : " clear: none;";

	$begin  = "<$t style=\"$bg$al$w$fl$cl\"$c $id>";
	$end = "</$t>";
	return $begin . $data . $end;
}
?>
