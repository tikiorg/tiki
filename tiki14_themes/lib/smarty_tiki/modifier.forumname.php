<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_forumname($commentid, $retrun_forumid = 'n') {
	global $tikilib, $cachelib;
	require_once 'lib/comments/commentslib.php';
	$comments = new Comments;
	
	if($retrun_forumid == 'y') {
		$cacheItem = "retrun_forumid". $commentid;
	} else {
		$cacheItem = "retrun_forumname". $commentid;
	}
	
	if ( $cached = $cachelib->getCached($cacheItem) ) {
		return $cached;
	}
	
	$forum_id = $comments->get_comment_forum_id($commentid);
	$cachelib->cacheItem($cacheItem, $forum_id);
	if ($retrun_forumid == 'y') {
		return $forum_id;
	}
	$ret = $comments->get_forum($forum_id);
	$cachelib->cacheItem($cacheItem, $ret['name']);
	return $ret['name'];
}
