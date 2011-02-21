<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

global $prefs, $smarty;
if ($prefs['feature_ajax'] !== 'y' || $prefs['ajax_autosave'] !== 'y') {
	return;
}
if ( isset($_REQUEST['noautosave']) === true ) {
	$smarty->assign('noautosave', $_REQUEST['noautosave'] === 'y');
}

function auto_save_name($id, $referer = '', $only_md5 = false) {
	global $user;
	$referer = preg_replace('/(\?|\&)noautosave=y/', '', ensureReferrer($referer));
	return ($only_md5 ? '' : 'temp/cache/auto_save-').md5("$user:$referer:$id");
}
function auto_save_log($id, $referer = '', $action = '') {
	global $user;
	file_put_contents('temp/cache/auto_save-log-'.(auto_save_name($id, $referer, true)), $user.' : '.ensureReferrer($referer)." : $id : $action\n", FILE_APPEND);
}

/**
 * @param string $id		editor id
 * @param string $data		content to save
 * @param string $referer	textarea specifier (user:section:item)
 * @return number			bytes that were written to the file, or false on failure
 */
function auto_save($id, $data, $referer = '') {
//	auto_save_log($id, $referer, 'auto_save');
	$result = file_put_contents(auto_save_name($id, $referer), $data);
	return $result;
}

/**
 * @param string $id		editor id
 * @param string $referer	textarea specifier (user:section:item)
 * @return bool				true on success or false on failure
 */
function remove_save($id, $referer = '') {
	$referer = ensureReferrer($referer);
//	auto_save_log($id, $referer, 'remove_save');
	$file_name = auto_save_name($id, $referer);
	if (file_exists($file_name)) {
		$result = unlink($file_name);
	} else {
		$result = false;
	}
	return $result;
}

function has_autosave($id, $referer = '') {
	return file_exists(auto_save_name($id, ensureReferrer($referer)));
}

function get_autosave($id, $referer = '') {
	$file_name = auto_save_name($id, $referer);
	if (file_exists($file_name)) {
		return file_get_contents($file_name);
	} else {
		return '';
	}
}

function ensureReferrer($referer = '') {
	
	// should be page name, but use URI if not?
	if (empty($referer)) {
		global $section,  $user, $tikilib;
		$referer .= empty($user) ? $tikilib->get_ip_address() : $user;
		$referer .= ':';
		if ($section == 'wiki page') {
			if (isset($_REQUEST['page'])) {
				$referer .= 'wiki_page:' . $_REQUEST['page'];
			}
		} else if ($section == 'blogs') {
			if (isset($_REQUEST['postId'])) {
				$referer .= 'blog:' . $_REQUEST['postId'];
			}
		} else {
			$referer .= $section;	// better than nothing?
		}
	}
	if (empty($referer)) {
		$referer = $_SERVER['REQUEST_URI'];
	}
	return $referer;
}
