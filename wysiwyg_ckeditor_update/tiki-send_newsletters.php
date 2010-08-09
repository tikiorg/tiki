<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'newsletters';
require_once ('tiki-setup.php');
@ini_set('max_execution_time', 0); //will not work if safe_mode is on
$prefs['feature_wiki_protect_email'] = 'n'; //not to alter the email
include_once ('lib/newsletters/nllib.php');
$auto_query_args = array('sort_mode', 'offset', 'find', 'nlId', 'cookietab');

$access->check_feature('feature_newsletters');
$access->check_permission('tiki_p_send_newsletters');

if (!isset($_REQUEST["nlId"])) {
	$_REQUEST["nlId"] = 0;
}
$smarty->assign('nlId', $_REQUEST["nlId"]);
$newsletters = $nllib->list_newsletters(0, -1, 'created_desc', '', '', array("tiki_p_admin_newsletters", "tiki_p_send_newsletters"), 'n');
if (!$newsletters['cant']) {
	$smarty->assign('msg', tra("No newsletters available."));
	$smarty->display("error.tpl");
	die;
}

if ( empty($_REQUEST["sendingUniqId"]) ) {
	$sendingUniqId = $tikilib->genRandomString();
	$smarty->assign('sendingUniqId', $sendingUniqId);
}

if(!isset($_REQUEST['cookietab'])) {
	$_REQUEST['cookietab'] = 1;
}
$smarty->assign('newsletters', $newsletters["data"]);
if ($_REQUEST["nlId"]) {
	$nl_info = $nllib->get_newsletter($_REQUEST["nlId"]);
	if (!isset($_REQUEST["editionId"])) $_REQUEST["editionId"] = 0;
	$smarty->assign('allowTxt', $nl_info['allowTxt']);
	$smarty->assign('allowArticleClip', $nl_info['allowArticleClip']);
	if ($_REQUEST["editionId"]) {
		$info = $nllib->get_edition($_REQUEST["editionId"]);
	} else {
		$info = array();
		$info["data"] = '';
		$info["datatxt"] = '';
		$info["subject"] = '';
		$info["editionId"] = 0;
		$info["files"] = array();
		$info['wysiwyg'] = 'n';
	}
	$smarty->assign_by_ref('info', $info);
} else {
	//No newsletter selected -> Check if the textarea for the first has to be displayed
	$smarty->assign('allowTxt', $newsletters['data'][0]['allowTxt']);
	$smarty->assign('allowArticleClip', $newsletters['data'][0]['allowTxt']);
}
// Display to newsletter txtarea or not depending on the preferences
$showBoxCheck = "
	<script type='text/javascript'>
	<!--
	function checkNewsletterTxtArea(nlIndex){
	browser();
	var allowTxt = new Array();
	var allowArticleClip = new Array();
	";
for ($i = 0, $tmp_count = count($newsletters['data']); $i < $tmp_count; $i++) {
$showBoxCheck .= "allowTxt[$i] = '" . $newsletters['data'][$i]['allowTxt'] . "';
	allowArticleClip[$i] = '" . $newsletters['data'][$i]['allowArticleClip'] . "';
	";
}
// allowTxt
$showBoxCheck .= "	if (document.getElementById('txtcol1').style.display=='none' && allowTxt[nlIndex] == 'y'){";
if (preg_match("/gecko/i", $_SERVER['HTTP_USER_AGENT'])) {
	$showBoxCheck.= "document.getElementById('txtcol1').style.display='table-cell';";
	$showBoxCheck.= "document.getElementById('txtcol2').style.display='table-cell';";
} else {
	$showBoxCheck.= "document.getElementById('txtcol1').style.display='inline';	";
	$showBoxCheck.= "document.getElementById('txtcol2').style.display='inline';";
};
$showBoxCheck.= "
    	}else if (allowTxt[nlIndex] == 'n') {
	document.getElementById('txtcol1').style.display='none';
	document.getElementById('txtcol2').style.display='none';
    	}";
