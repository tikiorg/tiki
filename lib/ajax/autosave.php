<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

class AutoSaveLib
{
	function __construct()
	{
		$access = TikiLib::lib('access');

		$access->check_feature(array('feature_ajax', 'ajax_autosave'));
	}

	/**
	 * Save data into cache file
	 *
	 * @param string $id        editor (textarea) id
	 * @param string $data		content to save
	 * @param string $referer   user:section:object id
	 * @return number			bytes that were written to the file, or false on failure
	 */
	function auto_save($id, $data, $referer = '')
	{
		//	auto_save_log($id, $referer, 'auto_save');
		$result = file_put_contents($this->auto_save_name($id, $referer), $data);
		return $result;
	}

	/**
	 * Delete cache file
	 *
	 * @param string $id		editor id
	 * @param string $referer	textarea specifier (user:section:item)
	 * @return bool				true on success or false on failure
	 */
	function remove_save($id, $referer = '')
	{
		$referer = $this->ensureReferrer($referer);
		//	auto_save_log($id, $referer, 'remove_save');
		$file_name = $this->auto_save_name($id, $referer);
		if (file_exists($file_name)) {
			$result = unlink($file_name);
		} else {
			$result = false;
		}
		return $result;
	}

	/**
	 * Check if cache file exists
	 *
	 * @param string $id         editor (textarea) id
	 * @param string $referer    user:section:object id
	 * @return bool
	 */
	function has_autosave($id, $referer = '')
	{
		return file_exists($this->auto_save_name($id, $this->ensureReferrer($referer)));
	}

	/**
	 * Get the contents of an autosave cache file
	 *
	 * @param string $id         editor (textarea) id
	 * @param string $referer    user:section:object id
	 * @return bool|string
	 */
	function get_autosave($id, $referer = '')
	{
		$file_name = $this->auto_save_name($id, $referer);
		if (file_exists($file_name)) {
			return file_get_contents($file_name);
		} else {
			return '';
		}
	}

	/**
	 * Make sure all types of object get a referrer
	 *
	 * @param string $referer
	 * @return string
	 */
	function ensureReferrer($referer = '')
	{

		// should be page name, but use URI if not?
		if (empty($referer)) {
			global $section,  $user, $tikilib;
			$referer .= empty($user) ? $tikilib->get_ip_address() : $user;
			$referer .= ':';
			if ($section == 'wiki page') {
				if (isset($_REQUEST['page'])) {
					$referer .= 'wiki_page:' . rawurlencode($_REQUEST['page']);
				}
			} else if ($section == 'blogs') {
				if (isset($_REQUEST['postId'])) {
					$referer .= 'blog:' . $_REQUEST['postId'];
				}
			} else {
				$referer .= rawurlencode($_SERVER['REQUEST_URI']);
			}
		}
		return $referer;
	}

	/**
	 * Get the name for the cache file
	 *
	 * @param string $id         editor (textarea) id
	 * @param string $referer    user:section:object id
	 * @param bool $only_md5
	 * @return string
	 */
	private function auto_save_name($id, $referer = '', $only_md5 = false)
	{
		global $user;
		$referer = preg_replace('/(\?|\&)noautosave=y/', '', $this->ensureReferrer($referer));
		$referer = rawurldecode($referer); // this is needed to ensure consistency whether coming from js or php
		return ($only_md5 ? '' : 'temp/cache/auto_save-').md5("$user:$referer:$id");
	}

	/**
	 * Dev routine to save a log of autosave events
	 *
	 * @param $id
	 * @param string $referer
	 * @param string $action
	 */
	private function auto_save_log($id, $referer = '', $action = '')
	{
		global $user;
		file_put_contents('temp/cache/auto_save-log-'.($this->auto_save_name($id, $referer, true)), $user.' : '.$this->ensureReferrer($referer)." : $id : $action\n", FILE_APPEND);
	}

}
