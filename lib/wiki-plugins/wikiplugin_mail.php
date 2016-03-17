<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_mail_info()
{
	global $prefs;

	return array(
		'name' => tra('Mail'),
		'documentation' => 'PluginMail',
		'description' => tra('Mail other users or groups'),
		'validate' => 'all',
		'prefs' => array('wikiplugin_mail'),
		'iconname' => 'envelope',
		'introduced' => 3,
		'params' => array(
			'group' => array(
				'required' => false,
				'name' => tra('Group'),
				'description' => tra('Limit the list of groups to the groups including each group'),
				'filter' => 'groupname',
				'since' => '5.0',
				'default' => '',
				'separator' => ':',
			),
			'showgroupdd' => array(
				'required' => false,
				'name' => tra('Group Dropdown'),
				'description' => tra('Show a dropdown list of groups (not shown by default)'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'recurse' => array(
				'required' => false,
				'name' => tra('Recurse Groups'),
				'description' => tra('Show each group and each group included in this group'),
				'since' => '8.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'recurseuser' => array(
				'required' => false,
				'name' => tra('Recurse Group Users'),
				'description' => tr('Indicate how many times to recurse to collect the users of a selected group and
					the users of each included groups. If %00%1 do not recurse, if %01%1, recurse one time, %02%1 for
					two times....', '<code>', '</code>'),
				'since' => '8.0',
				'filter' => 'int',
				'default' => '0',
			),
			'showuserdd' => array(
				'required' => false,
				'name' => tra('User Dropdown'),
				'description' => tra('Show a dropdown list of users (not shown by default)'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showuser' => array(
				'required' => false,
				'name' => tra('User Emails'),
				'description' => tra('Show a box for user to enter email addresses'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showrealnamedd' => array(
				'required' => false,
				'name' => tra('Real Name Dropdown'),
				'description' => tra('Show a dropdown list of user real names (not shown by default)'),
				'since' => '5.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'popup' => array(
				'required' => false,
				'name' => tra('Popup'),
				'description' => tra('Show in popup instead of inline.'),
				'since' => '14.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'label_name' => array(
				'required' => false,
				'name' => tra('Button Text'),
				'description' => tr('Text to show on the button to send emails (default: %0Send mail%1)', '<code>', '</code>'),
				'since' => '14.0',
				'filter' => 'text',
				'default' => tra('Send mail'),
			),
			'mail_subject' => array(
				'required' => true,
				'name' => tra('Email Subject'),
				'description' => tra('Present Email subject content'),
				'since' => '14.0',
				'filter' => 'text',
				'default' => '',
			),
			'bypass_preview' => array(
				'required' => false,
				'name' => tra('Bypass Preview'),
				'description' => tra('Send emails without first previewing'),
				'since' => '14.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'debug' => array(
				'required' => false,
				'name' => tra('Debug mode (admins only)'),
				'description' => tra('Show list of emails that are sent (admins only)'),
				'since' => '14.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		)
	);
}

function wikiplugin_mail($data, $params)
{
	global $user;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	static $ipluginmail=0;
	$smarty->assign_by_ref('ipluginmail', $ipluginmail);
	$default = array('showuser' => 'y', 'showuserdd' => 'n', 'showrealnamedd' => 'n', 'showgroupdd' => 'n', 'group' => array(), 'recurse' => 'y', 'recurseuser' => 0,
		'popup' => 'n', 'label_name' => tra('Send mail'), 'mail_subject' => '', 'bypass_preview' => 'n', 'debug' => 'n');
	$params = array_merge($default, $params);
	$default = array('mail_subject' =>'', 'mail_mess' => '', 'mail_user_dd' => '', 'mail_group_dd' => array());
	$_REQUEST = array_merge($default, $_REQUEST);
	$mail_error = false;
	$preview = false;
	$smarty->assign('mail_popup', $params['popup']);
	$smarty->assign('mail_label_name', $params['label_name']);
	$smarty->assign('mail_subject', $params['mail_subject']);
	$smarty->assign('bypass_preview', $params['bypass_preview']);
	if ($params['showrealnamedd'] == 'y') {
		$users = $tikilib->list_users(0, -1, 'pref:realName_asc', '', true);
		$smarty->assign('names', $users['data']);
	}
	if ($params['showuserdd'] == 'y') {
		$users = $tikilib->list_users(0, -1, 'login_asc');
		$smarty->assign_by_ref('users', $users['data']);
	}
	
	if ($params['showgroupdd'] == 'y') {
		if (!empty($params['group'])) {
			foreach ($params['group'] as $g) {
				$groups[$g] = $userlib->get_including_groups($g, $params['recurse']);
			}
		} else {
			$groups[] = $userlib->list_all_groups();
		}
		$smarty->assign_by_ref('groups', $groups);
	}
	if (isset($_REQUEST["mail_preview$ipluginmail"])) {
		$to = wikiplugin_mail_to(array_merge($_REQUEST, $params));
		$_SESSION['wikiplugin_mail_to'] = $to;
		$preview = true;
		$smarty->assign('preview', $preview);
		$smarty->assign('nbTo', count($to));
	}
	if (isset($_REQUEST["mail_send$ipluginmail"])) { // send something
		if ($params['bypass_preview'] == 'y') {
			$to = wikiplugin_mail_to(array_merge($_REQUEST, $params));
		} else {
			$to = $_SESSION['wikiplugin_mail_to'];
		}
		if (!empty($to)) {
			include_once ('lib/webmail/tikimaillib.php');
			$mail = new TikiMail(null, $userlib->get_user_email($user));
			$mail->setSubject($_REQUEST['mail_subject']);
			$mail->setText($_REQUEST['mail_mess']);
			$myself = array($userlib->get_user_email($GLOBALS['user']));
			$mail->setBcc(array_diff($to, $myself));
			if ($mail->send($myself)) {
				$smarty->assign('nbSentTo', count($to));
				if ($userlib->user_has_permission($user, 'tiki_p_admin') && $params['debug'] == 'y') {
					$smarty->assign('sents', $to);
				} else {
					$smarty->assign('sents', array());
				}
			} else {
				$mail_error = true;
			}
		}
		unset($_SESSION['wikiplugin_mail_to']);
	}
	$smarty->assign_by_ref('mail_error', $mail_error);
	if ($preview || $mail_error) {
		$smarty->assign('mail_user', isset($_REQUEST['mail_user'])? $_REQUEST['mail_user']:'');
		$smarty->assign('mail_user_dd', isset($_REQUEST['mail_user_dd'])? $_REQUEST['mail_user_dd']:array());
		$smarty->assign('mail_group_dd', isset($_REQUEST['mail_group_dd'])? $_REQUEST['mail_group_dd']:array());
		$smarty->assign('mail_subject', $_REQUEST['mail_subject']);
		$smarty->assign('mail_mess', $_REQUEST['mail_mess']);
	}

	// Convert the array of mail_user into a string of emails separated by comma, and expose the values to the smarty tpl
	$smarty->assign('mail_user', isset($_REQUEST['mail_user'])? implode(", ", $_REQUEST['mail_user']):'');

	$smarty->assign_by_ref('params', $params);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_mail.tpl').'~/np~';
}
function wikiplugin_mail_to($params)
{
	$userlib = TikiLib::lib('user');
	$to = array();
	if (!empty($params['mail_user_dd'])) {
		$to = array_merge($to, $_REQUEST['mail_user_dd']);
	}
	if (!empty($_REQUEST['mail_group_dd'])) {
		foreach ($_REQUEST['mail_group_dd'] as $mgp) {
			foreach ($mgp as $mgroup) {
				if (!empty($mgroup)) {
					$to = array_merge($to, $userlib->get_recur_group_users($mgroup, $params['recurseuser'], 'userId'));
				}
			}
		}
	}
	$to[] = $userlib->get_user_id($GLOBALS['user']);
	$to = array_unique($to);
	if (!empty($to)) {
		$to = $userlib->get_userId_what($to);
	}
	if (!empty($params['mail_user'])) {
		$to = array_merge($to, preg_split('/ *, */', $params['mail_user']));
	}
	return (array_unique($to));
}
