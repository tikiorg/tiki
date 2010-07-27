<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-tell_a_friend.php 27748 2010-06-23 03:01:50Z sampaioprimo $

require_once ('tiki-setup.php');
require_once ('lib/socialnetworkslib.php');
// To include a link in your tpl do
//<a href="tiki-promote.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Promote this page{/tr}</a>

$smarty->assign('headtitle', tra('Promote this page'));
$access->check_feature('feature_promote_page');
$access->check_permission('tiki_p_promote_page');

include_once ("textareasize.php");
$errors = array();
$ok=true;

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
if (strstr($_REQUEST['url'], 'tiki-promote.php')) {
	$_REQUEST['url'] = preg_replace('/.*tiki-promote.php\?url=/', '', $_REQUEST['url']);
	header('location: tiki-promote.php?url=' . $_REQUEST['url']);
}
$url_for_friend = $tikilib->httpPrefix( true ) . $_REQUEST['url'];
if( $prefs['auth_token_promote'] == 'y' && $prefs['auth_token_access'] == 'y' && isset($_POST['share_access']) ) {
	require_once 'lib/auth/tokens.php';
	$tokenlib = AuthTokens::build( $prefs );
	$url_for_friend = $tokenlib->includeToken( $url_for_friend, $globalperms->getGroups() );
}

$smarty->assign('url', $_REQUEST['url']);
$smarty->assign('prefix', $tikilib->httpPrefix( true ));
$smarty->assign('shorturl', $url_for_friend);
include_once ('lib/registration/registrationlib.php');
$smarty->assign('twitterRegistered',$socialnetworkslib->twitterRegistered());
$smarty->assign('facebookRegistered',$socialnetworkslib->facebookRegistered());
$twitter_token=$tikilib->get_user_preference($user, 'twitter_token', '');
$smarty->assign('twitter', ($twitter_token!=''));
$facebook_token=$tikilib->get_user_preference($user, 'facebook_token', '');
$smarty->assign('facebook', ($facebook_token!=''));

if (isset($_REQUEST['send'])) {
	if (!empty($_REQUEST['comment'])) {
		$smarty->assign('comment', $_REQUEST['comment']);	
	}
	if (!empty($_REQUEST['subject'])) {
		$subject = $_REQUEST['subject'];
	} else {
		$subject = $smarty->fetch('mail/promote_subject.tpl');
	}
	$smarty->assign('subject', $subject);
	$smarty->assign('do_email', $_REQUEST['do_email']);
	$smarty->assign('do_tweet', $_REQUEST['do_tweet']);
	$smarty->assign('do_fb', $_REQUEST['do_fb']);
	
	check_ticket('promote');
	if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
		$errors[] = $captchalib->getErrors();
	}
	if (isset ($_REQUEST['do_email']) and $_REQUEST['do_email']==1) {
		$emailSent = sendMail($_REQUEST['email'], $_REQUEST['addresses'], $subject, $url_for_friend);
		$smarty->assign_by_ref('email', $_REQUEST['email']);
		if (!empty($_REQUEST['addresses'])) {
			$smarty->assign('addresses', $_REQUEST['addresses']);
		}
		if (!empty($_REQUEST['name'])) {
			$smarty->assign('name', $_REQUEST['name']);
		}
		$smarty->assign('emailSent', $emailSent);
		$ok = $ok && $emailSent;
	} // do_email
	if (isset ($_REQUEST['do_tweet']) and $_REQUEST['do_tweet']==1) {
		$tweet=substr($_REQUEST['tweet'],0,140);
		if (strlen($tweet)==0) {
			$ok=false;
			$errors[]=tra("No text given for tweet");
		} else {
			$tweetId=$socialnetworkslib->tweet($tweet, $user);
			if ($tweetId>0) {
				$smarty->assign('tweetId',$tweetId);
			} else {
				$ok=false;
				$tweetId=-$tweetId;
				$errors[]=tra("Error sending tweet:")." $tweetId";
			}
		}
	} // do_tweet
	if (isset ($_REQUEST['do_fb']) and $_REQUEST['do_fb']==1) {
		$msg=$_REQUEST['comment'];
		$linktitle=$_REQUEST['fblinktitle'];
		$facebookResponse=$socialnetworkslib->facebookWallPublish($user, $msg, $url_for_friend, $linktitle, $_REQUEST['subject']);
		
	} // do_fb
	
	$smarty->assign_by_ref('errors', $errors);
	$smarty->assign('errortype', 'no_redirect_login');
	if ($ok) {
		//$access->redirect( $_REQUEST['url'], tra('Your link was sent.') );
	}
	
} else {
	$smarty->assign_by_ref('name', $user);
	$smarty->assign('email', $userlib->get_user_email($user));
}
ask_ticket('promote');
$smarty->assign('mid', 'tiki-promote.tpl');
$smarty->display('tiki.tpl');

