<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \brief send the email notifications dealing with the forum changes to
  * \brief outbound address + admin notification addresses / forum admin email + watching users addresses
  * \param $event = 'forum_post_topic' or 'forum_post_thread'
  * \param $object = forumId watch if forum_post_topic or topicId watch if forum_post_thread
  * \param $threadId = topicId if forum_post_thread
  * \param $title of the message
  * \param $topicName name of the parent topic
  */

include_once('lib/reportslib.php');
  
function sendForumEmailNotification($event, $object, $forum_info, $title, $data, $author, $topicName, $messageId='', $inReplyTo='', $threadId, $parentId, $contributions='', $postId='') {
	global $tikilib, $prefs, $smarty, $userlib;

	// Per-forum From address overrides global default.
	if( $forum_info['outbound_from'] )
	{
		$author = $userlib->clean_user($author);
	    $my_sender = '"' . "$author" . '" <' . $forum_info['outbound_from'] . '>';
	} else {
	    $my_sender = $prefs['sender_email'];
	}

	//outbound email ->  will be sent in utf8 - from sender_email
	if ($forum_info['outbound_address']) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$mail->setSubject($title);
		if (!empty($forum_info['outbound_mails_reply_link']) && $forum_info['outbound_mails_reply_link'] == 'y') {
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $tikilib->httpPrefix( true ) . dirname( $foo["path"] );
			if ($event == 'forum_post_topic') {
				$reply_link="$machine/tiki-view_forum_thread.php?forumId=" .
				$forum_info['forumId'] .
				"&comments_parentId=$threadId#form";
			} else {
		  		$reply_link="$machine/tiki-view_forum_thread.php?forumId=" .
				$forum_info['forumId'] .
				"&comments_reply_threadId=$object&comments_parentId=$threadId&post_reply=1#form";
			}
		} else {
			$reply_link = '';
		}
		$smarty->assign('title', $title);
		$smarty->assign('data', $data);
		$smarty->assign('reply_link', $reply_link);
		$smarty->assign('author', $author);
		$mail_data = $smarty->fetch("mail/forum_outbound.tpl");
		$mail->setText($mail_data);
		$mail->setHeader("Reply-To", $my_sender);
		$mail->setHeader("From", $my_sender);
		$mail->setSubject($topicName);

		if ($inReplyTo)	{
		    $mail->setHeader("In-Reply-To", "<".$inReplyTo.">");
		}

		global $commentslib;
		$attachments = $commentslib->get_thread_attachments( $event == 'forum_post_topic'? $threadId: $object, 0 );

		if( count( $attachments ) > 0 )
		{
		    foreach ( $attachments as $att )
		    {
				$att_data = $commentslib->get_thread_attachment( $att['attId'] );
				if($att_data['dir'].$att_data['path'] == ""){ // no path to file on disk
					$file = $att_data['data']; // read file from database
				} else {
					$file = $mail->getFile( $att_data['dir'].$att_data['path'] ); // read file from disk
				}
				$mail->addAttachment( $file, $att_data['filename'], $att_data['filetype'] );
		    }
		}


		$mail->buildMessage();

		// Message-ID is set below buildMessage because otherwise lib/webmail/htmlMimeMail.php will over-write it.
		$mail->setHeader("Message-ID", "<".$messageId.">");

		$mail->send(array($forum_info['outbound_address']));
	}

	$nots = array();
	$defaultLanguage = $prefs['site_language'];

	// Users watching this forum or this post
	if ($prefs['feature_user_watches'] == 'y' || $prefs['feature_group_watches'] == 'y') {
		$nots_raw = $tikilib->get_event_watches($event, $event == 'forum_post_topic'? $forum_info['forumId']: $threadId, $forum_info);
		$nots = array();
		foreach( $nots_raw as $n ) {
			if( $n['user'] != $author ) {
				$n['language'] = $tikilib->get_user_preference($n['user'], "language", $defaultLanguage);
				$nots[] = $n;
			}
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

	global $reportslib;

	if ($prefs['feature_user_watches'] == 'y' && $prefs['feature_daily_report_watches'] == 'y') {
		$reportslib->makeReportCache($nots, array("event"=>$event, "forumId"=>$forum_info['forumId'], "forumName"=>$forum_info['name'], "topicId"=>$threadId, "threadId"=>$postId, "threadName"=>$topicName, "user"=>$author));
	}

	if (count($nots)) {
		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$smarty->assign('mail_forum', $forum_info["name"]);
		$smarty->assign('mail_title', $title);
		$smarty->assign('mail_date', $tikilib->now);
		$smarty->assign('mail_message', $data);
		$smarty->assign('mail_author', $author);
		if ($prefs['feature_contribution'] == 'y' && !empty($contributions)) {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
		}
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix( true ) . dirname( $foo["path"] );
		$machine = preg_replace("!/$!", "", $machine); // just incase
 		$smarty->assign('mail_machine', $machine);
		$smarty->assign('forumId', $forum_info["forumId"]);
		if ($event == "forum_post_topic") {
			$smarty->assign('new_topic', 'y');
		} else {
		$smarty->assign('threadId', $object);
		}
		$smarty->assign('topicId', $threadId);
		$smarty->assign('mail_topic', $topicName);
		foreach ($nots as $not) {
			$mail->setUser($not['user']);
			$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_forum_subject.tpl");
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
  * \$event: 'wiki_page_created'|'wiki_page_changed'|wiki_page_deleted |wiki_file_attached
  */
function sendWikiEmailNotification($wikiEvent, $pageName, $edit_user, $edit_comment, $oldver, $edit_data, $machine='', $diff='', $minor=false, $contributions='', $structure_parent_id=0, $attId=0, $lang='') {
	global $tikilib, $prefs, $smarty, $userlib;
	global $notificationlib; include_once('lib/notifications/notificationlib.php');
	$nots = array();
	$defaultLanguage = $prefs['site_language'];
	if ($wikiEvent == 'wiki_file_attached') {
		$event = 'wiki_page_changed';
	} else {
		$event = $wikiEvent;
	}

	if ($prefs['feature_user_watches'] == 'y') {
		$nots = $tikilib->get_event_watches($event, $pageName);
	}
	
	if ($prefs['feature_user_watches'] == 'y' && $event == 'wiki_page_changed') {
		global $structlib; include_once('lib/structures/structlib.php');
		$nots2 = $structlib->get_watches($pageName);
		if (!empty($nots2)) {
			$nots = array_merge($nots, $nots2);
		}

		if ($prefs['wiki_watch_editor'] != "y") {
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

	if ($prefs['feature_user_watches'] == 'y' && $event == 'wiki_page_created' && $structure_parent_id) {
		global $structlib; include_once('lib/structures/structlib.php');
		$nots = array_merge( $nots, $structlib->get_watches('', $structure_parent_id) );
	}

	// admin notifications
    // If it's a minor change, get only the minor change watches.
	if( $minor ){
		$emails = $notificationlib->get_mail_events('wiki_page_changes_incl_minor', $pageName); // look for pageName and any page
	} else { // else if it's not minor change, get both watch types.
		$emails1 = $notificationlib->get_mail_events('wiki_page_changes', $pageName); // look for pageName and any page
		$emails2 = $notificationlib->get_mail_events('wiki_page_changes_incl_minor', $pageName); // look for pageName and any page
		$emails = array_merge( $emails1, $emails2 );
	}
	foreach ($emails as $email) {
		if ($prefs['wiki_watch_editor'] != "y" && $email == $edit_user)
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

	if ($edit_user=='') $edit_user = tra('Anonymous');

	global $reportslib;
	
	if ($prefs['feature_user_watches'] == 'y' && $prefs['feature_daily_report_watches'] == 'y') {
		if ($wikiEvent == 'wiki_file_attached') {
			$reportslib->makeReportCache($nots, array("event"=>$wikiEvent, "pageName"=>$pageName, 'attId'=>$attId, "editUser"=>$edit_user, "editComment"=>$edit_comment, 'filename'=>$edit_data));
		} else {
			$reportslib->makeReportCache($nots, array("event"=>$wikiEvent, "pageName"=>$pageName, "object"=>$pageName, "editUser"=>$edit_user, "editComment"=>$edit_comment, "oldVer"=>$oldver));
		}
	}

	if (count($nots)) {
	    $edit_data = TikiLib::htmldecode($edit_data);
	    include_once('lib/mail/maillib.php');
	    $smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
	    $smarty->assign('mail_page', $pageName);
	    $smarty->assign('mail_date', $tikilib->now);
	    $smarty->assign('mail_user', $edit_user);
	    $smarty->assign('mail_comment', $edit_comment);
	    $newver = $oldver + 1;
	    $smarty->assign('mail_oldver', $oldver);
	    $smarty->assign('mail_newver', $newver);
	    $smarty->assign('mail_data', $edit_data);
		$smarty->assign('mail_attId', $attId);
	    $foo = parse_url($_SERVER["REQUEST_URI"]);
	    $machine = $tikilib->httpPrefix( true ). dirname( $foo["path"] );
	    $smarty->assign('mail_machine', $machine);
		if ($prefs['feature_contribution'] == 'y' && !empty($contributions)) {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
		}
	    $parts = explode('/', $foo['path']);
	    if (count($parts) > 1)
		unset ($parts[count($parts) - 1]);
	    $smarty->assign('mail_machine_raw', $tikilib->httpPrefix( true ). implode('/', $parts));
	    $smarty->assign_by_ref('mail_pagedata', $edit_data);
	    $smarty->assign_by_ref('mail_diffdata', $diff);
	    if ($event == 'wiki_page_created') {
			$smarty->assign('mail_action', 'new');
	    } elseif ($event == 'wiki_page_deleted') {
	    	$smarty->assign('mail_action', 'delete');
		} elseif ($wikiEvent == 'wiki_file_attached') {
			$smarty->assign('mail_action', 'attach');
	    } else {
	    	$smarty->assign('mail_action', 'edit');
	    }

		include_once('lib/webmail/tikimaillib.php');
	    foreach ($nots as $not) {
			if (empty($not['email'])) continue;
		    $smarty->assign('watchId', $not['watchId']);

			$mail_subject = $smarty->fetchLang($not['language'], "mail/user_watch_wiki_page_changed_subject.tpl");
			$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_wiki_page_changed.tpl");

			$mail = new TikiMail($not['user']);
			$mail->setSubject(sprintf($mail_subject, $pageName));
			$mail->setText($mail_data);
			$mail->setHeader("From", $prefs['sender_email']);
			$mail->send(array($not['email']));

		}
	}
}

/** \brief Send email notification to a list of emails or a list of (email, user) in a charset+language associated with each email
 * \param $watches : bidimensional array of watches. Each watch has user, language, email and watchId keys.
 * \param $dummy: unused
 * \param $subjectTpl: subject template file or null (ex: "submission_notifcation.tpl")
 * \param $subjectParam: le param to be inserted in the subject or null
 * \param $txtTpl : texte template file (ex: "submission_notifcation.tpl")
 * \param $from email from to not the default one
 * \ $smarty is supposed to be already built to fit $txtTpl
 * \return the nb of sent emails
 */
function sendEmailNotification($watches, $dummy, $subjectTpl, $subjectParam, $txtTpl, $from='') {
    global $smarty, $tikilib;
	include_once('lib/webmail/tikimaillib.php');
	$sent = 0;
	$mail = new TikiMail(null, $from);
	$smarty->assign('mail_date', $tikilib->now);
	
	$foo = parse_url($_SERVER["REQUEST_URI"]);
	$smarty->assign('mail_machine', $tikilib->httpPrefix( true ). $foo["path"] );
	$parts = explode('/', $foo['path']);

	if (count($parts) > 1)
		unset ($parts[count($parts) - 1]);

	$smarty->assign('mail_machine_raw', $tikilib->httpPrefix( true ). implode('/', $parts));
	// TODO: mail_machine_site may be required for some sef url with rewrite to sub-directory. To refine. (nkoth)  
	$smarty->assign('mail_machine_site', $tikilib->httpPrefix( true ));

	foreach ($watches as $watch) {
		$smarty->assign('watchId', $watch['watchId']);
		if ($watch['user']) {
			$mail->setUser($watch['user']);
		}
		if ($subjectTpl) {
			$mail_data = $smarty->fetchLang($watch['language'], "mail/".$subjectTpl);
			if ($subjectParam) {
				$mail_data = sprintf($mail_data, $subjectParam);
			}
			$mail_data = preg_replace('/%[sd]/', '', $mail_data);// partial cleaning if param not supply and %s in text
			$mail->setSubject($mail_data);
		} else {
			$mail->setSubject($subjectParam);
		}
		$mail->setText($smarty->fetchLang($watch['language'], "mail/".$txtTpl));
		$mail->buildMessage();
		if ($mail->send(array($watch['email']))) {
			$sent++;
		}
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

function sendFileGalleryEmailNotification($event, $galleryId, $galleryName, $name, $filename, $description, $action, $user, $fileId) {
        global $tikilib, $prefs, $smarty, $userlib, $reportslib;

        $nots = array();
        $defaultLanguage = $prefs['site_language'];

        // Users watching this gallery
        if ($prefs['feature_user_watches'] == 'y') {
                $nots = $tikilib->get_event_watches($event, $galleryId);
                for ($i = count($nots) - 1; $i >=0; --$i) {
                        $nots[$i]['language'] = $tikilib->get_user_preference($nots[$i]['user'], "language", $defaultLanguage);
                }

				if ($prefs['feature_daily_report_watches'] == 'y') {
					$reportslib->makeReportCache($nots, array("event"=>$event, "name"=>$name, "fileId"=>$fileId, "fileName"=>$filename, "galleryId"=>$galleryId, "galleryName"=>$galleryName, "action"=>$action, "user"=>$user));
				}
        }
        
        if (count($nots)) {
                include_once('lib/webmail/tikimaillib.php');
                $mail = new TikiMail();
                $smarty->assign('galleryName', $galleryName);
				$smarty->assign('galleryId', $galleryId);
				$smarty->assign('fname', $name);
				$smarty->assign('filename', $filename);
				$smarty->assign('fdescription', $description);
                $smarty->assign('mail_date', $tikilib->now);
                $smarty->assign('author', $user);
                $foo = parse_url($_SERVER["REQUEST_URI"]);
                $machine = $tikilib->httpPrefix( true ). dirname( $foo["path"] );
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

/**
 * Sends E-Mail notifications for a created/changed/removed category.
 * The Array $values contains a selection of the following items:
 * event, categoryId, categoryName, categoryPath, description, parentId, parentName, action,
 * oldCategoryName, oldCategoryPath, oldDescription, oldParendId, oldParentName,
 * objectName, objectType, objectUrl
 */
function sendCategoryEmailNotification($values) {
        $event=$values['event'];
        $action=$values['action'];
        $categoryId=$values['categoryId'];
        $categoryName=$values['categoryName'];
        $categoryPath=$values['categoryPath'];
        $description=$values['description'];
		$parentId=$values['parentId'];
		$parentName=$values['parentName'];
		
		if ($action == 'category updated'){
        	$oldCategoryName=$values['oldCategoryName'];
        	$oldCategoryPath=$values['oldCategoryPath'];
        	$oldDescription=$values['oldDescription'];
			$oldParentId=$values['oldParentId'];
			$oldParentName=$values['oldParentName'];
		} else 	if ($action == 'object entered category' || $action == 'object leaved category'){
			$objectName=$values['objectName'];
			$objectType=$values['objectType'];
			$objectUrl=$values['objectUrl'];
		}

        
        //$event, $categoryId, $categoryName, $categoryPath, 
		//$description, $parentId, $parentName, $action
        global $tikilib, $prefs, $smarty, $userlib, $user, $reportslib;

        $nots = array();
        $defaultLanguage = $prefs['site_language'];

        // Users watching this gallery
		if ($prefs['feature_user_watches'] == 'y') {
			if ($action == 'category created') {                                
				$nots = $tikilib->get_event_watches($event, $parentId);
			} else if ($action == 'category removed') { 
                $nots = $tikilib->get_event_watches($event, $categoryId);
                $nots = array_merge($nots,$nots = $tikilib->get_event_watches($event, $parentId));                
			} else {
				$nots = $tikilib->get_event_watches($event, $categoryId);
			} 
                
			for ($i = count($nots) - 1; $i >=0; --$i) {
				$nots[$i]['language'] = $tikilib->get_user_preference($nots[$i]['user'], "language", $defaultLanguage);
			}
			
			if ($prefs['feature_daily_report_watches'] == 'y') {
				$cache_data = $values;
				$cache_data['user'] = $user;
				$cache_data['event'] = $event;
				$reportslib->makeReportCache($nots, $cache_data);
			}
		}

        if (count($nots)) {        		
                include_once('lib/webmail/tikimaillib.php');
                $mail = new TikiMail();
                
                $smarty->assign('categoryId', $categoryId);
                $smarty->assign('categoryName', $categoryName);
                $smarty->assign('categoryPath', $categoryPath);
                $smarty->assign('description', $description);
                $smarty->assign('parentId', $parentId);
                $smarty->assign('parentName', $parentName);                
                $smarty->assign('mail_date', date("U"));
                $smarty->assign('author', $user);                
                
                $foo = parse_url($_SERVER["REQUEST_URI"]);
                $machine = $tikilib->httpPrefix( true ). dirname( $foo["path"] );
				$machine = preg_replace("!/$!", "", $machine); // just incase
                $smarty->assign('mail_machine', $machine);

				$nots_send = array(); 
                foreach ($nots as $not) {
					if (!empty($nots_send[$not['user']])) {
						break;               			
					}
               			$nots_send[$not['user']] = true;
                        $mail->setUser($not['user']);
                        if ($action == 'category created') {                                                        
                        	$mail_subject = $smarty->fetchLang($not['language'], "mail/user_watch_category_created_subject.tpl");
                        	$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_category_created.tpl");
                        } else if ($action == 'category removed'){
                        	$mail_subject = $smarty->fetchLang($not['language'], "mail/user_watch_category_removed_subject.tpl");
                        	$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_category_removed.tpl");
                        } else if ($action == 'category updated'){
                        	$mail_subject = $smarty->fetchLang($not['language'], "mail/user_watch_category_updated_subject.tpl");                        	                                                	                        

							$smarty->assign('oldCategoryName',$oldCategoryName);
           			    	$smarty->assign('oldCategoryPath',$oldCategoryPath);
               				$smarty->assign('oldDescription',$oldDescription);
               				$smarty->assign('oldParentId', $oldParentId);
               				$smarty->assign('oldParentName',$oldParentName);                									                        	
                        	$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_category_updated.tpl");                         	                        	
                        } else if ($action == 'object entered category') {
                        	$mail_subject = $smarty->fetchLang($not['language'], "mail/user_watch_object_entered_category_subject.tpl");
							$smarty->assign('objectName',$objectName);
							$smarty->assign('objectType',$objectType);							
							$smarty->assign('objectUrl',$objectUrl);                      
                        	$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_object_entered_category.tpl");
                        } else if ($action == 'object leaved category') {
                        	$mail_subject = $smarty->fetchLang($not['language'], "mail/user_watch_object_leaved_category_subject.tpl");
							$smarty->assign('objectName',$objectName);
							$smarty->assign('objectType',$objectType);							
							$smarty->assign('objectUrl',$objectUrl);                      
                        	$mail_data = $smarty->fetchLang($not['language'], "mail/user_watch_object_leaved_category.tpl");
                        }                        
                                                
                        $mail->setSubject($mail_subject);                        
                        $mail->setText($mail_data);
                        $mail->buildMessage();
                        $mail->send(array($not['email']));
                }
        }        
}
function sendStructureEmailNotification($params) {
	global $tikilib, $smarty, $prefs;
	global $structlib; include_once('lib/structures/structlib.php');
	if ($params['action'] == 'move_up' || $params['action'] == 'move_down') {
		$nots = $structlib->get_watches('', $params['parent_id'], false);
	} else {
		$nots = $structlib->get_watches('', $params['page_ref_id']);
	}
	if (!empty($nots)) {
		$defaultLanguage = $prefs['site_language'];
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix( true ). dirname( $foo["path"] );
		$smarty->assign_by_ref('mail_machine', $machine);
	    include_once('lib/webmail/tikimaillib.php');
        $mail = new TikiMail();
		$smarty->assign_by_ref('action',$params['action']);
		$smarty->assign_by_ref('page_ref_id', $params['page_ref_id']);
		if (!empty($params['name'])) {
			$smarty->assign('name', $params['name']);
		}
		foreach ($nots as $not) {
			$mail->setUser($not['user']);
			$not['language'] = $tikilib->get_user_preference($not['user'], 'language', $defaultLanguage);
			$mail_subject = $smarty->fetchLang($not['language'], 'mail/user_watch_structure_subject.tpl');
			$mail_data = $smarty->fetchLang($not['language'], 'mail/user_watch_structure.tpl');
			$mail->setSubject($mail_subject);
			$mail->setText($mail_data);
			$mail->buildMessage();
			$mail->send(array($not['email']));
		}
	}
}

/**
 * @param $type Type of the object commented on, 'wiki' or 'article'
 * @param $id Identifier of the object commented on. For articles, their id and for wiki pages, their name
 * @param $title Comment title
 * @param $content Comment content
 */
function sendCommentNotification($type, $id, $title, $content) {
	global $user, $tikilib, $smarty, $prefs;
	if ($type == 'wiki') {
		$event = 'wiki_comment_changes';
	} elseif ($type == 'article') {
		$event = 'article_commented';
	} else {
		throw new Exception('Unknown type');
	}
	$watches = $tikilib->get_event_watches($event, $id);
	if (count($watches)) {
		if ($type == 'wiki') {
			$smarty->assign('mail_objectname', $id);
		} elseif ($type == 'article') {
			global $artlib;	include_once ('lib/articles/artlib.php');
			$smarty->assign('mail_objectname', $artlib->get_title($id));
			$smarty->assign('mail_objectid', $id);
		}
		$smarty->assign('objecttype', $type);		
		$smarty->assign('mail_user', $user);
		$smarty->assign('mail_title', $title);
		$smarty->assign('mail_comment', $content);

		foreach ($watches as $key => $watch) {
			if ($watch['user'] == $user && ($type != 'wiki' || $prefs['wiki_watch_editor'] != 'y')) {
				unset($watches[$key]);
			}
		}
		sendEmailNotification($watches, null, 'user_watch_comment_subject.tpl', null, 'user_watch_comment.tpl');
	}
}