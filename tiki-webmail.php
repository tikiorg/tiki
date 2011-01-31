<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'webmail';
require_once ('tiki-setup.php');
include_once ('lib/webmail/webmaillib.php');
include_once ('lib/webmail/contactlib.php');

$access->check_feature('feature_webmail');
$access->check_permission_either( array('tiki_p_use_webmail', 'tiki_p_use_group_webmail') );

require_once ('lib/webmail/net_pop3.php');
require_once ('lib/mail/mimelib.php');
include_once ('lib/webmail/class.rc4crypt.php');
include_once ('lib/webmail/tikimaillib.php');

function handleWebmailRedirect($inUrl) {		// AJAX_TODO
	global $prefs;
	
//	if ($prefs['ajax_xajax'] != 'y' || empty($_REQUEST['xjxfun'])) {
//		header ('location: tiki-webmail.php?'.$inUrl);
//		die();
//	} else {
//	    global $ajaxlib, $headerlib;
////			$objResponse = new xajaxResponse('UTF-8');					// should be possible server-side, no?
////			$objResponse->Redirect('tiki-webmail.php?'.$urlq);
//	    $headerlib->add_js('window.location.replace("tiki-webmail.php?'.$inUrl.'")');
//	    $ajaxlib->registerTemplate('tiki-webmail.tpl');
////   	    $ajaxlib->registerTemplate('error.tpl');
////	    $ajaxlib->registerFunction('loadComponent');
//		$ajaxlib->processRequests();
//		die();
//	}
	
}

$access->check_user($user);

$auto_query_args = array(
    'msgid',
	'locSection',
	'filter'
);


if (!isset($_REQUEST['locSection'])) {
	$_REQUEST['locSection'] = 'mailbox';
}
$headerlib->add_js('var webmailTimeoutId = null;',0);

