<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/userfiles/userfileslib.php');


if(!isset($_REQUEST["fileId"])) {
  die;
}
$info = $userfileslib->get_userfile($user,$_REQUEST["fileId"]);
$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];


header("Content-type: $type");
header( "Content-Disposition: inline; filename=$file" );
echo "$content";    
?>