<?php
// Initialization
require_once('tiki-setup.php');


if($feature_drawings != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display('error.tpl');
  die;
}

if($tiki_p_admin_drawings != 'y') {
  $smarty->assign('msg',tra("You dont have permission to use this feature"));
  $smarty->display('error.tpl');
  die;  
}


$pars=parse_url($_SERVER["REQUEST_URI"]);
    $pars_parts=split('/',$pars["path"]);
    $pars=Array();
    for($i=0;$i<count($pars_parts)-1;$i++) {
      $pars[]=$pars_parts[$i];
    }
$pars=join('/',$pars);
$smarty->assign('path',$pars);

// Get templates from the templates directory
$files=Array();
$h = opendir("img/wiki");
while (($file = readdir($h)) !== false) {
  if(strstr($file,'.gif')) {
    $files[]=substr($file,0,strlen($file)-4);
  }
}  
closedir($h);

sort($files);
$smarty->assign('files',$files);

// Get templates from the templates/modules directori


$smarty->assign('mid','tiki-admin_drawings.tpl');
$smarty->display('tiki.tpl');
?>