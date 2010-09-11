<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_poll($params, &$smarty) {
	global $polllib, $dbTiki, $commentslib, $prefs;
	global $tiki_p_view_poll_results, $tiki_p_vote_poll;
    extract($params);
    // Param = zone
	if (!is_object($polllib)) {
		include_once('lib/polls/polllib_shared.php');
	}
	include_once('lib/comments/commentslib.php');

    if (isset($rate)) {
			if (!$tikilib->page_exists($rate)) {
				return false;
			}
		}
		if (empty($id)) { 
      $id = $polllib->get_random_poll("a");
    }
	if ($id == "current")
		$id = $polllib->get_random_poll("c");
	if ($tiki_p_view_poll_results != 'y' && $tiki_p_vote_poll != 'y') {
		return tra('You do not have permission to use this feature');
	}
    if($id) {
      $menu_info = $polllib->get_poll($id);
	if ($menu_info) {
      $channels = $polllib->list_poll_options($id);
			if ($prefs['feature_poll_comments'] == 'y') {
				$commentslib = new Comments($dbTiki);
				$comments_count = $commentslib->count_comments("poll:".$menu_info["pollId"]);
			} else
				$comments_count = 0;
      $smarty->assign('comments_cant', $comments_count);
      if ($tiki_p_view_poll_results == 'y')
		  $smarty->assign('ownurl','tiki-poll_results.php?pollId='.$id);
      $smarty->assign('menu_info',$menu_info);
      $smarty->assign('channels',$channels);
      $smarty->display('tiki-poll.tpl');
	}
    }
}
