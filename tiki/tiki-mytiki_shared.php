<?php
if($feature_messages=='y' && $tiki_p_messages=='y') {
  $unread = $tikilib->user_unread_messages($user);
  $smarty->assign('unread',$unread);
}
?>
