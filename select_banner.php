<?
// show_image.php
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if(!isset($_REQUEST["zone"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
$banner = $tikilib->select_banner($_REQUEST["zone"]);
print($banner);
?>