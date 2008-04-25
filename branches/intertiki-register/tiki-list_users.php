<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-list_users.php,v 1.10 2007-10-12 07:55:28 nyloth Exp $

// Initialization
require_once('tiki-setup.php');
include_once ('lib/userprefs/userprefslib.php');

if($prefs['feature_friends'] != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}

if($tiki_p_list_users != 'y') {
    $smarty->assign('msg',tra("You do not have permission to use this feature"));
    $smarty->display("error.tpl");
    die;
}

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = $prefs['user_list_order'];
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 


$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$listusers = $tikilib->list_users($offset,$maxRecords,$sort_mode,$find);

// If there're more records then assign next_offset
$cant_pages = ceil($listusers["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($listusers["cant"] > ($offset + $maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);
} else {
  $smarty->assign('prev_offset',-1);
}

//get the distance
$listdistance = array();
$listuserscountry = array();
for ($i=0;$i<count($listusers["data"]);$i++) {
	if ($prefs['feature_community_list_distance'] == "y") {
		$userlogin=$listusers["data"][$i]["login"];
		$distance=$userprefslib->get_userdistance($userlogin,$user);
		if (is_null($distance)) {
			$listdistance[]=NULL;
		} else {
			$listdistance[]=round($distance,0);
		}
	}

	if ($prefs['feature_community_list_country'] == "y") {
		$userprefs=$listusers["data"][$i]["preferences"];
		$country="None";
		for ($j=0;$j<count($userprefs);$j++) {
				if ($userprefs[$j]["prefName"]=="country") $country=$userprefs[$j]["value"];
				if ($userprefs[$j]["prefName"]=="realName") $listusers["data"][$i]["realName"]=$userprefs[$j]["value"];
		}
	}
	$listuserscountry[]=$country;
}

$smarty->assign_by_ref('listusers',$listusers["data"]);
$smarty->assign_by_ref('cant_users',$listusers["cant"]);
$smarty->assign_by_ref('listdistance',$listdistance);
$smarty->assign_by_ref('listuserscountry',$listuserscountry);

$section='users';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid','tiki-list_users.tpl');
$smarty->display("tiki.tpl");
?>
