<?php
// require_once("ggg-trace.php");
// $ggg_tracer->outln(__FILE__." line: ".__LINE__);

/* GGG
    The idea here is that all access to the hw tables goes through this
     library.
 */
include_once ('lib/tikilib.php');

class HomeworkLib extends TikiLib {
  // Constructor receiving a PEAR::Db database object.
  function HomeworkLib($db) {
	TikiLib::TikiLib($db);
  }

  function assignment_store($id,$dueDate,$data,$user,$ip,$description){
	//	global $ggg_tracer;
	//	$ggg_tracer->outln("$id = ".$id);
	//	$ggg_tracer->outln("$dueDate = ".$dueDate);
	//	$ggg_tracer->outln("$data = ".$data);
	//	$ggg_tracer->outln("$user = ".$user);
	//	$ggg_tracer->outln("$ip = ".$ip);
	//	$ggg_tracer->outln("$description = ".$description);
  }

  function create_assignment($name, $hits, $data, $lastModif, $comment, $user = 'system', $ip = '0.0.0.0', $description = '') {

	//	$ggg_tracer->outln("In create_assignment...");
	//	$ggg_tracer->outln("$name = ".$name);
	//	$ggg_tracer->outln("$hits = ".$hits);
	//	$ggg_tracer->outln("$data = ".$data);
	//	$ggg_tracer->outln("$lastModif = ".$lastModif);
	//	$ggg_tracer->outln("$comment = ".$comment);
	//	$ggg_tracer->outln("$user = ".$user);
	//	$ggg_tracer->outln("$ip = ".$ip);
	//	$ggg_tracer->outln("$description = ".$description);
//     global $smarty;
//     global $dbTiki;
//     global $notificationlib;
//     global $sender_email;
//     include_once ('lib/notifications/notificationlib.php');
//     include_once ("lib/commentslib.php");

//     $commentslib = new Comments($dbTiki);

//     // Collect pages before modifying data
//     $pages = $this->get_pages($data);
	
//     if ($this->page_exists($name))
// 	  return false;

//     $query = "insert into `tiki_pages`(`pageName`,`hits`,`data`,`lastModif`,`comment`,`version`,`user`,`ip`,`description`,`creator`,`page_size`) ";
//     $query.= " values(?,?,?,?,?,?,?,?,?,?,?)";
//     $result = $this->query($query, array(
// 		$name,
// 		(int)$hits,
// 		$data,
// 		(int)$lastModif,
// 		$comment,
// 		1,
// 		$user,
// 		$ip,
// 		$description,
// 		$user,
// 		(int)strlen($data)
// 		));

//     $this->clear_links($name);

//     // Pages are collected before adding slashes
//     foreach ($pages as $a_page) {
// 	  $this->replace_link($name, $a_page);
//     }

//     // Update the log
//     if ($name != 'SandBox') {
// 	$action = "Created";

// 	$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
// 	$result = $this->query($query, array(
// 		    $action,
// 		    $name,
// 		    (int)$lastModif,
// 		    $user,
// 		    $ip,
// 		    $comment
// 		    ));
//     }

//     $emails = $notificationlib->get_mail_events('wiki_page_changes', '*');

//     foreach ($emails as $email) {
// 	$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

// 	$smarty->assign('mail_page', $name);
// 	$smarty->assign('mail_date', date("U"));
// 	$smarty->assign('mail_user', $user);
// 	$smarty->assign('mail_comment', $comment);
// 	$smarty->assign('mail_last_version', 1);
// 	$smarty->assign('mail_data', $data);
// 	$foo = parse_url($_SERVER["REQUEST_URI"]);
// 	$machine = httpPrefix(). dirname( $foo["path"] );
// 	$smarty->assign('mail_machine', $machine);
// 	$smarty->assign('mail_pagedata', $data);
// 	$mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');

// 	if( $this->get_preference('wiki_forum') )
// 	{
// 	    $forums = $commentslib->list_forums( 0, 1,
// 		    'name_asc',
// 		    $this->get_preference('wiki_forum') );
//     if ($forums) {
// 	    $forumEmail = $forums["data"][0]["outbound_from"];

// 	    @mail($email, $name, $mail_data,
// 		    "From: $forumEmail\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		 );
//     }
// 	} else {
// 	    @mail($email, tra('Wiki page'). ' ' . $name . '
// 		    ' . tra('changed'), $mail_data,
// 		    "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		 );
// 	}
//     }

    return true;
}

function update_assignment($pageName, $edit_data, $edit_comment, $edit_user, $edit_ip, $description = '', $minor = false) {
  //  global $ggg_tracer;
  //  $ggg_tracer->outln("In update_assignment...");
  //  $ggg_tracer->outln("$pageName = ".$pageName);
  //  $ggg_tracer->outln("$edit_data = ".$edit_data);
  //  $ggg_tracer->outln("$edit_comment = ".$edit_comment);
  //  $ggg_tracer->outln("$edit_user = ".$edit_user);
  //  $ggg_tracer->outln("$edit_ip = ".$edit_ip);
  //  $ggg_tracer->outln("$description = ".$description);
  //  $ggg_tracer->outln("$minor = ".$minor);
//     global $smarty;

//     global $dbTiki;
//     global $notificationlib;
//     global $feature_user_watches;
//     global $wiki_watch_author;
//     global $wiki_watch_comments;
//     global $wiki_watch_editor;
//     global $sender_email;
//     //include_once ('lib/notifications/notificationlib.php');
//     include_once ("lib/commentslib.php");

//     $commentslib = new Comments($dbTiki);

//     $this->invalidate_cache($pageName);
//     // Collect pages before modifying edit_data (see update of links below)
//     $pages = $this->get_pages($edit_data);

//     if (!$this->page_exists($pageName))
// 	return false;

//     $t = date("U");
//     // Get this page information
//     $info = $this->get_page_info($pageName);
//     // Store the old version of this page in the history table
//     $version = $info["version"];
//     $lastModif = $info["lastModif"];
//     $user = $info["user"];
//     $ip = $info["ip"];
//     $comment = $info["comment"];
//     $data = $info["data"];
//     // WARNING: POTENTIAL BUG
//     // The line below is not consistent with the rest of Tiki
//     // (I commented it out so it can be further examined by CVS change control)
//     //$pageName=addslashes($pageName);
//     // But this should work (comment added by redflo):
//     $version += 1;

//     if (!$minor) {
// 	$query = "insert into `tiki_history`(`pageName`, `version`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`)
// 	    values(?,?,?,?,?,?,?,?)";

// # echo "<pre>";print_r(get_defined_vars());echo "</pre>";die();
// 	if ($pageName != 'SandBox') {
// 	    $result = $this->query($query,array($pageName,(int) $version,(int) $lastModif,$user,$ip,$comment,$data,$description));
// 	}
// 	// Update the pages table with the new version of this page

// 	$emails = $notificationlib->get_mail_events('wiki_page_changes', 'wikipage' . $pageName);

// 	foreach ($emails as $email) {
// 	    $smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

// 	    $smarty->assign('mail_page', $pageName);
// 	    $smarty->assign('mail_date', date("U"));
// 	    $smarty->assign('mail_user', $edit_user);
// 	    $smarty->assign('mail_comment', $edit_comment);
// 	    $smarty->assign('mail_last_version', $version);
// 	    $smarty->assign('mail_data', $edit_data);
// 	    $foo = parse_url($_SERVER["REQUEST_URI"]);
// 	    $machine = httpPrefix(). dirname( $foo["path"] );
// 	    $smarty->assign('mail_machine', $machine);
// 	    $smarty->assign('mail_pagedata', $edit_data);
// 	    $mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');

// 	    if( $this->get_preference('wiki_forum') )
// 	    {
// 		$forums = $commentslib->list_forums( 0, 1,
// 			'name_asc',
// 			$this->get_preference('wiki_forum') );

// 		$forumEmail = $forums["data"][0]["outbound_from"];

// 		@mail($email, $pageName, $mail_data,
// 			"From: $forumEmail\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		     );
// 	    } else {
// 		@mail($email, tra('Wiki page'). ' ' . $pageName . '
// 			' . tra('changed'), $mail_data,
// 			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n"
// 		     );
// 	    }
// 	}

// 	if ($feature_user_watches == 'y') {
// 	    $nots = $this->get_event_watches('wiki_page_changed', $pageName);

// 	    foreach ($nots as $not) {
// 		if ($wiki_watch_editor != 'y' && $not['user'] == $user) break;
// 		$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

// 		$smarty->assign('mail_page', $pageName);
// 		$smarty->assign('mail_date', date("U"));
// 		$smarty->assign('mail_user', $edit_user);
// 		$smarty->assign('mail_comment', $edit_comment);
// 		$smarty->assign('mail_last_version', $version);
// 		$smarty->assign('mail_data', $edit_data);
// 		$smarty->assign('mail_hash', $not['hash']);
// 		$foo = parse_url($_SERVER["REQUEST_URI"]);
// 		$machine = httpPrefix(). dirname( $foo["path"] );
// 		$smarty->assign('mail_machine', $machine);
// 		$parts = explode('/', $foo['path']);

// 		if (count($parts) > 1)
// 		    unset ($parts[count($parts) - 1]);

// 		$smarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
// 		$smarty->assign('mail_pagedata', $edit_data);
// 		$mail_data = $smarty->fetch('mail/user_watch_wiki_page_changed.tpl');
// 		@mail($not['email'], tra('Wiki page'). ' ' . $pageName . ' ' . tra('changed'), $mail_data, "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
// 	    }
// 	}
//     }

//     $query = "update `tiki_pages` set `description`=?, `data`=?, `comment`=?, `lastModif`=?, `version`=?, `user`=?, `ip`=?, `page_size`=? where `pageName`=?";
//     $result = $this->query($query,array($description,$edit_data,$edit_comment,(int) $t,$version,$edit_user,$edit_ip,(int)strlen($data),$pageName));
//     // Parse edit_data updating the list of links from this page
//     $this->clear_links($pageName);

//     // Pages collected above
//     foreach ($pages as $page) {
// 	$this->replace_link($pageName, $page);
//     }

//     // Update the log
//     if ($pageName != 'SandBox' && !$minor) {
// 	$action = "Updated";

// 	$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
// 	$result = $this->query($query,array($action,$pageName,(int) $t,$edit_user,$edit_ip,$edit_comment));
// 	$maxversions = $this->get_preference("maxVersions", 0);

// 	if ($maxversions) {
// 	    // Select only versions older than keep_versions days
// 	    $keep = $this->get_preference('keep_versions', 0);

// 	    $now = date("U");
// 	    $oktodel = $now - ($keep * 24 * 3600);
// 	    $query = "select `pageName` ,`version` from `tiki_history` where `pageName`=? and `lastModif`<=? order by `lastModif` desc";
// 	    $result = $this->query($query,array($pageName,$oktodel),-1,$maxversions);
// 	    $toelim = $result->numRows();

// 	    while ($res = $result->fetchRow()) {
// 		$page = $res["pageName"];

// 		$version = $res["version"];
// 		$query = "delete from `tiki_history` where `pageName`=? and `version`=?";
// 		$this->query($query,array($pageName,$version));
// 	    }
// 	}
//     }
}

// adaped from get_article in lib/tikilib.php
function get_assignment($articleId) {
    $query = "select `hw_assignments`.*,
	`users_users`.`avatarLibName`,
	`tiki_article_types`.`use_ratings`,
	`tiki_article_types`.`show_pre_publ`,
	`tiki_article_types`.`show_post_expire`,
	`tiki_article_types`.`heading_only`,
	`tiki_article_types`.`allow_comments`,
	`tiki_article_types`.`comment_can_rate_article`,		
	`tiki_article_types`.`show_image`,
	`tiki_article_types`.`show_avatar`,
	`tiki_article_types`.`show_author`,
	`tiki_article_types`.`show_pubdate`,
	`tiki_article_types`.`show_expdate`,
	`tiki_article_types`.`show_reads`,
	`tiki_article_types`.`show_size`,
	`tiki_article_types`.`creator_edit`
	from `hw_assignments`, `tiki_article_types`, `users_users` where `hw_assignments`.`articleId`=?";
    //$query = "select * from `tiki_articles` where `articleId`=?";
    $result = $this->query($query,array((int)$articleId));
//    	global $ggg_tracer;
//  	$ggg_tracer->outln(__FILE__." line: ".__LINE__);
// 	$ggg_tracer->outln('$result = '.$result);
// 	$ggg_tracer->outln('$result->numRows() = '.$result->numRows());
    if ($result->numRows()) {
//  	$ggg_tracer->outln(__FILE__." line: ".__LINE__." Found an assignment.");
	$res = $result->fetchRow();
	$res["entrating"] = floor($res["rating"]);
    } else {
//  	$ggg_tracer->outln(__FILE__." line: ".__LINE__." Failed to find an assignment.");
	return false;
    }
    return $res;
}

