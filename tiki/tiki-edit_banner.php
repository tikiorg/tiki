<?php
// Initialization
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

// CHECK FEATURE BANNERS AND ADMIN PERMISSION HERE
if($feature_banners != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_admin_banners != 'y') {
  $smarty->assign('msg',tra("You dont have permissions to edit banners"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(isset($_REQUEST["bannerId"]) && $_REQUEST["bannerId"]>0) {
  $info = $tikilib->get_banner($_REQUEST["bannerId"]);
  if(!$info) {
    $smarty->assign('msg',tra("Banner not found"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  // Check user is admin or the client
  if( ($user != $info["client"]) && ($tiki_p_admin_banners != 'y') ){
    $smarty->assign('msg',tra("You dont have permission to edit this banner"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  $smarty->assign('bannerId',$info["bannerId"]);
  $smarty->assign('client',$info["client"]);
  $smarty->assign('maxImpressions',$info["maxImpressions"]);
  $smarty->assign('fromDate',$info["fromDate"]);
  $smarty->assign('toDate',$info["toDate"]);
  $smarty->assign('useDates',$info["useDates"]);
  $smarty->assign("fromTime",$info["hourFrom"]);
  $smarty->assign("toTime",$info["hourTo"]);
  $smarty->assign("Dmon",$info["mon"]);
  $smarty->assign("Dtue",$info["tue"]);
  $smarty->assign("Dwed",$info["wed"]);
  $smarty->assign("Dthu",$info["thu"]);
  $smarty->assign("Dfri",$info["fri"]);
  $smarty->assign("Dsat",$info["sat"]);
  $smarty->assign("Dsun",$info["sun"]);
  $smarty->assign("use",$info["which"]);
  $smarty->assign("zone",$info["zone"]);
  $smarty->assign("HTMLData",$info["HTMLData"]);
  $smarty->assign("fixedURLdata",$info["fixedURLData"]);
  $smarty->assign("textData",$info["textData"]);
  $smarty->assign("url",$info["url"]);
  $smarty->assign("imageName",$info["imageName"]);
  $smarty->assign("imageData",urlencode($info["imageData"]));
  $smarty->assign("imageType",$info["imageType"]);
  $smarty->assign("hasImage",'n');
  if(strlen($info["imageData"])>0) {
     $tmpfname = tempnam ($tmpDir, "TMPIMG").$info["imageName"];     
     $fp = fopen($tmpfname,"w");
     if($fp) {
       fwrite($fp,urldecode($info["imageData"]));
       fclose($fp);
       $smarty->assign('tempimg',$tmpfname);
       $smarty->assign('hasImage','y');
     } else {
       $smarty->assign('hasImage','n');
     }
  }
  
} else {
  $smarty->assign('client','');
  $smarty->assign('maxImpressions',1000);
  $now=date("U");
  $smarty->assign('fromDate',$now);
  $smarty->assign('toDate',$now+(365*24*3600));
  $smarty->assign('useDates','n');
  $smarty->assign('fromTime','0000');
  $smarty->assign('toTime','2359');
  // Variables for dates are fromDate_ and toDate_ plus fromTime_ and toTime_
  $smarty->assign('Dmon','y');
  $smarty->assign('Dtue','y');
  $smarty->assign('Dwed','y');
  $smarty->assign('Dthu','y');
  $smarty->assign('Dfri','y');
  $smarty->assign('Dsat','y');
  $smarty->assign('Dsun','y');
  $smarty->assign('bannerId',0);
  $smarty->assign('zone','');
  $smarty->assign('use','useHTML');
  $smarty->assign('HTMLData','');
  $smarty->assign('fixedURLData','');
  $smarty->assign('textData','');
  $smarty->assign('url','');
  $smarty->assign('imageData','');
  $smarty->assign('hasImage','n');
  $smarty->assign('imageName','');
  $smarty->assign('imageType','');
}

if(isset($_REQUEST["removeZone"])) {
  $tikilib->banner_remove_zone($_REQUEST["removeZone"]);
}

// Now assign if the set button was pressed
if(isset($_REQUEST["save"]) || isset($_REQUEST["create_zone"])) {
  $fromDate = mktime(0,0,0,$_REQUEST["fromDate_Month"],$_REQUEST["fromDate_Day"],$_REQUEST["fromDate_Year"]);
  $toDate = mktime(0,0,0,$_REQUEST["toDate_Month"],$_REQUEST["toDate_Day"],$_REQUEST["toDate_Year"]);
  $fromTime = $_REQUEST["fromTimeHour"].$_REQUEST["fromTimeMinute"];
  $toTime = $_REQUEST["toTimeHour"].$_REQUEST["toTimeMinute"];
  $smarty->assign('fromDate',$fromDate);
  $smarty->assign('toDate',$toDate);
  $smarty->assign('fromTime',$fromTime);
  $smarty->assign('toTime',$toTime);
  $smarty->assign('client',$_REQUEST["client"]);
  $smarty->assign('maxImpressions',$_REQUEST["maxImpressions"]);
  $smarty->assign('HTMLData',$_REQUEST["HTMLData"]);
  $smarty->assign('fixedURLData',$_REQUEST["fixedURLData"]);
  $smarty->assign('textData',$_REQUEST["textData"]);
  if(isset($_REQUEST["zone"])) {
    $smarty->assign('zone',$_REQUEST["zone"]);
  } else {
    $smarty->assign('zone','');
  }
  if(substr($_REQUEST["url"],0,4)!='http') {
    $_REQUEST["url"]=httpScheme().'://'.$_REQUEST["url"];
  }
  $smarty->assign('url',$_REQUEST["url"]);
  if(isset($_REQUEST["use"])) {
    $smarty->assign('use',$_REQUEST["use"]);
  }
  
  if(isset($_REQUEST["useDates"]) && $_REQUEST["useDates"]=='on') {
    $smarty->assign('useDates','y');
    $useDates = 'y';
  } else {
    $smarty->assign('useDates','n');
    $useDates = 'n';
  }
  if(isset($_REQUEST["Dmon"]) && $_REQUEST["Dmon"]=='on') {
    $smarty->assign('Dmon','y');
    $Dmon = 'y';
  } else {
    $smarty->assign('Dmon','n');
    $Dmon = 'n';
  }
  if(isset($_REQUEST["Dtue"]) && $_REQUEST["Dtue"]=='on') {
    $smarty->assign('Dtue','y');
    $Dtue = 'y';
  } else {
    $smarty->assign('Dtue','n');
    $Dtue = 'n';
  }
  if(isset($_REQUEST["Dwed"]) && $_REQUEST["Dwed"]=='on') {
    $smarty->assign('Dwed','y');
    $Dwed = 'y';
  } else {
    $smarty->assign('Dwed','n');
    $Dwed = 'n';
  }
  if(isset($_REQUEST["Dthu"]) && $_REQUEST["Dthu"]=='on') {
    $smarty->assign('Dthu','y');
    $Dthu = 'y';
  } else {
    $smarty->assign('Dthu','n');
    $Dthu = 'n';
  }
  if(isset($_REQUEST["Dfri"]) && $_REQUEST["Dfri"]=='on') {
    $smarty->assign('Dfri','y');
    $Dfri = 'y';
  } else {
    $smarty->assign('Dfri','n');
    $Dfri = 'n';
  }
  if(isset($_REQUEST["Dsat"]) && $_REQUEST["Dsat"]=='on') {
    $smarty->assign('Dsat','y');
    $Dsat = 'y';
  } else {
    $smarty->assign('Dsat','n');
    $Dsat = 'n';
  }
  if(isset($_REQUEST["Dsun"]) && $_REQUEST["Dsun"]=='on') {
    $smarty->assign('Dsun','y');
    $Dsun = 'y';
  } else {
    $smarty->assign('Dsun','n');
    $Dsun = 'n';
  }
  $smarty->assign('bannerId',$_REQUEST["bannerId"]);
  if(isset($_REQUEST["create_zone"])) {
    $tikilib->banner_add_zone($_REQUEST["zoneName"]);
  }
  
  // If we have an upload then process the upload and setup the data in a field
  // that will be hidden is this is a nightmare?
  $imgname = $_REQUEST["imageName"];
  $imgtype = $_REQUEST["imageType"];
  if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
    $data = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
    fclose($fp);
    $imgtype = $_FILES['userfile1']['type'];
    $imgsize = $_FILES['userfile1']['size'];
    $imgname = $_FILES['userfile1']['name'];
    $smarty->assign('imageData',urlencode($data));
    $smarty->assign('imageName',$imgname);
    $smarty->assign('imageType',$imgtype);
    $_REQUEST["imageData"]=urlencode($data);
    $_REQUEST["imageName"]=$imgname;
    $_REQUEST["imageType"]=$imgtype;
  }
  $smarty->assign('imageData',$_REQUEST["imageData"]);
  $smarty->assign('tempimg','n');  
  if(strlen($_REQUEST["imageData"])>0) {
     $tmpfname = tempnam ($tmpDir , "TMPIMG").$imgname;     
     $fp = fopen($tmpfname,"w");
     if($fp) {
       fwrite($fp,urldecode($_REQUEST["imageData"]));
       fclose($fp);
       $smarty->assign('tempimg',$tmpfname);
       $smarty->assign('hasImage','y');
     } else {
       $smarty->assign('hasImage','n');
     }
  }
  if(!isset($_REQUEST["create_zone"])) {
    $bannerId = $tikilib->replace_banner($_REQUEST["bannerId"], $_REQUEST["client"], $_REQUEST["url"], '', '', $_REQUEST["use"], $_REQUEST["imageData"],$_REQUEST["imageType"],$_REQUEST["imageName"],
                          $_REQUEST["HTMLData"], $_REQUEST["fixedURLData"], $_REQUEST["textData"], $fromDate, $toDate, $useDates, 
                          $Dmon, $Dtue, $Dwed, $Dthu, $Dfri, $Dsat, $Dsun,
                          $fromTime, $toTime, $_REQUEST["maxImpressions"],$_REQUEST["zone"]);
    $smarty->assign('bannerId',$bannerId);
  }
}


$zones = $tikilib->banner_get_zones();
$smarty->assign_by_ref('zones',$zones);
$clients = $userlib->get_users(0,-1,'login_desc', '');
$smarty->assign_by_ref('clients',$clients["data"]);


// Display the template
$smarty->assign('mid','tiki-edit_banner.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
