<?php
// Initialization
require_once('tiki-setup.php');

/*
if($feature_file_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
*/
if($tiki_p_download_files != 'y') {
  $smarty->assign('msg',tra("You cant download files"));
  $smarty->display('error.tpl');
  die;  
}


if(!isset($_REQUEST["fileId"])) {
  $smarty->assign('msg',tra("No file"));
  $smarty->display('error.tpl');
  die;
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-browse_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-browse_image","show_image",$foo["path"]);
$smarty->assign('url_browse',$_SERVER["SERVER_NAME"].$foo1);
$smarty->assign('url_show',$_SERVER["SERVER_NAME"].$foo2);


$tikilib->add_file_hit($_REQUEST["fileId"]);
$info = $tikilib->get_file($_REQUEST["fileId"]);
$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];

//print("File:$file<br/>");
//die;
header("Content-type: $type");
header( "Content-Disposition: attachment; filename=$file" );
//header( "Content-Disposition: inline; filename=$file" );
echo "$content";    
?>