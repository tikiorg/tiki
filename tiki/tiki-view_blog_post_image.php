<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/blogs/bloglib.php');

if(!$user) die;
if(!isset($_REQUEST["imgId"])) {
  die;
}

$info = $bloglib->get_post_image($_REQUEST["imgId"]);
$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];
header("Content-type: $type");
header( "Content-Disposition: inline; filename=$file" );
echo "$content";    
?>