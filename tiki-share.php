<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// To include a link in your tpl do
//<a href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>

$section = 'share';
require_once ('tiki-setup.php');
if (empty($_REQUEST['report'])) {
	$access->check_feature('feature_share');
	$access->check_permission('tiki_p_share');
} else {
	if ($_REQUEST['report'] == 'y') {
		$access->check_feature('feature_site_report', '', 'look');
		$access->check_permission('tiki_p_site_report');
	}
}

// email related:

$smarty->assign('do_email', (isset($_REQUEST['do_email'])?$_REQUEST['do_email']:true));
if (empty($_REQUEST['report']) || $_REQUEST['report'] != 'y') {
	// twitter/facebook related
	if (isset($prefs['feature_socialnetworks']) and $prefs['feature_socialnetworks'] == 'y') {

		require_once ('lib/socialnetworkslib.php');
		$smarty->assign('twitterRegistered', $socialnetworkslib->twitterRegistered());
		$smarty->assign('facebookRegistered', $socialnetworkslib->facebookRegistered());

		$twitter_token = $tikilib->get_user_preference($user, 'twitter_token', '');

		$smarty->assign('twitter', ($twitter_token != ''));
		$facebook_token = $tikilib->get_user_preference($user, 'facebook_token', '');
		$smarty->assign('facebook', ($facebook_token != ''));
		$smarty->assign('do_tweet', (isset($_REQUEST['do_tweet']) ? $_REQUEST['do_tweet'] : true));
		$smarty->assign('do_fb', (isset($_REQUEST['do_fb']) ? $_REQUEST['do_fb'] : true));
		$smarty->assign('fblike', (isset($_REQUEST['fblike']) ? $_REQUEST['fblike'] : 1));
	} else {
		$smarty->assign('twitterRegistered', false);
		$smarty->assign('twitter', false);
		$smarty->assign('facebookRegistered', false);
		$smarty->assign('facebook', false);
	}

	// message related
	if (isset($prefs['feature_messages']) and $prefs['feature_messages'] == 'y') {
		$logslib = TikiLib::lib('logs');

		$smarty->assign('priority', (isset($_REQUEST['priority'])?$_REQUEST['priority']:3));
		$smarty->assign('do_message', (isset($_REQUEST['do_message'])?$_REQUEST['do_message']:true));
		$send_msg = ($tiki_p_messages == 'y');

		if ($prefs['allowmsg_is_optional'] == 'y') {
			if ($tikilib->get_user_preference($user, 'allowMsgs', 'y') != 'y') {
				$send_msg = false;
			}
		}
		$smarty->assign('send_msg', $send_msg);
	} else {
		$smarty->assign('send_msg', false);
	}

	if (isset($prefs['feature_forums']) and $prefs['feature_forums'] == 'y') {
		$commentslib = TikiLib::lib('comments'); // not done in commentslib
		$sort_mode = $prefs['forums_ordering'];
		$channels = $commentslib->list_forums(0, -1, $sort_mode, '');
		Perms::bulk(array( 'type' => 'forum' ), 'object', $channels['data'], 'forumId');
		$forums = array();
		$temp_max = count($channels['data']);
		for ($i = 0; $i < $temp_max; $i++) {
			$forumperms = Perms::get(array( 'type' => 'forum', 'object' => $channels['data'][$i]['forumId'] ));
			if (($forumperms->forum_post and $forumperms->forum_post_topic) or $forumperms->admin_forum) {
				$forums[] = $channels['data'][$i];
			}
		}
		$smarty->assign('forumId', (isset($_REQUEST['forumId']) ? $_REQUEST['forumId'] : 0));
	} else {
		$forums = array();
	}
	$smarty->assign('forums', $forums);
	$report='n';
} else {
	$report='y';
}
$smarty->assign('report', isset($_REQUEST['report']) ? $_REQUEST['report'] : '');

$errors = array();
$ok = true;

if (empty($_REQUEST['url']) && !empty($_SERVER['HTTP_REFERER'])) {
	$u = parse_url($_SERVER['HTTP_REFERER']);

	if ($u['host'] != $_SERVER['SERVER_NAME']) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}
	$_REQUEST['url'] = $_REQUEST['HTTP_REFERER'];
}

if (empty($_REQUEST['url'])) {
	$smarty->assign('msg', tra('missing parameters'));
	$smarty->display('error.tpl');
	die;
}

