<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once('tiki-setup.php');

$access->check_feature(array('feature_trackers', 'feature_ajax', 'wikiplugin_tracker'));

include_once ('lib/wiki-plugins/wikiplugin_tracker.php');
$json_data = array();
$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
	$json_data['res'] = wikiplugin_tracker('',
		array(
			'trackerId' => $re['usersTrackerId'],
			'fields' => $re['registrationUsersFieldIds'],
			'showdesc' => 'y',
			'showmandatory' => 'y',
			'embedded' => 'y',
			'action' => tra('Register'),
			'registration' => 'n',
			'formtag'=>'n',
			'_ajax_form_ins_id' => 'group',
		));
} else {
	$json_data['res'] = $_REQUEST['chosenGroup'];
	$json_data['debug'] = $re;
}

if ($prefs['feature_jquery_validation'] === 'y') {	// dig out the new rules for the js validation
	foreach($headerlib->js as $rank) {
		foreach($rank as $js) {
			if (strpos($js, 'ajaxTrackerFormInit_group') !== false) {
				$json_data['res'] .= $headerlib->wrap_js($js);
			}
		}
	}
}

header('Content-Type: application/json');

$access->output_serialized($json_data);
