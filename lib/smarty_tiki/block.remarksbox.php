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

	extract($params);
	if (!isset($type))  $type = 'tip';
	if (!isset($title)) $title = '';
	if (!isset($close)) $close = 'y';
	if (!isset($width)) $width = '';
	if (!isset($id)) $id = '';
	if (!isset($version)) $version = '';
	if ($close != 'y' || !isset($store_cookie) || !isset($id) || !isset($version)) $store_cookie = 'n';
	
	if (isset($highlight) && $highlight == 'y') {
		$highlightClass = ' highlight';
	} else {
		$highlightClass = '';
	}

	switch ($type) {
	case 'warning':
		$class = 'alert-warning';
		$icon = 'warning';
		break;
	case 'error':
	case 'errors':
		$class = 'alert-danger';
		$icon = 'error';
		break;
	case 'confirm':
	case 'feedback':
		$class = 'alert-success';
		$icon = 'success';
		break;
	default:
		$class = 'alert-info';
		$icon = 'information';
		break;
	}
	
	if (isset($prefs['javascript_enabled']) && $prefs['javascript_enabled'] != 'y') {
		$close = false;
		$store_cookie = false;
	} else {
		$close = $close != 'n';
		$store_cookie = $store_cookie != 'n';
	}

	if ($store_cookie) {
		global $user;
		$cookie_hash = md5($id . $version . $_REQUEST['page'] . $user);
	} else {
		$cookie_hash = '';
	}

	$smarty->assign('remarksbox_cookiehash', $cookie_hash);
	$smarty->assign('remarksbox_cookie', $store_cookie);
	$smarty->assign('remarksbox_id', $id);
	$smarty->assign('remarksbox_version', $version);
	$smarty->assign('remarksbox_title', $title);
	$smarty->assign('remarksbox_type', $type);
	$smarty->assign('remarksbox_icon', $icon);
	$smarty->assign('remarksbox_class', $class);
	$smarty->assign('remarksbox_highlight', $highlightClass);
	$smarty->assign('remarksbox_close', $close);
	$smarty->assign('remarksbox_width', $width);
	$smarty->assignByRef('remarksbox_content', $content);
	return $smarty->fetch('remarksbox.tpl');
}
