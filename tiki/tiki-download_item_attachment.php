<?php
// Initialization
require_once('tiki-setup_base.php');

if($tiki_p_view_trackers !='y' ) {
  die;  
}


if(!isset($_REQUEST["attId"])) {
  die;
}
$info = $tikilib->get_item_attachment($_REQUEST["attId"]);

$t_use_db=$tikilib->get_preference('t_use_db','y');
$t_use_dir=$tikilib->get_preference('t_use_dir','');




$tikilib->add_item_attachment_hit($_REQUEST["attId"]);

$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];


//print("File:$file<br/>");
//die;
header("Content-type: $type");
//header( "Content-Disposition: attachment; filename=$file" );
header( "Content-Disposition: inline; filename=$file" );
if($info["path"]) {
  readfile($t_use_dir.$info["path"]);
} else {
  echo "$content";    
}
?>