<?php
// Initialization
require_once('tiki-setup_base.php');
include_once('lib/commentslib.php');

if ($tiki_p_forum_attach != 'y') {
  die;
}
if(!isset($_REQUEST["attId"])) {
  die;
}
$commentslib = new Comments($dbTiki);
$info = $commentslib->get_thread_attachment($_REQUEST["attId"]);

$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];

header("Content-type: $type");
header( "Content-Disposition: inline; filename=$file" );
if($info["dir"]) {
  readfile($info["dir"].$info["path"]);
} else {
  echo "$content";    
}
?>