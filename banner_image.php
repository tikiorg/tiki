<?php # $Header: /cvsroot/tikiwiki/tiki/banner_image.php,v 1.2 2003-01-04 19:34:15 rossta Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if(!isset($_REQUEST["id"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
$data = $tikilib->get_banner($_REQUEST["id"]);
$type=$data["imageType"];
$data = $data["imageData"];
header("Content-type: $type");
echo $data;
?>