<?php
include_once('lib/messu/messulib.php');
if($user) {
  $modUnread = $messulib->user_unread_messages($user);
  $smarty->assign('modUnread',$modUnread);
}
?>