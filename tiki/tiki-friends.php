<?php
// Initialization
require_once('tiki-setup.php');
include_once("lib/imagegals/imagegallib.php");
include_once('lib/messu/messulib.php');


if($feature_friends != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}

if (isset($_REQUEST['request_friendship'])) {
    if (!$tikilib->verify_friendship($_REQUEST['request_friendship'],$user)) {
    	$userlib->request_friendship($user,$_REQUEST['request_friendship']);
	$smarty->assign('request_friendship',$_REQUEST['request_friendship']);
    } else {
	$smarty->assign('msg',sprintf(tra("You're already friend of %s"), $_REQUEST['request_friendship']));
	$smarty->display("error.tpl");
	die;
    }
}

if (isset($_REQUEST['accept'])) {
    $userlib->accept_friendship($user,$_REQUEST['accept']);
    $smarty->assign('friendship_accepted', sprintf(tra('FriendshipAccepted_%s'),$_REQUEST['accept']));
}

if (isset($_REQUEST['refuse'])) {
    $userlib->refuse_friendship($user,$_REQUEST['refuse']);
    $smarty->assign('friendship_refused', sprintf(tra('FriendshipRefused_%s'),$_REQUEST['refuse']));
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = $user_list_order;
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 


$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
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

$smarty->assign('pending_requests',$userlib->list_pending_friendship_requests($user));
$smarty->assign('waiting_requests',$userlib->list_waiting_friendship_requests($user));

$listpages = $tikilib->list_user_friends($user,$offset,$maxRecords,$sort_mode,$find);

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($listpages["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('listpages',$listpages["data"]);

$section='friends';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid','tiki-friends.tpl');
$smarty->display("tiki.tpl");
?>
