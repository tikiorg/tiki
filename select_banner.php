<?php # $Header: /cvsroot/tikiwiki/tiki/select_banner.php,v 1.2 2003-01-04 19:34:16 rossta Exp $

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