<?php
// Initialization
require_once('tiki-setup.php');
include_once("lib/xmlrpc.inc");
include_once("lib/xmlrpcs.inc");

if($feature_comm != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($tiki_p_send_pages != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["username"])){
  $_REQUEST["username"]=$user;
}
if(!isset($_REQUEST["path"])){
  $_REQUEST["path"]='/tiki/commxmlrpc.php';
}
if(!isset($_REQUEST["site"])){
  $_REQUEST["site"]='';
}
if(!isset($_REQUEST["password"])){
  $_REQUEST["password"]='';
}
if(!isset($_REQUEST["sendpages"])) {
  $sendpages = Array();
} else {
  $sendpages = unserialize(urldecode($sendpages));
}

$smarty->assign('username',$_REQUEST["username"]);
$smarty->assign('site',$_REQUEST["site"]);
$smarty->assign('path',$_REQUEST["path"]);
$smarty->assign('password',$_REQUEST["password"]);

if(isset($_REQUEST["addpage"])) {
  if(!in_array($_REQUEST["pageName"],$sendpages)) {
    $sendpages[] = $_REQUEST["pageName"];
  }
}
if(isset($_REQUEST["clearpages"])) {
  $sendpages = Array();
}
$msg = '';

if(isset($_REQUEST["send"])) {
  // Create XMLRPC object
  $client = new xmlrpc_client($_REQUEST["path"], $_REQUEST["site"], 80);
  $client->setDebug(0);
  
  foreach($sendpages as $page) {
    $page_info = $tikilib->get_page_info($page);
    if($page_info) {
      
      $searchMsg = new xmlrpcmsg('sendPage',array(
        new xmlrpcval($_SERVER["SERVER_NAME"],"string"),
        new xmlrpcval($_REQUEST["username"],"string"),
        new xmlrpcval($_REQUEST["password"],"string"),
        new xmlrpcval($page,"string"),
        new xmlrpcval(base64_encode($page_info["data"]),"string"),
        new xmlrpcval($page_info["comment"],"string")
      ));
      $result=$client->send($searchMsg);
      if(!$result) {
        $errorMsg='Cannot login to server maybe the server is down';
      } else {
        if(!$result->faultCode()) {
           // We have a response
           $res=xmlrpc_decode($result->value());
           if($res) {
             $msg.=tra('page').': '.$page.tra(' succesfully sent')."<br/>";
           }
        } else {
           $errorMsg=$result->faultstring();
           $msg.=tra('page').': '.$page.tra(' not sent').$errorMsg."<br/>";
        }
      }
    }
  }
}

$smarty->assign('msg',$msg);

$smarty->assign('sendpages',$sendpages);
$form_sendpages = urlencode(serialize($sendpages));
$smarty->assign('form_sendpages',$form_sendpages);




$pages = $tikilib->list_pages(0, -1,  'pageName_asc');
$smarty->assign_by_ref('pages',$pages["data"]);

// Display the template
$smarty->assign('mid','tiki-send_objects.tpl');
$smarty->display('tiki.tpl');
?>