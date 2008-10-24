<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_userlink($other_user,$class='link',$idletime='not_set', $fullname='', $max_length=0) {
    global $tikilib, $userlib, $cachelib, $user, $prefs, $userprefslib;

    $cachePeriod = 60*60*2; // how long does an entry stay in the cache for?  2hr

    $show_mouseover = $prefs['feature_community_mouseover'] == 'y' && $userlib->get_user_preference($user, 'show_mouseover_user_info','y') == 'y';
    $show_friends = $prefs['feature_friends'] == 'y' && $tikilib->verify_friendship($user, $other_user);
    
    if( $show_mouseover || $show_friends ) {
        $cacheItem = "userlink.".$user.".".$other_user.$fullname.$max_length;
    } else {
        $cacheItem = "userlink.".$other_user.$fullname.$max_length;
    }
    $cacheDate = $cachelib->getCachedDate($cacheItem);
    if( $cacheDate ) {
        if( (time() - $cacheDate) < $cachePeriod ) {
            return $cachelib->getCached($cacheItem);
        } else {
            $cachelib->invalidate($cacheItem);
        }
    }

    
    $star = '';
    $info = array();
    if ($prefs['feature_community_mouseover'] || $prefs['feature_score']) {
	$info = $userlib->get_user_info($other_user);
    }

    if ($prefs['feature_score'] == 'y') {
	if (empty($info['score']) || $other_user == "admin" || $other_user == "system" || $other_user == "Anonymous") {
		$star = "";
	} else {
		$star = $tikilib->get_star($info['score']);
	}
    }

    $friend = "";
    
    if ($show_friends) {
        $friend = '&nbsp;<img src="img/icons/ico_friend.gif" width="7" height="10" alt="'.tra("Friend").'" />&nbsp;';
    }

    if ( $fullname != '' ) { $ou = $fullname; }
    elseif ( $prefs['user_show_realnames'] == 'y' ) {
	    $user_details = $userlib->get_user_details($other_user);
	    $ou = $user_details['info']['realName'];
	    unset($user_details);
    }
    if ( empty($ou) || $ou == '' ) $ou = $other_user;
    if ( $max_length > 0 ) $ou = smarty_modifier_truncate($ou, $max_length, '...', true);

    if($userlib->user_exists($other_user)&&(!empty($friend) || $tikilib->get_user_preference($other_user,'user_information','public')=='public')) {
			if (isset($info) and is_array($info) and $prefs['highlight_group'] and in_array($prefs['highlight_group'],$info['groups'])) { 
			    $ou = '<i class="highlightgroup"><b>'.$ou.'</b></i>';
			}
			
			$mouseover = '';

			if ($show_mouseover) {
			    $content = '';
			    if ($prefs['feature_community_mouseover_name'] == 'y') {
				$line = $userlib->get_user_preference($other_user, "realName");
				if ($line) {
				    $content .= $line."<br />";
				}
			    }
			    if ($prefs['feature_community_mouseover_friends'] == 'y' && $prefs['feature_friends'] == 'y') {
				$content .= "<img src='img/icons/ico_friend.gif' />&nbsp;";
				$content .= $tikilib->get_friends_count($other_user) . '&nbsp;&nbsp;&nbsp;';
			    }
			    if ($prefs['feature_community_mouseover_score'] == 'y' && $star) {
				$content .= $star . $info['score'];
			    }
			    if (($prefs['feature_community_mouseover_score'] == 'y' || $prefs['feature_community_mouseover_friends'] == 'y') && $star) $content .= "<br />";

			    if ($prefs['feature_community_mouseover_country'] == 'y') {
				$country = $tikilib->get_user_preference($other_user, "country", "");
				if ($country && $country != "Other") {
				   $content .= "<img src='img/flags/$country.gif' /> ".tra($country) . "<br />";
				}
					}
					if ($prefs['feature_community_mouseover_distance'] == 'y') {
				if (!is_object($userprefslib)) include_once 'lib/userprefs/userprefslib.php';
				$distance = $userprefslib->get_userdistance($other_user,$user);
				if (!is_null($distance)) {
				   $content .= $distance." ".tra("km") . "<br />";
				}
			    }
			    if($prefs['feature_community_mouseover_email'] == 'y') {
				$email_isPublic = $tikilib->get_user_preference($other_user, "email is public");
				if ($email_isPublic != 'n') {
				    include_once ('lib/userprefs/scrambleEmail.php');
				    $content .= scrambleEmail($info['email'], $email_isPublic) . "<br />";
				} elseif (!empty($friend)) {
				    $content .= $info['email'] . "<br />";
				}	    
			    }
			    if ($prefs['feature_community_mouseover_lastlogin'] == 'y') {
				$content .= tra("Last seen on ") . $tikilib->get_short_datetime($info['lastLogin']);
				$content .= "<br />";
			    }

			    if (is_numeric($idletime)) {
				$content .= sprintf(tra("(idle for %s seconds)"), $idletime) . "<br />";
			    }

			    if ($prefs['feature_community_mouseover_picture'] == 'y') {
				if ($info['avatarLibName'] != "") {
					$img = "<img border='0' width='45' height='45' src='" . $info['avatarLibName']. "'  alt='' />";
				} else if ($info['avatarData'] != "") {
					$img = "<img src='tiki-show_user_avatar.php?user=$other_user' width='45' height='45' alt='' />";
				} else {
					$img = "";
				}
				if (empty($content)) {
				    $content = $img;
				} elseif ($img != "") {
				    $content = "<table><tr><td>$img</td><td>$content</td></tr></table>";
				}
			    }

			    if (!empty($content)) {
				$mouseover = " onmouseover=\"return overlib('".addslashes($content)."',HAUTO,VAUTO,CAPTION,'<div align=\'center\'>".tra("User information - Click for more info")."</div>');\" onmouseout=\"nd()\" ";
			    }
			}


		if (is_numeric($idletime) && empty($mouseover)) {
		    $ret = "<a class='$class' target='_top' href='tiki-user_information.php?userId=".urlencode($info['userId'])."' title='".tra("More info about $other_user")." ".tra("(idle for $idletime seconds)")."'>$ou</a>$friend$star";
                    $cachelib->cacheItem($cacheItem, $ret);
                    return $ret;
		} else {
                    $ret = "<a class='$class' $mouseover target='_top' href='tiki-user_information.php?userId=".urlencode($info['userId'])."' >$ou</a>$friend$star";
                    $cachelib->cacheItem($cacheItem, $ret);
                    return $ret;
		}
    } else {
	$ret = "<span class='$class'>$ou</span>$friend$star";
        $cachelib->cacheItem($cacheItem, $ret);
        return $ret;
    }
}

?>