/**
 * 
 * Validates the given recipients and returns false on error or an array containing the recipients on success
 * @param array|string	$recipients		list of recipients as an array or a comma/semicolon separated list	
 */
function checkAddresses($recipients) {
	global $errors;
	global $registrationlib; include_once ('lib/registration/registrationlib.php');
	$e=array();
	if (!is_array($recipients)) {
		$recipients=preg_split('/[\s*?](,|;)[\s*?]/',$recipients);
	}
	$ok=true;
	foreach($recipients as &$recipient) {
		$recipient=trim($recipient);
		if (function_exists('validate_email')) {
			$ok = validate_email($recipient, $prefs['validateEmail']);
		} else {
			$ret = $registrationlib->SnowCheckMail($recipient, '', 'mini');
			$ok = $ret[0];
		}
		if (!$ok) {
			$e[] = tra('One of the email addresses you typed is invalid') . ': ' . $recipient;
		}
	}
	if(count($e) != 0) {
		$errors=array_merge($errors, $e);
		return false;
	} else {
		return $recipients;
	}
}

/**
 * 
 * Sends a promotional email to the given recipients
 * @param string		$sender		Sender e-Mail address
 * @param string|array	$recipients	List of recipients either as array or comma/semi colon separated string
 * @param string		$subject	E-Mail subject
 * @param string		$url_for_friend		URL to promote
 * @return bool						true on success / false if the supplied parameters were incorrect/missing or an error occurred sending the mail
 */
function sendMail($sender, $recipients, $subject, $url_for_friend) {
	global $errors, $prefs, $smarty;
	global $registrationlib; include_once ('lib/registration/registrationlib.php');
	
	if (empty($sender)) {
		$errors[] = tra('Your email is mandatory');
		return false;
	}
	if (function_exists('validate_email')) {
		$ok = validate_email($sender, $prefs['validateEmail']);
	} else {
		$ret = $registrationlib->SnowCheckMail($sender, '', 'mini');
		$ok=$ret[0];
	}
	if ($ok) {
		$from = str_replace(array("\r", "\n"), '', $sender);
	} else {
		$errors[] = tra('Invalid email') . ': ' . $_REQUEST['email'];
		return false;
	}
	$recipients=checkAddresses($recipients);
	if ($recipients===false) {
		return false;
	}
	include_once ('lib/webmail/tikimaillib.php');
	$mail = new TikiMail();
	$smarty->assign_by_ref('mail_site', $_SERVER['SERVER_NAME']);
	$mail->setFrom($from);
	$mail->setHeader("Return-Path", "<$from>");
	$mail->setHeader("Reply-To", "<$from>");

	$smarty->assign( 'url_for_friend', $url_for_friend );
	$txt = $smarty->fetch('mail/promote.tpl');
	$mail->setSubject($subject);
	$mail->setText($txt);
	$mail->buildMessage();
	$ok = true;
	foreach($recipients as $recipient) {
		$mailsent = $mail->send(array($recipient));
		if (!$mailsent) {
			$errors[] = tra("Error sending mail to"). " $email";
		}
		$ok = $ok && $mailsent;
	}
	return $ok;
}
