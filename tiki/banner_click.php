<?php # $Header: /cvsroot/tikiwiki/tiki/banner_click.php,v 1.4 2003-04-04 21:30:57 lrargerich Exp $

// Receive URI and id
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
include_once('lib/banners/bannerlib.php');

if(!isset($bannerlib)) {
  $bannerlib = new BannerLib($dbTiki);
}

$bannerlib->add_click($_REQUEST["id"]);
$url = urldecode($_REQUEST["url"]);
header("location: $url");
?>