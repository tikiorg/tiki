<?php # $Header: /cvsroot/tikiwiki/tiki/banner_click.php,v 1.3 2003-03-21 18:55:19 lrargerich Exp $

// Receive URI and id
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
include_once('lib/banners/bannerlib.php');

$bannerlib->add_click($_REQUEST["id"]);
$url = urldecode($_REQUEST["url"]);
header("location: $url");
?>