<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/userfiles/userfileslib.php');


if(!isset($_REQUEST["fileId"])) {
  die;
}
$uf_use_db=$tikilib->get_preference('uf_use_db','y');
$uf_use_dir=$tikilib->get_preference('uf_use_dir','');

$info = $userfileslib->get_userfile($user,$_REQUEST["fileId"]);
$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];

header("Content-type: $type");
header( "Content-Disposition: inline; filename=$file" );
if($info["path"]) {
  readfile($uf_use_dir.$info["path"]);
} else {
  echo "$content";    
}
?>