$_REQUEST['url'] = urldecode($_REQUEST['url']);

if (strstr($_REQUEST['url'], 'tiki-share.php')) {
	$_REQUEST['url'] = preg_replace('/.*tiki-share.php\?url=/', '', $_REQUEST['url']);
	header('location: tiki-share.php?url=' . $_REQUEST['url']);
}

$url_for_friend = $tikilib->httpPrefix(true) . $_REQUEST['url'];

if ($report != 'y') {
	if (isset($_REQUEST['shorturl'])) {
		$shorturl = $_REQUEST['shorturl'];
	} else {
		if (isset($prefs['feature_socialnetworks']) and $prefs['feature_socialnetworks'] == 'y') {
			$shorturl = $socialnetworkslib->bitlyShorten($user, $url_for_friend);
		} else {
			$shorturl = false;
		}
		if ($shorturl == false) {
			$shorturl = $url_for_friend;
		}
	}
	$smarty->assign('shorturl', $shorturl);
}

$smarty->assign('url', $_REQUEST['url']);
$smarty->assign('prefix', $tikilib->httpPrefix(true));
$smarty->assign_by_ref('url_for_friend', $url_for_friend);
$smarty->assign('back_url', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

if (!empty($_REQUEST['subject'])) {
	$subject = $_REQUEST['subject'];
	$smarty->assign('subject', $subject);
} else {
	if ($report == 'y') {
		$subject = tra('Report to the webmaster', $prefs['site_language']);
	} else {
		$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
		$subject = $smarty->fetch('mail/share_subject.tpl');
	}
}

$smarty->assign('subject', $subject);

if (isset($_REQUEST['send'])) {

	if (!empty($_REQUEST['comment'])) {
		$smarty->assign('comment', $_REQUEST['comment']);
	}

	if (!empty($_REQUEST['share_token_notification'])) {
		$smarty->assign('share_token_notification', $_REQUEST['share_token_notification']);
	}

	if (!empty($_REQUEST['how_much_time_access'])) {
		$smarty->assign('how_much_time_access', $_REQUEST['how_much_time_access']);
	}

	check_ticket('share');
	if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
		$errors[] = $captchalib->getErrors();
	} else {
		if ($report == 'y') {
			$email = !empty($prefs['feature_site_report_email']) ? $prefs['feature_site_report_email'] : (!empty($prefs['sender_email']) ? $prefs['sender_email'] : '');
			if (empty($email)) {
				$errors[] = tra("The mail can't be sent. Contact the administrator");
			}
			$_REQUEST['addresses'] = $email;
			$_REQUEST['do_email'] = 1;
		}

		if (isset ($_REQUEST['do_email']) and $_REQUEST['do_email'] == 1) {
			// send

			// Fix for multi adresses with autocomplete funtionnality
			if (substr($_REQUEST['addresses'], -2) == ', ') {
				$_REQUEST['addresses'] = substr($_REQUEST['addresses'], 0, -2);
			}
			// Call checkAddresses with error = false to avoid double error reporting
			$adresses = checkAddresses($_REQUEST['addresses'], false);

			require_once 'lib/auth/tokens.php';
			if ($prefs['share_can_choose_how_much_time_access']
						&& isset($_REQUEST['how_much_time_access'])
						&& is_numeric($_REQUEST['how_much_time_access'])
						&& $_REQUEST['how_much_time_access'] >= 1
			) {
				$prefs['auth_token_access_maxhits'] = $_REQUEST['how_much_time_access'];

				/* To upload, you need 2 token: one to see the page and another */
				if (strpos($_REQUEST['url'], 'tiki-upload_file')) {
					$prefs['auth_token_access_maxhits'] = $prefs['auth_token_access_maxhits'] * 2 + 1;
				}

			}

			if ($_REQUEST['share_token_notification'] == 'y') {
				// list all users to give an unique token for notification
				$tokenlib = AuthTokens::build($prefs);

				if (is_array($adresses)) {
					$contactlib = TikiLib::lib('contact');
					foreach ($adresses as $adresse) {
						$tokenlist[] = $tokenlib->includeToken($url_for_friend, $globalperms->getGroups(), $adresse);
						// if preference share_contact_add_non_existant_contact the add auomaticly to contact
						if ($prefs['share_contact_add_non_existant_contact'] == 'y' && $prefs['feature_contacts'] == 'y') {
							// check if email exist for at least one contact in
							if (!$contactlib->exist_contact($adresse, $user)) {
								$contacts = array(array('email'=>$adresse));
								$contactlib->add_contacts($contacts, $user);
							}
						}
					}
				}

				$smarty->assign('share_access', true);

				if (is_array($tokenlist)) {
					foreach ($tokenlist as $i=>$data) {
						$query = parse_url($data);
						parse_str($query['query'],$query_vars);
						$detailtoken = $tokenlib->getToken($query_vars['TOKEN']);
						// Delete old user watch if it's necessary => avoid bad mails
						$tikilib->remove_user_watch_object('auth_token_called', $detailtoken['tokenId'], 'security');
						$tikilib->add_user_watch($user, 'auth_token_called', $detailtoken['tokenId'], 'security', tra('Token called'), $data);
					}
				}

			} else {
				if ( $prefs['auth_token_share'] == 'y' && ($prefs['auth_token_access'] == 'y' || isset($_POST['share_access']))) {
					$tokenlib = AuthTokens::build($prefs);
					$url_for_friend = $tokenlib->includeToken($url_for_friend, $globalperms->getGroups(), $_REQUEST['addresses']);
					$smarty->assign('share_access', true);
				}
				$tokenlist[0] = $url_for_friend;
			}

			$smarty->assign_by_ref('email', $_REQUEST['email']);

			if (!empty($_REQUEST['addresses'])) {
				$smarty->assign('addresses', $_REQUEST['addresses']);
			}

			if (!empty($_REQUEST['name'])) {
				$smarty->assign('name', $_REQUEST['name']);
			}
			$emailSent = sendMail($_REQUEST['email'], $_REQUEST['addresses'], $subject, $tokenlist);
			$smarty->assign('emailSent', $emailSent);
			$ok = $ok && $emailSent;
		} // do_email

		if ($report != 'y') {
			if (isset ($_REQUEST['do_tweet']) and $_REQUEST['do_tweet'] == 1) {
				$tweet = substr($_REQUEST['tweet'], 0, 140);
				if (strlen($tweet) == 0) {
					$ok = false;
					$errors[] = tra("No text given for tweet");
				} else {
					$tweetId = $socialnetworkslib->tweet($tweet, $user);
					if ($tweetId > 0) {
						$smarty->assign('tweetId', $tweetId);
					} else {
						$ok = false;
						$tweetId =- $tweetId;
						$errors[] = tra('Error sending tweet:') . " $tweetId";
					}
				}
			} // do_tweet
			if (isset ($_REQUEST['do_fb']) and $_REQUEST['do_fb']==1) {
				$msg = $_REQUEST['comment'];
				$linktitle = $_REQUEST['fblinktitle'];
				$facebookId = $socialnetworkslib->facebookWallPublish($user, $msg, $url_for_friend, $linktitle, $_REQUEST['subject']);
				$smarty->assign('facebookId', $facebookId);
				$ok = $ok && ($facebookId != false);
				if ($_REQUEST['fblike'] == 1) {
					if ($facebookId != false) {
						$like = $socialnetworkslib->facebookLike($user, $facebookId);
						$ok = $ok && ($like != false);
					}
				}
			} // do_fb

			if (isset($_REQUEST['do_message']) and $_REQUEST['do_message'] == 1) {
				$messageSent = sendMessage($_REQUEST['messageto'], $subject);
				$smarty->assign('messageSent', $messageSent);
				$ok = $ok || $messageSent;
				if ($messageSent) {
					$errors = array();
				}
			} // do_message

			if (isset($_REQUEST['do_forum']) and $_REQUEST['do_forum'] == 1) {
				if (isset($_REQUEST['forumId'])) {
					$threadId = postForum($_REQUEST['forumId'], $subject);
					$smarty->assign('threadId', $threadId);
					$ok = $ok && ($threadId != 0);
				}
			} // do_forum
		} //report != y

		$smarty->assign('errortype', 'no_redirect_login');
		if ($ok && $report == 'y') {
			$access->redirect($_REQUEST['url'], tra('Your link was sent.'));
		}
		$smarty->assign('sent', true);
		$smarty->assign('back_url', $_REQUEST['back_url']);
	}
	$smarty->assign_by_ref('errors', $errors);
} else {
	$smarty->assign_by_ref('name', $user);
	$smarty->assign('email', $userlib->get_user_email($user));
}

