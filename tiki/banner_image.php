<?php # $Header: /cvsroot/tikiwiki/tiki/banner_image.php,v 1.3 2003-03-21 18:55:19 lrargerich Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if(!isset($_REQUEST["id"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
include_once('lib/banners/bannerlib.php');
$data = $bannerlib->get_banner($_REQUEST["id"]);
$type=$data["imageType"];
$data = $data["imageData"];
header("Content-type: $type");
echo $data;
?>