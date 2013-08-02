<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * \brief Smarty {remarksbox}{/remarksbox} block handler (tip (default), comment, note or warning)
 *
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {remarksbox type="tip|comment|note|warning|errors" title="Remark title" highlight="y|n" icon="id"}
 *    <!-- module Smarty/HTML/Text here -->
 *  {/remarksbox}
 * \endcode
 *
 * \params
 *  - type		"tip|comment|note|warning|errors|feedback|confirm default=tip
 *  - title		Text as a label. Leave out for no label (or icon)
 *  - highlight	"y|n" default=n
 *  - icon		Override default icons. See function.icon.php for more info
 *  - close		"y|n" default=y (close button)
 *  - width		e.g. "50%", "200px" default=""
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_remarksbox($params, $content, $smarty, &$repeat)
{
	global $prefs;
	
	if ( $repeat ) return;

	//extract($params);

	$params = array_merge(array(
		'type' => 'tip',
		'title' => '',
		'close' => 'y',
		'width' => '',
		'highlight' => 'n',
		'icon' => '',
	), $params);

	if ($params['highlight'] === 'y') {
		$params['highlight'] = ' highlight';
	} else {
		$params['highlight'] = '';
	}

	if ($params['icon'] ==='') {
		if ($params['type']=='tip') {//get_strings tra('tip')
			$params['icon']='book_open';
		} else if ($params['type']=='comment') {//get_strings tra('comment')
			$params['icon']='comments';
		} else if ($params['type']=='warning' || $params['type'] == 'confirm') {//get_strings tra('warning') tra('confirm')
			$params['icon']='exclamation';
		} else if ($params['type']=='note') {//get_strings tra('note')
			$params['icon']='information';
		} else if ($params['type'] == 'errors') {//get_strings tra('errors')
			$params['icon'] = 'delete';
		} else {//get_strings tra('information')
			$params['icon'] = 'information';
		}
	}

	if ($prefs['javascript_enabled'] != 'y') {
		$params['close'] = false;
	} else {
		$params['close'] = $params['close'] === 'y';
	}

	$smarty->assign('rbox_params', $params);
	$smarty->assign('remarksbox_content', $content);
	return $smarty->fetch('remarksbox.tpl');
}