 function replace_assignment($title, $authorName, $topicId, $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $user, $articleId, $image_x, $image_y, $type, $rating = 0, $isfloat = 'n') {
   
//    global $ggg_tracer;
//    $ggg_tracer->outln(__FILE__." line: ".__LINE__);

   if ($expireDate < $publishDate) {
     $expireDate = $publishDate;
   }
   $hash = md5($title . $heading . $body);
   $now = date("U");
   if(empty($imgdata)) $imgdata='';
   // Fixed query. -rlpowell
   $query = "select `name`  from `tiki_topics` where `topicId` = ?";
   $topicName = $this->getOne($query, array($topicId) );
   $size = strlen($body);

   // Fixed query. -rlpowell
   if ($articleId) {
     // Update the article
     $query = "update `hw_assignments` set `title` = ?, `authorName` = ?, `topicId` = ?, `topicName` = ?, `size` = ?, `useImage` = ?, `image_name` = ?, ";
     $query.= " `image_type` = ?, `image_size` = ?, `image_data` = ?, `isfloat` = ?, `image_x` = ?, `image_y` = ?, `heading` = ?, `body` = ?, ";
     $query.= " `publishDate` = ?, `expireDate` = ?, `created` = ?, `author` = ?, `type` = ?, `rating` = ?  where `articleId` = ?";

     $result = $this->query($query, array(
       $title, $authorName, (int) $topicId, $topicName, (int) $size, $useImage, $imgname, $imgtype, (int) $imgsize, $imgdata, $isfloat,
       (int) $image_x, (int) $image_y, $heading, $body, (int) $publishDate, (int) $expireDate, (int) $now, $user, $type, (float) $rating, (int) $articleId ) );
   } else {
     // Fixed query. -rlpowell
     // Insert the article
     $query = "insert into `hw_assignments` (`title`, `authorName`, `topicId`, `useImage`, `image_name`, `image_size`, `image_type`, `image_data`, ";
     $query.= " `publishDate`, `expireDate`, `created`, `heading`, `body`, `hash`, `author`, `reads`, `votes`, `points`, `size`, `topicName`, `image_x`, `image_y`, `type`, `rating`, `isfloat`) ";
     $query.= " values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

     $result = $this->query($query, array(
       $title, $authorName, (int) $topicId, $useImage, $imgname, (int) $imgsize, $imgtype, $imgdata, (int) $publishDate, (int) $expireDate, (int) $now, $heading,
       $body, $hash, $user, 0, 0, 0, (int) $size, $topicName, (int) $image_x, (int) $image_y, $type, (float) $rating, $isfloat));

       // Fixed query. -rlpowell
       $query2 = "select max(`articleId`) from `tiki_articles` where `created` = ? and `title`=? and `hash`=?";
       $articleId = $this->getOne($query2, array( (int) $now, $title, $hash ) );
   }
   
   return $articleId;
 }

} //end of class HomeworkLib


?>
