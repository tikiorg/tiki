<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		$input->replaceFilters(
			[
				'customVerb' => new Zend_Filter_Alpha(true),
				'customObject' => new Zend_Filter_Alpha(true),
				'confirmButton' => new Zend_Filter_Alpha(true),
				'ticket' => 'alnum',
			]
		);
		$title = !empty($input->offsetGet('title')) ? $input->offsetGet('title') : tra('Please confirm');
		$confirmButton = !empty($input->offsetGet('confirmButton')) ? $input->offsetGet('confirmButton') : tra('Yes');

		/*** confirm message ***/
		$customMsg = $input->offsetGet('customMsg');
		$customVerb = $input->offsetGet('customVerb');
		$customObject = $input->offsetGet('customObject');
		if (empty($customMsg)) {
			if (!empty($customVerb) && !empty($customObject)) {
				$customMsg = tr('Are you sure you want to %0 the following %1?', $customVerb, $customObject);
			} else {
				$customMsg = tra('Please confirm your action for the following items:');
			}
		}

		global $auto_query_args;
		$auto_query_args = ['items', 'extra'];
		return [
			'items' => $items = $input->asArray('items'),
			'confirmAction' => $input->offsetGet('confirmAction'),
			'extra' => $input->asArray('extra'),
			'ticket' => $input->offsetGet('ticket'),
			'title' => $title,
			'customMsg' => $customMsg,
			'confirmButton' => $confirmButton,
		];
	}
}
