<?
// show_image.php
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if(!isset($_REQUEST["id"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
$data = $tikilib->get_received_article($_REQUEST["id"]);
$type=$data["image_type"];
$data = $data["image_data"];
header("Content-type: $type");
echo $data;
?>