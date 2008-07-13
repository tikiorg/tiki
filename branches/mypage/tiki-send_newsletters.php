<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-send_newsletters.php,v 1.41.2.2 2007-12-23 23:18:49 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'newsletters';
require_once ('tiki-setup.php');
@ini_set('max_execution_time', 0); //will not work in safe_mode is on
$prefs['feature_wiki_protect_email'] = 'n'; //not to alter the email

include_once ('lib/newsletters/nllib.php');

$sender_email = $userlib->get_user_email($user);

if ($prefs['feature_newsletters'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["nlId"])) {
	$_REQUEST["nlId"] = 0;
}
$smarty->assign('nlId', $_REQUEST["nlId"]);

$newsletters = $nllib->list_newsletters(0, -1, 'created_desc', '', '', array("tiki_p_admin_newsletters", "tiki_p_send_newsletters"));

if (($user=="admin") && (count($newsletters["data"])==0)) {
	$smarty->assign('msg', tra("No newsletters available."));
	$smarty->display("error.tpl");
	die;
}

if (!$newsletters["cant"]) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if(!isset($_REQUEST['cookietab'])) {
	$_REQUEST['cookietab'] = 1;
}
$smarty->assign('newsletters', $newsletters["data"]);

if ($_REQUEST["nlId"]) {
	$nl_info = $nllib->get_newsletter($_REQUEST["nlId"]);

	if (!isset($_REQUEST["editionId"])) $_REQUEST["editionId"] = 0;
	$smarty->assign('allowTxt',$nl_info['allowTxt']);
	if ($_REQUEST["editionId"]) {
		$info = $nllib->get_edition($_REQUEST["editionId"]);
	} else {
		$info = array();
		$info["data"] = '';
		$info["datatxt"] = '';
		$info["subject"] = '';
		$info["editionId"] = 0;
	}
	$smarty->assign('info', $info);
}else{
	//No newsletter selected -> Check if the textarea for the first
	//as to be displayed
	$smarty->assign('allowTxt', $newsletters['data'][0]['allowTxt']);	
}


// Display to newsletter txtarea or not depending on the preferences
$showBoxCheck="
	<script type='text/javascript'>
	<!--
	function checkNewsletterTxtArea(){
	browser();
	if (document.getElementById('txtcol1').style.display=='none'){";
  if (preg_match("/gecko/i",$_SERVER['HTTP_USER_AGENT'])){
	$showBoxCheck.= "document.getElementById('txtcol1').style.display='table-cell';";
	$showBoxCheck.= "document.getElementById('txtcol2').style.display='table-cell';";
  }else{
	$showBoxCheck.= "document.getElementById('txtcol1').style.display='inline';	";
	$showBoxCheck.= "document.getElementById('txtcol2').style.display='inline';";
  };
  $showBoxCheck.="
    	}else{
	document.getElementById('txtcol1').style.display='none';
	document.getElementById('txtcol2').style.display='none';
    	}
	}
	-->
	</script>
	";
$smarty->assign('showBoxCheck',$showBoxCheck);



if (isset($_REQUEST["remove"])) {
	$area = 'delnewsletter';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$nllib->remove_edition($_REQUEST["nlId"], $_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0 && (!isset($_REQUEST['previousTemplateId']) || $_REQUEST['previousTemplateId'] != $_REQUEST['templateId'])) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);
	$_REQUEST["data"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
	$smarty->assign("templateId", $_REQUEST["templateId"]);
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');
	//if (eregi("\<[ \t]*html[ \t\>]",  $_REQUEST["data"]))  // html newsletter - this will be the text sent with the html part
	//	$smarty->assign('txt', nl2br(strip_tags($_REQUEST["data"])));
	//TODO: the sent text version is not pretty: the text must be a textarea
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
   	if (isset($_REQUEST["datatxt"])) {
 		$info["datatxt"] = $_REQUEST["datatxt"];
		//For the hidden input
		$smarty->assign('datatxt',$_REQUEST["datatxt"]);
		//For the display
		$info["datatxt"] = preg_replace ( "/\n/", "<br>", $info["datatxt"] );
	} else {
		$info["datatxt"] = '';
	}
	if (!empty($_REQUEST["usedTpl"])) {
		$smarty->assign('dataparsed', $tikilib->parse_data($info["data"], false, true));
		$smarty->assign('subject', $info["subject"]);
		$info["dataparsed"]  = $smarty->fetch("newsletters/".$_REQUEST["usedTpl"]);
	        if (stristr($info['dataparsed'], "<body>") === false) {
        	        $info['dataparsed'] = "<html><body>".$info['dataparsed']."</body></html>";
        	}
		$smarty->assign("usedTpl", $_REQUEST["usedTpl"]);
	} else {
		$info["dataparsed"] = "<html><body>".$tikilib->parse_data($info["data"], false, true)."</body></html>";
	}
	$smarty->assign('info', $info);
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
	if (!empty($_REQUEST["usedTpl"])) {
		$smarty->assign('dataparsed', $tikilib->parse_data($_REQUEST["data"], false, true));
		$smarty->assign('subject', $_REQUEST["subject"]);
		$parsed = $smarty->fetch("newsletters/".$_REQUEST["usedTpl"]);
	} else {
		$parsed = $tikilib->parse_data($_REQUEST["data"], false, true);
	}
	if (empty($parsed) && !empty($_REQUEST['datatxt'])) {
		$parsed = $_REQUEST['datatxt'];
	}
	if (stristr($parsed, "<body>") === false) {
		$parsed = "<html><body>$parsed</body></html>";
	}
	$smarty->assign('dataparsed',$parsed);
	
	$smarty->assign('subject', $_REQUEST["subject"]);
	$cant = count($subscribers);
	$smarty->assign('subscribers', $cant);
}

