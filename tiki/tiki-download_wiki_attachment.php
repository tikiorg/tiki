<?php
// Initialization
require_once('tiki-setup_base.php');

if($tiki_p_wiki_view_attachments !='y' && $tiki_p_wiki_admin_attachments != 'y') {
  die;  
}


if(!isset($_REQUEST["attId"])) {
  die;
}
$info = $tikilib->get_wiki_attachment($_REQUEST["attId"]);

$w_use_db=$tikilib->get_preference('w_use_db','y');
$w_use_dir=$tikilib->get_preference('w_use_dir','');




$tikilib->add_wiki_attachment_hit($_REQUEST["attId"]);

$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];


//print("File:$file<br/>");
//die;
header("Content-type: $type");
//header( "Content-Disposition: attachment; filename=$file" );
header( "Content-Disposition: inline; filename=$file" );
if($info["path"]) {
  readfile($w_use_dir.$info["path"]);
} else {
  echo "$content";    
}
?>