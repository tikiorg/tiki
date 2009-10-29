<?php
/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_div.php,v 1.10 2007-07-19 21:02:35 ricks99 Exp $
 *
 * 
 * 
 */
function wikiplugin_div_help() {
	return tra("Insert a division block, span, blockquote or other text formatting on wiki page.").":<br />~np~{DIV(class=>class, id=>id, type=>div|span|pre|i|b|tt|blockquote, align=>right|center|justify, bg=>color, width=>num[%]|numpx, float=>left|right], clear=>left|right|both)}".tra("text")."{DIV}~/np~";
}

function wikiplugin_div_info() {
	return array(
		'name' => tra('Div'),
		'documentation' => 'PluginDiv',
		'description' => tra("Insert a division block, span, blockquote or other text formatting on wiki page."),
		'prefs' => array('wikiplugin_div'),
		'body' => tra('text'),
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('div|span|pre|b|i|tt|p|blockquote'),
				'filter' => 'alpha',
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Div'), 'value' => 'div'), 
					array('text' => tra('Span'), 'value' => 'span'), 
					array('text' => tra('Pre'), 'value' => 'pre'),
					array('text' => tra('Bold'), 'value' => 'b'),
					array('text' => tra('Italic'), 'value' => 'i'),
					array('text' => tra('Teletype'), 'value' => 'tt'),
					array('text' => tra('Paragraph'), 'value' => 'p'),
					array('text' => tra('Block quote'), 'value' => 'blockquote'),
				),
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
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Right'), 'value' => 'right'), 
					array('text' => tra('Center'), 'value' => 'center'), 
					array('text' => tra('Justify'), 'value' => 'justify'), 
				),
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float position'),
				'description' => tra('left|right, for box with width less than 100%, make text wrap around the box.'),
				'filter' => 'alpha',
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Right'), 'value' => 'right'), 
					array('text' => tra('Left'), 'value' => 'left'), 
				),
			),
			'clear' => array(
				'required' => false,
				'name' => tra('Clear'),
				'description' => tra('Determine how other elements can wrap around the element.'),
				'filter' => 'text',
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Right'), 'value' => 'right'), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Both'), 'value' => 'both'), 
				),
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
