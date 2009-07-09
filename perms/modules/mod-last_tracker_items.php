<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

// in wikipages, add params like this:    ,trackerId=...,name=...
// in module list, add params like this in params fiels:  trackerId=...&name=...
// name is the name of the tracker field to be displayed (should be descriptive)
global $prefs, $tikilib, $smarty, $user;
global $trklib; include_once('lib/trackers/trackerlib.php');

$smarty->assign('module_error', '');
if ($prefs['feature_trackers'] == 'y') {
	if (empty($module_params['trackerId'])) {
		$smarty->assign('module_error', 'Incorrect parameter');
	} else {
		if ($tikilib->user_has_perm_on_object($user, $module_params['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
			if (isset($module_params['name'])) {
				$module_params['fieldId'] = $trklib->get_field_id($module_params['trackerId'], $module_params['name']);
			}
			if (empty($module_params['fieldId'])) {
				$module_params['fieldId'] = $trklib->get_main_field($module_params['trackerId']);
			}
			if (empty($module_params['fieldId'])) {
				$smarty->assign('module_error', 'Incorrect parameter');
			} else {
				$field_info = $trklib->get_tracker_field($module_params['fieldId']);
				if (!isset($module_params['status'])) {
					$module_params['status'] = '';
				}
				if (empty($module_params['sort_mode'])) {
					$module_params['sort_mode'] = 'created_desc';
				}
				$modLastItems = array();
				//list_items filters the fieldId if hidden...
				$tmp = $trklib->list_items($module_params['trackerId'], 0, $module_rows, $module_params['sort_mode'], array($module_params['fieldId']=>$field_info),'','', $module_params['status']);
				foreach ($tmp['data'] as $data) {
					if (!empty($data['field_values'][0]['value'])) {
						$data['subject'] = $data['field_values'][0]['value'];
						$modLastItems[] = $data;
					}
				}
				$smarty->assign_by_ref('module_params', $module_params);
				$smarty->assign_by_ref('modLastItems', $modLastItems);
			}
		}
	}
} else {
	$smarty->assign('module_error', 'This feature is disabled');
}