$smarty->assign('emited', 'n');
if (!empty($_REQUEST['datatxt']))
   $txt = $_REQUEST['datatxt'];
if (empty($txt)&&!empty($_REQUEST["data"]))
	{
		//No txt message is explicitely provided -> 
		//Create one with the html Version & remove Wiki tags
		$txt = strip_tags(str_replace(array("\r\n","&nbsp;") , array("\n"," ") , $_REQUEST["data"]));
		$txt=ereg_replace("[^ a-zA-Z0-9]*!",'\n',$txt);
		$txt=ereg_replace("!!",'\n',$txt);
		$txt=ereg_replace("!!!",'\n',$txt);
	}


if (isset($_REQUEST["send"])) {
	include_once ('lib/webmail/tikimaillib.php');
	check_ticket('send-newsletter');
	set_time_limit(0);
	$mail = new TikiMail();	
	
	if (stristr($_REQUEST["dataparsed"], "<body>") === false) {
		$html = "<html><body>".$tikilib->parse_data($_REQUEST["dataparsed"], false, true)."</body></html>";
	} else {
		$html = $_REQUEST["dataparsed"];
	}
	$sent = 0;
	$unsubmsg = '';
	$errors =  array();

	if (isset($_REQUEST['errorEditionId'])) {
		$users = $nllib->get_edition_errors($_REQUEST['errorEditionId']);
	} else {
		$users = $nllib->get_all_subscribers($_REQUEST["nlId"], $nl_info["unsubMsg"]);
	}
	
	$nllib->memo_subscribers_edition($editionId, $users);
	$sender_email = $prefs['sender_email'];
	foreach ($users as $us) {
		$userEmail  = $us["login"];
		$email = $us["email"];
		if ($email == "") {
			$errors[] = array("user"=>$userEmail, "email"=>"");
			continue;
		}
		if ($userEmail == "") {
			$userEmail = $userlib->get_user_by_email($email);
		}
	
		if ($userEmail) {
			$mail->setUser($userEmail);
		} else {
			$userEmail = '';
		}
			$mail->setFrom($sender_email);
			$mail->setSubject($_REQUEST["subject"]); // htmlMimeMail memorised the encoded subject 
			$languageEmail = ! $userEmail ? $prefs['site_language'] : $tikilib->get_user_preference($userEmail, "language", $prefs['site_language']);
			if ($nl_info["unsubMsg"] == 'y') {
				$unsubmsg = $nllib->get_unsub_msg($_REQUEST["nlId"], $email, $languageEmail, $us["code"], $userEmail);
				if (stristr($html, "</body>") === false) {
					$msg = $html.nl2br($unsubmsg);
				} else {
					$msg = str_replace("</body>", nl2br($unsubmsg)."</body>", $html);
				}
			} else {
				$msg = $html;
			}
			$mail->setHtml($msg, $txt.strip_tags($unsubmsg));
			$mail->buildMessage();
			if ($mail->send(array($email))) {
				$sent++;
				$nllib->delete_edition_subscriber($editionId, $us);
			} else {
				$errors[] = array("user"=>$userEmail, "email"=>$email);
				$nllib->mark_edition_subscriber($editionId, $us);
			}
	}

	$smarty->assign('sent', $sent);
	$smarty->assign('emited', 'y');
	if (count($errors) > 0) {
		$smarty->assign_by_ref('errors', $errors);
	}
	$editionId = $nllib->replace_edition($_REQUEST["nlId"], $_REQUEST["subject"], $_REQUEST["data"], $sent, $editionId, false, $txt);
}

if (isset($_REQUEST["save_only"])) {
	if (!isset($txt))$txt="";
	$smarty->assign('nlId', $_REQUEST['nlId']);	
	$editionId = $nllib->replace_edition($_REQUEST['nlId'], $_REQUEST['subject'], $_REQUEST['data'], -1, $_REQUEST['editionId'], true,$txt);
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
	
$editions = $nllib->list_editions($_REQUEST["nlId"], $ed_offset, $maxRecords, $ed_sort_mode, $ed_find);
$drafts = $nllib->list_editions($_REQUEST["nlId"], $dr_offset, $maxRecords, $dr_sort_mode, $dr_find);

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

if ($tiki_p_use_content_templates == 'y') {
	$templates = $tikilib->list_templates('newsletters', 0, -1, 'name_asc', '');
}
$smarty->assign_by_ref('templates', $templates["data"]);
$tpls = $nllib->list_tpls();
if (count($tpls) > 0) {
	$smarty->assign_by_ref('tpls', $tpls);
}
include_once("textareasize.php");
include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','newsletters');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);

include_once ('tiki-section_options.php');

setcookie('tab',$_REQUEST['cookietab']);
$smarty->assign('cookietab', $_REQUEST['cookietab']);

ask_ticket ('send-newsletter');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-send_newsletters.tpl');
$smarty->display("tiki.tpl");

?>