ask_ticket('share');
$smarty->assign('mid', 'tiki-share.tpl');
$smarty->display('tiki.tpl');

/**
 *
 * Validates the given recipients and returns false on error or an array containing the recipients on success
 * @param array|string	$recipients		list of recipients as an array or a comma/semicolon separated list
 * @return array|bool
 */
function checkAddresses($recipients, $error = true)
{
	global $errors, $prefs, $user;
	$userlib = TikiLib::lib('user');
	$registrationlib = TikiLib::lib('registration');
	$logslib = TikiLib::lib('logs');

	$e = array();

	if (!is_array($recipients)) {
		$recipients = preg_split('/(,|;)/', $recipients);
	}

	$ok = true;

	foreach ($recipients as &$recipient) {
		$recipient = trim($recipient);
		if (function_exists('validate_email')) {
			$ok = validate_email($recipient, $prefs['validateEmail']);
		} else {
			$ret = $registrationlib->SnowCheckMail($recipient, '', 'mini');
			$ok = $ret[0];
		}
		if ( $error && !$ok) {
			$e[] = tra('One of the email addresses that was input is invalid:') . '&nbsp;' . $recipient;
			$logslib->add_log('share', tra('One of the email addresses that was input is invalid:') . ' ' . $recipient . ' ' . tra('by') . ' ' . $user);
		}
	}

	if (count($e) != 0) {
		$errors = array_merge($errors, $e);
		return false;
	} else {
		return $recipients;
	}
}

