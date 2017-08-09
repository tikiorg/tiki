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
 * \brief Smarty message (tip/note (default), confirmation, warning or error) box
 *
 * To make a box, a template may contain something like this:
 * \code
 *  {remarksbox title="Welcome to the control panels"}
 *    This page is a kind of <a href="http://en.wikipedia.org/wiki/Map">map</a> to all control panels.
 *    In each control panel, this large map will be replaced by a miniature version.
 *  {/remarksbox}
 * \endcode
 *
 * \params
 *  - type
 *  	String determining the appearance (style/icon) by categorizing as one of 4 types of messages.
 *  	Each type has at least 1 identifier. By increasing importance:
 * 			success|confirm|feedback: Confirmation message. The "feedback" identifier is deprecated (use "success" or "confirm").
 *	 		tip|comment|note|info: Informative message, such as a tip (default)
 * 			warning: Warning message
 * 			errors|error|danger: Error message (or extremely important warning?)
 *  - title		    Text as a label. Leave out for no label (or icon)
 *  - highlight	    "y|n" default=n
 *  - icon		    Override default icons. See function.icon.php for more info
 *  - close		    "y|n" default=y (close button)
 *  - width		    e.g. "50%", "200px" default=""
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
		case 'error':
		case 'errors':
		case 'danger':
			$class = 'alert-danger';
			$icon = 'error';
			break;
		case 'warning':
			$class = 'alert-warning';
			$icon = 'warning';
			break;
		case 'success':
		case 'confirm':
		case 'feedback': // Deprecated
			$class = 'alert-success';
			$icon = 'success';
			break;
		case 'comment':
		case 'info':
		case 'note':
		case 'tip':
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
		$params['close'] = $params['close'] !== 'n';
		$params['store_cookie'] = false;
		$cookie_hash = '';
		$hidden = false;
	}

	$smarty->assign('remarksbox_cookiehash', $cookie_hash);
	$smarty->assign('remarksbox_cookie', $params['store_cookie']);
	$smarty->assign('remarksbox_hidden', $hidden);
	$smarty->assign('remarksbox_id', $params['id']);
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
