<?
// Receive URI and id
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);

$tikilib->add_click($_REQUEST["id"]);
$url = urldecode($_REQUEST["url"]);
header("location: $url");
?>