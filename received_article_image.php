<?php # $Header: /cvsroot/tikiwiki/tiki/received_article_image.php,v 1.3 2003-03-21 14:42:42 lrargerich Exp $
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if(!isset($_REQUEST["id"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
include_once('lib/commcenter/commlib.php');
$data = $commlib->get_received_article($_REQUEST["id"]);
$type=$data["image_type"];
$data = $data["image_data"];
header("Content-type: $type");
echo $data;
?>