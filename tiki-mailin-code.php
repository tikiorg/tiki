<?php

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],"tiki-mailin-code.php")!=FALSE) {
  //smarty is not there - we need setup
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra("This script cannot be called directly"));
  $smarty->display("error.tpl");
  die;
}

include_once ('lib/mailin/mailinlib.php');

require_once ("lib/webmail/pop3.php");
include_once ("lib/webmail/class.rc4crypt.php");

include_once ("lib/mail/mimelib.php");
include_once ("lib/webmail/tikimaillib.php");
include_once ('lib/wiki/wikilib.php');

function mailin_check_attachments(&$output, &$out, $page, $user) {
  global  $wikilib;
  $cnt = 0;
  if (!isset($output["parts"]))
	return;
  for ($it = 0; $it < count($output["parts"]); $it++)
  {
    if (isset($output["parts"][$it]["d_parameters"]["filename"]))
    {
      $attachmentPart = $output["parts"][$it];
      $fileName = $attachmentPart["d_parameters"]["filename"];
	if (isset($attachmentPart["ctype_primary"]))
		$fileType = $attachmentPart["ctype_primary"] ."/". $attachmentPart["ctype_secondary"];
	else
	      $fileType="";
      $fileData = $attachmentPart["body"];
      $fileSize = strlen($fileData);
      $wikilib->wiki_attach_file($page, $fileName, $fileType, $fileSize, $fileData, "attached by mail", $user, "");
      $cnt++;
    }
  }
  $out .= $cnt;
  $out .= " attachment(s) added<br/>";
}
function mailin_get_body($output) {
	if (isset($output['text'][0]))
		$body = $output["text"][0];
	elseif (isset($output['parts'][0]) && isset($output['parts'][0]["text"][0]))
		$body = $output['parts'][0]["text"][0];
	elseif (isset($output['parts'][0]) && isset($output['parts'][0]['parts'][0]) && isset($output['parts'][0]['parts'][0]["text"][0]))
		$body = $output['parts'][0]['parts'][0]["text"][0];
	else
		$body = '';
	return $body;

}
// The mailin script is used to get / set wiki pages using an email account

// Get a list of ACTIVE emails accounts configured for mailin procedures
$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');

//print ('<a href="tiki-admin_mailin.php">Admin</a><br /><br />');
$content = '<br /><br />';

