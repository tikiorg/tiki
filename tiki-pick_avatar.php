<?php
// Initialization
require_once('tiki-setup.php');

// User preferences screen

if($feature_userPreferences != 'y') {
   $smarty->assign('msg',tra("This feature is disabled"));
   $smarty->display("styles/$style_base/error.tpl");
   die;
}

if(!$user) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display("styles/$style_base/error.tpl");
   die;
}

$userwatch=$user;
if(isset($_REQUEST["view_user"])) {
  if($_REQUEST["view_user"]<>$user) {
    if($tiki_p_admin == 'y') {
      $userwatch = $_REQUEST["view_user"];
    } else {
      $smarty->assign('msg',tra("You dont have permission to view other users data"));
      $smarty->display("styles/$style_base/error.tpl");
      die;
    }
  } else {
    $userwatch = $user;
  }
}
$smarty->assign('userwatch',$userwatch);

// Upload avatar is processed here
if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
  $type = $_FILES['userfile1']['type'];
  $size = $_FILES['userfile1']['size'];
  $name = $_FILES['userfile1']['name'];
  $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
  $data = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
  fclose($fp);
  if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {
    $img = imagecreatefromstring($data);
    $size_x = imagesx($img);
    $size_y = imagesy($img);
    if ($size_x > $size_y)
      $tscale = ((int)$size_x / 45);
    else
      $tscale = ((int)$size_y / 45);
    $tw = ((int)($size_x / $tscale));
    $ty = ((int)($size_y / $tscale));
    if (chkgd2()) {
      $t = imagecreatetruecolor($tw,$ty);
      imagecopyresampled($t, $img, 0,0,0,0, $tw,$ty, $size_x, $size_y);
    } else {
      $t = imagecreate($tw,$ty);
      $tikilib->ImageCopyResampleBicubic( $t, $img, 0,0,0,0, $tw,$ty, $size_x, $size_y);
    }
    // CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
    $tmpfname = tempnam ($tmpDir, "FOO").'.jpg';     
    imagejpeg($t,$tmpfname);
    // Now read the information
    $fp = fopen($tmpfname,"rb");
    $t_data = fread($fp, filesize($tmpfname));
    fclose($fp);
    unlink($tmpfname);
    $t_pinfo = pathinfo($tmpfname);
    $t_type = $t_pinfo["extension"];
    $t_type='image/'.$t_type;
    $tikilib->set_user_avatar($user,'u','',$name, $size, $t_type, $t_data);
  } else {
    $tikilib->set_user_avatar($user,'u','',$name, $size, $type, $data);    
  }
}  


if(isset($_REQUEST["uselib"])) {
  $tikilib->set_user_avatar($user,'l',$_REQUEST["avatar"],'','','','');
}

$avatars=Array();
$h=opendir("img/avatars/");
while($file=readdir($h)) {
  if($file!='.' && $file!='..' ) {
    $avatars[]='img/avatars/'.$file;
  }
}
closedir($h);
$smarty->assign_by_ref('avatars',$avatars);

include_once('tiki-mytiki_shared.php');

$avatar = $tikilib->get_user_avatar($user);
$smarty->assign('avatar',$avatar);

$smarty->assign('mid','tiki-pick_avatar.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
