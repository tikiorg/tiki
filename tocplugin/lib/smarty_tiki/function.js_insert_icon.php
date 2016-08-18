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
 * Function to load jQuery code to insert an iconset icon into an element
 * Useful for when there's no other way to make 3rd party code consistent with the Tiki iconsets
 *
 * type     - determines the js string that will be returned
 * iconname - set the icon to override the default
 * return   - return the js code rather than add to the header
 * @param $params
 * @param $smarty
 * @return string
 * @throws Exception
 */
function smarty_function_js_insert_icon($params, $smarty)
{
	if (!empty($params['type'])) {
		//set icon
		$iconmap = [
			'jscalendar' => 'calendar'
		];
		$iconname = !empty($params['iconname']) ? $params['iconname'] : $iconmap[$params['type']];
		$smarty->loadPlugin('smarty_function_icon');
		$icon = smarty_function_icon(['name' => $iconname], $smarty);
		//set js
		switch ($params['type']) {
			case 'jscalendar' :
				$js = "$('div.jscal > button.ui-datepicker-trigger').empty().append('$icon').addClass('btn btn-sm btn-link').css({'padding' : '0px', 'font-size': '16px'});";
				break;
		}
		//load js
		if (!empty($js)) {
			if (isset($params['return']) && $params['return'] === 'y') {
				return $js;
			} else {
				$headerlib = TikiLib::lib('header');
				$headerlib->add_jq_onready($js);
			}
		}
	}
}