<?php
  require_once('tiki-setup.php');
  require_once('lib/tikilib.php');

if ($rss_forum != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
	$smarty -> assign('msg', tra("Permission denied you can not view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

if(!isset($_REQUEST["forumId"])) {
	$smarty -> assign('msg', tra("No forumId specified"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: object not found
  die;
}

$tmp = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
$title = "Tiki RSS feed for forum: ".$tmp["name"]; // TODO: make configurable
$desc = $tmp["description"]; // TODO: make configurable
$now = date("U");
$id = "forumId";
$descId = "data";
$dateId = "commentDate";
$titleId = "title";
$readrepl = "tiki-view_forum_thread.php";
$changes = $tikilib->list_forum_topics($_REQUEST["$id"],0, $max_rss_forum, $dateId.'_desc', '');

require ("tiki-rss.php");

?>