<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_userlink($other_user,$class='link',$idletime='not_set') {
    global $tikilib, $userlib, $user, $feature_score, $feature_friends, $highlight_group,
	$feature_community_mouseover, $feature_community_mouseover_name,$feature_community_mouseover_picture,
	$feature_community_mouseover_friends,$feature_community_mouseover_score,$feature_community_mouseover_country,
	$feature_community_mouseover_email, $feature_community_mouseover_lastlogin;

    
    $star = '';

    $info = array();
    if ($feature_community_mouseover || $feature_score) {
	$info = $userlib->get_user_info($other_user);
    }

    if ($feature_score == 'y') {
	if ($other_user == "admin" || $other_user == "system" || $other_user == "Anonymous") {
		$star = "";
	} else {
		$star = $tikilib->get_star($info['score']);
	}
    }

    $friend = "";
    
    if ($feature_friends == 'y') {
	if ($tikilib->verify_friendship($user, $other_user)) {
	    $friend = '&nbsp;<img src="img/icons/ico_friend.gif" width="7" height="10" alt="'.tra("Friend").'" />&nbsp;';
	}
    }

		$ou = $other_user;
    if($userlib->user_exists($other_user)&&(!empty($friend) || $tikilib->get_user_preference($other_user,'user_information','public')=='public')) {
			if (isset($info) and $highlight_group and in_array($highlight_group,$info['groups'])) { 
			    $ou = '<i class="highlightgroup"><b>'.$other_user.'</b></i>';
			}
			
			$mouseover = '';
			$show_mouseover = $feature_community_mouseover == 'y' &&
			    $userlib->get_user_preference($user, 'show_mouseover_user_info','y') == 'y';

			if ($show_mouseover) {
			    $content = '';
			    if ($feature_community_mouseover_name == 'y') {
				$line .= $userlib->get_user_preference($other_user, "realName");
				if ($line) {
				    $content .= $line."<br/>";
				}
			    }
			    if ($feature_community_mouseover_friends == 'y' && $feature_friends == 'y') {
				$content .= "<img src='img/icons/ico_friend.gif'>&nbsp;";
				$content .= $tikilib->get_friends_count($other_user) . '&nbsp;&nbsp;&nbsp;';
			    }
			    if ($feature_community_mouseover_score == 'y') {
				$content .= $star . $info['score'];
			    }
			    if ($feature_community_mouseover_score == 'y' || $feature_community_mouseover_friends == 'y') $content .= "<br/>";

			    if ($feature_community_mouseover_country == 'y') {
				$country = $tikilib->get_user_preference($other_user, "country", "");
				if ($country && $country != "Other") {
				   $content .= "<img src='img/flags/$country.gif' /> ".tra($country) . "<br/>";
				}
			    }
			    if($feature_community_mouseover_email == 'y') {
				$email_isPublic = $tikilib->get_user_preference($other_user, "email is public");
				if ($email_isPublic != 'n') {
				    include_once ('lib/userprefs/scrambleEmail.php');
				    $content .= scrambleEmail($info['email'], $email_isPublic) . "<br/>";
				} elseif (!empty($friend)) {
				    $content .= $info['email'] . "<br/>";
				}	    
			    }
			    if ($feature_community_mouseover_lastlogin == 'y') {
				$content .= tra("Last seen on ") . $tikilib->get_short_datetime($info['lastLogin']);
				$content .= "<br/>";
			    }

			    if (is_numeric($idletime)) {
				$content .= sprintf(tra("(idle for %s seconds)"), $idletime) . "<br/>";
			    }

			    if ($feature_community_mouseover_picture == 'y') {
				if (empty($content)) {
				    $content = "<img src='tiki-show_user_avatar.php?user=$other_user'>";
				} else {
				    $content = "<table><tr><td><img src='tiki-show_user_avatar.php?user=$other_user'></td><td>$content</td></tr></table>";
				}
			    }

			    if (!empty($content)) {
				$mouseover = " onmouseover=\"return overlib('".addslashes($content)."',HAUTO,VAUTO,CAPTION,'<div align=\'center\'>".tra("User information - Click for more info")."</div>');\" onmouseout=\"nd()\" ";
			    }
			}


		if (is_numeric($idletime) && empty($mouseover)) {
			return "<a class='$class' href='tiki-user_information.php?view_user=$other_user' title='".tra("More info about $other_user")." ".tra("(idle for $idletime seconds)")."'>$ou</a>$friend$star";
		} else {
			return "<a class='$class' $mouseover href='tiki-user_information.php?view_user=$other_user' >$ou</a>$friend$star";
		}
    } else {
	return "<span class='$class'>$ou</span>$friend$star";
    }
}

/* vim: set expandtab: */

?>
