<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/minical/minicallib.php');

if(!$user) die;
if(!isset($_REQUEST["topicId"])) {
  die;
}

$info = $minicallib->minical_get_topic($user,$_REQUEST["topicId"]);
$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];
header("Content-type: $type");
header( "Content-Disposition: inline; filename=$file" );
echo "$content";    
?>