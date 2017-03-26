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
	public $access;
	public $items;
	public $extra;
	private $action;

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
			$referer = new JitFilter(['referer' => $referer]);
			TikiLib::lib('access')->redirect($referer->referer->striptags());
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

	/**
	 * The following functions are used in the services actions that first present a popup for confirmation before the
	 * action is completed by the user confirm the action
	 */


	/**
	 * CSRF ticket - Check the ticket to either set it or match to the ticket previously set
	 *
	 * @param string $error
	 */
	function checkTicket($error = 'services')
	{
		$this->access = TikiLib::lib('access');
		$this->access->checkAuthenticity($error);
	}

	/**
	 * Set the items variable
	 *
	 * @param JitFilter $input
	 * @param $offset
	 */
	function setVars(JitFilter $input, $offset = false, $filter = false)
	{
		if ($offset) {
			if ($filter) {
				$this->items = $input->asArray($offset)->$filter();
			} else {
				$this->items = $input->asArray($offset);
			}
		}
		$this->action = $input->action->word();
		$this->extra = $input->extra;
	}


	function setDecodedVars(JitFilter $input)
	{
		$this->items = json_decode($input['items'], true);
		$this->extra = json_decode($input['extra'], true);
	}

	/**
	 * Create array for standard confirmation popup
	 *
	 * @param $msg
	 * @param $confirmController
	 * @param $button
	 * @param array $moreExtra
	 * @return array
	 */
	function confirm($msg, $confirmController, $button, array $moreExtra = [])
	{
		//provide redirect if js is not enabled
		$extra['referer'] = Services_Utilities::noJsPath();
		$extra = array_merge($extra, $moreExtra);
		$ret = [
			'FORWARD' => [
				'modal' => '1',
				'controller' => 'access',
				'action' => 'confirm',
				'confirmAction' => $this->action,
				'confirmController' => $confirmController,
				'customMsg' => $msg,
				'confirmButton' => $button,
				'items' => $this->items,
				'extra' => $extra,
				'ticket' => $this->access->getTicket(),
			]
		];
		return $ret;
	}
}
