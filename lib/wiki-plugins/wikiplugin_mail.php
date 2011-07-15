<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_mail_info() {
	global $prefs;

	return array(
		'name' => tra('Mail'),
		'documentation' => 'PluginMail',
		'description' => tra('Directly mail other users or groups'),
		'validate' => 'all',
		'icon' => 'pics/icons/email.png',
		'params' => array(
			'group' => array(
				'required' => false,
				'name' => tra('Group'),
				'description' => tra('Limit the list of groups to the groups including each group'),
				'filter' => 'groupname',
				'default' => '',
				'separator' => ':',
			),
			'showgroupdd' => array(
				'required' => false,
				'name' => tra('Show Group Dropdown'),
				'description' => tra('Show a dropdown list of groups (not shown by default)'),
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
				'name' => tra('Recurse on groups'),
				'description' => tra('show each group and each group included in this group'),
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
				'name' => tra('Recurse on groups to have the users'),
				'description' => tra('Collect the users of a selected group and the users of each included groups. If 0 do not recurse, if 1, recurse one time, 2 two times....'),
				'filter' => 'int',
				'default' => '0',
			),
			'showuserdd' => array(
				'required' => false,
				'name' => tra('Show User Dropdown'),
				'description' => tra('Show a dropdown list of users (not shown by default)'),
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
				'name' => tra('User Autocomplete'),
				'description' => tra('Show an autocomplete box on user name'),
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
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			)
		)
	);
}

function wikiplugin_mail($data, $params) {
	global $userlib, $smarty, $tikilib, $user;
	static $ipluginmail=0;
	$smarty->assign_by_ref('ipluginmail', $ipluginmail);
	$default = array('showuser' => 'y', 'showuserdd' => 'n', 'showrealnamedd' => 'n', 'showgroupdd' => 'n', 'group' => array(), 'recurse' => 'y', 'recurseuser' => 0);
	$params = array_merge($default, $params);
	$default = array('mail_subject' =>'', 'mail_mess' => '', 'mail_user_dd' => '', 'mail_group_dd' => array());
	$_REQUEST = array_merge($default, $_REQUEST);
	$mail_error = false;
	$preview = false;
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
		$_SESSION['to'] = $to;
		$preview = true;
		$smarty->assign('preview', $preview);
		$smarty->assign('nbTo', count($to));
	}
	if (isset($_REQUEST["mail_send$ipluginmail"])) { // send something
		$to = $_SESSION['to'];
		if (!empty($to)) {
			include_once ('lib/webmail/tikimaillib.php');
			$mail = new TikiMail(null, $userlib->get_user_email($user));
			$mail->setSubject($_REQUEST['mail_subject']);
			$mail->setText($_REQUEST['mail_mess']);
			if ($mail->send($to)) {
				//echo '<pre>MAIL'; print_r($to); echo '</pre>';
				$smarty->assign_by_ref('sents', $to);
			} else {
				$mail_error = true;
			}
		}
		unset($_SESSION['to']);
	}
	$smarty->assign_by_ref('mail_error', $mail_error);
	if ($preview || $mail_error) {
		$smarty->assign('mail_user', isset($_REQUEST['mail_user'])? $_REQUEST['mail_user']:'');
		$smarty->assign('mail_user_dd', isset($_REQUEST['mail_user_dd'])? $_REQUEST['mail_user_dd']:array());
		$smarty->assign('mail_group_dd', isset($_REQUEST['mail_group_dd'])? $_REQUEST['mail_group_dd']:array());
		$smarty->assign('mail_subject', $_REQUEST['mail_subject']);
		$smarty->assign('mail_mess', $_REQUEST['mail_mess']);
	}
	
	$smarty->assign_by_ref('params', $params);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_mail.tpl').'~/np~';
}
function wikiplugin_mail_to($params) {
	global $userlib;
	$to = array();
	if (!empty($params['mail_user_dd'])) {
		$to = array_merge($to, $params['mail_user_dd']);
	}
	if (!empty($params['mail_group_dd'])) {
		foreach ($params['mail_group_dd'] as $mgp) {
			foreach ($mgp as $mgroup) {
				if (!empty($mgroup)) {
					$to = array_merge($to, $userlib->get_recur_group_users($mgroup, $params['recurseuser'], 'userId'));
				}
			}
		}
	}
	$to = array_unique($to);
	if (!empty($to)) {
		$to = $userlib->get_userId_what($to);
	}
	if (!empty($params['mail_user'])) {
		$to = array_merge($to, preg_split('/ *, */', $params['mail_user']));
	}
	return (array_unique($to));
}