// allowArticleClip
$showBoxCheck .= "	if (document.getElementById('clipcol1').style.display=='none' && allowArticleClip[nlIndex] == 'y'){";
if (preg_match("/gecko/i", $_SERVER['HTTP_USER_AGENT'])) {
	$showBoxCheck.= "document.getElementById('clipcol1').style.display='table-cell';";
	$showBoxCheck.= "document.getElementById('clipcol2').style.display='table-cell';";
} else {
	$showBoxCheck.= "document.getElementById('clipcol1').style.display='inline';	";
	$showBoxCheck.= "document.getElementById('clipcol2').style.display='inline';";
};
$showBoxCheck.= "
    	}else if (allowArticleClip[nlIndex] == 'n') {
	document.getElementById('clipcol1').style.display='none';
	document.getElementById('clipcol2').style.display='none';
    	}";
// end of function
$showBoxCheck .= "
	}
	-->
	</script>
	";
$smarty->assign('showBoxCheck', $showBoxCheck);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$nllib->remove_edition($_REQUEST["nlId"], $_REQUEST["remove"]);
}

// wysiwyg decision
include_once ('lib/wiki/editlib.php');

// Handles switching editor modes
if (isset($_REQUEST['mode_normal']) && $_REQUEST['mode_normal']=='y') {
	if ($_REQUEST['wikiparse'] == 'on') {
		// Parsing page data as first time seeing html page in normal editor
		$smarty->assign('msg', "Parsing html to wiki");
		$info["data"] = $editlib->parseToWiki($_REQUEST["data"]);
	} else {
		$info["data"] = $_REQUEST["data"];
	}
	
} elseif (isset($_REQUEST['mode_wysiwyg']) && $_REQUEST['mode_wysiwyg']=='y') {
	// Parsing page data as first time seeing wiki page in wysiwyg editor
	$smarty->assign('msg', "Parsing wiki to html");
	$info["data"] = $editlib->parseToWysiwyg($_REQUEST["data"]);
}

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0 && (!isset($_REQUEST['previousTemplateId']) || $_REQUEST['previousTemplateId'] != $_REQUEST['templateId'])) {
	global $templateslib; require_once 'lib/templates/templateslib.php';
	$template_data = $templateslib->get_template($_REQUEST["templateId"]);
	$_REQUEST["data"] = $template_data["content"];
	if (isset($_SESSION['wysiwyg']) && $_SESSION['wysiwyg'] == 'y') {
		$_REQUEST['data'] = $tikilib->parse_data($_REQUEST['data'], array('is_html'=>true, 'absolute_links' => true, 'suppress_icons' => true));
	}
	$_REQUEST["preview"] = 1;
	$smarty->assign("templateId", $_REQUEST["templateId"]);
}
$newsletterfiles_post = isset($_REQUEST['newsletterfile']) && is_array($_REQUEST['newsletterfile']) ? $_REQUEST['newsletterfile'] : array();
$newsletterfiles = array();
foreach($newsletterfiles_post as $k => $id) {
	$f = array();
	if ((strlen($id) == 32) && preg_match('/^[0-9a-f]{32}$/', $id)) { // this is a valid md5 hash, so the file was just saved at preview time
		$fpath = $prefs['tmpDir'] . '/newsletterfile-preview-' . $id;
		$f = unserialize(file_get_contents($fpath . '.infos'));
		$f['path'] = $fpath;
		$newsletterfiles[] = $f;
	} else if ((int)$_REQUEST['nlId'] > 0) {
		foreach($info['files'] as $f) {
			if ($f['id'] == (int)$id) {
				$newsletterfiles[] = $f;
				break;
			}
		}
	}
}
if (!empty($_FILES) && !empty($_FILES['newsletterfile'])) {
	foreach($_FILES['newsletterfile']['name'] as $i => $v) {
		if ($_FILES['newsletterfile']['error'][$i] == UPLOAD_ERR_OK) {
			$newsletterfiles[] = array('name' => $_FILES['newsletterfile']['name'][$i], 'type' => $_FILES['newsletterfile']['type'][$i], 'path' => $_FILES['newsletterfile']['tmp_name'][$i], 'error' => $_FILES['newsletterfile']['error'][$i], 'size' => $_FILES['newsletterfile']['size'][$i], 'savestate' => 'phptmp');
		} else {
			$smarty->assign('upload_err_msg', tra('A problem occured during file uploading') . '<br />' . tra('File which was causing trouble was at rank') . '&nbsp;' . ($i + 1) . '<br />' . tra('The error was:') . '&nbsp;<strong>' . $tikilib->uploaded_file_error($_FILES['newsletterfile']['error'][$i]) . '</strong>');
		}
	}
}
$info['files'] = $newsletterfiles;
foreach($info['files'] as $k => $newsletterfile) {
	if ($newsletterfile['savestate'] == 'phptmp') {
		// move it to temp
		$tmpfnamekey = md5(rand() . time() . $newsletterfile['path'] . $newsletterfile['name'] . $newsletterfile['type']);
		$tmpfname = $prefs['tmpDir'] . '/newsletterfile-preview-' . $tmpfnamekey;
		if (move_uploaded_file($newsletterfile['path'], $tmpfname)) {
			$info['files'][$k]['savestate'] = 'tikitemp';
			$info['files'][$k]['path'] = $tmpfname;
			$info['files'][$k]['id'] = $tmpfnamekey;
			$info['files'][$k]['filename'] = $tmpfnamekey;
			file_put_contents($tmpfname . '.infos', serialize($info['files'][$k]));
		}
	}
}
$smarty->assign('preview', 'n');
if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');
	if (isset($_REQUEST["subject"])) {
		$info["subject"] = $_REQUEST["subject"];
	} else {
		$info["subject"] = '';
	}
	if (isset($_REQUEST["data"])) {
		$info["data"] = $_REQUEST["data"];
	} else {
		$info["data"] = '';
	}
	if (isset($_REQUEST['wikiparse']) && $_REQUEST['wikiparse'] == 'on') $info['wikiparse'] = 'y';
	else $info['wikiparse'] = 'n';
	if (!empty($_REQUEST["datatxt"])) {
		$info["datatxt"] = $_REQUEST["datatxt"];
		//For the hidden input
		$smarty->assign('datatxt', $_REQUEST["datatxt"]);
	} else {
		$info["datatxt"] = '';
	}
	if (!empty($_REQUEST["usedTpl"])) {
		$smarty->assign('dataparsed', (($info['wikiparse'] == 'y') ? $tikilib->parse_data($info["data"], array('absolute_links' => true, 'suppress_icons' => true)) : $info['data']));
		$smarty->assign('subject', $info["subject"]);
		$info["dataparsed"] = $smarty->fetch("newsletters/" . $_REQUEST["usedTpl"]);
		if (stristr($info['dataparsed'], "<body") === false) {
			$info['dataparsed'] = "<html><body>" . $info['dataparsed'] . "</body></html>";
		}
		$smarty->assign("usedTpl", $_REQUEST["usedTpl"]);
	} else {
		$info["dataparsed"] = "<html><body>" . (($info['wikiparse'] == 'y') ? $tikilib->parse_data($info["data"], array('absolute_links' => true, 'suppress_icons' => true)) : $info['data']) . "</body></html>";
	}
	if (!empty($_REQUEST['replyto'])) {
		$smarty->assign('replyto', $_REQUEST['replyto']);
	}
	$previewdata = $info['dataparsed'];
	if ($nl_info["allowArticleClip"] == 'y' && $nl_info["autoArticleClip"] == 'y') {
		$articleClip = $nllib->clip_articles($_REQUEST["nlId"]);
		$txtArticleClip = generateTxtVersion($articleClip);
		$info['datatxt'] = str_replace("~~~articleclip~~~", $txtArticleClip, $info['datatxt']);
		$previewdata = str_replace("~~~articleclip~~~", $articleClip, $previewdata);
	}
	$smarty->assign('info', $info);
	$smarty->assign('previewdata', $previewdata);
}
$smarty->assign('presend', 'n');
if (isset($_REQUEST["save"])) {
	check_ticket('send-newsletter');
	// Now send the newsletter to all the email addresses and save it in sent_newsletters
	$smarty->assign('presend', 'y');
	$subscribers = $nllib->get_all_subscribers($_REQUEST["nlId"], "");
	$smarty->assign('nlId', $_REQUEST["nlId"]);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('datatxt', $_REQUEST["datatxt"]);
	$parsed = '';
	if (isset($_REQUEST['wikiparse']) && $_REQUEST['wikiparse'] == 'on') {
		$wikiparse = 'y';
	} elseif ($_SESSION['wysiwyg'] == 'y' && $prefs['wysiwyg_wiki_parsed'] == 'y') {
		$wikiparse = 'y';
	} else {
		$wikiparse = 'n';
	}
	if (!empty($_REQUEST["usedTpl"])) {
		$smarty->assign('dataparsed', (($wikiparse == 'y') ? $tikilib->parse_data($_REQUEST["data"], array('absolute_links' => true, 'suppress_icons' => true)) : $_REQUEST['data']));
		$smarty->assign('subject', $_REQUEST["subject"]);
		$parsed = $smarty->fetch("newsletters/" . $_REQUEST["usedTpl"]);
	} else {
		$parsed = ($wikiparse == 'y') ? $tikilib->parse_data($_REQUEST["data"], array('absolute_links' => true, 'suppress_icons' => true)) : $_REQUEST['data'];
	}
	if (empty($parsed) && !empty($_REQUEST['datatxt'])) {
		$parsed = $_REQUEST['datatxt'];
	}
	if (stristr($parsed, "<body") === false) {
		$parsed = "<html><body>$parsed</body></html>";
	}
	$previewdata = $parsed;
	if ($nl_info["allowArticleClip"] == 'y' && $nl_info["autoArticleClip"] == 'y') {
		$articleClip = $nllib->clip_articles($_REQUEST["nlId"]);
		$txtArticleClip = generateTxtVersion($articleClip);
		$info['datatxt'] = str_replace("~~~articleclip~~~", $txtArticleClip, $info['datatxt']);
		$previewdata = str_replace("~~~articleclip~~~", $articleClip, $previewdata);
	}
	$smarty->assign('previewdata', $previewdata);
	$smarty->assign('dataparsed', $parsed);
	$smarty->assign('subject', $_REQUEST["subject"]);
	$cant = count($subscribers);
	$smarty->assign('subscribers', $cant);
	$smarty->assign_by_ref('subscribers_list', $subscribers);
	$smarty->assign('info', $info);
	if (!empty($_REQUEST['replyto'])) {
		$smarty->assign('replyto', $_REQUEST['replyto']);
	}
}
$smarty->assign('emited', 'n');
if (!empty($_REQUEST['datatxt'])) { $txt = $_REQUEST['datatxt']; }
if (empty($txt) && !empty($_REQUEST["data"])) {
	//No txt message is explicitely provided -> Create one with the html Version & remove Wiki tags
	$txt = $_REQUEST["data"];
	$txt = generateTxtVersion($txt);
	$info["datatxt"] = $txt;
	$smarty->assign('datatxt', $txt);
	if ($nl_info["allowArticleClip"] == 'y' && $nl_info["autoArticleClip"] == 'y') {
		if (!isset($txtArticleClip)) {
			$articleClip = $nllib->clip_articles($_REQUEST["nlId"]);
			$txtArticleClip = generateTxtVersion($articleClip);
		}
		$info['datatxt'] = str_replace("~~~articleclip~~~", $txtArticleClip, $info['datatxt']);
	}
}

