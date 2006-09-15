<?php
// $Id: notificationemaillib.php,v 1.22 2006-09-15 13:21:10 sylvieg Exp $
/** \brief send the email notifications dealing with the forum changes to
  * \brief outbound address + admin notification addresses / forum admin email + watching users addresses
  * \param $event = 'forum_post_topic' or 'forum_post_thread'
  * \param $object = forumId watch if forum_post_topic or topicId watch if forum_post_thread
  * \param $threadId = topicId if forum_post_thread
  * \param $title of the message
  * \param $topicName name of the parent topic
  */

function sendForumEmailNotification($event, $object, $forum_info, $title, $data, $author, $topicName, $messageId='', $inReplyTo='', $threadId, $parentId, $contributions='' ) {
	global $tikilib, $feature_user_watches, $smarty, $userlib, $sender_email, $feature_contribution;

	// Per-forum From address overrides global default.
	if( $forum_info['outbound_from'] )
	{
	    $my_sender = '"' . "$author" . '" <' . $forum_info['outbound_from'] . '>';
	} else {
	    $my_sender = $sender_email;
	}

	//outbound email ->  will be sent in utf8 - from sender_email
	if ($forum_info['outbound_address']) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$mail->setSubject($title);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix() . dirname( $foo["path"] );
		if ($event == 'forum_post_topic') {
			$reply_link="\n\n----\n\nReply Link: <" . $machine
			. "/tiki-view_forum_thread.php?forumId=" .
			$forum_info['forumId'] .
			"&comments_parentId=$threadId#form>\n";
		} else {
		  $reply_link="\n\n----\n\nReply Link: <" . $machine
			. "/tiki-view_forum_thread.php?forumId=" .
			$forum_info['forumId'] .
			"&comments_reply_threadId=$object&comments_parentId=$threadId&post_reply=1#form>\n";
		}

		if( array_key_exists( 'outbound_mails_reply_link', $forum_info )
		    && $forum_info['outbound_mails_reply_link'] )
		{
		    $mail->setText($title."\n".$data.$reply_link);
		} else {
		    $mail->setText($title."\n".$data);
		}

		$mail->setHeader("Reply-To", $my_sender);
		$mail->setHeader("From", $my_sender);
		$mail->setSubject($topicName);

		if ($inReplyTo)
		{
		    $mail->setHeader("In-Reply-To", "<".$inReplyTo.">");
		}

		global $commentslib;
		$attachments = $commentslib->get_thread_attachments( $object, 0 );

		if( count( $attachments ) > 0 )
		{
		    foreach ( $attachments as $att )
		    {
			$att_data = $commentslib->get_thread_attachment( $att['attId'] );
			$file = $mail->getFile( $att_data['dir'].$att_data['path'] );
			$mail->addAttachment( $file, $att_data['filename'], $att_data['filetype'] );
		    }
		}


		$mail->buildMessage();

		// Message-ID is set below buildMessage because otherwise lib/webmail/htmlMimeMail.php will over-write it.
		$mail->setHeader("Message-ID", "<".$messageId.">");

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
		if ($feature_contribution == 'y' && !empty($contributions)) {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
		}
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix() . dirname( $foo["path"] );
		$machine = preg_replace("!/$!", "", $machine); // just incase
 		$smarty->assign('mail_machine', $machine);
		$smarty->assign('forumId', $forum_info["forumId"]);
		if ($event == "forum_post_topic") {
			$smarty->assign('new_topic', 'y');
			$smarty->assign('topicId', $threadId);
		} else {
			$smarty->assign('topicId', $object);
		}
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

/** \brief test if email already in the notification list
 */
function testEmailInList($nots, $email) {
	foreach (array_keys($nots) as $i) {
		if ($nots[$i]['email'] == $email)
			return true;
	}
	return false;
}

/** \brief send the email notifications dealing with wiki page  changes to
  * admin notification addresses + watching users addresses (except editor is configured)
  * \$event: 'wiki_page_created'|'wiki_page_changed'
  */
function sendWikiEmailNotification($event, $pageName, $edit_user, $edit_comment, $oldver, $edit_data, $machine, $diff='', $minor=false, $contributions='') {
	global $tikilib, $notificationlib, $feature_user_watches, $smarty, $userlib, $wiki_watch_editor, $feature_contribution;
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
		foreach (array_keys($nots) as $i) {
			$nots[$i]['language'] = $tikilib->get_user_preference($nots[$i]['user'], "language", $defaultLanguage);
		}
	}

	// admin notifications
	if ($notificationlib) {
	    // If it's a minor change, get only the minor change watches.
	    if( $minor )
	    {
		$emails = $notificationlib->get_mail_events('wiki_page_changes_incl_minor', 'wikipage' . $pageName); // look for pageName and any page
	    // else if it's not minor change, get both watch types.
	    } else {
		$emails1 = $notificationlib->get_mail_events('wiki_page_changes', 'wikipage' . $pageName); // look for pageName and any page
		$emails2 = $notificationlib->get_mail_events('wiki_page_changes_incl_minor', 'wikipage' . $pageName); // look for pageName and any page
		$emails = array_merge( $emails1, $emails2 );
	    }

	    foreach ($emails as $email) {
		if ($wiki_watch_editor != "y" && $email == $edit_user)
		    continue;
		if (!testEmailInList($nots, $email)) {
		    $not = array('email' =>  $email);
		    if ($not['user'] = $userlib->get_user_by_email($email))
			$not['language'] = $tikilib->get_user_preference($not['user'], "language", $defaultLanguage);
		    else
			$not['language'] = $defaultLanguage;
		    $nots[] = $not;
			}
		}
	}

	if ($edit_user=='') $edit_user = tra('Anonymous');

	if (count($nots)) {
	    if (function_exists("html_entity_decode"))
		$edit_data = html_entity_decode($edit_data);
	    include_once('lib/webmail/tikimaillib.php');
	    $mail = new TikiMail();
	    $smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
	    $smarty->assign('mail_page', $pageName);
	    $smarty->assign('mail_date', date("U"));
	    $smarty->assign('mail_user', $edit_user);
	    $smarty->assign('mail_comment', $edit_comment);
	    $newver = $oldver + 1;
	    $smarty->assign('mail_oldver', $oldver);
	    $smarty->assign('mail_newver', $newver);
	    $smarty->assign('mail_data', $edit_data);
	    $foo = parse_url($_SERVER["REQUEST_URI"]);
	    $machine = $tikilib->httpPrefix(). dirname( $foo["path"] );
	    $smarty->assign('mail_machine', $machine);
		if ($feature_contribution == 'y' && !empty($contributions)) {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
		}
	    $parts = explode('/', $foo['path']);
	    if (count($parts) > 1)
		unset ($parts[count($parts) - 1]);
	    $smarty->assign('mail_machine_raw', $tikilib->httpPrefix(). implode('/', $parts));
	    $smarty->assign_by_ref('mail_pagedata', $edit_data);
	    $smarty->assign_by_ref('mail_diffdata', $diff);
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
function activeErrorEmailNotivation() {
	set_error_handler("sendErrorEmailNotification");
}
function sendErrorEmailNotification($errno, $errstr, $errfile='?', $errline= '?') {
	global $tikilib;
	if (($errno & error_reporting()) == 0) /* ignore error */
		return;
	switch($errno) {
		case E_ERROR: $err = 'FATAL';break;
		case E_WARNING: $err = 'ERROR';break;
		case E_NOTICE: $err = 'WARNING';break;
		default: $err="";
	}
	$email = $tikilib->get_user_email('admin');
//	include_once('lib/webmail/tikimaillib.php');
//	$mail = new TikiMail();
	mail($email,
        "PHP: $errfile, $errline",
        "$errfile, Line $errline\n$err($errno)\n$errstr");
}

function sendFileGalleryEmailNotification($event, $galleryId, $galleryName, $name, $filename, $description, $action, $user) {
        global $tikilib, $feature_user_watches, $smarty, $userlib, $sender_email;

        $nots = array();
        $defaultLanguage = $tikilib->get_preference("language", "en");

        // Users watching this gallery
        if ($feature_user_watches == 'y') {
                $nots = $tikilib->get_event_watches($event, $galleryId);
                for ($i = count($nots) - 1; $i >=0; --$i) {
                        $nots[$i]['language'] = $tikilib->get_user_preference($nots[$i]['user'], "language", $defaultLanguage);
                }
        }

        if (count($nots)) {
                include_once('lib/webmail/tikimaillib.php');
                $mail = new TikiMail();
                $smarty->assign('galleryName', $galleryName);
                $smarty->assign('mail_date', date("U"));
                $smarty->assign('author', $user);
                $foo = parse_url($_SERVER["REQUEST_URI"]);
                $machine = $tikilib->httpPrefix(). dirname( $foo["path"] );
                $smarty->assign('mail_machine', $machine);

                foreach ($nots as $not) {
                        $mail->setUser($not['user']);
                        $mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_file_gallery_changed_subject.tpl");
                        $mail->setSubject(sprintf($mail_data, $galleryName));
                        if ($action == 'upload file') {
                                $mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_file_gallery_upload.tpl");
                        } elseif ($action == 'remove file') {
                                $mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_file_gallery_remove_file.tpl");
                        }
                        $mail->setText($mail_data);
                        $mail->buildMessage();
                        $mail->send(array($not['email']));
                }
        }
}

?>
