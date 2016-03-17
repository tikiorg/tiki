<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_select_all: Display a checkbox that allows users with javascript to select multiple checkboxes in one click
 *
 * params:
 *  - checkbox_names: Values of the 'name' HTML attribute of the checkboxes to check/uncheck, either as an array or as a comma-separated list
 *	- label: text to display on the right side of the checkbox. If empty, no default text is displayed
 *  - hidden_too: switch the hidden checkboxes too (n/y)
 */
function smarty_function_select_all($params, $smarty)
{
	global $prefs;
	static $checkbox_count = -1;
	
	if ( $prefs['javascript_enabled'] == 'n' || ! is_array($params) || empty($params['checkbox_names']) ) return;

	$checkbox_count++;
	if ($checkbox_count > 0) {
		$id = '_' . $checkbox_count;
	} else {
		$id = '';
	}
	$onclick = '';
	$checkbox_names = $params['checkbox_names'];
	if (!is_array($checkbox_names)) {
		$checkbox_names = explode(',', $checkbox_names);
	}
	if (isset($params['hidden_too']) && $params['hidden_too'] === 'y') {
		$hidden_too = ',true';
	} else {
		$hidden_too = '';
	}

  $smarty->loadPlugin('smarty_modifier_escape');

	foreach ( $checkbox_names as $cn ) {
		$onclick .= "switchCheckboxes(this.form,'" . htmlspecialchars(smarty_modifier_escape($cn, 'javascript')) . "',this.checked$hidden_too);";
	}

	return "<div>\n" . 
			'<input name="switcher'.$id.'" id="clickall'.$id.'" type="checkbox" onclick="' . $onclick . '"' . 
			( empty($params['label']) ? ' title="' . tra('Select All') . '"' : '' ) .
			'/>' . "\n" .
			( ! empty($params['label']) ? '<label for="clickall'.$id.'">' . $params['label'] . "</label>\n" : '' ) . 
			"</div>\n";
}