/**
 *
 * Sends a promotional email to the given recipients
 * @param string        $sender        Sender e-Mail address
 * @param string|array    $recipients    List of recipients either as array or comma/semi colon separated string
 * @param string        $subject    E-Mail subject
 * @param array            $tokenlist
 * @internal param string $url_for_friend URL to share
 * @return bool                        true on success / false if the supplied parameters were incorrect/missing or an error occurred sending the mail
 */
function sendMail($sender, $recipients, $subject, $tokenlist = array())
{
	global $errors, $prefs, $user;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	$registrationlib = TikiLib::lib('registration');
	$logslib = TikiLib::lib('logs');

	if (empty($sender)) {
		$errors[] = tra('Your email is mandatory');
		return false;
	}

	if (function_exists('validate_email')) {
		$ok = validate_email($sender, $prefs['validateEmail']);
	} else {
		$ret = $registrationlib->SnowCheckMail($sender, '', 'mini');
		$ok = $ret[0];
	}

	if ($ok) {
		$from = str_replace(array("\r", "\n"), '', $sender);
	} else {
		$errors[] = tra('Invalid email') . ': ' . $_REQUEST['email'];
		return false;
	}

	$recipients=checkAddresses($recipients);

	if ($recipients === false) {
		return false;
	}

	include_once ('lib/webmail/tikimaillib.php');
	$smarty->assign_by_ref('mail_site', $_SERVER['SERVER_NAME']);

	$applyFrom = (!empty($user) && $from == $userlib->get_user_email($user));

	$ok = true;
	foreach ($recipients as $i=>$recipient) {
		$mail = new TikiMail();

		$mail->setSubject($subject);

		if ($applyFrom) {
			$mail->setFrom($from);
			$mail->setReplyTo("<$from>");
		}

		if (count($tokenlist) > 1) {
			$url_for_friend = $tokenlist[$i];
		} else {
			$url_for_friend = $tokenlist[0];		// only one token if not "subscribing"
		}
		$smarty->assign('url_for_friend', $url_for_friend);
		$txt = $smarty->fetch('mail/share.tpl');
		// Rebuild email message texte
		$mail->setText($txt);
		$mailsent = $mail->send(array($recipient));
		if (!$mailsent) {
			$errors[] = tra('Error sending mail to'). " $recipient";
			$logslib->add_log('share', tra('Error sending mail to'). " $recipient "  . tra('by') .' ' . $user);
		} else {
			$logslib->add_log('share', tra('Share page').': '.$url_for_friend.' '.tra('to').' '.$recipient.' '.tra('by').' '.$user);
		}
		$ok = $ok && $mailsent;
	}
	return $ok;
}

