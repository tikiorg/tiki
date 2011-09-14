<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_invite_info() {
	return array(
		'name' => tra( 'Invite' ),
		'documentation' => 'PluginInvite',
		'description' => tra('Invite a user to join your groups'),
		'prefs' => array( 'wikiplugin_invite' ),
		'body' => tra('Confirmation message after posting form'),
		'icon' => 'pics/icons/group.png',
		'params' => array(
			'including' => array(
				'required' => false,
				'name' => tra('Including Group'),
				'description' => tra('Will list only the groups that include this group'),
			),
			'defaultgroup' => array(
				'required' => false,
				'name' => tra('Default Group'),
				'description' => tra('Dropdown list will show this group by default'),
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('Dropdown list will show the group related to this item ID (in group selector or creator field) by default'),
			),
		)
	);
}
function wikiplugin_invite( $data, $params) {
	global $prefs, $tikilib, $userlib, $user, $smarty, $tiki_p_invite_to_my_groups;

	if ($tiki_p_invite_to_my_groups != 'y') {
		return;
	}
	$userGroups = $userlib->get_user_groups_inclusion($user);
	if (!empty($params['including'])) {
		$groups = $userlib->get_including_groups($params['including']);
		foreach ($userGroups as $gr=>$inc) {
			if (!in_array($gr, $groups)) {
				unset($userGroups[$gr]);
			}
		}
	}
	$errors = array();
	$feedbacks = array();
	if (isset($_REQUEST['invite'])) {
		if (empty($_REQUEST['email'])) {
			$errors[] = tra('Following mandatory fields are missing').' '.tra('Email address');
		}
		if (!validate_email($_REQUEST['email'])) {
			$errors[] = tra('Invalid Email').' '.$_REQUEST['email'];
		}
		if (!empty($_REQUEST['groups'])) {
			foreach ($_REQUEST['groups'] as $group) {
				if (empty($userGroups[$group])) {
					$errors[] = tra('Incorrect param').' '.$group;
				}
			}
		}
		if (empty($errors)) {
			$email = $_REQUEST['email'];
			if (!($invite = $userlib->get_user_by_email($email))) {
				$new_user = true;
				$password =  'toto';//$tikilib->genPass();
				$codedPassword = md5($password);
				$userlib->add_user($email, $password, $email, $password, true, NULL);
				$smarty->assign('codedPassword', $codedPassword);
				$invite = $email;
			} else {
				$new_user = false;
			}
			$smarty->assign_by_ref('new_user', $new_user);
			$smarty->assign_by_ref('invite', $invite);
			if (!empty($_REQUEST['groups'])) {
				foreach ($_REQUEST['groups'] as $group) {
					$userlib->assign_user_to_group($invite, $group);
					$invitedGroups[] = $userlib->get_group_info($group);
				}
			}
			include_once ('lib/webmail/tikimaillib.php');
			$mail = new TikiMail();
			$machine = parse_url($_SERVER['REQUEST_URI']);
			$machine = $tikilib->httpPrefix( true ).dirname($machine['path']);
			$smarty->assign_by_ref('machine', $machine);
			$subject = sprintf($smarty->fetch('mail/mail_invite_subject.tpl'), $_SERVER['SERVER_NAME']);
			$mail->setSubject($subject);
			if (!empty($_REQUEST['message'])) {
				$smarty->assign('message', $_REQUEST['message']);
			}
			$smarty->assign_by_ref('groups', $invitedGroups);
			$txt = $smarty->fetch('mail/mail_invite.tpl');
			$mail->setText($txt);
			$mail->send(array($email));

			return $data;
		} else {
			$smarty->assign_by_ref('errors', $errors);
			$smarty->assign_by_ref('email', $_REQUEST['email']);
			if (!empty($_REQUEST['groups'])) $smarty->assign_by_ref('groups', $_REQUEST['groups']);
			if (!empty($_REQUEST['message'])) $smarty->assign_by_ref('message', $_REQUEST['message']);
		}	
	}
	if (!empty($_REQUEST['itemId'])) {
		$params['itemId'] = $_REQUEST['itemId'];
	}
	if (!empty($params['itemId'])) {
		global $trklib; include_once('lib/trackers/trackerlib.php');
		$item = $trklib->get_tracker_item($params['itemId']);
		$params['defaultgroup'] = $trklib->get_item_group_creator($item['trackerId'], $params['itemId']);
	}
	$smarty->assign_by_ref('params', $params);
	$smarty->assign_by_ref('userGroups', $userGroups);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_invite.tpl').'~/np~';
}
