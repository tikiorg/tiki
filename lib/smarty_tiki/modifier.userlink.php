<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_userlink($other_user,$class='link',$idletime='not_set')
{
    global $tikilib, $userlib, $user, $feature_score, $feature_friends;
    
    $star = '';

    if ($feature_score == 'y') {
	$info = $userlib->get_user_info($other_user);
	if ($other_user == "admin" || $other_user == "system" || $other_user == "Anonymous")
		$star = "";
	else
		$star = $tikilib->get_star($info['score']);
    }

    $friend = "";
    
    if ($feature_friends == 'y') {
	if ($tikilib->verify_friendship($user, $other_user)) {
	    $friend = '&nbsp;<img src="img/icons/ico_friend.gif" width="7" height="10" alt="'.tra("Friend").'" />&nbsp;';
	}
    } 
    
    if($userlib->user_exists($other_user)&&(!empty($friend) || $tikilib->get_user_preference($other_user,'user_information','public')=='public')) {
		if (is_numeric($idletime)) {
			return "<a class='$class' href='tiki-user_information.php?view_user=$other_user' title='".tra("More info about $other_user")." ".tra("(idle for $idletime seconds)")."'>$other_user</a>$friend$star";
		} else {
			return "<a class='$class' href='tiki-user_information.php?view_user=$other_user' title='".tra("More info about $other_user")."'>$other_user</a>$friend$star";
		}
    } else {
	return "<span class='$class'>$other_user</span>$friend$star";
    }
}

/* vim: set expandtab: */

?>