/**
 * sends a message via the internal messaging to a list of recipients
 * @param string|array	$recipients	comma separated list (or array) of recipients
 * @param string		$subject	subject of the message
 * @return bool						true on success/sent to all users successfully
 */
function sendMessage($recipients, $subject)
{
	global $errors, $prefs, $user;
	$messulib = TikiLib::lib('message');
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$logslib = TikiLib::lib('logs');

	$ok = true;
	if (!is_array($recipients)) {
		$arr_to = preg_split('/\s*(?<!\\\)[;,]\s*/', $recipients);
	} else {
		$arr_to = $recipients;
	}
    if ($prefs['user_selector_realnames_messu'] == 'y') {
        $groups = '';
        $arr_to = $userlib->find_best_user($arr_to, $groups, 'login');
    }

	$users = array();

	foreach ($arr_to as $a_user) {
		if (!empty($a_user)) {
			$a_user = str_replace('\\;', ';', $a_user);
			if ($userlib->user_exists($a_user)) {
				// mail only to users with activated message feature
				if ($prefs['allowmsg_is_optional'] != 'y' || $tikilib->get_user_preference($a_user, 'allowMsgs', 'y') == 'y') {
					// only send mail if nox mailbox size is defined or not reached yet
					if (($messulib->count_messages($a_user) < $prefs['messu_mailbox_size']) || ($prefs['messu_mailbox_size'] == 0)) {
						$users[] = $a_user;
					} else {
						$errors[] = tra('User %s can not receive messages, mailbox is full');
						$ok = false;
					}
				} else {
					$errors[] = tra('User %s can not receive messages');
					$ok = false;
				}
			} else {
				$errors[] = tra('Invalid user: %s');
				$ok = false;
			}
		}
	}

	$users = array_unique($users);
	$txt = $smarty->fetch('mail/share.tpl');

	foreach ($users as $a_user) {
		$messulib->post_message(
			$a_user,
			$user,
			$a_user,
			'',
			$subject,
			$txt,
			$_REQUEST['priority'],
			isset($_REQUEST['replyto_hash']) ? $_REQUEST['replyto_hash'] : ''
		);

		TikiLib::events()->trigger('tiki.user.message',
			array(
				'type' => 'user',
				'object' => $a_user,
				'user' => $user,
			)
		);
	}

	// Insert a copy of the message in the sent box of the sender
	$messulib->save_sent_message(
		$user,
		$user,
		$recipients,
		'',
		$subject,
		$txt,
		$_REQUEST['priority'],
		isset($_REQUEST['replyto_hash']) ? $_REQUEST['replyto_hash'] : ''
	);

    // Assign users e-mail was sent to a SMARTY variable to be displayed:
    $users_string = implode(',  ', $users);
    $smarty->assign('messageSentTo', $users_string);

	if ($prefs['feature_actionlog'] == 'y') {
		$logslib->add_action('Posted', '', 'message', 'add=' . strlen($_REQUEST['body']));
	}

	return $ok;
}

/**
 * @param $forumId
 * @param $subject
 * @return bool|int
 */
function postForum($forumId, $subject)
{
	global $errors, $prefs, $user;
	global $feedbacks;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$commentslib = TikiLib::lib('comments');

	$forum_info = $commentslib->get_forum($forumId);
	$forumperms = Perms::get(array( 'type' => 'forum', 'object' => $forumId ));

	if (!($forumperms->forum_post and $forumperms->forum_post_topic) or !$forumperms->admin_forum) {
		$errors[] = tra("You don't have permission to post in this forum");
		return 0;
	}

	if ($forum_info['is_locked'] == 'y') {
		// this is a "die" in $commentslib->post_in_forum so we must check here
		$errors[] = tra('This forum is locked');
		return 0;
	}

	$postErrors = array();
	$feedbacks = array();
	$txt = $smarty->fetch('mail/share.tpl');

	$data = array(
				'comments_title' => $subject,
				'comments_data' => $txt,
				'password' => $_REQUEST['forum_password'],
				'comments_threadId' => 0,
				'forumId' => $forumId,
	);

	$threadId = $commentslib->post_in_forum($forum_info, $data, $feedbacks, $postErrors);

	if (count($postErrors)>0) {
		$errors = array_merge($errors, $postErrors);
	}

	$smarty->assign('feedbacks', $feedbacks);
	return $threadId;
}
