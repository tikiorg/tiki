<?php
// Initialization
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

if($feature_banners != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

// CHECK FEATURE BANNERS AND ADMIN PERMISSION HERE
if(!isset($_REQUEST["bannerId"])) {
  $smarty->assign('msg',tra("No banner indicated"));
  $smarty->display('error.tpl');
  die;  
}

$info = $tikilib->get_banner($_REQUEST["bannerId"]);
if(!$info) {
  $smarty->assign('msg',tra("Banner not found"));
  $smarty->display('error.tpl');
  die;
}
// Check user is admin or the client
if( ($user != $info["client"]) && ($tiki_p_admin_banners != 'y') ){
  $smarty->assign('msg',tra("You dont have permission to edit this banner"));
  $smarty->display('error.tpl');
  die;
}
$smarty->assign('bannerId',$info["bannerId"]);
$smarty->assign('created',$info["created"]);
$smarty->assign('client',$info["client"]);
$smarty->assign('maxImpressions',$info["maxImpressions"]);
$impressions = $info["impressions"];
$clicks = $info["clicks"];
$smarty->assign('impressions',$impressions);
$smarty->assign('clicks',$clicks);
if($impressions) {
  $smarty->assign('ctr',$clicks/$impressions);
} else {
   $smarty->assign('ctr',0);
}
$smarty->assign('fromDate',$info["fromDate"]);
$smarty->assign('toDate',$info["toDate"]);
$smarty->assign('useDates',$info["useDates"]);
$smarty->assign("fromTime_h",substr($info["hourFrom"],0,2));
$smarty->assign("fromTime_m",substr($info["hourFrom"],2,2));
$smarty->assign("toTime_h",substr($info["hourTo"],0,2));
$smarty->assign("toTime_m",substr($info["hourTo"],2,2));
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
   $tmpfname = tempnam ("/tmp", "TMPIMG").$info["imageName"];     
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

$bannerId=$info["bannerId"];

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-view_banner","display_banner",$foo["path"]);


$fp=fopen(httpPrefix().$foo1."?id=$bannerId","r");
$raw='';
while(!feof($fp)) {
$raw .= fread($fp,8192);
}
fclose($fp);
$smarty->assign_by_ref('raw',$raw);

$smarty->assign('mid','tiki-view_banner.tpl');
$smarty->display('tiki.tpl');
?>
