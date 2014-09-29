<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sendmail_info()
{
	return array(
		'name' => tra('Send Mail to list of users'),
		'description' => tra('sends an email message to all the users based on selection'),
		'default' => 'y',
		'format' => 'html',
		'filter' => 'wikicontent',
		'tags' => array('advanced'),
		'params' => array(
			'mailto' => array(
				'name' => tr('sends an email message'),
				'description' => tr('sends an email message to all the users'),
				'required' => true,
				'filter' => 'text',
				'options' => array(
					array('value' => 'userlist', 'text' => tr('Send email to All users')),
					array('value' => 'group', 'text' => tr('Send email to selected group.')),
					array('value' => 'eventparticipants', 'text' => tr('Send email to all Event Attendees'))
				),
			),
			'groupname' => array(
				'name' => tr('Group name'),
				'description' => tr('If you need to send an email to selected group, then fill this groupname field (multiple groups separated with(:))'),
				'required' => false,
				'filter' => 'text'
			),
			'mail_subject' => array(
				'required' => true,
				'name' => tra('Email subject'),
				'description' => tra('Email subject content'),
				'filter' => 'text',
				'default' => '',
			),
			'label_name' => array(
				'required' => false,
				'name' => tra('Button Text'),
				'description' => tra('Text to show on the button to send emails (default: Send mail)'),
				'filter' => 'text',
				'default' => tra('Send mail'),
			),
			'event_details' => array(
				'required' => false,
				'name' => tra('Event Details'),
				'description' => tra('Event Details in the format [$object_type:$id:$qualifier]'),
				'filter' => 'text',
				'default' => tra('Send mail'),
			),
		),
	);
}

function wikiplugin_sendmail($data, $params)
{
	global $user;
	$relationlib = TikiLib::lib('relation');
	$smarty = TikiLib::lib('smarty');
	$userlib = TikiLib::lib('user');
	if (empty($params['mailto']) || ($params['mailto'] == 'group' && empty($params['groupname'])) || ($params['mailto'] == 'eventparticipants' && empty($params['event_details']))) {
		return false;
	}

	if (empty($params['label_name'])) {
		$params['label_name'] = 'Send mail';
	}

	$smarty->assign('label_name', $params['label_name']);

	if (isset($_POST['bodycontent']) && !empty($_POST['bodycontent'])) {
		$targetemails = $targetusers = array();
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$mail->setSubject($params['mail_subject']);
		$mail->setHtml($_POST['bodycontent']);

		function getmail($a) {
		    return $a['email'];
		}

		function getitem($b) {
			return $b['itemId'];
		}

		if ($params['mailto'] == 'userlist') {
			$targetusers = $userlib->get_users();
			$targetemails = array_map("getmail", $targetusers['data']);
			$mail->setBcc(array_unique($targetemails));
		} elseif ($params['mailto'] == 'group') {
			$params['groupname'] = explode(":", $params['groupname']);
			$targetusers = $userlib->get_users(0,-1,'login_asc','','','',$params['groupname']);
			$targetemails = array_map("getmail", $targetusers['data']);
			$mail->setBcc(array_unique($targetemails));
		} elseif ($params['mailto'] == 'eventparticipants') {
			$object = explode(":", $params['event_details']);
			$found = $relationlib->get_relations_from($object[0], $object[1], $object[2]);
			foreach (array_map("getitem",$found) as $value) {
				$targetemails[] = $userlib->get_user_email($value);
			}
			$mail->setBcc(array_unique($targetemails));
		}
		$mail->send(array($userlib->get_user_email($user)));
	}

	return $smarty->fetch('wiki-plugins/wikiplugin_sendmail.tpl');
}
