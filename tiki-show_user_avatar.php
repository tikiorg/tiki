<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-show_user_avatar.php,v 1.3 2003-03-21 15:48:16 lrargerich Exp $

include_once("tiki-setup_base.php");
include_once('lib/userprefs/userprefslib.php');

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if(!isset($_REQUEST["user"])) {
  die;
}

$info = $userprefslib->get_user_avatar_img($_REQUEST["user"]);
$type = $info["avatarFileType"];
$content = $info["avatarData"];

header("Content-type: $type");
echo "$content";    
?>