<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$messulib = TikiLib::lib('message');

$trklib = TikiLib::lib('trk');

if (isset($_REQUEST['userId'])) {
	$userwatch = $tikilib->get_user_login($_REQUEST['userId']);
	if ($userwatch === false) {
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->assign('msg', tra("Unknown user"));
		$smarty->display("error.tpl");
		die;
	}
} elseif (isset($_REQUEST['view_user'])) {
	$userwatch = $_REQUEST['view_user'];
	if (!$userlib->user_exists($userwatch)) {
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->assign('msg', tra("Unknown user"));
		$smarty->display("error.tpl");
		die;
	}
} else {
	$access->check_user($user);
	$userwatch = $user;
}

$smarty->assign('userwatch', $userwatch);

if ($prefs['feature_score'] == 'y' and isset($user) and $user != $userwatch) {
	TikiLib::events()->trigger('tiki.user.view',
		array(
			'type' => 'user',
			'object' => $userwatch,
			'user' => $user,
		)
	);
}

$lib = TikiLib::lib('unifiedsearch');
$query = $lib->buildQuery([
	'type' => 'user',
	'object_id' => $userwatch,
]);
$user_info = $query->search($lib->getIndex());

//pass the first user, since there should only be one result. the index, by default returns a list
$user_info[0]['score'] = TikiLib::lib('score')->get_user_score($userwatch);
$smarty->assign("userinfo", $user_info[0]);

//user_tracker_infos is a pref that allows you to list certain tracker fields in the user profile, if set.
if (!empty($prefs['user_tracker_infos'])){
	$trackerinfo = explode(',', $prefs['user_tracker_infos']);
	// in user_tracker_infos, the first entry refers to the tracker id of the user tracker
	$userTrackerId = $trackerinfo[0];
	array_shift($trackerinfo);

	$definition = Tracker_Definition::get($userTrackerId);
	$fields = $definition->getFields();
	$template_fields = array();
	// for each field in the tracker, if it's in the user_tracker_infos field, pass it  to template fields,
	// which will be passed to the tpl
	foreach ($fields as $field){
		if (in_array($field['fieldId'], $trackerinfo)) {
			$template_fields[] = array(
				'label' => $field['name'],
				'permName' => $field['permName'],
			);
		}
	}
	$smarty->assign("template_fields",$template_fields);
}
ask_ticket('user-information');
$userprefslib = TikiLib::lib('userprefs');
if ($user_picture_id = $userprefslib->get_user_picture_id($userwatch)) {
	$smarty->assign('user_picture_id', $user_picture_id);
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-user_unified_details.tpl');
$smarty->display("tiki.tpl");
