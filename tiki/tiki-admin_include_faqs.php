<?php

if(isset($_REQUEST["faqcomprefs"])) {
  if(isset($_REQUEST["faq_comments_per_page"])) {
    $tikilib->set_preference("faq_comments_per_page",$_REQUEST["faq_comments_per_page"]);
    $smarty->assign('faq_comments_per_page',$_REQUEST["faq_comments_per_page"]);
  }
  if(isset($_REQUEST["faq_comments_default_ordering"])) {
    $tikilib->set_preference("faq_comments_default_ordering",$_REQUEST["faq_comments_default_ordering"]);
    $smarty->assign('faq_comments_default_ordering',$_REQUEST["faq_comments_default_ordering"]);
  }
  if(isset($_REQUEST["feature_faq_comments"]) && $_REQUEST["feature_faq_comments"]=="on") {
    $tikilib->set_preference("feature_faq_comments",'y'); 
    $smarty->assign("feature_faq_comments",'y');
  } else {
    $tikilib->set_preference("feature_faq_comments",'n');
    $smarty->assign("feature_faq_comments",'n');
  }
}

?>
