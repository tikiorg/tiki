<?php

if(isset($_REQUEST["pollprefs"])) {
  if(isset($_REQUEST["poll_comments_per_page"])) {
    $tikilib->set_preference("poll_comments_per_page",$_REQUEST["poll_comments_per_page"]);
    $smarty->assign('poll_comments_per_page',$_REQUEST["poll_comments_per_page"]);
  }
  if(isset($_REQUEST["poll_comments_default_ordering"])) {
    $tikilib->set_preference("poll_comments_default_ordering",$_REQUEST["poll_comments_default_ordering"]);
    $smarty->assign('poll_comments_default_ordering',$_REQUEST["poll_comments_default_ordering"]);
  }
  if(isset($_REQUEST["feature_poll_comments"]) && $_REQUEST["feature_poll_comments"]=="on") {
    $tikilib->set_preference("feature_poll_comments",'y'); 
    $smarty->assign("feature_poll_comments",'y');
  } else {
    $tikilib->set_preference("feature_poll_comments",'n');
    $smarty->assign("feature_poll_comments",'n');
  }
}

?>
