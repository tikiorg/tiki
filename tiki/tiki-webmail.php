<?php
// Initialization
require_once('tiki-setup.php');

// User preferences screen
/*
if($feature_userPreferences != 'y') {
   $smarty->assign('msg',tra("This feature is disabled"));
   $smarty->display('error.tpl');
   die;
}
*/

require ("lib/webmail/pop3.php");
require ("lib/webmail/mimeDecode.php");
include ("lib/webmail/class.rc4crypt.php");

function parse_output(&$obj, &$parts,$i){

                if(!empty($obj->parts)){
                        for($i=0; $i<count($obj->parts); $i++)
                                parse_output($obj->parts[$i], $parts,$i);

                }else{
                        $ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;
                        switch($ctype){
                                case 'text/plain':
                                        if(!empty($obj->disposition) AND $obj->disposition == 'attachment'){
                                                $aux['name']=$obj->headers["content-description"];
                                                $aux['content-type']=$obj->headers["content-type"];
                                                //$aux['body']=$obj->body;
                                                $aux['part']=$i;
                                                $parts['attachments'][] = $aux;
                                                
                                        }else{
                                                $parts['text'][] = nl2br($obj->body);
                                        }
                                        break;

                                case 'text/html':
                                        if(!empty($obj->disposition) AND $obj->disposition == 'attachment'){
                                                
                                                $aux['name']=$obj->headers["content-description"];
                                                $aux['content-type']=$obj->headers["content-type"];
                                                //$aux['body']=$obj->body;
                                                $aux['part']=$i;
                                                $parts['attachments'][] = $aux;
                                                
                                        }else{
                                                $parts['html'][] = nl2br($obj->body);
                                        }
                                        break;

                                default:
                                        
                                        $aux['name']=$obj->headers["content-description"];
                                        $aux['content-type']=$obj->headers["content-type"];
                                        //$aux['body']=$obj->body;
                                        $aux['part']=$i;
                                        $parts['attachments'][] = $aux;
                                        
                        }
                }
        }



if(!$user) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display('error.tpl');
   die;
}

if(!isset($_REQUEST["section"])) {
  $_REQUEST["section"]='mailbox';
}
$smarty->assign('section',$_REQUEST["section"]);

if($_REQUEST["section"]=='read') {
  $current=$tikilib->get_current_webmail_account($user);
  $smarty->assign('current',$current);
  $pop3=new POP3($current["pop"],$current["username"],$current["pass"]);
  $pop3->Open();
  $message = $pop3->GetMessage($_REQUEST["msgid"]) ;
  $smarty->assign('msgid',$_REQUEST["msgid"]);
  $s = $pop3->Stats() ;
  $body = $message["body"];
  $header = $message["header"];
  $full = $message["full"];
  $pop3->Close();
  
  $params = array(
                                        'input'          => $full,
                                        'crlf'           => "\r\n",
                                        'include_bodies' => TRUE,
                                        'decode_headers' => TRUE,
                                        'decode_bodies'  => TRUE
                                        );

  $output = Mail_mimeDecode::decode($params);
  parse_output($output, $parts,0);
  //print_r($parts);
  //print_r($output);
  
  if(isset($parts["html"])) {
    $bodies=$parts["html"];
  } else {
    $bodies=$parts["text"];
  }
  if(isset($parts["attachments"])) {
    $attachs=$parts["attachments"];
  } else {
    $attachs=Array();
    
  }
  //print_r($attachs);
  //print_r($output);
  $smarty->assign('attachs',$attachs);
  $smarty->assign('bodies',$bodies);
  $smarty->assign('headers',$output->headers);
  
 
}

if($_REQUEST["section"]=='mailbox') {
  
  $current=$tikilib->get_current_webmail_account($user);
  $smarty->assign('current',$current);
  // Now get messages from mailbox
  $pop3=new POP3($current["pop"],$current["username"],$current["pass"]);
  $pop3->Open();
  
  
  if(isset($_REQUEST["delete"])) {
    if(isset($_REQUEST["msg"])) {
      // Now we can delete the messages
      foreach(array_keys($_REQUEST["msg"]) as $msg) {
        $pop3->DeleteMessage($msg);
      }
    }
  }
  
  $s = $pop3->Stats() ;
  
  $mailsum = $s["message"];
  $numshow=$current["msgs"];
  if (!isset($_REQUEST["start"])) $upperlimit = $mailsum; else $upperlimit = $start;
  $lowerlimit = $upperlimit - $numshow;
  if ($lowerlimit < 0) $lowerlimit = 0;
  $showstart =  $mailsum - $upperlimit + 1;
  $showend = $mailsum - $lowerlimit;

  if (!isset($_REQUEST["offset"])) $_REQUEST["offset"]=0;
  $lowerlimit = $upperlimit - $numshow;
  if ($lowerlimit < 0) $lowerlimit = 0;
  $showstart =  $mailsum - $upperlimit + 1;
  $showend = $mailsum - $lowerlimit;

  $list=Array();
  for ($i=$upperlimit;$i>$lowerlimit;$i--) {
    $aux = $pop3->ListMessage($i);
    $aux["msgid"]=$i;
    if(empty($aux["sender"]["name"])) $aux["sender"]["name"]=$aux["sender"]["email"];
    $aux["sender"]["name"]=htmlspecialchars($aux["sender"]["name"]);
    $aux["subject"]=htmlspecialchars($aux["subject"]);
    $list[]=$aux;
  }
  $pop3->Close();
  $smarty->assign('list',$list);
  
  
}

if($_REQUEST["section"]=='settings') {
  // Add a new mail account for the user here
  if(!isset($_REQUEST["accountId"])) $_REQUEST["accountId"]=0;
  $smarty->assign('accountId',$_REQUEST["accountId"]);
  
  if(isset($_REQUEST["new_acc"])) {
    $tikilib->replace_webmail_account($_REQUEST["accountId"],$user,$_REQUEST["account"],$_REQUEST["pop"],$_REQUEST["port"],$_REQUEST["username"],$_REQUEST["pass"],$_REQUEST["msgs"]);
    $_REQUEST["accountId"]=0;
  }
  if(isset($_REQUEST["remove"])) {
    $tikilib->remove_webmail_account($user,$_REQUEST["remove"]);
  }
  if(isset($_REQUEST["current"])) {
    $tikilib->current_webmail_account($user,$_REQUEST["current"]);
  }
  if($_REQUEST["accountId"]) {
    $info = $tikilib->get_webmail_account($user,$_REQUEST["accountId"]);
  } else {
    $info["account"]='';
    $info["username"]='';
    $info["pass"]='';
    $info["pop"]='';
    $info["port"]=110;
    $info["msgs"]=20;
  }
  $smarty->assign('info',$info);
  // List
  $accounts = $tikilib->list_webmail_accounts($user,0,-1,'account_asc','');
  $smarty->assign('accounts',$accounts["data"]);
}

$smarty->assign('mid','tiki-webmail.tpl');
$smarty->display('tiki.tpl');
?>