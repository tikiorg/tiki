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
 * -------------------------------------------------------------
 * File:     block.translate.php
 * Type:     block
 * Name:     translate
 * Purpose:  translate a block of text
 * -------------------------------------------------------------
 * Note that the tr *prefilter* deals with most of the apparent calls to the tr block at compile time,
 * leaving only a few Smarty translations reach this block.
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_tr($params, $content, $smarty, &$repeat)
{

	if ( $repeat || empty($content)) return;

	if (empty($params['lang'])) {
		$lang = '';
	} else {
		$lang = $params['lang'];
	}

	$args = array();
	foreach ( $params as $key => $value ) {
		if ( preg_match('/_([[:digit:]])+/', $key, $matches) )
			$args[$matches[1]] = $value;
	}

	if (empty($params['interactive']) || $params['interactive'] == 'y')
		return tra($content, $lang, false, $args);
	else
		return tra($content, $lang, true);
}
