<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-show_user_avatar.php,v 1.2 2003-01-04 19:34:16 rossta Exp $

include_once("tiki-setup_base.php");

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if(!isset($_REQUEST["user"])) {
  die;
}

$info = $tikilib->get_user_avatar_img($_REQUEST["user"]);
$type = $info["avatarFileType"];
$content = $info["avatarData"];

header("Content-type: $type");
echo "$content";    
?>