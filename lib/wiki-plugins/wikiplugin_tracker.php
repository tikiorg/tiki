<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Includes a tracker field
// Usage:
// {TRACKER()}{TRACKER}

function wikiplugin_tracker_info()
{
	return array(
		'name' => tra('Tracker'),
		'documentation' => 'PluginTracker',
		'description' => tra('Create a form in a wiki page to populate a tracker'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_tracker' ),
		'body' => tra('Confirmation message after posting form'),
		'icon' => 'pics/icons/application_form.png',
		'params' => array(
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value representing the tracker ID'),
				'filter' => 'digits',
				'default' => '',
			),
			'fields' => array(
				'required' => true,
				'name' => tra('Fields'),
				'description' => tra('Colon-separated list of field IDs to be displayed. Example: 2:4:5'),
				'default' => '',
			),
			'action' => array(
				'required' => false,
				'name' => tra('Action'),
				'description' => tra('Label on the submit button. Default is "Save".'),
				'separator' => ':',
				'default' => 'Save'
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Display the title of the tracker (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => tra('Show the tracker\'s description (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showmandatory' => array(
				'required' => false,
				'name' => tra('Mark Mandatory'),
				'description' => tra('Indicate mandatory fields with an asterisk (shown by default).'),
				'filter' => 'alpha',
				'default' => 'y',
						'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showstatus' => array(
				'required' => false,
				'name' => tra('Show Status'),
				'description' => tra('Show the status of the items (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'embedded' => array(
				'required' => false,
				'name' => tra('Embedded'),
				'description' => tra('Embedded'),
				'filter' => 'alpha',
				'default' => 'n',
			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'email' => array(
				'required' => false,
				'name' => tra('Email'),
				'description' => tra('To send an email once the tracker item has been created. Format: from').'|'.tra('to').'|'.tra('template'),
				'default' => '',
			),
			'emailformat' => array(
				'required' => false,
				'name' => tra('Email format'),
				'description' => tra('Text or html setting'),				
				'default' => 'text',
			),			
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('URL the user is sent to after the form is submitted'),
				'filter' => 'url',
				'separator' => ':',
				'default' => '',
			),
			'target' => array(
				'required' => false,
				'name' => tra('URL Target'),
				'description' => tra('Set the target parameter for the url (determines whether target will open in a new page, etc.'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Blank'), 'value' => '_blank'), 
					array('text' => tra('Parent'), 'value' => '_parent'),
					array('text' => tra('Self'), 'value' => '_self'),
					array('text' => tra('Top'), 'value' => '_top')
				)
			),
			'values' => array(
				'required' => false,
				'name' => tra('Values'),
				'description' => tra('Colon-separated list of default values.for the fields. First value corresponds to first field, second value to second field, etc.'),
				'default' => '',
			),
			'overwrite' => array(
				'required' => false,
				'name' => tra('Overwrite'),
				'description' => tra('Overwrite current field values of the item with the input values'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort'),
				'description' => tra('Display columns in the order listed in the fields parameter instead of by field ID (field ID order is used by default'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'preview' => array(
				'required' => false,
				'name' => tra('Preview'),
				'description' => tra('Label for the preview button. Default:').' "'. tra('Preview') . '"',
				'default' => 'Preview',
			),
			'reset' => array(
				'required' => false,
				'name' => tra('Reset'),
				'description' => tra('Label for the reset button, to return all fields to their default values.'),
				'default' => tra('reset'),
			),
			'view' => array(
				'required' => false,
				'name' => tra('View'),
				'description' => tra('View'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Group'), 'value' => 'group'), 
					array('text' => tra('Page'), 'value' => 'page'),
					array('text' => tra('User'), 'value' => 'user')
				)
			),
			'status' => array(
				'required' => false,
				'name' => tra('Status'),
				'description' => tra('Status of the item used in combination with:').' view=user',
				'default' => '',
			),
			'itemId' =>array(
				'required' => false,
				'name' => tra('ItemId'),
				'description' => tra('ItemId allowing for editing an item'),
				'filter' => 'digits',
				'default' => '',
			),
			'ignoreRequestItemId' => array(
				'required' => false,
				'name' => tra('Ignore ItemId'),
				'description' => tra('Do not filter on the parameter itemId if in the url'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'tpl' => array(
				'required' => false,
				'name' => tra('Template File'),
				'description' => tra('Name of the template used to display the tracker items.'),
				'default' => '',
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki'),
				'description' => tra('Name of the wiki page containing the template to display the tracker items.'),
				'filter' => 'pagename',
				'default' => '',
			),
			'newstatus' => array(
				'required' => false,
				'name' => tra('New Status'),
				'description' => tra('Default status applied to newly created items.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Open'), 'value' => 'o'), 
					array('text' => tra('Pending'), 'value' => 'p'),
					array('text' => tra('Closed'), 'value' => 'c')
				)
			),
			'colwidth' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Specify the width in pixels or percentage of the first column (the labels) in the tracker form.'),
				'default' => '',
				'accepted' => '## or ##%',
			),
			'autosavefields' => array(
				'required' => false,
				'name' => tra('Autosave fields'),
				'description' => tra('Colon-separated list of field IDs to be automatically filled with values'),
				'filter' => 'digits',
				'separator' => ':',
				'default' => '',
			),
			'autosavevalues' => array(
				'required' => false,
				'name' => tra('Autosavevalue'),
				'description' => tra('Colon-separated values corresponding to autosavefields'),
				'filter' => 'text',
				'separator' => ':',
				'default' => '',
			),
			'registration' => array(
				'required' => false,
				'name' => tra('Registration Fields'),
				'description' => tra('Add registration fields such as Username and Password'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'outputtowiki' => array(
				'required' => false,
				'name' => tra('Output To Wiki'),
				'description' => tra('Output result to a new wiki page with the name taken from the input for the specified fieldId'),
				'filter' => 'digits',
				'default' => '',
			),
			'discarditem' => array(
				'required' => false,
				'name' => tra('Discard After Output'),
				'description' => tra('Used when results are output to a wiki page to discard the tracker item itself once the wiki page is created'),
				'filter' => 'alpha',
				'default' => '',
			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'outputwiki' => array(
				'required' => false,
				'name' => tra('Template Page'),
				'description' => tra('Name of the wiki page containing the template to format the output to wiki page'),
				'filter' => 'pagename',
				'default' => '',
			),
			'formtag' => array(
				'required' => false,
				'name' => tra('Embed the tracker in a form tag'),
				'description' => tra('If set to Yes, the tracker is contained in a <form> tag and has action buttons'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_tracker_name($fieldId, $name, $field_errors)
{
	foreach($field_errors['err_mandatory'] as $f) {
		if ($fieldId == $f['fieldId'])
			return '<span class="highlight">'.$name.'</span>';
	}
	foreach($field_errors['err_value'] as $f) {
		if ($fieldId == $f['fieldId'])
			return '<span class="highlight">'.$name.'</span>';
	}
	return $name;
}

function wikiplugin_tracker($data, $params)
{
	global $tikilib, $userlib, $dbTiki, $user, $group, $page, $tiki_p_admin_trackers, $smarty, $prefs, $trklib, $tiki_p_view, $captchalib;
	static $iTRACKER = 0;
	++$iTRACKER;
	include_once('lib/trackers/trackerlib.php');
	$default = array('overwrite' => 'n', 'embedded' => 'n', 'showtitle' => 'n', 'showdesc' => 'n', 'sort' => 'n', 'showmandatory'=>'y', 'status' => '', 'registration' => 'n', 'emailformat' => 'text');
	$params = array_merge($default, $params);
	$item = array();
	
	//var_dump($_REQUEST);
	extract ($params, EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y') {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	if (empty($trackerId) || !($tracker = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	if ($t = $trklib->get_tracker_options($trackerId)) {
		$tracker = array_merge($tracker, $t);
	}
	if (empty($trackerId) && !empty($view) && $view == 'user' && $prefs['userTracker'] == 'y') { // the user tracker item
		$utid = $userlib->get_tracker_usergroup($user);
		if (!empty($utid) && !empty($utid['usersTrackerId'])) {
			$itemId = $trklib->get_item_id($utid['usersTrackerId'], $utid['usersFieldId'], $user);
			$trackerId = $utid['usersTrackerId'];
			$usertracker = true;
		}
	} elseif (!empty($trackerId) && !empty($view) && $view == 'user') {// the user item of a tracker
		$itemId = $trklib->get_user_item($trackerId, $tracker, null, null, strlen($status) == 1 ? $status : '');
		$usertracker = true;
	} elseif (!empty($trackerId) && !empty($view) && $view == 'page' && !empty($_REQUEST['page']) && (($f = $trklib->get_field_id_from_type($trackerId, 'k', '1%')) || ($f = $trklib->get_field_id_from_type($trackerId, 'k', '%,1%')) || ($f =  $trklib->get_field_id_from_type($trackerId, 'k')))) {// the page item
		$itemId = $trklib->get_item_id($trackerId, $f, $_REQUEST['page']);
	} elseif (!empty($trackerId) && !empty($_REQUEST['view_user'])) {
		$itemId = $trklib->get_user_item($trackerId, $tracker, $_REQUEST['view_user']);
	} elseif (!empty($_REQUEST['itemId']) && (empty($ignoreRequestItemId) || $ignoreRequestItemId != 'y')) {
		$itemId = $_REQUEST['itemId'];
		$item = $trklib->get_tracker_item($itemId);
		$trackerId = $item['trackerId'];
	} elseif (!empty($view) && $view == 'group') {
		$gtid = $userlib->get_grouptrackerid($group);
		if(isset($gtid['groupTrackerId'])) {
			$trackerId = $gtid['groupTrackerId'];
			$itemId = $trklib->get_item_id($trackerId, $gtid['groupFieldId'], $group);
			$grouptracker = true;
		}
	}
	if (!isset($trackerId)) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}

	if (!isset($action)) {
		$action = array('Save');
	}
	if (!is_array($action)) {
		$action = array( $action );
	}
	if (isset($preview)) {
		if (empty($preview)) {
			$preview = 'Preview';
		}
	} else {
		unset($_REQUEST['tr_preview']);
	}
	if (isset($reset)) {
		if (empty($reset)) {
			$reset = 'reset';
		}
	} else {
		unset($_REQUEST['tr_reset']);
	}
	$smarty->assign('showmandatory',  empty($wiki) && empty($tpl)? 'n': $showmandatory); 
	if (!empty($wiki)) {
		if (preg_match('/^wiki:(.+)$/', $wiki, $wiki_matches)) {
			$wiki = $wiki_matches[1];
		}

		$wiki = trim($wiki);
	}

	if (!isset($params['formtag'])) {
		$params['formtag'] = 'y';
	}

	$fields_prefix = 'ins_';

	if (isset($values)) {
		if (!is_array($values)) {
			$values = $tikilib->quotesplit(':', $values);
			foreach ($values as $i=>$v) {
				$values[$i] = preg_replace('/^"(.*)"$/', '$1', $v);
			}
		}
	}
	if (isset($_REQUEST['values'])) {
		if (is_array($_REQUEST['values'])) {
			foreach ($_REQUEST['values'] as $i=>$k) {
				$_REQUEST['values'][$i] = urldecode($k);
			}
		} else {
			$_REQUEST['values'] = urldecode($_REQUEST['values']);
		}
	}

	$perms = $tikilib->get_perm_object($trackerId, 'tracker', $tracker, false);
	
	if (empty($_SERVER['SCRIPT_NAME']) || strpos($_SERVER['SCRIPT_NAME'], 'tiki-register.php') === false) {
		if (!empty($itemId) && $tracker['writerCanModify'] == 'y' && isset($usertracker) && $usertracker) { // user tracker he can modify
		} elseif (!empty($itemId) && $tracker['writerCanModify'] == 'y' && $user && (($itemUser = $trklib->get_item_creator($trackerId, $itemId)) == $user || ($tracker['userCanTakeOwnership'] == 'y' && empty($itemUser)))) {
		} elseif (!empty($itemId) && isset($grouptracker) && $grouptracker) {
		} else {
			if ($perms['tiki_p_create_tracker_items'] == 'n' && empty($itemId)) {
				return '<b>'.tra("You do not have permission to insert an item").'</b>';
			} elseif (!empty($itemId)) {
				$item_info = $trklib->get_tracker_item($itemId);
				if (!(($perms['tiki_p_modify_tracker_items'] == 'y' and $item_info['status'] != 'p' and $item_info['status'] != 'c') || ($perms['tiki_p_modify_tracker_items_pending'] == 'y' and $item_info['status'] == 'p') ||  ($perms['tiki_p_modify_tracker_items_closed'] == 'y' and $item_info['status'] == 'c'))) { 
					if ($tracker['writerGroupCanModify'] == 'y' && in_array($trklib->get_item_group_creator($trackerId, $itemId), $tikilib->get_user_groups($user))) {
						global $group;
						$smarty->assign_by_ref('ours', $group);
					} else 
						return '<b>'.tra("You do not have permission to modify an item").'</b>';
				}
			}
		}
	}
	if (!empty($itemId)) {
		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_action('Viewed', $itemId, 'trackeritem', $_SERVER['REQUEST_URI']);
	}

	if (isset($_REQUEST['removeattach']) && $tracker['useAttachments'] == 'y') {
		$owner = $trklib->get_item_attachment_owner($_REQUEST['removeattach']);
		if ($perms['tiki_p_admin_trackers'] == 'y' || ($user && $user == $owner)) {
			$trklib->remove_item_attachment($_REQUEST["removeattach"]);
			unset($_REQUEST['removeattach']);
		}
	}
	if (isset($_REQUEST['removeImage']) && !empty($_REQUEST['trackerId']) && !empty($_REQUEST['itemId']) && !empty($_REQUEST['fieldId']) && !empty($_REQUEST['fieldName'])) {
		$img_field = array('data' => array());
		$img_field['data'][] = array('fieldId' => $_REQUEST['fieldId'], 'type' => 'i', 'name' => $_REQUEST['fieldName'], 'value' => 'blank');
		$trklib->replace_item($_REQUEST['trackerId'], $_REQUEST['itemId'], $img_field);
	}
	$back = '';
	$js = '';

	$thisIsThePlugin = isset($_REQUEST['iTRACKER']) && $_REQUEST['iTRACKER'] == $iTRACKER;

	if (!isset($_REQUEST["ok"]) || $_REQUEST["ok"]  == "n" || !$thisIsThePlugin || isset($_REQUEST['tr_preview'])) {
		$field_errors = array('err_mandatory'=>array(), 'err_value'=>array());
	
			global $notificationlib; include_once('lib/notifications/notificationlib.php');
			$tracker = $trklib->get_tracker($trackerId);
			$tracker = array_merge($tracker, $trklib->get_tracker_options($trackerId));
			if ((!empty($tracker['start']) && $tikilib->now < $tracker['start']) || (!empty($tracker['end']) && $tikilib->now > $tracker['end']))
				return;
			$outf = array();
			if (!empty($fields)  || !empty($wiki) || !empty($tpl)) {
				if ($registration == 'y' && $prefs["user_register_prettytracker"] == 'y' && !empty($prefs["user_register_prettytracker_tpl"])) {
					$smarty->assign('register_login', $smarty->fetch('register-login.tpl'));
					$smarty->assign('register_email', $smarty->fetch('register-email.tpl'));
					$smarty->assign('register_pass', $smarty->fetch('register-pass.tpl'));
					$smarty->assign('register_pass2', $smarty->fetch('register-pass2.tpl'));
					$smarty->assign('register_passcode', $smarty->fetch('register-passcode.tpl'));
					$smarty->assign('register_groupchoice', $smarty->fetch('register-groupchoice.tpl'));
				}
				if (!empty($wiki)) {
					$outf = $trklib->get_pretty_fieldIds($wiki, 'wiki', $outputPretty);
				} elseif (!empty($tpl)) {
					$outf = $trklib->get_pretty_fieldIds($tpl, 'tpl', $outputPretty);
				} elseif (!empty($fields)) {
					$outf = preg_split('/ *: */', $fields);
				}
				if (!empty($_REQUEST['autosavefields'])) {
					$autosavefields = explode(':', $_REQUEST['autosavefields']);
					$autosavevalues = explode(':', $_REQUEST['autosavevalues']);
					if (isset($params['autosavefields'])) {
						$autosavefields = array_merge($autosavefields, $params['autosavefields']);
						$autosavevalues = array_merge($autosavevalues, $params['autosavevalues']);
					}
				}
				if (!empty($autosavefields)) {
					$outf = array_merge($outf, $autosavefields);
				}
			}

			$definition = Tracker_Definition::get($trackerId);
			$factory = new Tracker_Field_Factory($definition, isset($item_info) ? $item_info : array());
			$flds = array('data' => $definition->getFields());
			$bad = array();
			$embeddedId = false;
			$onemandatory = false;
			$full_fields = array();
			$mainfield = '';

			if ($thisIsThePlugin) {
				/* ------------------------------------- Recup all values from REQUEST -------------- */
				foreach ($flds['data'] as $fl) {
					// First convert track_xx to array (need to be up here before setting autosave fields)
					$incomingTrackName = $fields_prefix . $fl["fieldId"];
					if (isset($_REQUEST[$incomingTrackName])) {
						$_REQUEST['track'][$fl["fieldId"]] = $_REQUEST[$incomingTrackName];
					} else if (isset($_FILES[$incomingTrackName])) {	// also for uploaded files
						foreach ($_FILES[$incomingTrackName] as $lbl => $val) {
							$_FILES['track'][$lbl][$fl["fieldId"]] = $val;
						}
					}
				}
				if (!empty($autosavefields)) {
					foreach ($autosavefields as $i=>$f) {
						if (!$ff = $trklib->get_field($f, $flds['data'])) {
							continue;
						}
						if (!$trklib->fieldId_is_editable($ff, $item_info)) {
							continue;
						}
						if (preg_match('/categories\(([0-9]+)\)/', $autosavevalues[$i], $matches)) {
							global $categlib; include_once('lib/categories/categlib.php');
							$categs = $categlib->list_categs($matches[1]);
							$_REQUEST["ins_$f"][] = $categs[0]['categId'];
						} elseif (preg_match('/preference\((.*)\)/', $autosavevalues[$i], $matches)) {
							$_REQUEST["$ins_id_$f"] = $prefs[$matches[1]];
						} elseif ($ff['type'] == 'e') {
							$_REQUEST["ins_$f"][] = $autosavevalues[$i];
						} else {
							$_REQUEST['track'][$f] = $autosavevalues[$i];
						}
					}
				}
				foreach ($flds['data'] as $k => $field) {
					$handler = $factory->getHandler($field);

					if ($handler) {
						$ins_fields['data'][$k] = array_merge($field, $handler->getValues($_REQUEST));
					}
				}
				$cpt = 0;
				if (isset($fields)) {
					$fields_plugin = preg_split('/:/', $fields);
				}
				if (!isset($itemId) && $tracker['oneUserItem'] == 'y') {
					$itemId = $trklib->get_user_item($trackerId, $tracker);
				}

				foreach ($flds['data'] as $fl) {
					if ($factory->getHandler($fl)) {
						continue;
					}
					// Types that were initially supported by the plugin
					if (! in_array($fl['type'], array('q', 'k', 'u', 'g', 'I', 'C', 'n', 'j', 'f'))) {
						continue;
					}
					// store value to display it later if form
					// isn't fully filled.
					if ($flds['data'][$cpt]['type'] == 's' && $flds['data'][$cpt]['name'] == 'Rating') {
						if (isset($_REQUEST['track'][$fl['fieldId']])) {
							$newItemRate = $_REQUEST['track'][$fl['fieldId']];
							$newItemRateField = $fl['fieldId'];
						} else {
							$newItemRate = NULL;
						}
					
					} elseif ($flds["data"][$cpt]["type"] == 'c') {
						if (!isset($_REQUEST['track'][$fl['fieldId']])) {
							$_REQUEST['track'][$fl['fieldId']] = 'n';
						}	
					} elseif (($flds['data'][$cpt]['type'] == 'u' || $flds['data'][$cpt]['type'] == 'g' || $flds['data'][$cpt]['type'] == 'I' || $flds['data'][$cpt]['type'] == 'k') &&	// user/group/ip
							  ($flds['data'][$cpt]['options_array'][0] == '1' || $flds['data'][$cpt]['options_array'][0] == '2')) {	// create or modif
						
						if ($tiki_p_admin_trackers === 'y' && isset($_REQUEST['track'][$fl['fieldId']]) && in_array($fl['fieldId'], $fields_plugin)) {
							// but admins can override if the field is in the form
							// do nothing, act casual
						} else if (empty($itemId) && ($flds['data'][$cpt]['options_array'][0] == '1' || $flds['data'][$cpt]['options_array'][0] == '2')) {
							if ($flds['data'][$cpt]['type'] == 'u') {
								$_REQUEST['track'][$fl['fieldId']] = empty($user)?(empty($_REQUEST['name'])? '':$_REQUEST['name']):$user;
							} elseif ($flds['data'][$cpt]['type'] == 'g') {
								$_REQUEST['track'][$fl['fieldId']] = $group;
							} elseif ($flds['data'][$cpt]['type'] == 'I') {
								$_REQUEST['track'][$fl['fieldId']] = $tikilib->get_ip_address();
							} elseif ($flds['data'][$cpt]['type'] == 'k') {
								$_REQUEST['track'][$fl['fieldId']] = isset($_REQUEST['page'])?$_REQUEST['page']: '';
							}
						} elseif (!empty($itemId) && $flds['data'][$cpt]['options_array'][0] == '2') {
							if ($flds['data'][$cpt]['type'] == 'u')
								$_REQUEST['track'][$fl['fieldId']] = $user;
							elseif ($flds['data'][$cpt]['type'] == 'g')
								$_REQUEST['track'][$fl['fieldId']] = $group;
							elseif ($flds['data'][$cpt]['type'] == 'I')
								$_REQUEST['track'][$fl['fieldId']] = $tikilib->get_ip_address();
						}
					} elseif (($flds['data'][$cpt]['type'] == 'C' || $flds['data'][$cpt]['type'] == 'e') && empty($_REQUEST['track'][$fl['fieldId']])) {
						$_REQUEST['track'][$fl['fieldId']] = '';
					} elseif ($flds['data'][$cpt]['type'] == 'f') {
						$ins_id = $fields_prefix . $fl['fieldId'];
						if (isset($_REQUEST[$ins_id.'Day']) || isset($_REQUEST[$ins_id.'Hour'])) {
							$_REQUEST['track'][$fl['fieldId']] = $trklib->build_date($_REQUEST, $flds['data'][$cpt], $ins_id);
						}
					}
					if (isset($_REQUEST['ins_'.$fl['fieldId']])) { // to remember if error
						$_REQUEST['track'][$fl['fieldId']] = $_REQUEST['ins_'.$fl['fieldId']];
					}

					if(isset($_REQUEST['track'][$fl['fieldId']])) {
						$flds['data'][$cpt]['value'] = $_REQUEST['track'][$fl['fieldId']];
					} else {
						$flds['data'][$cpt]['value'] = '';
						if (empty($itemId)) {
							if ($fl['type'] == 'c') {
								$_REQUEST['track'][$fl['fieldId']] = 'n';
							} elseif ($fl['type'] == 'R' && $fl['isMandatory'] == 'y') {
								// if none radio is selected, there will be no value and no error if mandatory
								$_REQUEST['track'][$fl['fieldId']] = '';
							}
						}
					}
					if (!empty($_REQUEST['other_track_'.$fl['fieldId']])) {
						$flds['data'][$cpt]['value'] = $_REQUEST['other_track_'.$fl['fieldId']];
					}
					if ($flds['data'][$cpt]['isMultilingual'] == 'y') {
						foreach ($prefs['available_languages'] as $num=>$tmplang) {
							if (isset($_REQUEST['track'][$fl['fieldId']][$tmplang])) {
								$fl['lingualvalue'][$num]['value'] = $_REQUEST['track'][$fl['fieldId']][$tmplang];
								$fl['lingualvalue'][$num]['lang'] = $tmplang;
							}
						}
					}
					$full_fields[$fl['fieldId']] = $fl;
					
					if ($embedded == 'y' and $fl['name'] == 'page') {
						$embeddedId = $fl['fieldId'];
					}
					if ($fl['isMain'] == 'y')
						$mainfield = $flds['data'][$cpt]['value'];
					$cpt++;
				} /*foreach */

				if (isset($_REQUEST['track'])) {
					foreach ($_REQUEST['track'] as $fld=>$val) {
						//$ins_fields["data"][] = array('fieldId' => $fld, 'value' => $val, 'type' => 1);
						if (!empty($_REQUEST['other_track_'.$fld])) {
							$val = $_REQUEST['other_track_'.$fld];
						}
						$ins_fields["data"][] = array_merge(array('value' => $val), $full_fields[$fld]);
					}
				}

				if ($embedded == 'y' && isset($_REQUEST['page'])) {
					$ins_fields["data"][] = array('fieldId' => $embeddedId, 'value' => $_REQUEST['page']);
				}
				$ins_categs = array();
				foreach ($ins_fields['data'] as $current_field) {
					if ($current_field['type'] == 'e' && isset($current_field['selected_categories'])) {
						$ins_categs = array_merge($ins_categs, $current_field['selected_categories']);
					}
				}
				$categorized_fields = $definition->getCategorizedFields();
				/* ------------------------------------- End recup all values from REQUEST -------------- */

				/* ------------------------------------- Check field values for each type and presence of mandatory ones ------------------- */
				$field_errors = $trklib->check_field_values($ins_fields, $categorized_fields, $trackerId, empty($itemId)?'':$itemId);

				if (empty($user) && $prefs['feature_antibot'] == 'y' && $registration != 'y') {
					// in_tracker session var checking is for tiki-register.php
					if (!$captchalib->validate()) {
						$field_errors['err_antibot'] = 'y';
					}
				}

				// check valid page name for wiki output if requested
				if (isset($outputtowiki) && !empty($outputwiki)) {
					$newpagename = '';
					foreach ($ins_fields["data"] as $fl) {
						if ($fl["fieldId"] == $outputtowiki) {
							$newpagename = $fl["value"];
						}
						if ($fl["type"] == 'F') {
							$newpagefreetags = $fl["value"];
						}
						$newpagefields[] = $fl["fieldId"];
					}
					if ($newpagename) {
						if ($tikilib->page_exists($newpagename)) {
							$field_errors['err_outputwiki'] = tra('The page to output the results to already exists. Try another name.');
						}
						$page_badchars_display = ":/?#[]@!$&'()*+,;=<>";
						$page_badchars = "/[:\/?#\[\]@!$&'()*+,;=<>]/";
						$matches = preg_match($page_badchars, $newpagename);
						if ($matches) {
							$field_errors['err_outputwiki'] = tra("The page to output the results to contains the following prohibited characters: $page_badchars_display. Try another name.");
						} 
					} else {
						unset($outputtowiki);
					}
				}
				if( count($field_errors['err_mandatory']) == 0  && count($field_errors['err_value']) == 0 && empty($field_errors['err_antibot']) && empty($field_errors['err_outputwiki']) && !isset($_REQUEST['tr_preview'])) {
					/* ------------------------------------- save the item ---------------------------------- */
					if (isset($_REQUEST['status'])) {
						$status = $_REQUEST['status'];
					} elseif (isset($newstatus) && ($newstatus == 'o' || $newstatus == 'c'|| $newstatus == 'p')) {
						$status = $newstatus;
					} elseif (empty($itemId) && isset($tracker['newItemStatus'])) {
						$status = $tracker['newItemStatus'];
					} else {
						$status = '';
					}
					$rid = $trklib->replace_item($trackerId, $itemId, $ins_fields, $status, $ins_categs);
					$trklib->categorized_item($trackerId, $rid, $mainfield, $ins_categs);
					if (isset($newItemRate)) {
						$trklib->replace_rating($trackerId, $rid, $newItemRateField, $user, $newItemRate);
					}
					// now for wiki output if desired
					if (isset($outputtowiki) && !empty($outputwiki)) {
						// note that values will be raw - that is the limit of the capability of this feature for now
						$newpageinfo = $tikilib->get_page_info($outputwiki);
						$wikioutput = $newpageinfo["data"];
						$newpagefields = $trklib->get_pretty_fieldIds($outputwiki, 'wiki', $outputPretty);
						foreach($newpagefields as $lf) {
							$wikioutput = str_replace('{$f_' . $lf . '}', $trklib->get_item_value($trackerId, $rid, $lf), $wikioutput);
						}
						$tikilib->create_page($newpagename, 0, $wikioutput, $tikilib->now, '', $user, $tikilib->get_ip_address());
						$cat_desc = '';
						$cat_type = 'wiki page';
						$cat_name = $newpagename;
						$cat_objid = $newpagename;
						$cat_href = "tiki-index.php?page=".urlencode($newpagename);
						if (count($ins_categs)) {
							$_REQUEST['cat_categories'] = $ins_categs;
							$_REQUEST['cat_categorize'] = 'on';
							include_once("categorize.php");
						}
						if (isset($newpagefreetags) && $newpagefreetags) {
							$_REQUEST['freetag_string'] = $newpagefreetags;
							include_once("freetag_apply.php");
						}
						if ($discarditem == 'y') {
							$trklib->remove_tracker_item($rid);
						}
						if (empty($url)) {
							global $wikilib;
							$url[0] = $wikilib->sefurl($newpagename);
						}
					}
					// end wiki output
					if (!empty($email)) {
						$emailOptions = preg_split("#\|#", $email);
						if (is_numeric($emailOptions[0])) {
							$emailOptions[0] = $trklib->get_item_value($trackerId, $rid, $emailOptions[0]);
						}
						if (empty($emailOptions[0])) { // from
							$emailOptions[0] = $prefs['sender_email'];
						}
						if (empty($emailOptions[1])) { // to
							$emailOptions[1][0] = $prefs['sender_email'];
						} else {
							$emailOptions[1] = preg_split('/ *, */', $emailOptions[1]);
							foreach ($emailOptions[1] as $key=>$email) {
								if (is_numeric($email))
									$emailOptions[1][$key] = $trklib->get_item_value($trackerId, $rid, $email);
							}
						}
						include_once('lib/webmail/tikimaillib.php');
						$mail = new TikiMail();
						$mail->setHeader('From', $emailOptions[0]);
						
						if (!empty($emailOptions[2])) { //tpl
							$emailOptions[2] = preg_split('/ *, */', $emailOptions[2]);
							foreach ($emailOptions[2] as $ieo=>$eo) {
								if (!preg_match('/\.tpl$/', $eo))
									$emailOptions[2][$ieo] = $eo.'.tpl';
								$tplSubject[$ieo] = str_replace('.tpl', '_subject.tpl', $emailOptions[2][$ieo]);
							}
						} else {
							$emailOptions[2] = array('tracker_changed_notification.tpl');
						}
						if (empty($tplSubject)) {
							$tplSubject = array('tracker_changed_notification_subject.tpl');
						}
						$itpl = 0;
						$smarty->assign('mail_date', $tikilib->now);
						$smarty->assign('mail_itemId', $rid);
						foreach ($emailOptions[1] as $ieo=>$ueo) {
							@$mail_data = $smarty->fetch('mail/'.$tplSubject[$itpl]);
							if (empty($mail_data))
								$mail_data = tra('Tracker was modified at '). $_SERVER["SERVER_NAME"];
							$mail->setSubject($mail_data);
							$mail_data = $smarty->fetch('mail/'.$emailOptions[2][$itpl]);
							if ($emailformat == 'html') {
							$mail->setHtml($mail_data);
							} else {
							$mail->setText($mail_data);
							}
							$mail->buildMessage(array('text_encoding' => '8bit'));
							$mail->send($ueo);
							if (isset($tplSubject[$itpl+1]))
								++$itpl;
						}
					}
					if (empty($url)) {
						if (!empty($page)) {
							$url = "tiki-index.php?page=".urlencode($page);
							if (!empty($itemId)) {
								$url .= "&itemId=".$itemId;
							}
							$url .= "&ok=y&iTRACKER=$iTRACKER";
							$url .= "#wikiplugin_tracker$iTRACKER";
							header("Location: $url");
							exit;
						} else {
							return '';
						}
					} else {
						$key = 0;
						foreach ($action as $key=>$act) {
							if (!empty($_REQUEST["action$key"])) {
								break;
							}
						}
						$itemIdPos = strpos($url[$key], 'itemId');
						if ($itemIdPos !== false) {
							if (strstr($url[$key], '#itemId')) {
								$url[$key] = str_replace('#itemId', $rid, $url[$key]);
							} else if (($itemIdPos+strlen('itemId') >= strlen($url[$key])-1) || (substr($url[$key],$itemIdPos+strlen('itemId'),1) == "&")) {
								// replace by the itemId if in the end (or -1: for backward compatibility so that "&itemId=" also works) or if it is followed by an '&'
								$url[$key] = str_replace('itemId', 'itemId='.$rid, $url[$key]);
							}
						}
						header('Location: '.$url[$key]);
						exit;
					}
					/* ------------------------------------- end save the item ---------------------------------- */
				} elseif (isset($_REQUEST['trackit']) and $_REQUEST['trackit'] == $trackerId) {
					$smarty->assign('wikiplugin_tracker', $trackerId);//used in vote plugin
				}

			} else if ((empty($itemId) || $overwrite == 'y') && !empty($values) || (!empty($_REQUEST['values']) and empty($_REQUEST['prefills']))) { // assign default values for each filedId specify
				if (empty($values)) { // url with values[]=x&values[] witouth the list of fields
					$values = $_REQUEST['values'];
				}
				if (!is_array($values)) {
					$values = array($values);
				}
				if (isset($fields)) {
					$fl = preg_split('/:/', $fields);
					for ($j = 0, $count_fl = count($fl); $j < $count_fl; $j++) {
						for ($i = 0, $count_flds = count($flds['data']); $i < $count_flds; $i++) {
							if ($flds['data'][$i]['fieldId'] == $fl[$j]) { 
								$flds['data'][$i]['value'] = $values[$j];
							}	
						}
					}
				} else { // values contains all the fields value in the default order
					$i = 0;
					foreach ($values as $value) {
						$flds['data'][$i++]['value'] = $value;
					}
				}
			
			} elseif (!empty($itemId)) {
				if (isset($fields)) {
					$fl = preg_split('/:/', $fields);
					$filter = '';
					foreach ($flds['data'] as $f) {
						if (in_array($f['fieldId'], $fl))
							$filter[] = $f;
					}
				} else {
					$filter = &$flds['data'];
				}
				if (!empty($filter)) {
					foreach ($filter as $f) {
						$filter2[$f['fieldId']] = $f;
					}
					$flds['data'] = $trklib->get_item_fields($trackerId, $itemId, $filter2, $itemUser, true);
				}
				// todo: apply the values for fields with no values
			} else {
				if (isset($_REQUEST['values']) && isset($_REQUEST['prefills'])) { //url:prefields=1:2&values[]=x&values[]=y
					if (!is_array($_REQUEST['values']))
						$_REQUEST['values'] = array($_REQUEST['values']);
					$fl = preg_split('/:/', $_REQUEST['prefills']);
				} else {
					unset($fl);
				}
				for ($i = 0, $count_flds2 = count($flds['data']); $i < $count_flds2; $i++) {
					if (isset($fl) && ($j = array_search($flds['data'][$i]['fieldId'], $fl)) !== false) {
						$flds['data'][$i]['value'] = $_REQUEST['values'][$j];
					} else {
						$flds['data'][$i]['value'] = ''; // initialize fields with blank values
					}
				}
			}

			$optional = array();
			$outf = array();
			if (isset($fields) && !empty($fields)) {
				$fl = preg_split('/:/', $fields);
				if ($sort == 'y')
					$flds = $trklib->sort_fields($flds, $fl);		
				foreach ($fl as $l) {
					if (substr($l, 0, 1) == '-') {
						$l = substr($l, 1);
						$optional[] = $l;
					}
					$ok = false;
					foreach ($flds['data'] as $f) {
						if ($f['fieldId'] == $l) {
							$ok = true;
							break;
						}
					}
					if (!$ok) {
						$back .= tra('Incorrect fieldId:').' '.$l;
					}
					$outf[] = $l;
				}
			} elseif (empty($fields) && !empty($wiki)) {
				$wiki_info = $tikilib->get_page_info($wiki);
				preg_match_all('/\$f_([0-9]+)/', $wiki_info['data'], $matches);
				$outf = $matches[1];
			} elseif (empty($fields) && !empty($tpl)) {
				$f = $smarty->get_filename($tpl);
				if (!empty($f)) {
					$f = file_get_contents($f);
					preg_match_all('/\$f_([0-9]+)/', $f, $matches);
					$outf = $matches[1];
				}
			} elseif (empty($fields) && empty($wiki)) {
				foreach ($flds['data'] as $f) {
					if ($f['isMandatory'] == 'y')
						$optional[] = $f['fieldId'];
					$outf[] = $f['fieldId'];
				}
			}

			// Display warnings when needed
			
			if(count($field_errors['err_mandatory']) > 0) {
				$smarty->assign_by_ref('err_mandatory', $field_errors['err_mandatory']);
			}
			if(count($field_errors['err_value']) > 0) {
				$smarty->assign_by_ref('err_value', $field_errors['err_value']);
			}
			if(count($field_errors['err_mandatory']) > 0 || count($field_errors['err_value']) > 0) {
				$back .= $smarty->fetch('tracker_error.tpl');
				$_REQUEST['error'] = 'y';
			}
			if (isset($field_errors['err_antibot'])) {
				$back.= '<div class="simplebox highlight"><img src="pics/icons/exclamation.png" alt=" '.tra('Error').'" style="vertical-align:middle" /> ';
				$back .= $captchalib->getErrors();
				$back.= '</div><br />';
				$_REQUEST['error'] = 'y';
			}
			if (isset($field_errors['err_outputwiki'])) {
				$back.= '<div class="simplebox highlight"><img src="pics/icons/exclamation.png" alt=" '.tra('Error').'" style="vertical-align:middle" /> ';
				$back .= $field_errors['err_outputwiki'];
				$back.= '</div><br />';
				$_REQUEST['error'] = 'y';
			}
			if (count($field_errors['err_mandatory']) > 0 || count($field_errors['err_value']) > 0 || isset($field_errors['err_antibot']) || isset($field_errors['err_outputwiki'])) {
				$smarty->assign('input_err', 'y');
			}
			if (!empty($page))
				$back .= '~np~';
			$smarty->assign_by_ref('tiki_p_admin_trackers', $perms['tiki_p_admin_trackers']);
			$smarty->assign('trackerEditFormId', $iTRACKER);
			if ($prefs['feature_jquery'] == 'y' && $prefs['feature_jquery_validation'] == 'y') {
				global $validatorslib;
				include_once('lib/validatorslib.php');
				$customvalidation = '';
				$customvalidation_m = '';
				if ($registration == 'y') {
					// email validation
					$customvalidation .= 'email: { ';
					$customvalidation .= 'required: true, ';
					$customvalidation .= 'email: true }, ';
					$customvalidation_m .= 'email: { email: "'. tra("Invalid email") .  '"}, ';
					// password validation
					$customvalidation .= 'pass: { ';
					$customvalidation .= 'required: true, ';
					$customvalidation .= 'remote: { ';
					$customvalidation .= 'url: "validate-ajax.php", ';
					$customvalidation .= 'type: "post", ';
					$customvalidation .= 'data: { ';
					$customvalidation .= 'validator: "password", ';
					$customvalidation .= 'input: function() { ';
					$customvalidation .= 'return $("#pass1").val(); ';
					$customvalidation .= '} } } ';
					$customvalidation .= '}, ';
					// password repeat validation
					$customvalidation .= 'passAgain: { equalTo: "#pass1" }, ';
					$customvalidation_m .= 'passAgain: { equalTo: "'. tra("Passwords do not match") .  '"}, ';
					// username validation
					$customvalidation .= 'name: { ';
					$customvalidation .= 'required: true, ';
					$customvalidation .= 'remote: { ';
					$customvalidation .= 'url: "validate-ajax.php", ';
					$customvalidation .= 'type: "post", ';
					$customvalidation .= 'data: { ';
					$customvalidation .= 'validator: "username", ';
					$customvalidation .= 'input: function() { ';
					$customvalidation .= 'return $("#name").val(); ';
					$customvalidation .= '} } } ';
					$customvalidation .= '}, ';
					if ($prefs['feature_antibot'] == 'y' && empty($user) && $prefs['recaptcha_enabled'] != 'y') {
						// antibot validation   
						$customvalidation .= '"captcha[input]": { ';
						$customvalidation .= 'required: true, ';
						$customvalidation .= 'remote: { ';
						$customvalidation .= 'url: "validate-ajax.php", ';
						$customvalidation .= 'type: "post", ';
						$customvalidation .= 'data: { ';
						$customvalidation .= 'validator: "captcha", ';
						$customvalidation .= 'parameter: function() { ';
						$customvalidation .= 'return $jq("#captchaId").val(); ';
						$customvalidation .= '}, ';
						$customvalidation .= 'input: function() { ';
						$customvalidation .= 'return $jq("#antibotcode").val(); ';
						$customvalidation .= '} } } ';
						$customvalidation .= '}, ';
					}
				}
				$validationjs = $validatorslib->generateTrackerValidateJS( $flds['data'], $fields_prefix, $customvalidation, $customvalidation_m );

				$smarty->assign('validationjs', $validationjs);
				$back .= $smarty->fetch('tracker_validator.tpl');
			}
			if ($params['formtag'] == 'y') {
				$back .= '<form name="editItemForm' . $iTRACKER . '" id="editItemForm' . $iTRACKER . '" enctype="multipart/form-data" method="post"'.(isset($target)?' target="'.$target.'"':'').' action="'. $_SERVER['REQUEST_URI'] .'"><input type="hidden" name="trackit" value="'.$trackerId.'" />';
				$back .= '<input type="hidden" name="iTRACKER" value="'.$iTRACKER.'" />';
				$back .= '<input type="hidden" name="refresh" value="1" />';
			}
			if (isset($_REQUEST['page']))
				$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
			 // for registration
			if (isset($_REQUEST['name']))
				$back.= '<input type="hidden" name="name" value="'.$_REQUEST["name"].'" />';
			if (isset($_REQUEST['pass'])) {
				$back.= '<input type="hidden" name="pass" value="'.$_REQUEST["pass"].'" />';
				$back.= '<input type="hidden" name="passAgain" value="'.$_REQUEST["pass"].'" />';
			}
			if (isset($_REQUEST['email']))
				$back.= '<input type="hidden" name="email" value="'.$_REQUEST["email"].'" />';
			if (isset($_REQUEST['antibotcode']))
				$back.= '<input type="hidden" name="antibotcode" value="'.$_REQUEST["antibotcode"].'" />';
			if (isset($_REQUEST['chosenGroup'])) // for registration
				$back.= '<input type="hidden" name="chosenGroup" value="'.$_REQUEST["chosenGroup"].'" />';
			if (isset($_REQUEST['register']))
				$back.= '<input type="hidden" name="register" value="'.$_REQUEST["register"].'" />';
			if ($showtitle == 'y') {
				$back.= '<div class="titlebar">'.$tracker["name"].'</div>';
			}
			if ($showdesc == 'y' && $tracker['description']) {

				if ($tracker['descriptionIsParsed'] == 'y') {
					$back .= '<div class="wikitext">'.$tikilib->parse_data($tracker['description']).'</div><br />';
				} else {
					$back.= '<div class="wikitext">'.tra($tracker["description"]).'</div><br />';
				}
			}
			if (isset($_REQUEST['tr_preview'])) { // use for the computed and join fields
				$assocValues = array();
				$assocNumerics = array();
				foreach ($flds['data'] as $f) {
					if (empty($f['value']) && ($f['type'] == 'u' || $f['type'] == 'g' || $f['type'] == 'I') && ($f['options_array'][0] == '1' || $f['options_array'][0] == '2')) { //need to fill the selector fields for the join
						$f['value'] = ($f['type'] == 'I')? $tikilib->get_ip_address(): (($f['type'] == 'g')? $group: $user);
					}
					$assocValues[$f['fieldId']] = $f['value'];
					$assocNumerics[$f['fieldId']] = preg_replace('/[^0-9\.\+]/', '', $f['value']); // get rid off the $ and such unit
				}
			}

			if (!empty($itemId)) {
				$item = array('itemId'=>$itemId, 'trackerId'=>$trackerId);
			} else {
				$item = array('itemId'=>'');
			}
			foreach ($flds['data'] as $i=>$f) { // collect additional infos
				if (in_array($f['fieldId'], $outf)) {
					$flds['data'][$i]['ins_id'] = ($f['type'] == 'e')?'ins_'.$f['fieldId']: $fields_prefix.$f['fieldId'];
					if (($f['isHidden'] == 'c' || $f['isHidden'] == 'p') && !empty($itemId) && !isset($item['creator'])) {
						$item['creator'] = $trklib->get_item_creator($trackerId, $itemId);
					}
					if ($f['type'] == 's' && ($f['name'] == 'Rating' || $f['name'] == tra('Rating')) && $perms['tiki_p_tracker_vote_ratings'] == 'y' && isset($item)) {
						$item['my_rate'] = $tikilib->get_user_vote("tracker$trackerId.$itemId", $user);
					}
					if ($f['isMultilingual'] == 'y') {
						$multi_languages = $prefs['available_languages'];
						foreach ($multi_languages as $num=>$tmplang){
							$flds['data'][$i]['lingualvalue'][$num]['lang'] = $tmplang;
						}
					}
					if ($f['type'] == 'r') {
						if (!isset($f['options_array'][3])) {
							$flds['data'][$i]['list'] = array_unique($trklib->get_all_items($f['options_array'][0], $f['options_array'][1], isset($f['options_array'][4])?$f['options_array'][4]:'poc', false));
						}
						else {
							$flds['data'][$i]['list'] = $trklib->get_all_items($f['options_array'][0], $f['options_array'][1], isset($f['options_array'][4])?$f['options_array'][4]:'poc', false);
						}
						if (isset($f['options_array'][3])) {
							$flds['data'][$i]['listdisplay'] = array_unique($trklib->concat_all_items_from_fieldslist($f['options_array'][0], $f['options_array'][3], isset($f['options_array'][4])?$f['options_array'][4]:'poc'));
						}
					} elseif ($f['type'] == 'y') {
						$flds['data'][$i]['flags'] = $tikilib->get_flags(true, true, isset($f['options_array'][1])&&$f['options_array'][1]==1?false:true);
					} elseif ($f['type'] == 'u') {
						if ($perms['tiki_p_admin_trackers'] == 'y' || ($f['options_array'][0] != 1 && $f['options_array'][0] != 2))
							$flds['data'][$i]['list'] = $userlib->list_all_users();
						elseif ($f['options_array'][0] == 1)
							$flds['data'][$i]['value'] = $user;
					} elseif ($f['type'] == 'g') {
						if ($perms['tiki_p_admin_trackers'] == 'y' || ($f['options_array'][0] != 1 && $f['options_array'][0] != 2)) {
							$flds['data'][$i]['list'] = $userlib->list_all_groups();
						} elseif ($f['options_array'][0] == 1) {
							global $group;
							$flds['data'][$i]['value'] = $group;
						}
					} elseif ($f['type'] == 'k') {
						if ($f['options_array'][0] == 1) {
							if (isset($page)) {
								$flds['data'][$i]['value'] = $page;
							}
						}
					} elseif ($f['type'] == 'e') {
						global $categlib; include_once('lib/categories/categlib.php');
						if (isset($f['options_array'][3]) && $f['options_array'][3] ==1) {
							$all_descends = true;
						} else {
							$all_descends = false;
						}
						$flds['data'][$i]['list'] = $categlib->get_viewable_child_categories($f['options_array'][0], $all_descends);
						// Need to load categories for purposes of showing
						foreach($f['categs'] as $tcat) {
							$flds['data'][$i]['cat'][$tcat['categId']] = 'y';
						}
					} elseif ($f['type'] == 'A') {
						if (!empty($f['value'])) {
							$flds['data'][$i]['info'] = $trklib->get_item_attachment($f['value']);
						}
					} elseif ($f['type'] == 'a') {
						if ($f['options_array'][0] == 1 && empty($toolbars)) {
							// all in the smarty object now
						}
					} elseif ($f['type'] == 'l' && isset($itemId)) {
						$opts[1] = preg_split('/:/', $f['options_array'][1]);
						$finalFields = explode('|', $f['options_array'][3]);
						$flds['data'][$i]['value'] = $trklib->get_join_values($trackerId, $itemId, array_merge(array($f['options_array'][2]), array($f['options_array'][1]), array($finalFields[0])), $f['options_array'][0], $finalFields, ' ', empty($f['options_array'][5])?'':$f['options_array'][5]);
					} elseif ($f['type'] == 'w') {
						$trklib->prepare_dynamic_items_list($f, $flds['data']);
						if ($flds['data'][$i]['type'] == 'r') {
							// we prentended it was an item link
							$bindingValue = $trklib->get_item_value($trackerId, $itemId, $f['options_array'][2]);
							$flds['data'][$i]['list'] = $trklib->get_filtered_item_values($f['options_array'][1], $bindingValue, $f['options_array'][3]);
						}
					} elseif ($f['type'] == 'f' && empty($itemId) && (empty($f['options_array'][3]) || $f['options_array'][3] != 'blank')) {
						$flds['data'][$i]['value'] = $tikilib->now;
					} elseif ($f['type'] == 'F') {
						global $freetaglib;
						if (!is_object($freetaglib)) {
							include_once('lib/freetag/freetaglib.php');
						}
						$flds['data'][$i]["freetags"] = $freetaglib->_parse_tag($f['value']);
						$flds['data'][$i]["tag_suggestion"] = $freetaglib->get_tag_suggestion($flds['data'][$i]["freetags"],$prefs['freetags_browse_amount_tags_suggestion']);
					}

					if (!empty($f['value'])) {
						// if it has an original or a suggested value, then set it as the default value
						$flds['data'][$i]['defaultvalue'] = $f['value'];
					}
				}
			}
			if (!empty($showstatus) && $showstatus == 'y') {
				$status_types = $trklib->status_types();
				$smarty->assign_by_ref('status_types', $status_types);
				$smarty->assign('form_status', 'status');
				$smarty->assign_by_ref('tracker', $tracker);
				if (!empty($item_info)) {
					$smarty->assign_by_ref('item', $item_info);
				}
				$status_input = $smarty->fetch('tracker_status_input.tpl');
			}

			if ($registration == "y") {
				$back .= '<input type="hidden" name="register" value="Register" />';
			}
			
			// Loop on tracker fields and display form
			if (empty($tpl) && empty($wiki)) {
				$back.= '<table class="wikiplugin_tracker">';
				if (!empty($showstatus) && $showstatus == 'y') {
					$back .= '<tr><td>'.tra('Status').'</td><td>'.$status_input.'</td></tr>';
				}
				if ($registration == 'y') {
					$back .= $smarty->fetch('register-form.tpl');
				}
			} else {
				$back .= '<div class="wikiplugin_tracker">';
				if (!empty($showstatus) && $showstatus == 'y') {
					$smarty->assign_by_ref('f_status_input', $status_input);
				}
			}
			$backLength0 = strlen($back);
			foreach ($flds['data'] as $f) {
				if ($f['type'] == 'u' && $f['options_array'][0] == '1' && !in_array($f['fieldId'], $outf)) {
					$back.= '<input type="hidden" name="authorfieldid" value="'.$f['fieldId'].'" />';
				} elseif ($f['type'] == 'I' and $f['options_array'][0] == '1') {
					$back.= '<input type="hidden" name="authoripid" value="'.$f['fieldId'].'" />';
				} elseif ($f['type'] == 'q') {
					$back .= '<input type="hidden" name="track['.$f['fieldId'].']" />';
				} elseif (in_array($f['fieldId'], $outf)) {
					if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
						$onemandatory = true;
					}
					if ($f['type'] == 'A') {
						$smarty->assign_by_ref('tiki_p_attach_trackers', $perms['tiki_p_attach_trackers']);
					}
					if (!empty($tpl) || !empty($wiki)) {
						if (!empty($outputPretty) && in_array($f['fieldId'], $outputPretty)) {
							$smarty->assign('f_'.$f['fieldId'], '<span class="outputPretty" id="track_'.$f['fieldId'].'" name="track_'.$f['fieldId'].'">'. wikiplugin_tracker_render_value($f, $item) . '</span>');
						} else {
							$smarty->assign('f_'.$f['fieldId'], wikiplugin_tracker_render_input($f, $item));
						}
					} else {
						if (in_array($f['fieldId'], $optional)) {
							$f['name'] = "<i>".$f['name']."</i>";
						}
						if ($f['type'] != 'h') {
							$back.= "<tr><td";
							if (!empty($colwidth)){
								$back .= " width='".$colwidth."'";
							}
							$back .= '><label for="' . $f['ins_id'] . '">' 
										. wikiplugin_tracker_name($f['fieldId'], tra($f['name'] . ':'), $field_errors) . '</label>';
							if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
								$back.= "&nbsp;<strong class='mandatory_star'>*</strong>&nbsp;";
							}
							$back.= '</td><td>';
						} else {
							$smarty->assign('inTable', (empty($tpl) && empty($wiki))?'wikiplugin_tracker':'');
						}

						$back .= wikiplugin_tracker_render_input($f, $item);
					}

					if (!empty($f['description']) && $f['type'] != 'h' && $f['type'] != 'S') {
						$back .= '<div class="trackerplugindesc">';
						if ($f['descriptionIsParsed'] == 'y') {
							$back .= $tikilib->parse_data($f['description']);
						} else {
							$back .= tra($f['description']);
						}
						$back .= '</div>';
					}
					if (empty($tpl) && empty($wiki)) {
					if ($f['type'] != 'h'){
						$back.= "</td></tr>";
						}
					}
					if (!empty($f['http_request']) && !empty($itemId)) {
						$js .= 'selectValues("trackerIdList=' . $f['http_request'][0] 
									. '&fieldlist=' . $f['http_request'][3]
									. '&filterfield=' . $f['http_request'][1]
									. '&status=' . $f['http_request'][4]
									. '&mandatory=' . $f['http_request'][6]
									. '&filtervalue=' . $f['http_request'][7]
									. '&selected=' . $f['http_request'][8]
									. '","' . $f['http_request'][5]
									.'");'
									;
					}
				}
			}
			if (!empty($tpl)) {
				$smarty->security = true;
				$back .= $smarty->fetch($tpl);
			} elseif (!empty($wiki)) {
				$smarty->security = true;
				$back .= $smarty->fetch('wiki:'.$wiki);
			}
			include_once('lib/smarty_tiki/function.trackerheader.php');
			$back .= smarty_function_trackerheader(array('level'=>-1, 'title'=>'', 'inTable' =>(empty($tpl) && empty($wiki))?'wikiplugin_tracker':'' ), $smarty);

			if ($prefs['feature_antibot'] == 'y' && empty($user)) {
				// in_tracker session var checking is for tiki-register.php
				$smarty->assign('showmandatory', $showmandatory);
				$smarty->assign('antibot_table', empty($wiki) && empty($tpl)?'n': 'y');
				$back .= $smarty->fetch('antibot.tpl');
			}
			if (empty($tpl) && empty($wiki)) {
				$back.= "</table>";
			} else {
				$back .= '</div>';
			}

			if ($params['formtag'] == 'y') {
				$back .= '<div class="input_submit_container">';

				if (!empty($reset)) {
					$back .= '<input class="button submit preview" type="reset" name="tr_reset" value="'.tra($reset).'" />';
				}
				if (!empty($preview)) {
					$back .= '<input class="button submit preview" type="submit" name="tr_preview" value="'.tra($preview).'" />';
				}
				foreach ($action as $key=>$act) {
					$back .= '<input class="button submit" type="submit" name="action'.$key.'" value="'.tra($act).'" onclick="needToConfirm=false" />';
				}
				$back .= '</div>';
			}
			if ($showmandatory == 'y' and $onemandatory) {
				$back.= "<em class='mandatory_note'>".tra("Fields marked with a * are mandatory.")."</em>";
			}
			if ($params['formtag'] == 'y') {
				$back.= '</form>';
			}
			if (!empty($js)) {
				$back .= '<script type="text/javascript">'.$js.'</script>';
			}
			if (!empty($page))
				$back .= '~/np~';
			$smarty->assign_by_ref('tiki_p_admin_trackers', $perms['tiki_p_admin_trackers']);
		return $back;
	}
	else {
		if (isset($_REQUEST['trackit']) and $_REQUEST['trackit'] == $trackerId)
			$smarty->assign('wikiplugin_tracker', $trackerId);//used in vote plugin
		$id = ' id="wikiplugin_tracker'.$iTRACKER.'"';
		if ($showtitle == 'y') {
			$back.= '<div class="titlebar"'.$id.'>'.$tracker["name"].'</div>';
			$id = '';
		}
		if ($showdesc == 'y') {
			$back.= '<div class="wikitext"'.$id.'>'.$tracker["description"].'</div><br />';
			$id = '';
		}
		$back.= "<div$id>".$data.'</div>';
		return $back;
	}
}

function wikiplugin_tracker_render_input($f, $item) {
	$trklib = TikiLib::lib('trk');
	$smarty = TikiLib::lib('smarty');

	// FIXME : Else condition is only transitional
	if (in_array($f['type'], $trklib->get_rendered_fields())) {
		$handler = $trklib->get_field_handler($f, $item);
		return $handler->renderInput();
	} else {
		$smarty->assign('field_value', $f);
		if (!empty($item)) {
			$smarty->assign('item', $item);
		}
		return $smarty->fetch('tracker_item_field_input.tpl');
	}
}

function wikiplugin_tracker_render_value($f, $item) {
	$smarty = TikiLib::lib('smarty');
	$trklib = TikiLib::lib('trk');

	// FIXME : Else condition is only transitional
	if (in_array($f['type'], $trklib->get_rendered_fields())) {
		$handler = $trklib->get_field_handler($f, $item);
		return $handler->renderOutput();
	} else {
		$smarty->assign('field_value', $f);
		if (!empty($item)) {
			$smarty->assign('item', $item);
		}
		return $smarty->fetch('tracker_item_field_value.tpl');
	}
}

