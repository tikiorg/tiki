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
$data = $tikilib->get_image($_REQUEST["id"]);
$tikilib->add_image_hit($_REQUEST["id"]);
if(!isset($_REQUEST["thumb"])) {
  $type=$data["filetype"];
  $data = $data["data"];
} else {
  if(!empty($data["t_data"])) {
    $type = $data["t_type"];
    $data = $data["t_data"];
  } else {
    $type=$data["filetype"];
    $data = $data["data"];
  }
}
header("Content-type: $type");
echo $data;
?>