if ( isset($_REQUEST["send"]) && ! empty($_REQUEST["sendingUniqId"]) ) {
	include_once ('lib/webmail/tikimaillib.php');
	$editionId = $_REQUEST['editionId'];
	check_ticket('send-newsletter');
	@set_time_limit(0);

	if ( ! is_array($_SESSION["sendingUniqIds"]) )
		$_SESSION["sendingUniqIds"] = array();

	if ( isset( $_SESSION["sendingUniqIds"][ $_REQUEST["sendingUniqId"] ] ) ) {
		// Avoid sending the same newsletter again if the user reload the page
		print tra('Error: You can\'t send the same newsletter by refreshing this frame content.');
		die;
	} else {
		$_SESSION["sendingUniqIds"][ $_REQUEST["sendingUniqId"] ] = 1;
	}

	$mail = new TikiMail();
	if (stristr($_REQUEST["dataparsed"], "<body") === false) {
		$html = "<html><body><div id='tiki-center' class='clearfix content'><div class='wikitext'>" . $tikilib->parse_data($_REQUEST["dataparsed"], array('absolute_links' => true, 'suppress_icons' => true)) . "</div></div></body></html>";
	} else {
		$html = str_ireplace('<body>','<body><div id="tiki-center" class="clearfix content"><div class="wikitext">',$_REQUEST["dataparsed"]);
		$html = str_ireplace('</body>','</div></div></body>', $html);
	}
	if ($nl_info["allowArticleClip"] == 'y' && $nl_info["autoArticleClip"] == 'y') {
		$articleClip = $nllib->clip_articles($_REQUEST["nlId"]);
		$txtArticleClip = generateTxtVersion($articleClip);
		$info['datatxt'] = str_replace("~~~articleclip~~~", $txtArticleClip, $info['datatxt']);
		$html = str_replace("~~~articleclip~~~", $articleClip, $html);
	}
	if (stristr($html, '<base') === false) {
		if (stristr($html, '<header') === false) {
			$html = str_ireplace('<html>', "<html><header><base href=\"$base_url\" /><style type=\"text/css\">" . $headerlib->get_all_css_content() . "</style></header>", $html);
		} else {
			$html = str_ireplace('<header>', "<header><base href=\"$base_url\" />", $html);
		}
	}
	$sent = array();
	$unsubmsg = '';
	$errors = array();
	$logFileName = $prefs['tmpDir'] . '/public/newsletter-log-' . $_REQUEST["sendingUniqId"] . '.txt';
	$logFileHandle = @fopen( $logFileName, 'a' );

	if (isset($_REQUEST['errorEditionId'])) {
		$users = $nllib->get_edition_errors($_REQUEST['errorEditionId']);
	} else {
		$users = $nllib->get_all_subscribers($_REQUEST["nlId"], $nl_info["unsubMsg"] == 'y');
	}

	$nllib->memo_subscribers_edition($editionId, $users);
	$sender_email = $prefs['sender_email'];
	if (!isset($info['files']) || $info['files'] === null || count($info['files']) <= 0) $info['files'] = $nllib->get_edition_files($editionId);
	foreach($info['files'] as $f) {
		$fpath = isset($f['path']) ? $f['path'] : $prefs['tmpDir'] . '/newsletterfile-' . $f['filename'];
		$mail->addAttachment(file_get_contents($fpath), $f['name'], $f['type']);
	}

	$smarty->assign('sectionClass', empty( $section ) ? '' : "tiki_$section " );
	echo $smarty->fetch('send_newsletter_header.tpl');

	@ini_set('zlib.output_compression', 0);
	foreach ($users as $us) {
		$userEmail = $us["login"];
		$email = $us["email"];
		if (!preg_match('/([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+/', trim($email))) {
			$errors[] = array("user" => $userEmail, "email" => $email, "msg" => tra("invalid email"));
			continue;
		}
		if ($userEmail == "") {
			$userEmail = $userlib->get_user_by_email($email);
		}
		$email = trim($email);
		if (in_array($email, $sent)) continue; // do not send the mail again
		if ($userEmail) {
			$mail->setUser($userEmail);
		} else {
			$userEmail = '';
		}
		if (!empty($_REQUEST['replyto'])) {
			$mail->setHeader("Reply-To", $_REQUEST['replyto']);
		}
		$mail->setSubject($_REQUEST["subject"]); // htmlMimeMail memorised the encoded subject
		$languageEmail = !$userEmail ? $prefs['site_language'] : $tikilib->get_user_preference($userEmail, "language", $prefs['site_language']);
		if ($nl_info["unsubMsg"] == 'y' && !empty($us["code"])) {
			$unsubmsg = $nllib->get_unsub_msg($_REQUEST["nlId"], $email, $languageEmail, $us["code"], $userEmail);
			if (stristr($html, "</body>") === false) {
				$msg = $html . $unsubmsg;
			} else {
				$msg = str_replace("</body>", nl2br($unsubmsg) . "</body>", $html);
			}
		} else {
			$msg = $html;
		}
		$mail->setHtml($msg, $txt . strip_tags($unsubmsg));
		$mail->buildMessage(array('text_encoding' => '8bit'));

		if (@ob_get_level() == 0)
			@ob_start();

		// Browsers needs a certain amount of data, for each flush, to display something
		print str_repeat(' ', 4096) . "\n";

		print tra("Sending to") . " '<b>$email</b>': <font color=";

		if ( $mail->send(array($email)) ) {
			$sent[] = $email;
			print "'green'>" . tra('OK');
			$nllib->delete_edition_subscriber($editionId, $us);
			$logStatus = 'OK';
		} else {
			print "'red'>" . tra('Error') . " - {$mail->errors}";
			$errors[] = array("user" => $userEmail, "email" => $email, "msg" => $mail->errors);
			$nllib->mark_edition_subscriber($editionId, $us);
			$logStatus = 'Error';
		}

		if ( $logFileHandle )
			@fwrite( $logFileHandle, "$email : $logStatus\n" );

		print "</font><br />\n";

		// Flush output to force the browser to display email addresses as soon as emails are sent
		// This should avoid CGI and/or proxy and/or browser timeouts when sending to a lot of emails
		//
		@ob_flush();
		@flush();
		@ob_end_flush();
	}

	$nb_sent = count($sent);
	$nb_errors = count($errors);

	$msg = '<h4>' . sprintf( tra('Newsletter successfully sent to %s users.'), $nb_sent ) . '</h4>';
	if ( $nb_errors > 0 )
		$msg .= "\n" . '<font color="red">' . '(' . sprintf( tra('There was %s errors.'), $nb_errors ) . ')' . '</font><br />';

	// If logfile exists and if it is reachable from the web browser, add a download link
	if ( $logFileHandle && $logFileName[0] != '/' && $logFileName[0] != '.' )
		$smarty->assign('downloadLink', $logFileName);

	echo str_replace( "'", "\\'", $msg);
	echo $smarty->fetch('send_newsletter_footer.tpl');

	$smarty->assign('sent', $nb_sent);
	$smarty->assign('emited', 'y');
	if (count($errors) > 0) {
		$smarty->assign_by_ref('errors', $errors);
	}
	$editionId = $nllib->replace_edition($_REQUEST["nlId"], $_REQUEST["subject"], $_REQUEST["data"], count($sent), $editionId, false, !empty($_REQUEST['datatxt']) ? $txt : '', $info['files'], $_REQUEST['wysiwyg']);
	foreach($info['files'] as $k => $f) {
		if ($f['savestate'] == 'tikitemp') {
			$newpath = $prefs['tmpDir'] . '/newsletterfile-' . $f['filename'];
			rename($f['path'], $newpath);
			unlink($f['path'] . '.infos');
			$info['files'][$k]['savestate'] = 'tiki';
			$info['files'][$k]['path'] = $newpath;
		}
	}

	@fclose( $logFileHandle );
	unset($_SESSION["sendingUniqIds"][ $_REQUEST["sendingUniqId"] ]);
	exit; // Stop here since we are in an iframe and don't want to use smarty display
}

