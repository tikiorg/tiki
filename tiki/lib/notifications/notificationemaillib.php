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

	//outbound email ->  will be sent in utf8 - from sender_email
	if ($forum_info['outbound_address']) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
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
/** \brief send the email notifications dealing with wiki page  changes to
  * admin notification addresses + watching users addresses (except editor is configured)
  * \$event: 'wiki_page_created'|'wiki_page_changed'
  */
function sendWikiEmailNotification($event, $pageName, $edit_user, $edit_comment, $version, $edit_data, $machine) {
	global $tikilib, $notificationlib, $feature_user_watches, $smarty, $userlib, $sender_email, $wiki_watch_editor;;
	$nots = array();
	$defaultLanguage = $tikilib->get_preference("language", "en");

	// Users watching this forum or this post
	if ($feature_user_watches == 'y' && $event == 'wiki_page_changed') {
		$nots = $tikilib->get_event_watches($event, $pageName);
		if ($wiki_watch_editor != "y") {
			for ($i = count($nots) - 1; $i >=0; --$i)
				if ($nots[$i]['user'] == $edit_user) {
					unset($nots[$i]);
					break;
				}
		}
		for ($i = count($nots) - 1; $i >=0; --$i) {
			$nots[$i]['language'] = $tikilib->get_user_preference($nots[$i]['user'], "language", $defaultLanguage);
		}
	}

	// admin notifications
	$emails = $notificationlib->get_mail_events('wiki_page_changes', 'wikipage' . $pageName); // look for pageName and any page
	foreach ($emails as $email) {
		if ($wiki_watch_editor != "y" && $email == $edit_user)
			continue;
		$not['email'] =  $email;
		if ($not['user'] = $userlib->get_user_by_email($email))
			$not['language'] = $tikilib->get_user_preference($not['user'], "language", $defaultLanguage);
		else
			$not['language'] = $defaultLanguage;
		$nots[] = $not;
	}

	if (count($nots)) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
       	$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
       	$smarty->assign('mail_page', $pageName);
       	$smarty->assign('mail_date', date("U"));
       	$smarty->assign('mail_user', $edit_user);
       	$smarty->assign('mail_comment', $edit_comment);
       	$smarty->assign('mail_last_version', $version);
       	$smarty->assign('mail_data', $edit_data);
       	$foo = parse_url($_SERVER["REQUEST_URI"]);
       	$machine = httpPrefix(). dirname( $foo["path"] );
       	$smarty->assign('mail_machine', $machine);
       	$parts = explode('/', $foo['path']);
        	if (count($parts) > 1)
           	unset ($parts[count($parts) - 1]);
       	$smarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
       	$smarty->assign('mail_pagedata', $edit_data);
		if ($event == 'wiki_page_created')
			$smarty->assign('new_page', 'y');

		foreach ($nots as $not) {
			if (isset($not['hash']))
	       		$smarty->assign('mail_hash', $not['hash']);
			$mail->setUser($not['user']);
			$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_wiki_page_changed_subject.tpl");
			$mail->setSubject(sprintf($mail_data, $pageName));
			$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_wiki_page_changed.tpl");
			$mail->setText($mail_data);
			$mail->buildMessage();
			$mail->send(array($not['email']));
		}
	}
}
/** \brief Send email notification to a list of emails or a list of (email, user) in a charset+language associated with each email 
  * \param $list : emails list or (users, email) list
  * \param $type: type of the list element =  'email'|'watch'
  * \param $subjectTpl: subject template file or null (ex: "submission_notifcation.tpl")
  * \param $subjectParam: le param to be inserted in the subject or null
  * \param $txtTpl : texte template file (ex: "submission_notifcation.tpl")
  * \ $smarty is supposed to be already built to fit $txtTpl
  * \return the nb of sent emails
  */
function sendEmailNotification($list, $type, $subjectTpl, $subjectParam, $txtTpl) {
	global $smarty, $tikilib, $userlib;
	include_once('lib/webmail/tikimaillib.php');
	$mail = new TikiMail();
	$sent = 0;
	$defaultLanguage = $tikilib->get_preference("language", "en");
	$languageEmail = $defaultLanguage;
	foreach ($list as $elt) {
		if ($type == "watch") {
			$email = $elt['email'];
			$userEmail = $elt['user'];
			$smarty->assign('mail_hash', $elt['hash']);
		}	
		else {
			$email = $elt;
			$userEmail = $userlib->get_user_by_email($email);
		}
		if ($userEmail) {
			$mail->setUser($userEmail);
			$languageEmail = $tikilib->get_user_preference($userEmail, "language", $defaultLanguage);
		}
		else
			$languageEmail = $defaultLanguage;
		if ($subjectTpl) {
			$mail_data = $smarty->fetchLang($languageEmail, "mail/".$subjectTpl);
			if ($subjectParam)
				$mail_data = sprintf($mail_data, $subjectParam);
			$mail_data = ereg_replace("\%[sd]", "", $mail_data);// partial cleaning if param not supply and %s in text
			$mail->setSubject($mail_data);
		}
		else
			$mail->setSubject($subjectParam);
		$mail->setText($smarty->fetchLang($languageEmail, "mail/".$txtTpl));
		$mail->buildMessage();
		if ($mail->send(array($email)))
			$sent++;
	}
return $sent;		
}
?>