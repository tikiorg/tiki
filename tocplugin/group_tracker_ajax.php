<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once('tiki-setup.php');

$access->check_feature(array('feature_trackers', 'feature_ajax', 'wikiplugin_tracker'));

include_once ('lib/wiki-plugins/wikiplugin_tracker.php');

$headerlib->clear_js();								// so store existing js for later and clear

$json_data = array();
$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
	$json_data['res'] = wikiplugin_tracker(
		'',
		array(
			'trackerId' => $re['usersTrackerId'],
			'fields' => explode(':', $re['registrationUsersFieldIds']),
			'showdesc' => 'y',
			'showmandatory' => 'y',
			'embedded' => 'y',
			'action' => tra('Register'),
			'registration' => 'n',
			'formtag'=>'n',
			'_ajax_form_ins_id' => 'group',
		)
	);

	$json_data['res'] .= $headerlib->output_js();
	
} else {
	$json_data['res'] = $_REQUEST['chosenGroup'];
	$json_data['debug'] = $re;
}

/*if ($prefs['feature_jquery_validation'] === 'y') {	// dig out the new rules for the js validation
	foreach ($headerlib->js as $rank) {
		foreach ($rank as $js) {
			if (strpos($js, 'ajaxTrackerValidation_group') !== false) {
				if (preg_match('/validation:\{([\s\S]*?\})\s*\};/s', $js, $m)) {						// get the rules and messages from the js function
					//$m = preg_replace('/\s(?:ignore|submitHandler).* /', '', $m[1]);				// lose a couple of duplicate options
					$m = preg_replace('/,\s*\}\s*$/m', '}', $m[1]);								// a trailing comma
					$o = preg_replace_callback('/(\w*):/', 'group_tracker_ajax_quote', $m);		// surround properties with double quotes
					$json_data['validation']  = json_decode('{' . $o . '}');
				}
			}
		}
	}
}*/

/**
 * @param $matches
 * @return string
 */
function group_tracker_ajax_quote($matches)
{
	return '"' . $matches[1] . '":';
}

header('Content-Type: application/json');

$access->output_serialized($json_data);
