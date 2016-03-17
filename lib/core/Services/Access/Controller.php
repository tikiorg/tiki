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
 * Class Services_Access_Controller
 */
class Services_Access_Controller
{
	/**
	 * Action to bring up the "confirm your action" popup.
	 * Designed to be used with the $access->check_authenticity() function to prevent CSRF
	 * by including a ticket in the popup form. See the action_delete_topic function at
	 * lib/core/Services/Forum/Controller.php for an example
	 *
	 * @param $input
	 * @return array
	 */
	function action_confirm($input)
	{
		$ret = $this->prepareReturn($input);
		return $ret;
	}

	function action_confirm_select($input)
	{
		$ret = $this->prepareReturn($input);
		return $ret;
	}

	private function prepareReturn($input) {
		$title = !empty($input['title']) ? $input['title'] : tra('Please confirm');
		$confirmButton = !empty($input['confirmButton']) ? $input['confirmButton'] : tra('OK');
		$confirmButtonClass = !empty($input['confirmButtonClass']) ? $input['confirmButtonClass'] : 'btn-primary';
		$items = $input->asArray('items');

		/*** confirm message ***/
		$customMsg = !empty($input['customMsg']) ? $input['customMsg'] : '';
		$customVerb = !empty($input['customVerb']) ? $input['customVerb'] : '';
		$customObject = !empty($input['customObject']) ? $input['customObject'] : '';
		if (empty($customMsg)) {
			if (!empty($customVerb) && !empty($customObject)) {
				$customMsg = tr('Are you sure you want to %0 the following %1?', $customVerb, $customObject);
			} else {
				if (count($items) === 1) {
					$customMsg = tra('Please confirm your action for the following item:');
				} else {
					$customMsg = tra('Please confirm your action for the following items:');
				}
			}
		}
		return [
			'items' => $items,
			'confirmAction' => $input->confirmAction->word(),
			'confirmController' => $input->confirmController->word(),
			'extra' => $input->asArray('extra'),
			'toMsg' => $input->toMsg->xss(),
			'toList' => $input->asArray('toList'),
			'ticket' => $input->ticket->alnum(),
			'title' => $title,
			'customMsg' => $customMsg,
			'confirmButton' => $confirmButton,
			'confirmButtonClass' => $confirmButtonClass,
			'confirm' => 'y',
		];
	}
}
