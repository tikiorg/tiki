<?php # $Header: /cvsroot/tikiwiki/tiki/select_banner.php,v 1.3 2003-03-21 18:55:19 lrargerich Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if(!isset($_REQUEST["zone"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
include_once('lib/banners/bannerlib.php');
$tikilib = new Tikilib($dbTiki);
$banner = $bannerlib->select_banner($_REQUEST["zone"]);
print($banner);
?>