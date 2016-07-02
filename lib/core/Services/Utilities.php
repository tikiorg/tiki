<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Services_Utilities
 */
class Services_Utilities
{
	/**
	 * Provide referer url if javascript not enabled.
	 * 
	 * @return bool|string
	 */
	static function noJsPath ()
	{
		global $prefs;
		//no javascript
		if ($prefs['javascript_enabled'] !== 'y') {
			global $base_url;
			$referer = substr($_SERVER['HTTP_REFERER'], strlen($base_url));
		//javascript
		} else {
			$referer = false;
		}
		return $referer;
	}

	/**
	 * Handle feedback after a non-modal form is clicked
	 * Send feedback using Feedback class (using 'session' for the method parameter) first before using this.
	 * Improves handling when javascript is not enabled compared to throwing a Services Exception because it takes the
	 * user back to the page where the action was initiated and shows the error message there.
	 * 
	 * @param bool $referer
	 * @throws Exception
	 */
	static function sendFeedback ($referer = false)
	{
		//no javascript
		if (!empty($referer)) {
			TikiLib::lib('access')->redirect($referer);
		//javascript
		} else {
			Feedback::send_headers();
			die;
		}
	}

	/**
	 * Handle Feedback message after a modal is clicked.
	 * Send feedback using Feedback class (using 'session' for the method parameter) first before using this.
	 * Improves handling when javascript is not enabled compared to throwing a Services Exception because it takes the
	 * user back to the page where the action was initiated and shows the error message there.
	 * 
	 * @param bool $referer
	 * @return array
	 * @throws Exception
	 */
	static function closeModal ($referer = false)
	{
		//no javascript
		if (!empty($referer)) {
			TikiLib::lib('access')->redirect($referer);
		//javascript
		} else {
			Feedback::send_headers();
			//the js confirmAction function in tiki-ajax_services.js uses this to close the modal
			return ['extra' => 'close'];
		}
	}

	/**
	 * Handle feedback message when the page is being refreshed, e.g., after a succesful action
	 * Send feedback using Feedback class (using 'session' for the method parameter) first before using this.
	 * Allows the same type of detailed feedback to be provided when javascript is not enabled.
	 *
	 * @param bool $referer
	 * @return array
	 * @throws Exception
	 */
	static function refresh ($referer = false)
	{
		//no javascript
		if (!empty($referer)) {
			TikiLib::lib('access')->redirect($referer);
		//javascript
		} else {
			//the js confirmAction function in tiki-ajax_services.js uses this to close the modal and refresh the page
			return ['extra' => 'refresh'];
		}
	}

	/**
	 * Handle a redirect depending on whether javascript is enabled or not
	 * Send any feedback using Feedback class (using 'session' for the method parameter) first before using this.
	 * 
	 * @param $url
	 * @return array
	 * @throws Exception
	 */
	static function redirect ($url)
	{
		//no javascript
		global $prefs;
		if ($prefs['javascript_enabled'] !== 'y') {
			TikiLib::lib('access')->redirect($url);
		//javascript
		} else {
			return ['url' => $url];
		}
	}

	/**
	 * Handle exception when initially clicking a modal service action according to whether javascript is enabled or not.
	 * Improves handling when javascript is not enabled compared to throwing a Services Exception because it takes the
	 * user back to the page where the action was initiated and shows the error message there.
	 * 
	 * @param $mes
	 * @throws Exception
	 * @throws Services_Exception
	 */
	static function modalException ($mes)
	{
		$referer = self::noJsPath();
		//no javascript
		if (!empty($referer)) {
			Feedback::error($mes, 'session');
			TikiLib::lib('access')->redirect($referer);
		//javascript
		} else {
			//this will show as a modal if exception occurs when first clicking the action
			throw new Services_Exception($mes);
		}
	}
}
