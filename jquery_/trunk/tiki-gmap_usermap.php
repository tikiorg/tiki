<?php
include "tiki-setup.php";

if ($prefs['feature_gmap'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");
	$smarty->display("error.tpl");
	die;
}

$style = 'style="float:left;margin-right:5px;"';
$query = "SELECT `login`, `avatarType`, `avatarLibName`, p1.`value` as lon, p2.`value` as lat FROM `users_users` as u ";
$query.= "left join `tiki_user_preferences` as p1 on p1.`user`=u.`login` and p1.`prefName`=? ";
$query.= "left join `tiki_user_preferences` as p2 on p2.`user`=u.`login` and p2.`prefName`=? ";
$result = $tikilib->query($query, array('lon','lat'));
while ($res = $result->fetchRow()) {
	if ($res['lon'] and $res['lon'] < 180 and $res['lon'] > -180 and $res['lat'] and $res['lat'] < 180 and $res['lat'] > -180) {
		$res['lon'] = number_format($res['lon'],5);
		$res['lat'] = number_format($res['lat'],5);
		// echo $res['login']." ".$res['lon'].' '.$res['lat']."<br />\n";
    $image = '';
    switch ($res["avatarType"]) {
      case 'l':
        $image = '<img border="0" width="45" height="45" src="' . $res["avatarLibName"] . '" ' . $style . ' alt="'.$res['login'].'" />';
        break;
      case 'u':
        $image = '<img border="0" width="45" height="45" src="tiki-show_user_avatar.php?user='.$res['login'].'" ' . $style . ' alt="'.$res['login'].'" />';
        break;
    }
		$out[] = array($res['lat'],$res['lon'],addslashes($image).'Login:'.$res['login'].'<br />Lat: '.$res['lon'].'&deg;<br /> Long: '.$res['lat'].'&deg;');
	}
}

$smarty->assign('users',$out);
$smarty->assign('mid','tiki-gmap_usermap.tpl');
$smarty->display('tiki.tpl');
?>
