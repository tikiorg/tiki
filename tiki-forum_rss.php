<?php
  require_once('tiki-setup.php');
  require_once('lib/tikilib.php');

if ($rss_forum != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

if(!isset($_REQUEST["forumId"])) {
        $errmsg=tra("No forumId specified");
        require_once ('tiki-rss_error.php');
}

require_once('lib/commentslib.php');
if (!isset($commentslib)) {
	$commentslib = new Comments($dbTiki);
}

$feed = "forum";
$tmp = $commentslib->get_forum($_REQUEST["forumId"]);
$title = tra("Tiki RSS feed for forum: ").$tmp["name"];
$desc = $tmp["description"];
$now = date("U");
$id = "forumId";
$descId = "data";
$dateId = "commentDate";
$titleId = "title";
$readrepl = "tiki-view_forum_thread.php?$id=";
$uniqueid = "$feed.$id=".$_REQUEST["$id"];

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib->list_forum_topics($_REQUEST["$id"],0, $max_rss_forum, $dateId.'_desc', '');
  $output = "";
}

require ("tiki-rss.php");

?>
