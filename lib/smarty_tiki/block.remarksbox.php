<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 *  - type		    "tip|comment|note|warning|errors|feedback|confirm default=tip
 *  - title		    Text as a label. Leave out for no label (or icon)
 *  - highlight	    "y|n" default=n
 *  - icon		    Override default icons. See function.icon.php for more info
 *  - close		    "y|n" default=y (close button)
 *  - width		    e.g. "50%", "200px" default=""
 *  - version       ??
 *  - store_cookie  "y|n" default y, set to n to not store closed state in a cookie
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_remarksbox($params, $content, $smarty, &$repeat)
{
	global $prefs;

	if ( $repeat ) {
		return '';
	}

	static $remarksboxInstance = 0;
	$remarksboxInstance++;
	
	$params = array_merge([	// default params
		'type' => 'tip',
		'title' => '',
		'close' => 'y',
		'width' => '',
		'id' => 'rbox_' . $remarksboxInstance,
		'version' => '',
		'store_cookie' => 'y',
	], $params);

	if ($params['close'] != 'y' || $prefs['remember_closed_rboxes'] === 'n') {
		$params['store_cookie'] = 'n';
	}
	
	if (isset($params['highlight']) && $params['highlight'] == 'y') {
		$highlightClass = ' highlight';
	} else {
		$highlightClass = '';
	}

	switch ($params['type']) {
		case 'warning':
			$class = 'alert-warning';
			$icon = 'warning';
			break;
		case 'error':
		case 'errors':
		case 'danger':
			$class = 'alert-danger';
			$icon = 'error';
			break;
		case 'success':
		case 'confirm':
		case 'feedback':
			$class = 'alert-success';
			$icon = 'success';
			break;
		case 'info':
		default:
			$class = 'alert-info';
			$icon = 'information';
			break;
	}
	
	if ($prefs['javascript_enabled'] != 'y') {
		$params['close'] = false;
		$params['store_cookie'] = false;
		$cookie_hash = '';
		$hidden = false;
	} else if ($params['store_cookie'] === 'y') {
		$params['close'] = $params['close'] !== 'n';
		$params['store_cookie'] = $params['store_cookie'] !== 'n';
		$cookie_hash = md5($params['title'] . $params['version'] . $content);
		$hidden = getCookie($cookie_hash, "rbox", false);
	} else {
		$params['store_cookie'] = false;
		$cookie_hash = '';
		$hidden = false;
	}

	$smarty->assign('remarksbox_cookiehash', $cookie_hash);
	$smarty->assign('remarksbox_cookie', $params['store_cookie']);
	$smarty->assign('remarksbox_hidden', $hidden);
	$smarty->assign('remarksbox_id', $params['id']);
	$smarty->assign('remarksbox_version', $params['version']);
	$smarty->assign('remarksbox_title', $params['title']);
	$smarty->assign('remarksbox_type', $params['type']);
	$smarty->assign('remarksbox_icon', $icon);
	$smarty->assign('remarksbox_class', $class);
	$smarty->assign('remarksbox_highlight', $highlightClass);
	$smarty->assign('remarksbox_close', $params['close']);
	$smarty->assign('remarksbox_width', $params['width']);
	$smarty->assignByRef('remarksbox_content', $content);
	return $smarty->fetch('remarksbox.tpl');
}