// Article Clipping
$articleClip = '';
if ($nl_info["allowArticleClip"] == 'y' && empty($articleClip)) {
	if ($nl_info["autoArticleClip"] == 'y' || isset($_REQUEST["clipArticles"])) {
		$articleClip = $nllib->clip_articles($_REQUEST["nlId"]);
		// prevent clearing of keyed in info if any
		if (!$info["data"] && isset($_REQUEST["data"])) {
			$info["data"] = $_REQUEST["data"];
		}
		if (!$info["datatxt"] && isset($_REQUEST["datatxt"])) {
			$info["datatxt"] = $_REQUEST["datatxt"];
		}
		if (!$info["subject"] && isset($_REQUEST["subject"])) {
			$info["subject"] = $_REQUEST["subject"];
		}		
	} elseif (isset($_REQUEST["articleClip"]) && $_REQUEST["articleClip"]) {
		$articleClip = $_REQUEST["articleClip"];
	}
}
$smarty->assign('articleClip', $articleClip);

if (isset($_REQUEST["save_only"])) {
	if (!isset($txt) || empty($_REQUEST['datatxt'])) $txt = "";
	$smarty->assign('nlId', $_REQUEST['nlId']);
	$editionId = $nllib->replace_edition($_REQUEST['nlId'], $_REQUEST['subject'], $_REQUEST['data'], -1, $_REQUEST['editionId'], true, $txt, $info['files'], $_REQUEST['wysiwyg']);
	foreach($info['files'] as $k => $f) {
		if ($f['savestate'] == 'tikitemp') {
			$newpath = $prefs['tmpDir'] . '/newsletterfile-' . $f['filename'];
			rename($f['path'], $newpath);
			unlink($f['path'] . '.infos');
			$info['files'][$k]['savestate'] = 'tiki';
			$info['files'][$k]['path'] = $newpath;
		}
	}
	$info = $nllib->get_edition($editionId);
	$smarty->assign('info', $info);
}
if (!isset($_REQUEST['ed_sort_mode']) && !isset($_REQUEST['dr_sort_mode'])) {
	$ed_sort_mode = $dr_sort_mode = 'sent_desc';
} else {
	$ed_sort_mode = $_REQUEST['ed_sort_mode'];
	$dr_sort_mode = $_REQUEST['dr_sort_mode'];
}
$smarty->assign_by_ref('ed_sort_mode', $ed_sort_mode);
$smarty->assign_by_ref('dr_sort_mode', $dr_sort_mode);
if (!isset($_REQUEST['ed_offset']) && !isset($_REQUEST['dr_offset'])) {
	$ed_offset = $dr_offset = 0;
} else {
	$ed_offset = $_REQUEST['ed_offset'];
	$dr_offset = $_REQUEST['dr_offset'];
}
$smarty->assign_by_ref('ed_offset', $ed_offset);
$smarty->assign_by_ref('dr_offset', $dr_offset);
if (isset($_REQUEST['ed_find']) && isset($_REQUEST['dr_find'])) {
	$ed_find = $_REQUEST['ed_find'];
	$dr_find = $_REQUEST['dr_find'];
} else {
	$ed_find = $dr_find = '';
}
$smarty->assign_by_ref('ed_find', $ed_find);
$smarty->assign_by_ref('dr_find', $dr_find);
$editions = $nllib->list_editions($_REQUEST["nlId"], $ed_offset, $maxRecords, $ed_sort_mode, $ed_find, false);
$drafts = $nllib->list_editions($_REQUEST["nlId"], $dr_offset, $maxRecords, $dr_sort_mode, $dr_find, true);
$ed_cant_pages = ceil($editions["cant"] / $maxRecords);
$dr_cant_pages = ceil($drafts["cant"] / $maxRecords);
$smarty->assign_by_ref('ed_cant_pages', $ed_cant_pages);
$smarty->assign('ed_actual_page', 1 + ($ed_offset / $maxRecords));
$smarty->assign_by_ref('dr_cant_pages', $dr_cant_pages);
$smarty->assign('dr_actual_page', 1 + ($dr_offset / $maxRecords));
if ($editions["cant"] > ($ed_offset + $maxRecords)) {
	$smarty->assign('ed_next_offset', $ed_offset + $maxRecords);
} else {
	$smarty->assign('ed_next_offset', -1);
}
if ($drafts["cant"] > ($dr_offset + $maxRecords)) {
	$smarty->assign('dr_next_offset', $dr_offset + $maxRecords);
} else {
	$smarty->assign('dr_next_offset', -1);
}
// If offset is > 0 then prev_offset
if ($ed_offset > 0) {
	$smarty->assign('ed_prev_offset', $ed_offset - $maxRecords);
} else {
	$smarty->assign('ed_prev_offset', -1);
}
if ($dr_offset > 0) {
	$smarty->assign('dr_prev_offset', $dr_offset - $maxRecords);
} else {
	$smarty->assign('dr_prev_offset', -1);
}
$smarty->assign_by_ref('editions', $editions["data"]);
$smarty->assign_by_ref('drafts', $drafts["data"]);
$smarty->assign_by_ref('cant_editions', $editions["cant"]);
$smarty->assign_by_ref('cant_drafts', $drafts["cant"]);
$smarty->assign('url', "tiki-send_newsletters.php");
if (!empty($tiki_p_use_content_templates) && $tiki_p_use_content_templates == 'y') {
	global $templateslib; require_once 'lib/templates/templateslib.php';
	$templates = $templateslib->list_templates('newsletters', 0, -1, 'name_asc', '');
}
$smarty->assign_by_ref('templates', $templates["data"]);
$tpls = $nllib->list_tpls();
if (count($tpls) > 0) {
	$smarty->assign_by_ref('tpls', $tpls);
}
include_once ("textareasize.php");
include_once ('tiki-section_options.php');
setcookie('tab', $_REQUEST['cookietab']);
$smarty->assign('cookietab', $_REQUEST['cookietab']);
ask_ticket('send-newsletter');
global $wikilib;
include_once ('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true, 'editwiki');
$smarty->assign_by_ref('plugins', $plugins);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-send_newsletters.tpl');
$smarty->display("tiki.tpl");

function generateTxtVersion($txt) {
	//Add line breaks if none before stripping headers, paras, and br
	$txt =  preg_replace('/(<\/h[1-6]>) *([^\r\n])/', "$1\n\n$2", $txt);
	$txt =  preg_replace('/(<\/div>) *([^\r\n])/', "$1\n\n$2", $txt);
	$txt =  preg_replace('/(<\/p>) *([^\r\n])/', "$1\n\n$2", $txt);
	$txt =  str_replace('/(<br *\/?>)/', "\n", $txt);
	// start stripping tags
	$txt = strip_tags(str_replace(array("\r\n", "&nbsp;"), array("\n", " "), $txt));
	$txt = preg_replace('/^!!!(.*?)$/m', "\n$1\n", $txt);
	$txt = preg_replace('/^!!(.*?)$/m', "\n$1\n", $txt);
	$txt = preg_replace('/^!(.*?)$/m', "\n$1\n", $txt);
	$txt = html_entity_decode($txt);
	return $txt;
}