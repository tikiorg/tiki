<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_select_all: Display a checkbox that allows users with javascript to select multiple checkboxes in one click
 *
 * params:
 *  - checkbox_names: comma separated list of the values of the 'name' HTML attribute of the checkboxes to check/uncheck
 *	- label: text to display on the right side of the checkbox. If empty, no default text is displayed
 */
function smarty_function_select_all($params, &$smarty) {
	global $prefs;
	if ( $prefs['javascript_enabled'] == 'n' || ! is_array($params) || empty($params['checkbox_names']) ) return;

	$onclick = '';
	$checkbox_names = explode(',', $params['checkbox_names']);
	foreach ( $checkbox_names as $cn ) $onclick .= "switchCheckboxes(this.form,'$cn',this.checked);";

	return "<div>\n"
		. '<input name="switcher" id="clickall" type="checkbox" onclick="' . $onclick . '"'
		. ( empty($params['label']) ? ' title="' . tra('Select All') . '"' : '' )
		.'/>' . "\n"
		. ( ! empty($params['label']) ? '<label for="clickall">' . $params['label'] . "</label>\n" : '' )
		. "</div>\n";
}
