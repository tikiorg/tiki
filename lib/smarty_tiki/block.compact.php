<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Smarty plugin to make result HTML code smaller
 * In opposite to {strip} this plugin can be used ONCE at top level template
 * to strip all HTML at once... And it have no nasty BUG which is incorrectly
 * join some words together...
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_compact($params, $content, $smarty, &$repeat)
{
	if ( $repeat ) return;
	// Tags with uncompactable content...
	$nct = array('textarea', 'pre');
	// Replace uncompactable content with unique marks
	$ncc = array();
	$num = 0;
	foreach ($nct as $tag) {
		if (preg_match('/<\s*'.$tag.'.*>(.*)<\/\s*'.$tag.'\s*>/Usi', $content, $ucb) != 0) {
			$mark = md5($ucb[1].$num++.microtime());
			$ncc[$mark] = $ucb[1];
			$content = str_replace($ucb[1], $mark, $content);
		}
	}
	// Compact the text
	$content = str_replace('> <', '><', preg_replace('/\s+/', ' ', $content));
	// Insert back all saved tags content
	$ncc = array_reverse($ncc);
	foreach ($ncc as $mark => $text)
		$content = str_replace($mark, $text, $content);
	return $content;
}
