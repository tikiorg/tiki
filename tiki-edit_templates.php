<?php
// Initialization
require_once('tiki-setup.php');

if($feature_edit_templates != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if($tiki_p_edit_templates != 'y') {
  $smarty->assign('msg',tra("You dont have permission to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!isset($_REQUEST["mode"])) {
  $mode = 'listing';
}

// Validate to prevent editing any file
if(isset($_REQUEST["template"])) {
  if((substr($_REQUEST["template"],0,10)!='templates/')||(strstr($_REQUEST["template"],'..'))) {
     $smarty->assign('msg',tra("You dont have permission to do that"));
     $smarty->display('error.tpl');
     die;
       
  }

}

if(isset($_REQUEST["save"])) {
  $fp = fopen($_REQUEST["template"],"w");
  if(!$fp) {
    $smarty->assign('msg',tra("You dont have permission to write the template"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  fwrite($fp,$_REQUEST["data"]);
  fclose($fp);
}

if(isset($_REQUEST["template"])) {
  $mode = 'editing';
  $fp = fopen($_REQUEST["template"],"r");
  if(!$fp) {
    $smarty->assign('msg',tra("You dont have permission to read the template"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  $data = fread($fp,filesize($_REQUEST["template"]));
  fclose($fp);
  $smarty->assign('data',$data);
  $smarty->assign('template',$_REQUEST["template"]);
}

$smarty->assign('mode',$mode);

// Get templates from the templates directory
$files=Array();
$h = opendir("templates");
while (($file = readdir($h)) !== false) {
  if(strstr($file,'.tpl')) {
    $files[]="templates/".$file;
  }
}  
closedir($h);
$h = opendir("templates/modules/");
while (($file = readdir($h)) !== false) {
  if(strstr($file,'.tpl')) {
    $files[]="templates/modules/".$file;
  }
}  
closedir($h);
$h = opendir("templates/mail/");
while (($file = readdir($h)) !== false) {
  if(strstr($file,'.tpl')) {
    $files[]="templates/mail/".$file;
  }
}  
closedir($h);

sort($files);
$smarty->assign('files',$files);

// Get templates from the templates/modules directori


$smarty->assign('mid','tiki-edit_templates.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>