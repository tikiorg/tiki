<?php

if ($user) {
	$modUnread = $tikilib->user_unread_messages($user);

	$smarty->assign('modUnread', $modUnread);
}

?>