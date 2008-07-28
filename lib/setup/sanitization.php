<?php

// $Id: compatibility.php 13491 2008-07-10 09:20:32Z lphuberdeau $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

/* RemoveXSS initially developped by kallahar - quickwired.com, modified for TikiWiki
 *   Original code can be found here: http://quickwired.com/smallprojects/php_xss_filter_function.php
 */
function RemoveXSS($val) {
	// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	// this prevents some character re-spacing such as <java\0script>
	// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

	// straight replacements, the user should never need these since they're normal characters
	// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
		// ;? matches the ;, which is optional
		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

		// &#x0040 @ search for the hex values
		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
		// &#00064 @ 0{0,7} matches '0' zero to seven times
		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	}

	// now the only remaining whitespace attacks are \t, \n, and \r
	$ra_as_tag_only = array('script', 'embed', 'object', 'applet', 'meta', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base');
	$ra_as_attribute = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload','ondragdrop', 'dynsrc');
	$ra_as_content = array('style', 'vbscript', 'expression', 'xml', 'blink', 'link', 'mocha', 'livescript');
	$ra_javascript = array('javascript');
	$ra_style = array('style');

	// keep replacing as long as the previous round replaced something
	while ( RemoveXSSregexp($ra_as_tag_only, $val, '(\<|\[\\\\xC0\]\[\\\\xBC\])') );
	while ( RemoveXSSregexp($ra_as_attribute, $val) );
	while ( RemoveXSSregexp($ra_as_content, $val, '[\.\\\+\*\?\[\^\]\$\(\)\{\}\=\!\<\|\:;\-\/`#"\']') );
	while ( RemoveXSSregexp($ra_javascript, $val, '', '', true) );
	while ( RemoveXSSregexp($ra_style, $val, '', '=') );

	return $val;
}

function RemoveXSSregexp(&$ra, &$val, $prefix = '', $suffix = '', $allow_spaces = false) {
	$val_before = $val;
	$found = true;

	$pattern_sep = '('
		. '(&#[xX]0{0,8}([9ab]);)'
		. '|'
		. '|(&#0{0,8}([9|10|13]);)'
		. ')*'
		. '(?ms)(\/\*.*?\*\/)*'
		. '\\\\?';

	if ( $allow_spaces ) {
		$pattern_sep = '\s*' . $pattern_sep . '\s*';
	}

	$pattern_start = '/';
	if ( $prefix != '' ) {
		$pattern_start .= '(' . $prefix . '\s*' . $pattern_sep . ')';
	}

	$pattern_end = '/i';
	if ( $suffix != '' ) {
		$pattern_end = '(' . $pattern_sep . '\s*' . $suffix . ')' . $pattern_end;
	}

	for ($i = 0; $i < sizeof($ra); $i++) {
		$pattern = $pattern_start;
		for ($j = 0; $j < strlen($ra[$i]); $j++) {
			if ($j > 0) {
				$pattern .= $pattern_sep;
			}
			$pattern .= $ra[$i][$j];
		}
		$pattern .= '/i';
		$replacement = ( $prefix != '' ) ? '\\1' : '';
		$replacement .= substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
		$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
		if ($val_before == $val) {
			// no replacements were made, so exit the loop
			$found = false;
		}
	}
	return $found;
}
