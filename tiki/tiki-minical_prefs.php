<?php
require_once('tiki-setup.php');
include_once('lib/minical/minicallib.php');

if($feature_minical != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


//if($tiki_p_minical != 'y') {
//  $smarty->assign('msg',tra("Permission denied to use this feature"));
//  $smarty->display("styles/$style_base/error.tpl");
//  die;  
//}


if(isset($_REQUEST['save'])) {
  $tikilib->set_user_preference($user,'minical_interval',$_REQUEST['minical_interval']);
  $tikilib->set_user_preference($user,'minical_reminders',$_REQUEST['minical_reminders']);
  $tikilib->set_user_preference($user,'minical_upcoming',$_REQUEST['minical_upcoming']);
  $tikilib->set_user_preference($user,'minical_start_hour',$_REQUEST['minical_start_hour']);
  $tikilib->set_user_preference($user,'minical_end_hour',$_REQUEST['minical_end_hour']);
//  $tikilib->set_user_preference($user,'minical_public',$_REQUEST['minical_public']);
}

$minical_interval = $tikilib->get_user_preference($user,'minical_interval',60*60);
$minical_start_hour = $tikilib->get_user_preference($user,'minical_start_hour',9);
$minical_end_hour = $tikilib->get_user_preference($user,'minical_end_hour',20);
$minical_public = $tikilib->get_user_preference($user,'minical_public','n');
$minical_upcoming = $tikilib->get_user_preference($user,'minical_upcoming',7);
if(isset($_REQUEST['minical_interval'])) {
  $minical_interval = $_REQUEST['minical_interval'];
} 
if(isset($_REQUEST['minical_start_hour'])) {
  $minical_start_hour = $_REQUEST['minical_start_hour'];
} 
if(isset($_REQUEST['minical_end_hour'])) {
  $minical_end_hour = $_REQUEST['minical_end_hour'];
} 
if(isset($_REQUEST['minical_public'])) {
  $minical_interval = $_REQUEST['minical_public'];
} 
if(isset($_REQUEST['minical_upcoming'])) {
  $minical_upcoming = $_REQUEST['minical_upcoming'];
} 
if(isset($_REQUEST['minical_reminders'])) {
  $minical_reminders = $_REQUEST['minical_reminders'];
  $smarty->assign('minical_reminders',$minical_reminders);
} 



$smarty->assign('minical_interval',$minical_interval);
$smarty->assign('minical_public',$minical_public);
$smarty->assign('minical_start_hour',$minical_start_hour);
$smarty->assign('minical_end_hour',$minical_end_hour);
$smarty->assign('minical_upcoming',$minical_upcoming);


$hours=range(0,23);
$smarty->assign('hours',$hours);

$upcoming=range(1,20);
$smarty->assign('upcoming',$upcoming);

if(isset($_REQUEST['removetopic'])) {
  $minicallib->minical_remove_topic($user,$_REQUEST['removetopic']);
}

// Process upload here
if(isset($_REQUEST['addtopic'])) {
	if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	  $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
	  $data = '';
	  while(!feof($fp)) {
	      $data .= fread($fp,8192*16);
	  }
	  fclose($fp);
	  $size = $_FILES['userfile1']['size'];
	  $name = $_FILES['userfile1']['name'];
	  $type = $_FILES['userfile1']['type'];
	} else {
	  $size=0;
	  $name='';
	  $type='';
	  $data='';
	}
	$minicallib->minical_upload_topic($user,$_REQUEST['name'],$name,$type,$size, $data,$_REQUEST['path']);
}
$topics = $minicallib->minical_list_topics($user,0,-1,'name_asc','');
$smarty->assign('topics',$topics['data']);
$smarty->assign('cols',4);


include_once('tiki-mytiki_shared.php');

$smarty->assign('mid','tiki-minical_prefs.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
