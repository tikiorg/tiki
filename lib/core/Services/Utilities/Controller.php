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
 * Class Services_Utilities_Controller
 */
class Services_Utilities_Controller
{
	/**
	 * Used by action functions to give feedback alerts: e.g., success, warning, info, error
	 *
	 * @param $input
	 * @return array
	 */
	function action_alert($input)
	{
		$input->replaceFilters(['type' => 'word',]);
		return [
			'title' => $input->offsetGet('title'),
			'type' => $input->offsetGet('type'),
			'icon' => $input->offsetGet('icon'),
			'heading' => $input->offsetGet('heading'),
			'msg' => $input->offsetGet('msg'),
			'items' => $input->asArray('items'),
			'timeoutMsg' => $input->offsetGet('timeoutMsg'),
		];
	}

}
