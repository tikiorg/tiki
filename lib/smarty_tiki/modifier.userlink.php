<?php

function smarty_modifier_userlink($other_user,$class='link')
{
    global $tikilib, $userlib, $user, $feature_score, $feature_friends;
    
    $star = '';

    if ($feature_score == 'y') {
	$info = $userlib->get_user_info($other_user);
	$star = $tikilib->get_star($info['score']);
    }

    $friend = "";
    
    if ($feature_friends == 'y') {
	if ($tikilib->verify_friendship($user, $other_user)) {
	    $friend = '<img src="img/icons/ico_friend.png">';
	}
    }
    
    if($userlib->user_exists($other_user)&&(!empty($friend) || $tikilib->get_user_preference($other_user,'user_information','public')=='public')) {
	return "$star<a class='$class' href='tiki-user_information.php?view_user=$other_user'>$other_user</a>$friend";
    } else {
	return "$star<span class='$class'>$other_user</span>$friend";
    }
}

/* vim: set expandtab: */

?>
