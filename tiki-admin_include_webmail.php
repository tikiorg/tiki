<?php

include_once ("lib/webmail/htmlMimeMail.php");

if(isset($_REQUEST["webmail"])) {
  if(isset($_REQUEST["webmail_view_html"]) && $_REQUEST["webmail_view_html"]=="on") {
    $tikilib->set_preference("webmail_view_html",'y'); 
    $smarty->assign('webmail_view_html','y');
  } else {
    $tikilib->set_preference("webmail_view_html",'n');
    $smarty->assign('webmail_view_html','n');
  }  
  $tikilib->set_preference('webmail_max_attachment',$_REQUEST["webmail_max_attachment"]);
  $smarty->assign('webmail_max_attachment',$_REQUEST["webmail_max_attachment"]);
}

?>
