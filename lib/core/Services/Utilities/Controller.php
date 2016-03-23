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
 * Class Services_Utilities_Controller
 */
class Services_Utilities_Controller
{
	function action_alert($input)
	{
		return $this->alert($input);
	}

	function action_modal_alert($input)
	{
		return $this->alert($input);
	}

	function action_alert_button($input)
	{
		$alert = $this->alert($input);
		$alert['ajaxhref'] = $input->ajaxhref->url();
		$alert['ajaxbuttonname'] = $input->ajaxbuttonname->text();
		return $alert;
	}

	private function alert($input)
	{
		$items = json_decode($input->offsetGet('ajaxitems'));
		$toList = json_decode($input->offsetGet('ajaxtoList'));
		$input->replaceFilters(['ajaxtype' => 'word',]);
		return [
			'title' => $input->offsetGet('ajaxtitle'),
			'ajaxtype' => $input->offsetGet('ajaxtype'),
			'ajaxicon' => $input->offsetGet('ajaxicon'),
			'ajaxheading' => $input->offsetGet('ajaxheading'),
			'ajaxitems' => $items,
			'ajaxmsg' => $input->offsetGet('ajaxmsg'),
			'ajaxtoMsg' => $input->offsetGet('ajaxtoMsg'),
			'ajaxtoList' => $toList,
			'ajaxtimeoutMsg' => $input->offsetGet('ajaxtimeoutMsg'),
			'ajaxtimer' => $input->offsetGet('ajaxtimer'),
			'ajaxdismissible' => $input->offsetGet('ajaxdismissible'),
		];
	}

	static function noJsPath ()
	{
		global $prefs;
		if ($prefs['javascript_enabled'] !== 'y') {
			global $base_url;
			$referer = substr($_SERVER['HTTP_REFERER'], strlen($base_url));
		} else {
			$referer = '';
		}
		return $referer;
	}
}