$smarty->assign('locSection', $_REQUEST['locSection']);
// Search if we have to add some contacts
if (isset($_REQUEST['add_contacts'])) {
	if (isset($_REQUEST['add'])) {
		check_ticket('webmail');
		foreach (array_keys($_REQUEST['add'])as $i) {
			$contactlib->replace_contact(0, $_REQUEST['addFirstName'][$i], $_REQUEST['addLastName'][$i], $_REQUEST['addemail'][$i],
				$_REQUEST['addNickname'][$i], $user);
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// Read an Email ////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

if ($_REQUEST['locSection'] == 'read') {
	if (isset($_REQUEST['fullheaders'])) {
		$smarty->assign('fullheaders', 'y');
	} else {
		$smarty->assign('fullheaders', 'n');
	}
	$headerlib->add_js('if (webmailTimeoutId) {window.clearTimeout(webmailTimeoutId);}',0);
	
	$current = $webmaillib->get_current_webmail_account($user);
	
	
	$smarty->assign_by_ref('current', $current);

	// connecting with Zend
	try {
		$mail = $webmaillib->get_mail_storage($current);
	} catch (Exception $e) {
		// do something better with the error
		$smarty->assign('conmsg', tra('There was a problem connecting to that account.').'<br />'.$e->getMessage());
	}

	if (isset($_REQUEST['delete_one'])) {
		check_ticket('webmail');
		$aux = $webmail_list[$_REQUEST['msgdel']-1];
		try {
			$mail->removeMessage($_REQUEST['msgdel']);
			$webmaillib->remove_webmail_message($current['accountId'], $user, $aux['realmsgid']);
			unset($_REQUEST['msgid']);
		} catch (Exception $e) {
			$smarty->assign('conmsg', tra('There was a problem deleting that mail.').'<br />'.$e->getMessage());
		}
	}

	if (isset($_REQUEST['msgid'])) {
		$message = $mail->getMessage($_REQUEST['msgid']);
		$aux = $message->getHeaders();
		$realmsgid = preg_replace('/[<>]/','',$aux['message-id']);
		$smarty->assign('msgid', $_REQUEST['msgid']);
		$smarty->assign('realmsgid', $realmsgid);
		$webmaillib->set_mail_flag($current['accountId'], $user, $realmsgid, 'isRead', 'y');
		$mailsum = $mail->countMessages();
		$numshow = $current['msgs'];
	
		if ($_REQUEST['msgid'] == $mailsum) {
			$smarty->assign('next', '');
		} else {
			$smarty->assign('next', $_REQUEST['msgid'] + 1);
		}
	
		if ($_REQUEST['msgid'] > 1) {
			$smarty->assign('prev', $_REQUEST['msgid'] - 1);
		} else {
			$smarty->assign('prev', '');
		}
	
	
		$attachments = array();
		
		if ($message->isMultipart()) {
			// TODO	deal with attachments here??	
		}
		
		$bodies = $webmaillib->get_mail_content($user, $current['accountId'], $_REQUEST['msgid'], true);

		
		for ($i = 0, $count_bodies = count($bodies); $i < $count_bodies; $i++) {
			if ($bodies[$i]['contentType'] == 'text/html') {
				
				$bod = $bodies[$i]['body'];
				
				// Clean the string using HTML Purifier
				require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
				$bod = HTMLPurifier($bod);
				
				if (preg_match_all('/<[\/]?body[^>]*>/i', $bod, $m, PREG_OFFSET_CAPTURE) && count($m) > 0 && count($m[0]) > 1) {
					// gets positions of the start and end body tags then substr the bit inbetween
					$bod = substr($bod, $m[0][0][1] + strlen($m[0][0][0]), $m[0][1][1]);
				}
				$bod = strip_tags( $bod, '<a><b><i><table><tbody><tr><td><th><ul><li><img><hr><ol><br /><h1><h2><h3><h4><h5><h6><div><span><font><form><input><textarea><checkbox><select><style>');
				// try to close malformed html not fixed by the purifier - because people email Really Bad Things and this messes up *lite.css layout
				$bod = closetags($bod);
				$bodies[$i]['body'] = $bod;
			
			} else if ($bodies[$i]['contentType'] == 'text/plain') {
				// reply text
				$smarty->assign('plainbody', format_email_reply($bodies[$i]['body'], $aux['from'], $aux['date']));
				$bodies[$i]['body'] = nl2br( $bodies[$i]['body'] );
			} else {
				// attachments?
			}
		}
		
		array_multisort($bodies);	// this doesn't do what we need properly but seems to fluke it mostly - TODO a manual re-sort
	
		$smarty->assign_by_ref('attachs', $attachments);
		$smarty->assign_by_ref('bodies', $bodies);
	
		try {
			$allbodies = $message->getContent();
		} catch (Exception $e) {
			$allbodies = $e->getMessage();		
		}
	
		$smarty->assign('allbodies', htmlspecialchars($allbodies));
	
		// collect addresses for reply
		$to_addresses = $aux['from'];
	
		// Get email addresses from the 'from' portion
		$to_addresses = explode(',', $to_addresses);
	
		$temp_max = count($to_addresses);
		for ($i = 0; $i < $temp_max; $i++) {
			preg_match('/<([^>]+)>/', $to_addresses[$i], $add);
	
			if (isset($add[1])) {
				$to_addresses[$i] = $add[1];
			}
		}
	
		if (isset($aux['cc']) || preg_match('/,/', $aux['to'])) {
			$cc_addresses = '';
	
			if (isset($aux['cc']))
				$cc_addresses .= $aux['cc'];
	
			//add addresses to cc from 'to' field (for 'reply to all')
			if ($cc_addresses != '')
				$cc_addresses .= ',';
	
			$cc_addresses .= $aux['to'];
			$cc_addresses = explode(',', $cc_addresses);
	
			$temp_max = count($cc_addresses);
			for ($i = 0; $i < $temp_max; $i++) {
				preg_match('/<([^>]+)>/', $cc_addresses[$i], $add);
	
				if (isset($add[1])) {
					$cc_addresses[$i] = $add[1];
				}
			}
		} else {
			$cc_addresses = array();
		}
	
		$to_addresses = join(',', $to_addresses);
		$cc_addresses = join(',', $cc_addresses);
	
		if (isset($aux['reply-to'])) {
			$aux['replyto'] = $aux['reply-to'];
	
			$aux['replycc'] = $cc_addresses;
		} else {
			$aux['replycc'] = $cc_addresses;
	
			$aux['replyto'] = $to_addresses;
		}
		if (!isset($aux['delivery-date'])) {
			$aux['delivery-date'] = $aux['date'];
		}
		$aux['timestamp'] = strtotime($aux['delivery-date']);
		
		//$aux['subject'] = isset($aux['subject']) ? utf8_encode($aux['subject']) : '';
		$aux['subject'] = isset($aux['subject']) ? mb_decode_mimeheader($aux['subject']) : ''; // the commented out line above doesn't work
		$aux['from']    = isset($aux['from'])    ? utf8_encode($aux['from']) : '';
		$aux['to']      = isset($aux['to'])      ? utf8_encode($aux['to']) : '';
		$aux['cc']      = isset($aux['cc'])      ? utf8_encode($aux['cc']) : '';
		$aux['date']    = isset($aux['date'])    ? utf8_encode($aux['date']) : '';
			
		$smarty->assign('headers', $aux);
		
	} else {	// $_REQUEST['msgid'] unset by delete
		handleWebmailRedirect('locSection=mailbox');
	}
}

///////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// Mailbox ////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

if ($_REQUEST['locSection'] == 'mailbox') {
	
	$current = $webmaillib->get_current_webmail_account($user);
	if (!$current) {
		handleWebmailRedirect('locSection=settings');
	}

	$autoRefresh = $current['autoRefresh'];
	$js = <<< END
function submit_form(msgname,flg)
{
  document.mailb.elements.quickFlag.value= flg;
  document.mailb.elements.quickFlagMsg.value= msgname;
  document.mailb.submit();
}
END;

	if ($autorefresh > 0) {
		$js .= 'webmailTimeoutId = window.setTimeout("window.location.reload(true);",$autoRefresh*1000);';
	}
	$headerlib->add_js($js,0);

	$h = opendir('temp/mail_attachs/');

	while ($file = readdir($h)) {
		if (substr($file, 0, strlen($user)) == $user) {
			@unlink ('temp/mail_attachs/' . $file);
		}
	}

	closedir ($h);

	$smarty->assign('current', $current);
	$smarty->assign('autoRefresh',$current['autoRefresh']);
	$smarty->assign('imap',$current['imap']);
	$smarty->assign('mbox',$current['mbox']);
	$smarty->assign('maildir',$current['maildir']);
	$smarty->assign('useSSL',$current['useSSL']);
	$smarty->assign('flagsPublic',$current['flagsPublic']);
	
	$webmail_reload = isset($_REQUEST['refresh_mail']);
	
	try {
		$webmail_list = $webmaillib->refresh_mailbox($user, $current['accountId'], $webmail_reload);
	} catch (Exception $e) {
		$err = $e->getMessage();

		$urlq = http_build_query(array('locSection'=>'settings', 'conmsg'=>$err),'','&');
		handleWebmailRedirect($urlq);
		return;
	}

	// connecting with Zend
	try {
		$mail = $webmaillib->get_mail_storage($current);
	} catch (Exception $e) {
		// do something better with the error
		$smarty->assign('conmsg', tra('There was a problem connecting to that account.').'<br />'.$e->getMessage());
	}

	// The user just clicked on one of the flags, so set up for flag change
	if (isset($_REQUEST['quickFlagMsg'])){
		$realmsg = $_REQUEST['quickFlagMsg'];
		switch ($_REQUEST['quickFlag']) {
		case 'y':
			$webmaillib->set_mail_flag($current['accountId'], $user, $realmsg, 'isFlagged', 'y');

			break;

		case 'n':
			$webmaillib->set_mail_flag($current['accountId'], $user, $realmsg, 'isFlagged', 'n');

			break;
		}
	}

	if (isset($_REQUEST['delete'])) {
		if (isset($_REQUEST['msg'])) {
			check_ticket('webmail');
			// Now we can delete the messages
			$err = '';
			foreach ($_REQUEST['msg'] as $msg) {
				$aux = $webmail_list[$msg-1];
				$realmsgid = $aux['realmsgid'];
				try {
					$mail->removeMessage($msg);
					$webmaillib->remove_webmail_message($current['accountId'], $user, $realmsgid);
					//$pop3->deleteMsg($msg);
				} catch (Exception $e) {
					$err .= $e->getMessage().' ('.tra('Mail ID').' '.$msg.')<br />';
				}
			}
			if (!empty($err)) {
				$smarty->assign('conmsg', tra('There was a problem while trying to delete these mails.').'<br />'.$err);
			}
		}
	}

	if (isset($_REQUEST['delete_one'])) {	// currently unused?
		check_ticket('webmail');
		$aux = $webmail_list[$_REQUEST['msgdel']-1];
		$webmaillib->remove_webmail_message($current['accountId'], $user, $aux['realmsgid']);
		try {
			$mail->removeMessage($_REQUEST['msgdel']);
		} catch (Exception $e) {
			$smarty->assign('conmsg', tra('There was a problem while trying to delete that mail.').'<br />'.$e->getMessage());
		}
	}
	
	
	if (isset($_REQUEST['delete_one']) || isset($_REQUEST['delete'])) {
		// Now reopen the mailbox to renumber messages
		try {
			$webmail_list = $webmaillib->refresh_mailbox($user, $current['accountId'], true);	// really need a smarter way of caching the whole mailbox...
		} catch (Exception $e) {
			$err = $e->getMessage();
	
			$urlq = http_build_query(array('locSection'=>'settings', 'conmsg'=>$err),'','&');
			handleWebmailRedirect($urlq);
			return;
		}

	}
	$mailsum = count($webmail_list);

	if (isset($_REQUEST['operate'])) {
		if (isset($_REQUEST['msg'])) {
			check_ticket('webmail');
			// Now we can operate the messages
			foreach ($_REQUEST['msg'] as $msg) {
				$aux = $webmail_list[$msg-1];
				$realmsg = $aux['realmsgid'];
				
				switch ($_REQUEST['action']) {
					
				case 'flag':
					$webmaillib->set_mail_flag($current['accountId'], $user, $realmsg, 'isFlagged', 'y');
					break;

				case 'unflag':
					$webmaillib->set_mail_flag($current['accountId'], $user, $realmsg, 'isFlagged', 'n');
					break;

				case 'read':
					$webmaillib->set_mail_flag($current['accountId'], $user, $realmsg, 'isRead', 'y');
					break;

				case 'unread':
					$webmaillib->set_mail_flag($current['accountId'], $user, $realmsg, 'isRead', 'n');
					break;
				}
			}
		}
	}

	$numshow = $current['msgs'];

	if (isset($_REQUEST['start']) && $_REQUEST['start'] > $mailsum)
		$_REQUEST['start'] = $mailsum;

	if (!isset($_REQUEST['filter']))
		$smarty->assign('filter', '');
	else
		$smarty->assign('filter', $_REQUEST['filter']);

	// If we have a filter then we have to
	if (!empty($_REQUEST['filter'])) {
		$tot = 0;

		$aux['msgid'] = 'foo';
		$filtered = array();
		$filtered[] = $aux;

		for ($i = 0; $i < $mailsum; $i++) {
			$aux = $webmail_list[$i];
			$webmaillib->replace_webmail_message($current['accountId'], $user, $aux['realmsgid']);
			list($aux['isRead'], $aux['isFlagged'], $aux['isReplied'])
				= $webmaillib->get_mail_flags($current['accountId'], $user, $aux['realmsgid']);

			if ($_REQUEST['filter'] == 'unread' && $aux['isRead'] == 'n') {
				$tot++;
				$filtered[] = $aux;
			} elseif ($_REQUEST['filter'] == 'flagged' && $aux['isFlagged'] == 'y') {
				$tot++;
				$filtered[] = $aux;
			}
		}

		$mailsum = count($filtered) - 1;
	}

	if (!isset($_REQUEST['start']))
		$_REQUEST['start'] = $mailsum;

	$upperlimit = $_REQUEST['start'];
	$smarty->assign('start', $_REQUEST['start']);
	$webmail_list_page = array();

	for ($i = $upperlimit, $icount_wlp = count($webmail_list_page); $i > 0 && $icount_wlp < $numshow; $i--) {
		if (!empty($_REQUEST['filter'])) {
			$aux = $filtered[$i];
		} else {
			$aux = $webmail_list[$i-1];
			$aux['subject'] = mb_decode_mimeheader($aux['subject']); // Lets decode the Subject before going to list it... otherwise it returns garbage for non-ascii subjects
			$webmaillib->replace_webmail_message($current['accountId'], $user, $aux['realmsgid']);
			list($aux['isRead'], $aux['isFlagged'], $aux['isReplied']) = $webmaillib->get_mail_flags($current['accountId'], $user, $aux['realmsgid']);
		}
		$webmail_list_page[] = $aux;
	}
	$lowerlimit = $i;

	if ($lowerlimit < 0)
		$lowerlimit = 0;

	$showstart = $mailsum - $upperlimit + 1;
	$showend = $mailsum - $lowerlimit;
	$smarty->assign('showstart', $showstart);
	$smarty->assign('showend', $showend);
	$smarty->assign('total', $mailsum);

	if ($lowerlimit > 0) {
		$smarty->assign('nextstart', $lowerlimit);
	} else {
		$smarty->assign('nextstart', '');
	}

	if ($upperlimit <> $mailsum) {
		$prevstart = $upperlimit + $numshow;

		if ($prevstart > $mailsum)
			$prevstart = $mailsum;

		$smarty->assign('prevstart', $prevstart);
	} else {
		$smarty->assign('prevstart', '');
	}

	if ($_REQUEST['start'] <> $mailsum) {
		$smarty->assign('first', $mailsum);
	} else {
		$smarty->assign('first', '');
	}

	// Now calculate the last message block
	$last = $mailsum % $numshow;

	if ($_REQUEST['start'] <> $last) {
		$smarty->assign('last', $last);
	} else {
		$smarty->assign('last', '');
	}

	$smarty->assign('list', $webmail_list_page);
}

///////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// Settings //////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

if ($_REQUEST['locSection'] == 'settings') {

	if ($prefs['feature_jquery']) {
		$deleteTitle = tra('Delete');
		$deleteConfirm = tra('Are you sure you want to delete this account?');
		$js = <<< END

// validate edit/add form
\$('[name=settings]').submit(function() {
	if (!\$('[name=account]').val()) {
		\$('[name=account]').css('background-color', '#fcc').focus();
		return false;
	}
	if (!\$('[name=imap]').val() && !\$('[name=pop]').val() && !\$('[name=mbox]').val() && !\$('[name=maildir]').val()) {
		\$('[name=imap]').css('background-color', '#fcc').focus();
		\$('[name=pop]').css('background-color', '#fcc');
		\$('[name=mbox]').css('background-color', '#fcc');
		\$('[name=maildir]').css('background-color', '#fcc');
		return false;
	}
});
// set port for imap
\$('[name=imap]').change(function() {
	if (\$('[name=imap]').val()) {
		\$('[name=port]').val(\$('[name=useSSL]').attr('checked')? '993' : '143');
	}
});
// set port for pop
\$('[name=pop]').change(function() {
	if (\$('[name=pop]').val() && !\$('[name=imap]').val()) {
		\$('[name=port]').val(\$('[name=useSSL]').attr('checked')? '995' : '110');
	}
});
// set ports for ssl
\$('[name=useSSL]').change(function(v,a) {
	if (\$('[name=useSSL]').attr('checked')) {
		\$('[name=port]').val(\$('[name=imap]').val() ? '993' : '995');
		\$('[name=smtpPort]').val('465');
	} else {
		\$('[name=port]').val(\$('[name=imap]').val() ? '143' : '110');
		\$('[name=smtpPort]').val('25');
	}
});
// confirm deletes
\$('a[title=$deleteTitle]').click(function() {
	return confirm('$deleteConfirm');
});

END;
		$headerlib->add_jq_onready($js);
	}
	$headerlib->add_js('if (webmailTimeoutId) {window.clearTimeout(webmailTimeoutId);}',0);
	
	if (isset($_REQUEST['conmsg'])) {
		check_ticket('webmail');
 		$smarty->assign('conmsg', $_REQUEST['conmsg']);
	}
	
	if (isset($_REQUEST['cancel_acc'])) {
		check_ticket('webmail');
	 	unset($_REQUEST['cancel_acc']);
		unset($_REQUEST['accountId']);
	}

	// The New/Update button was pressed
	if (isset($_REQUEST['new_acc'])) {
		check_ticket('webmail');
		
		if (empty($_REQUEST['accountId'])) {
			// Add new account
			$_REQUEST['accountId'] = $webmaillib->new_webmail_account($user,
					$_REQUEST['account'], $_REQUEST['pop'], $_REQUEST['port'], $_REQUEST['username'],
					$_REQUEST['pass'], $_REQUEST['msgs'], $_REQUEST['smtp'], $_REQUEST['useAuth'],
					$_REQUEST['smtpPort'], $_REQUEST['flagsPublic'], $_REQUEST['autoRefresh'],
					$_REQUEST['imap'], $_REQUEST['mbox'], $_REQUEST['maildir'], isset($_REQUEST['useSSL']) ? $_REQUEST['useSSL'] : 'n', $_REQUEST['fromEmail']);

			if ($webmaillib->count_webmail_accounts($user) == 1) {	// first account?
				$webmaillib->current_webmail_account($user, $_REQUEST['accountId']);
			}
			
		} else {
			// Update existing account
			$webmaillib->replace_webmail_account($_REQUEST['accountId'], $user,
					$_REQUEST['account'], $_REQUEST['pop'], $_REQUEST['port'], $_REQUEST['username'],
					$_REQUEST['pass'], $_REQUEST['msgs'], $_REQUEST['smtp'], $_REQUEST['useAuth'],
					$_REQUEST['smtpPort'], $_REQUEST['flagsPublic'], $_REQUEST['autoRefresh'],
					$_REQUEST['imap'], $_REQUEST['mbox'], $_REQUEST['maildir'], isset($_REQUEST['useSSL']) ? $_REQUEST['useSSL'] : 'n', $_REQUEST['fromEmail']);
		}
		unset($_REQUEST['accountId']);
	}
	
//	if (empty($_REQUEST['accountId']) || isset($_REQUEST['new_acc']) && $webmaillib->count_webmail_accounts($user) > 0) {
//		$headerlib->add_jq_onready('$("#settingsFormDiv").hide();');
//	}
	// The red cross was pressed
	if (isset($_REQUEST['remove'])) {
		check_ticket('webmail');
		$webmaillib->remove_webmail_account($user, $_REQUEST['remove']);
	}

	if (isset($_REQUEST['current'])) {
		$webmaillib->current_webmail_account($user, $_REQUEST['current']);
		$headerlib->add_js('if (typeof doRefreshWebmail == "function") { doRefreshWebmail(); }');
	}

	$smarty->assign('mailCurrentAccount', $tikilib->get_user_preference($user, 'mailCurrentAccount', 0));

	$smarty->assign('accountId', empty($_REQUEST['accountId']) ? 0 : $_REQUEST['accountId']);
	$smarty->assign('userEmail', trim($userlib->get_user_email($user)));

	if (!empty($_REQUEST['accountId'])) {
		$info = $webmaillib->get_webmail_account($user, $_REQUEST['accountId']);
		$cookietab = 2;
	} else {
		$info['account'] = '';
		$info['username'] = '';
		$info['pass'] = '';
		$info['pop'] = '';
		$info['smtp'] = '';
		$info['useAuth'] = 'n';
		$info['port'] = 110;
		$info['smtpPort'] = 25;
		$info['msgs'] = 20;
		$info['flagsPublic'] = 'n';
		$info['autoRefresh'] = 0;
		$info['imap'] = '';
		$info['mbox'] = '';
		$info['maildir'] = '';
		$info['useSSL'] = 'n';
		$info['fromEmail'] = '';
	}

	$smarty->assign('info', $info);
	// List
	$accounts = $webmaillib->list_webmail_accounts($user, 0, -1, 'account_asc', '');
	$smarty->assign('accounts', $accounts['data']);
	
	$pubAccounts = $webmaillib->list_webmail_group_accounts($user, 0, -1, 'account_asc', '');
	$smarty->assign('pubAccounts', $pubAccounts['data']);
}

///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////// Compose /////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

if ($_REQUEST['locSection'] == 'compose') {
	$current = $webmaillib->get_current_webmail_account($user);

	if (!$current) {
		handleWebmailRedirect('locSection=settings');
	}
	$headerlib->add_js('if (webmailTimeoutId) {window.clearTimeout(webmailTimeoutId);}',0);
	
	// Send a message
	if (isset($_REQUEST['reply']) || isset($_REQUEST['replyall'])) {
		check_ticket('webmail');
		$webmaillib->set_mail_flag($current['accountId'], $user, $_REQUEST['realmsgid'], 'isReplied', 'y');
	}

	$smarty->assign('sent', 'n');
	$smarty->assign('attaching', 'n');

	if (isset($_REQUEST['send'])) {
		$email = empty($current['fromEmail']) ? $userlib->get_user_email($user) : $current['fromEmail'];
		$mail = new TikiMail($user, $email);

		if (!empty($_REQUEST['cc'])) {
			$mail->setCc($_REQUEST['cc']);
		}
		if (!empty($_REQUEST['bcc'])) {
			$mail->setBcc($_REQUEST['bcc']);
		}
		$mail->setSubject($_REQUEST['subject']);

		if ($_REQUEST['attach1']) {
			check_ticket('webmail');
			$a1 = $mail->getFile('temp/mail_attachs/' . $_REQUEST['attach1file']);

			$mail->addAttachment($a1, $_REQUEST['attach1'], $_REQUEST['attach1type']);
			@unlink ('temp/mail_attachs/' . $_REQUEST['attach1file']);
		}

		if ($_REQUEST['attach2']) {
			check_ticket('webmail');
			$a2 = $mail->getFile('temp/mail_attachs/' . $_REQUEST['attach2file']);

			$mail->addAttachment($a2, $_REQUEST['attach2'], $_REQUEST['attach2type']);
			@unlink ('temp/mail_attachs/' . $_REQUEST['attach2file']);
		}

		if ($_REQUEST['attach3']) {
			check_ticket('webmail');
			$a3 = $mail->getFile('temp/mail_attachs/' . $_REQUEST['attach3file']);

			$mail->addAttachment($a3, $_REQUEST['attach3'], $_REQUEST['attach3type']);
			@unlink ('temp/mail_attachs/' . $_REQUEST['attach3file']);
		}

		$mail->setSMTPParams($current['smtp'], $current['smtpPort'], '', $current['useAuth'], $current['username'], $current['pass']);

		if (isset($_REQUEST['useHTML']) && $_REQUEST['useHTML'] == 'on') {
			$mail->setHTML($_REQUEST['body'], strip_tags($_REQUEST['body']));
		} else {
			$mail->setText($_REQUEST['body']);
		}

		$to_array_1 = preg_split('/[, ;]/', $_REQUEST['to']);
		$to_array = array();

		foreach ($to_array_1 as $to_1) {
			if (!empty($to_1)) {
				$to_array[] = $to_1;
			}
		}

		$to_array = $contactlib->parse_nicknames($to_array);

		// Get email addresses not in the address book
		$not_contacts = $contactlib->are_contacts($to_array, $user);

		if (count($not_contacts) > 0) {
			$smarty->assign('notcon', 'y');
		} else {
			$smarty->assign('notcon', 'n');
		}

		$smarty->assign('not_contacts', $not_contacts);

		if ($mail->send($to_array,'smtp')) {
			$msg=tra('Your email was sent');
		} else {
			if (is_array($mail->errors)) {
				$msg = '';
				$temp_max = count($mail->errors);
				for ($i = 0; $i < $temp_max; $i ++) {
					$msg .= $mail->errors[$i].'<br />';
				}
			} else {
				$msg=$mail->errors;
			}
		}

		$smarty->assign('sent', 'y');
		$smarty->assign('msg', $msg);
	}

	if (isset($_REQUEST['attach'])) {
		$smarty->assign('attaching', 'y');
	}

	if (isset($_REQUEST['remove_attach1'])) {
		check_ticket('webmail');
		@unlink ($_REQUEST['attach1file']);

		$_REQUEST['attach1'] = '';
		$_REQUEST['attach1file'] = '';
		$_REQUEST['attach1type'] = '';
	}

	if (isset($_REQUEST['remove_attach2'])) {
		check_ticket('webmail');
		@unlink ($_REQUEST['attach2file']);

		$_REQUEST['attach2'] = '';
		$_REQUEST['attach2file'] = '';
		$_REQUEST['attach2type'] = '';
	}

	if (isset($_REQUEST['remove_attach3'])) {
		check_ticket('webmail');
		@unlink ($_REQUEST['attach3file']);

		$_REQUEST['attach3'] = '';
		$_REQUEST['attach3file'] = '';
		$_REQUEST['attach3type'] = '';
	}

	if (isset($_REQUEST['attached'])) {
		// Now process the uploads
		if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			check_ticket('webmail');
			$size = $_FILES['userfile1']['size'];

			if ($size < 1500000) {
				$name = $_FILES['userfile1']['name'];

				$type = $_FILES['userfile1']['type'];
				$_REQUEST['attach1file'] = $user . md5($webmaillib->genPass());
				$_REQUEST['attach1type'] = $type;
				$_REQUEST['attach1'] = $name;
				move_uploaded_file($_FILES['userfile1']['tmp_name'], 'temp/mail_attachs/' . $_REQUEST['attach1file']);
			}
		}

		if (isset($_FILES['userfile2']) && is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
			check_ticket('webmail');
			$size = $_FILES['userfile2']['size'];

			if ($size < 1500000) {
				$name = $_FILES['userfile2']['name'];

				$type = $_FILES['userfile2']['type'];
				$_REQUEST['attach2file'] = $user . md5($webmaillib->genPass());
				$_REQUEST['attach2type'] = $type;
				$_REQUEST['attach2'] = $name;
				move_uploaded_file($_FILES['userfile2']['tmp_name'], 'temp/mail_attachs/' . $_REQUEST['attach2file']);
			}
		}

		if (isset($_FILES['userfile3']) && is_uploaded_file($_FILES['userfile3']['tmp_name'])) {
			check_ticket('webmail');
			$size = $_FILES['userfile3']['size'];

			if ($size < 1500000) {
				$name = $_FILES['userfile3']['name'];

				$type = $_FILES['userfile3']['type'];
				$_REQUEST['attach3file'] = $user . md5($webmaillib->genPass());
				$_REQUEST['attach3type'] = $type;
				$_REQUEST['attach3'] = $name;
				move_uploaded_file($_FILES['userfile3']['tmp_name'], 'temp/mail_attachs/' . $_REQUEST['attach3file']);
			}
		}
	}

	// Build the to array
	if (!isset($_REQUEST['attach1']))
		$_REQUEST['attach1'] = '';

	if (!isset($_REQUEST['attach2']))
		$_REQUEST['attach2'] = '';

	if (!isset($_REQUEST['attach3']))
		$_REQUEST['attach3'] = '';

	if (!isset($_REQUEST['attach1file']))
		$_REQUEST['attach1file'] = '';

	if (!isset($_REQUEST['attach2file']))
		$_REQUEST['attach2file'] = '';

	if (!isset($_REQUEST['attach3file']))
		$_REQUEST['attach3file'] = '';

	if (!isset($_REQUEST['attach1type']))
		$_REQUEST['attach1type'] = '';

	if (!isset($_REQUEST['attach2type']))
		$_REQUEST['attach2type'] = '';

	if (!isset($_REQUEST['attach3type']))
		$_REQUEST['attach3type'] = '';

	if (!isset($_REQUEST['to']))
		$_REQUEST['to'] = '';

	if (!isset($_REQUEST['cc']))
		$_REQUEST['cc'] = '';

	if (!isset($_REQUEST['bcc']))
		$_REQUEST['bcc'] = '';

	if (!isset($_REQUEST['body']))
		$_REQUEST['body'] = '';

	if (!isset($_REQUEST['subject']))
		$_REQUEST['subject'] = '';

	if (!isset($_REQUEST['useHTML']))
		$_REQUEST['useHTML'] = 'n';

	$smarty->assign('cc', $_REQUEST['cc']);
	$smarty->assign('to', $_REQUEST['to']);
	$smarty->assign('bcc', $_REQUEST['bcc']);
	$smarty->assign('body', $_REQUEST['body']);
	$smarty->assign('useHTML', $_REQUEST['useHTML']);
	$smarty->assign('subject', $_REQUEST['subject']);
	$smarty->assign('attach1', $_REQUEST['attach1']);
	$smarty->assign('attach2', $_REQUEST['attach2']);
	$smarty->assign('attach3', $_REQUEST['attach3']);
	$smarty->assign('attach1file', $_REQUEST['attach1file']);
	$smarty->assign('attach2file', $_REQUEST['attach2file']);
	$smarty->assign('attach3file', $_REQUEST['attach3file']);
	$smarty->assign('attach1type', $_REQUEST['attach1type']);
	$smarty->assign('attach2type', $_REQUEST['attach2type']);
	$smarty->assign('attach3type', $_REQUEST['attach3type']);
}

include_once ('tiki-mytiki_shared.php');

include_once ('tiki-section_options.php');

ask_ticket('webmail');
$smarty->assign('mid', 'tiki-webmail.tpl');
$smarty->display('tiki.tpl');
