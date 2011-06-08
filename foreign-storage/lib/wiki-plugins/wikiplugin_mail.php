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
				'description' => tra('Limit the group to the groups including this group'),
				'filter' => 'groupname',
				'default' => '',
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
	$default = array('showuser' => 'y', 'showuserdd' => 'n', 'showrealnamedd' => 'n', 'showgroupdd' => 'n', 'group' => '');
	$params = array_merge($default, $params);
	$mail_error = false;
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
			$groups = $userlib->get_including_groups($params['group'], 'y');
		} else {
			$groups = $userlib->list_all_groups();
		}
		$smarty->assign_by_ref('groups', $groups);
	}
	if (isset($_REQUEST["mail_send$ipluginmail"])) { // send something
		$to = array();
		if (!empty($_REQUEST['mail_user_dd'])) {
			$to = array_merge($to, $_REQUEST['mail_user_dd']);
		}
		if (!empty($_REQUEST['mail_group_dd'])) {
			foreach ($_REQUEST['mail_group_dd'] as $mgroup) {
				if (!empty($mgroup)) {
					$to = array_merge($to, $userlib->get_group_users($mgroup, 0, -1, 'userId'));
				}
			}
		}
		$to = array_unique($to);
		if (!empty($to)) {
			$to = $userlib->get_userId_what($to);
		}
		if (!empty($_REQUEST['mail_user'])) {
			$to = array_merge($to, preg_split('/ *, */', $_REQUEST['mail_user']));
		}
		$to = array_unique($to);
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
	}
	$smarty->assign_by_ref('mail_error', $mail_error);
	$smarty->assign('mail_user', ($mail_error && isset($_REQUEST['mail_user']))? $_REQUEST['mail_user']:'');
	$smarty->assign('mail_user_dd', ($mail_error && isset($_REQUEST['mail_user_dd']))? $_REQUEST['mail_user_dd']:array());
	$smarty->assign('mail_group_dd', ($mail_error && isset($_REQUEST['mail_group_dd']))? $_REQUEST['mail_group_dd']:array());
	$smarty->assign_by_ref('params', $params);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_mail.tpl').'~/np~';
}