// foreach account
foreach ($accs['data'] as $acc) {
  $content .= "<b>Processing account</b><br />";
  $content .= "Account :" . $acc['account'] . "<br />";
  $content .= "Type    :" . $acc['type'] . "<br />";
  $content .= "--------------------------<br />";
  $pop3 = new POP3($acc["pop"], $acc["username"], $acc["pass"]);
  $pop3->Open();
  $s = $pop3->Stats();
  $mailsum = $s["message"];

  for ($i = 1; $i <= $mailsum; $i++) {
    $aux = $pop3->ListMessage($i);

    $aux["msgid"] = $i;
    $aux["realmsgid"] = $pop3->GetMessageID($i);
    $message = $pop3->GetMessage($i);
    $output = mime::decode($message['full']);
        //mailin_parse_output($output, $parts, 0);

    $content .= "Reading a request.<br />From: " . $aux["sender"]["email"] . "<br />Subject: " . $output['header']['subject'] . "<br />";

    $content .= "sender email: " . $aux["sender"]["email"] . "<br />";
    $aux["sender"]["user"] = $userlib->get_user_by_email($aux["sender"]["email"]);
    $content .= "sender user: " .  $aux["sender"]["user"] . "<br />";

    $cantUseMailIn = $acc["anonymous"]=='n' && empty($aux["sender"]["user"]);
    if($cantUseMailIn) {
      $content .= "Anonymous user acces denied, sending auto-reply to email address:&nbsp;" .  $aux["sender"]["email"] . "<br />";
      $mail = new TikiMail();
      $mail->setFrom($acc["account"]);
      $c = $tikilib->get_preference("default_mail_charset", "utf8");
      $mail->setHeadCharset($c);
      $mail->setTextCharset($c);
      $l = $tikilib->get_preference("language", "en");
      $mail->setSubject(tra('Tiki mail-in auto-reply', $l));
      $mail->setSMTPParams($acc["smtp"], $acc["smtpPort"], '', $acc["useAuth"], $acc["username"], $acc["pass"]);
      $mail->setText(tra("Sorry, you can't use this feature.", $l));
      $res = $mail->send(array($aux["sender"]["email"]), 'mail');
      $content .= "Response sent<br />";
    } else {
      if (empty($aux["sender"]["user"]))
        $aux["sender"]["user"] = $aux["sender"]["email"];
  
      if (empty($aux["sender"]["name"]))
        $aux["sender"]["name"] = $aux["sender"]["email"];
  
      if ($acc['type'] == 'article-put') {
        // This is used to CREATE articles
        $title = trim($output['header']['subject']);
  
        $msgbody = mailin_get_body($output);
        if ($msgbody && isset($acc['discard_after'])) {
             $msgbody = preg_replace("/".$acc['discard_after'].".*$/s", "", $msgbody);
        }
        
        $heading = $msgbody;
        $topicId = $acc['article_topicId'];
        $user = $aux['sender']['user'];
        $authorName = $user;
        $body = '';
        $publishDate = date('U');
        $expireDate = mktime (0,0,0,date("m"),  date("d"),  date("Y")+1);
        $subId = 0;
        $type = $acc['article_type'];
        $useImage = 'n';
        $image_x = '';
        $image_y = '';
        $imgname = '';
        $imgsize = '';
        $imgtype = '';
        $imgdata = '';
       	$topline = '';
		$subtitle = '';
		$linkto = '';
		$image_caption = '';
		$lang = '';
		$rating = 7;
		$isfloat = 'n';
        
		global $artlib;
		if (!is_object($artlib)) {
			include_once('lib/articles/artlib.php');
		}
		$subid = $artlib->replace_submission($title, $authorName, $topicId, $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, 
											$body, $publishDate, $expireDate, $user, $subId, $image_x, $image_y, $type, 
											$topline, $subtitle, $linkto, $image_caption, $lang, $rating, $isfloat);

		global $tiki_p_autoapprove_submission;
		if ($tiki_p_autoapprove_submission == 'y') {
			$artlib->approve_submission($subid);
			$content .= "Article: $title has been submitted<br />";
		} else {
			$content .= "Article: $title has been created<br />";
		}

      } else {
         if ($acc['type'] == 'wiki') {
           $p_page = trim($output['header']['subject']);
           $parts = explode(':', $p_page);
           if (!isset($parts[1])) {
             $parts[1] = $parts[0];
             $parts[0] = 'GET';
           }
           $method = $parts[0];
           $page = $parts[1];
         } else {
           $page = trim($output['header']['subject']);
         }

      if ($acc['type'] == 'wiki-get' || ($acc['type'] == 'wiki' && $method == "GET")) {
        // A wiki-get account sends a copy of the page to the sender
        // and also sends the source of the page
        $mail = new TikiMail();
        $mail->setFrom($acc["account"]);
        $c = $tikilib->get_preference("default_mail_charset", "utf8");
        $mail->setHeadCharset($c);
        $mail->setHtmlCharset($c); 
        $mail->setTextCharset($c);
        $mail->setSMTPParams($acc["smtp"], $acc["smtpPort"], '', $acc["useAuth"], $acc["username"], $acc["pass"]);
  
        if ($tikilib->page_exists($page)) {
          $mail->setSubject($page);
          $info = $tikilib->get_page_info($page);
          $data = $tikilib->parse_data($info["data"]);
          $mail->addAttachment($info['data'], 'source.txt', 'plain/txt');
          $mail->setHTML($data, strip_tags($data));
        } else {
          $l = $tikilib->get_preference("language", "en");
          $mail_data = $smarty->fetchLang($l, "mail/mailin_reply_subject.tpl");
          $mail->setSubject($mail_data.$page);
        }
        $res = $mail->send(array($aux["sender"]["email"]), 'mail');
        $content .= "Response sent<br />";
      }
  
      elseif ($acc['type'] == 'wiki-put' || ($acc['type'] == 'wiki' && $method == "PUT")) {
        // This is used to UPDATE wiki pages
    
        $body = mailin_get_body($output);
  
        if (isset($acc['discard_after']) && $body) {
           $body = preg_replace("/".$acc['discard_after'].".*$/s", "", $body);
        }
        if (!empty($body)) {
          if (!$tikilib->page_exists($page)) {
            $content .= "Page: $page has been created<br />";
  
            $tikilib->create_page($page,
              0, $body, date('U'), "Created from " . $acc["account"], $aux["sender"]["user"], '0.0.0.0', '');
          } else {
            $tikilib->update_page($page, $body, "Created from " . $acc["account"], $aux["sender"]["user"],
              '0.0.0.0', '');
  
            $content .= "Page: $page has been updated";
          }
        }
      mailin_check_attachments($output, $content, $page, $aux["sender"]["user"]);
      }
  
      elseif ($acc['type'] == 'wiki-append' || $acc['type'] == 'wiki-prepend' || ($acc['type'] == 'wiki' && $method == "APPEND") || ($acc['type'] == 'wiki' && $method == "PREPEND")) {
        // This is used to UPDATE wiki pages
  
        $body = mailin_get_body($output);
        if ($body && isset($acc['discard_after'])) {
              $body = preg_replace("/".$acc['discard_after'].".*$/s", "", $body);
          }
  
        if (isset($body)) {
          if (!$tikilib->page_exists($page)) {
            $content .= "Page: $page has been created<br />";
  
            $tikilib->create_page($page,
              0, $body, date('U'), "Created from " . $acc["account"], $aux["sender"]["user"], '0.0.0.0', '');
          } else {
            $info = $tikilib->get_page_info($page);
            if ($acc['type'] == 'wiki-append' || $acc['type'] == 'wiki' && $method == "APPEND")
                  $body = $info['data'] . $body;
            else
                  $body = $body . $info['data'];
            $tikilib->update_page($page, $body,
              "Updated from " . $acc["account"], $aux["sender"]["user"], '0.0.0.0', '');
            $content .= "Page: $page has been updated";
          }
        }
      mailin_check_attachments($output, $content, $page, $aux["sender"]["user"]);
      }

      else {
          $mail = new TikiMail();
  
          $mail->setFrom($acc["account"]);
          $c = $tikilib->get_preference("default_mail_charset", "utf8");
          $mail->setHeadCharset($c);
          $mail->setTextCharset($c);
          $l = $tikilib->get_preference("language", "en");
          $mail_data = $smarty->fetchLang($l, "mail/mailin_help_subject.tpl");
          $mail->setSubject($mail_data);
          $mail->setSMTPParams($acc["smtp"], $acc["smtpPort"], '', $acc["useAuth"], $acc["username"], $acc["pass"]);
          $smarty->assign('subject', $output['header']['subject']);
          $mail_data = $smarty->fetchLang($l, "mail/mailin_help.tpl");
          $mail->setText($mail_data);
          $res = $mail->send(array($aux["sender"]["email"]), 'mail');
        }
      }
    }//end if($cantUseMailIn)
    // Remove the email from the pop3 server
    $pop3->DeleteMessage($i);
  }//end for ($i = 1; $i <= $mailsum; $i++)

  $pop3->close();
//echo $content;
}//end foreach ($accs['data'] as $acc) {

?>
