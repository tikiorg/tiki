<?php
/** \brief send the email notifications dealing with the forum changes to
  * \brief outbound address + admin notification addresses / forum admin email + watching users addresses
  * \param $event = 'forum_post_topic' or 'forum_post_thread'
  * \param $object = objectid watch
  * \param $title of the message
  * \param $topicName name of the parent topic
  */

function sendForumEmailNotification($event, $object, $forum_info, $title, $data, $author, $topicName, $messageId='', $inReplyTo='') { 
	global $tikilib, $feature_user_watches, $smarty, $userlib, $sender_email;

	//outbound email
	if ($forum_info['outbound_address']) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		// will be sent in utf8 - from sender_email
		$mail->setSubject($title);
		$mail->setText($title."\n".$data);
		$mail->setHeader("Reply-To", $sender_email);
		if ($event == 'forum_post_thread') {
			$mail->setSubject($topicName);
			if ($inReplyTo)
				$mail->setHeader("In-Reply-To", "<".$inReplyTo.">");
			$mail->buildMessage(); // trick htmlmimepart  for not having the message id rewriten
			$mail->setHeader("Message-ID", "<".$messageId.">");
		}
		else
			$mail->setSubject($title);
		$mail->send(array($forum_info['outbound_address']));
	}

	$nots = array();
	$defaultLanguage = $tikilib->get_preference("language", "en");

	// Users watching this forum or this post
	if ($feature_user_watches == 'y') {
		$nots = $tikilib->get_event_watches($event, $object);
		for ($i = count($nots) - 1; $i >=0; --$i) {
			$nots[$i]['language'] = $tikilib->get_user_preference($nots[$i]['user'], "language", $defaultLanguage);
		}
	}

	// Special forward address
	//TODO: merge or use the admin notification feature 
	if ($forum_info["useMail"] == 'y') {
		$not['email'] =  $forum_info['mail'];
		if ($not['user'] = $userlib->get_user_by_email($forum_info['mail']) )
			$not['language'] = $tikilib->get_user_preference($not['user'], "language", $defaultLanguage);
		else
			$not['language'] = $defaultLanguage;
		$nots[] = $not;
	}

	if (count($nots)) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$smarty->assign('mail_forum', $forum_info["name"]);
		$smarty->assign('mail_title', $title);
		$smarty->assign('mail_date', date("U"));
		$smarty->assign('mail_message', $data);
		$smarty->assign('mail_author', $author);
		if ($event == "forum_post_topic")
			$smarty->assign('new_topic', 'y');
		$smarty->assign('mail_topic', $topicName);
		foreach ($nots as $not) {
			$mail->setUser($not['user']);
			$mail_data = $smarty->fetchLang($not['language'], "mail/notification_subject.tpl");
			$mail->setSubject($mail_data);
			$mail_data = $smarty->fetchLang($not['language'], "mail/forum_post_notification.tpl");
			$mail->setText($mail_data);
			$mail->buildMessage();
			$mail->send(array($not['email']));
		}
	}
}
?>