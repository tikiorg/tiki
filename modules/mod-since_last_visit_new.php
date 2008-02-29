<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once('lib/smarty_tiki/modifier.userlink.php');

if (!function_exists('mod_since_last_visit_new_help')) {
	function mod_since_last_visit_new_help() {
		return "showuser=n&showtracker=n&calendar_focus=ignore";
	}
}

if (!function_exists('since_last_visit_new')) {
function since_last_visit_new($user, $params = null) {
  if (!$user) return false;

  global $tikilib, $userlib, $prefs;
  $ret = array();
  $ret["label"] = tra("Since your last visit");
  if ( $params == null ) $params = array();

  if ((empty($params['calendar_focus']) || $params['calendar_focus'] != 'ignore') && strpos($_SERVER["SCRIPT_NAME"],"tiki-calendar.php") && isset($_REQUEST["todate"]) && $_REQUEST["todate"]) {
    $last = $_REQUEST["todate"];
    $_SESSION["slvn_last_login"] = $last;
    $ret["label"] = tra("Changes")." ".tra("since");
  }
  else if (isset($_SESSION["slvn_last_login"])) {
    $last = $_SESSION["slvn_last_login"];
    $ret["label"] = tra("Changes since");
  }
  else {
    $last = $tikilib->getOne("select `lastLogin`  from `users_users` where `login`=?",array($user));
    if (!$last) $last = time();
  }
  $ret["lastLogin"] = $last;
  //if (!isset($_SESSION["slvn_last_login"])) $_SESSION["slvn_last_login"] = $last;
  //$last = strtotime ("-2 week");

    $ret["items"]["comments"]["label"] = tra("new comments");
    $ret["items"]["comments"]["cname"] = "slvn_comments_menu";
	$query = "select `object`,`objectType`,`title`,`commentDate`,`userName`,`threadId`, `parentId` from `tiki_comments` where `commentDate`>? and `objectType` != 'forum' order by `commentDate` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
      switch($res["objectType"]){
        case "article":
		$perm = 'tiki_p_read_article';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-read_article.php?articleId=" . $res["object"];
          break;
        case "post":
		$perm = 'tiki_p_read_blog';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-view_blog_post.php?postId=" . $res["object"];
          break;
        case "blog":
		$perm = 'tiki_p_read_blog';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-view_blog.php?blogId=" . $res["object"];
          break;
        case "faq":
		$perm = 'tiki_p_view_faqs';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-view_faq.php?faqId=" . $res["object"];
          break;
        case "file gallery":
		$perm = 'tiki_p_view_file_gallery';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-list_file_gallery.php?galleryId=" . $res["object"];
          break;
        case "image gallery":
		$perm = 'tiki_p_view_image_gallery';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-browse_gallery.php?galleryId=" . $res["object"];
          break;
        case "poll":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-poll_results.php?pollId=" . $res["object"];
          break;
        case "wiki page":
		$perm = 'tiki_p_view';
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-index.php?page=" . urlencode($res["object"]);
          break;
         default:
            $perm = 'tiki_p_read_comments';
            break;
      }
	if (!isset($perm) || $userlib->user_has_perm_on_object($user,$res['object'], $res['objectType'], $perm)) {
				if (isset($ret["items"]["comments"]["list"][$count]["href"])) {
					$ret["items"]["comments"]["list"][$count]["href"] .= '&amp;comments_show=y#threadId'.$res['threadId'];
				}
      	$ret["items"]["comments"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["commentDate"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["userName"])));
      	$ret["items"]["comments"]["list"][$count]["label"] = $res["title"]; 
      	$count++;
	}
    }
    $ret["items"]["comments"]["count"] = $count;

 if ($prefs['feature_forums'] == 'y') {
    $ret["items"]["posts"]["label"] = tra("new posts");
    $ret["items"]["posts"]["cname"] = "slvn_posts_menu";
    $query = "select `object`,`objectType`,`title`,`commentDate`,`userName`,`threadId`, `parentId` from `tiki_comments` where `commentDate`>? and `objectType` = 'forum' order by `commentDate` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
       if ($userlib->user_has_perm_on_object($user,$res['object'], $res['objectType'], 'tiki_p_forum_read')) {
          $ret["items"]["posts"]["list"][$count]["href"]
            = "tiki-view_forum_thread.php?forumId=" . $res["object"] . "&comments_parentId=";
	  if ($res["parentId"]) {
          	$ret["items"]["posts"]["list"][$count]["href"].=$res["parentId"].'#threadId'.$res['threadId'];
	  } else {
          	$ret["items"]["posts"]["list"][$count]["href"].=$res["threadId"];
	  }
      	$ret["items"]["posts"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["commentDate"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["userName"])));
      	$ret["items"]["posts"]["list"][$count]["label"] = $res["title"]; 
        ++$count;	
	 }
    }
    $ret["items"]["posts"]["count"] = $count;
 }

  if ($prefs['feature_wiki'] == 'y') {
    // && $tikilib->getOne("select count(*) from `tiki_pages` where `lastModif`>?",array((int)$last))!=0) {    
    $ret["items"]["pages"]["label"] = tra("wiki pages changed");
    $ret["items"]["pages"]["cname"] = "slvn_pages_menu";
    $query = "select `pageName`, `user`, `lastModif`  from `tiki_pages` where `lastModif`>? order by `lastModif` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['pageName'], 'wiki page', 'tiki_p_view')) {
           $ret["items"]["pages"]["list"][$count]["href"]  = "tiki-index.php?page=" . urlencode($res["pageName"]);
           $ret["items"]["pages"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["lastModif"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["pages"]["list"][$count]["label"] = $res["pageName"]; 
           $count++;
       }
    }
    $ret["items"]["pages"]["count"] = $count;
  }

  if ($prefs['feature_articles'] == 'y' ) {    
    $ret["items"]["articles"]["label"] = tra("new articles");
    $ret["items"]["articles"]["cname"] = "slvn_articles_menu";

    if($userlib->user_has_permission($user, "tiki_p_edit_article")) {
      $query = "select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `created`>? and `expireDate`>?";
	  $bindvars = array((int)$last,time());
    }else {
      $query = "select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `publishDate`>? and `publishDate`<=? and `expireDate`>?";
	  $bindvars = array((int)$last,time(),time());
    }
    $result = $tikilib->query($query, $bindvars);

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['articleId'], 'article', 'tiki_p_read_article')) {
           $ret["items"]["articles"]["list"][$count]["href"]  = "tiki-read_article.php?articleId=" . $res["articleId"];
           $ret["items"]["articles"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["publishDate"]) ." ". tra("by") ." ". $res["authorName"];
           $ret["items"]["articles"]["list"][$count]["label"] = $res["title"]; 
           $count++;
        }
    }
    $ret["items"]["articles"]["count"] = $count;
  }
  
  if ($prefs['feature_submissions'] == 'y' && $userlib->user_has_permission($user, "tiki_p_edit_submission")) {    
    $ret["items"]["submissions"]["label"] = tra("new submissions");
    $ret["items"]["submissions"]["cname"] = "slvn_submissions_menu";

    $query = "select `subId`,`title`,`publishDate`,`authorName` from `tiki_submissions` where `created`>? and `expireDate`>?";
	$bindvars = array((int)$last,time());
    
    $result = $tikilib->query($query, $bindvars);

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['subId'], 'submission', 'tiki_p_edit_submission')) {
           $ret["items"]["submissions"]["list"][$count]["href"]  = "tiki-edit_submission.php?subId=" . $res["subId"];
           $ret["items"]["submissions"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["publishDate"]) ." ". tra("by") ." ". $res["authorName"];
           $ret["items"]["submissions"]["list"][$count]["label"] = $res["title"]; 
           $count++;
        }
    }
    $ret["items"]["submissions"]["count"] = $count;
  }

  if ($prefs['feature_faqs'] == 'y') {    
    $ret["items"]["faqs"]["label"] = tra("new FAQs");
    $ret["items"]["faqs"]["cname"] = "slvn_faqs_menu";

    $query = "select `faqId`, `title`, `created`  from `tiki_faqs` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['faqId'], 'faq', 'tiki_p_view_faqs')) {
           $ret["items"]["faqs"]["list"][$count]["href"]  = "tiki-view_faq.php?faqId=" . $res["faqId"];
           $ret["items"]["faqs"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]);
           $ret["items"]["faqs"]["list"][$count]["label"] = $res["title"]; 
           $count++;
        }
    }
    $ret["items"]["faqs"]["count"] = $count;
  }

// directories

if ($prefs['feature_directory'] == 'y') {    
    $ret["items"]["dirs"]["label"] = tra("new sites");
    $ret["items"]["dirs"]["cname"] = "slvn_dirs_menu";

    $query = "select `siteId`, `name`, `lastModif`  from `tiki_directory_sites` where lastModif>? order by `lastModif` desc";
    $result = $tikilib->query($query, array((int)$last));
    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['siteId'], 'directory', 'tiki_p_view_directory')) {
           $ret["items"]["dirs"]["list"][$count]["href"]  = "tiki-directory_redirect.php?siteId=" . $res["siteId"];
           $ret["items"]["dirs"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["lastModif"]);
           $ret["items"]["dirs"]["list"][$count]["label"] = $res["name"]; 
           $count++;
        }
    }
    $ret["items"]["dirs"]["count"] = $count;
  }

  if ($prefs['feature_blogs'] == 'y') {    
    $ret["items"]["blogs"]["label"] = tra("new blogs");
    $ret["items"]["blogs"]["cname"] = "slvn_blogs_menu";

    $query = "select `blogId`, `title`, `user`, `created`  from `tiki_blogs` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['blogId'], 'blog', 'tiki_p_read_blog')) {
           $ret["items"]["blogs"]["list"][$count]["href"]  = "tiki-view_blog.php?blogId=" . $res["blogId"];
           $ret["items"]["blogs"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["blogs"]["list"][$count]["label"] = $res["title"]; 
           $count++;
       }
    }


    $ret["items"]["blogs"]["count"] = $count;

    $ret["items"]["blogPosts"]["label"] = tra("new blog posts");
    $ret["items"]["blogPosts"]["cname"] = "slvn_blogPosts_menu";

    $query = "select `postId`, `blogId`, `title`, `user`, `created`  from `tiki_blog_posts` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['blogId'], 'blog', 'tiki_p_read_blog')) {
           $ret["items"]["blogPosts"]["list"][$count]["href"]  = "tiki-view_blog_post.php?blogId=" . $res["blogId"] . "&postId=" . $res["postId"];
           $ret["items"]["blogPosts"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["blogPosts"]["list"][$count]["label"] = $res["title"]; 
           $count++;
       }
    }
    $ret["items"]["blogPosts"]["count"] = $count;
  }

  if ($prefs['feature_galleries'] == 'y') {
    //image galleries
    $ret["items"]["imageGalleries"]["label"] = tra("new image galleries");
    $ret["items"]["imageGalleries"]["cname"] = "slvn_imageGalleries_menu";
    $query = "select `galleryId`,`name`,`created`,`user` from `tiki_galleries` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
           $ret["items"]["imageGalleries"]["list"][$count]["href"]  = "tiki-browse_gallery.php?galleryId=" . $res["galleryId"];
           $ret["items"]["imageGalleries"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["imageGalleries"]["list"][$count]["label"] = $res["name"]; 
           $count++;
       }
    }
    $ret["items"]["imageGalleries"]["count"] = $count;

    //images
    $ret["items"]["images"]["label"] = tra("new images");
    $ret["items"]["images"]["cname"] = "slvn_images_menu";
    $query = "select `imageId`,`galleryId`,`name`,`created`,`user` from `tiki_images` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
           $ret["items"]["images"]["list"][$count]["href"]  = "tiki-browse_image.php?galleryId=" . $res["galleryId"]. "&imageId=" .$res["imageId"];
           $ret["items"]["images"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["images"]["list"][$count]["label"] = $res["name"]; 
           $count++;
       }
    }
    $ret["items"]["images"]["count"] = $count;
  }


  if ($prefs['feature_file_galleries'] == 'y') {
    //file galleries
    $ret["items"]["fileGalleries"]["label"] = tra("new file galleries");
    $ret["items"]["fileGalleries"]["cname"] = "slvn_fileGalleries_menu";
    $query = "select `galleryId`,`name`,`created`,`user` from `tiki_file_galleries` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
           $ret["items"]["fileGalleries"]["list"][$count]["href"]  = "tiki-list_file_gallery.php?galleryId=" . $res["galleryId"];
           $ret["items"]["fileGalleries"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["fileGalleries"]["list"][$count]["label"] = $res["name"]; 
           $count++;
        }
    }
    $ret["items"]["fileGalleries"]["count"] = $count;

    //files
    $ret["items"]["files"]["label"] = tra("new files");
    $ret["items"]["files"]["cname"] = "slvn_files_menu";
    $query = "select `galleryId`,`name`,`filename`,`created`,`user` from `tiki_files` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
           $ret["items"]["files"]["list"][$count]["href"]  = "tiki-list_file_gallery.php?galleryId=" . $res["galleryId"];
           $ret["items"]["files"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". trim(strip_tags(smarty_modifier_userlink($res["user"])));
           $ret["items"]["files"]["list"][$count]["label"] = $res["name"]. " (".$res["filename"].")"; 
           $count++;
        }
    }
    $ret["items"]["files"]["count"] = $count;
  }

  if ($prefs['feature_polls'] == 'y') {
    $ret["items"]["polls"]["label"] = tra("new polls");
    $ret["items"]["polls"]["cname"] = "slvn_polls_menu";

    $query = "select `pollId`, `title`, `publishDate` from `tiki_polls` where `publishDate`>? order by `publishDate` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        $ret["items"]["polls"]["list"][$count]["href"]  = "tiki-poll_results.php?pollId=" . $res["pollId"];
        $ret["items"]["polls"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["publishDate"]);
        $ret["items"]["polls"]["list"][$count]["label"] = $res["title"]; 
        $count++;
    }
    $ret["items"]["polls"]["count"] = $count;
  }
  
  if (!isset($params['showuser']) || $params['showuser'] != 'n') {
	$ret['items']['users']['label'] = tra('new users');
	$ret['items']['users']['cname'] = 'slvn_users_menu';
	$query = 'select `userId`, `login`, `registrationDate` from `users_users` where `registrationDate`>?';
	$result = $tikilib->query($query, array((int)$last));
	$count = 0;
	while ($res = $result->fetchRow()) {
		$ret['items']['users']['list'][$count]['href']  = 'tiki-user_information.php?userId=' . $res['userId'];
		$ret['items']['users']['list'][$count]['title'] = $tikilib->get_short_datetime($res['registrationDate']);
		$ret['items']['users']['list'][$count]['label'] = $res['login']; 
		$count++;
	}
	$ret['items']['users']['count'] = $count;
  }

  if ($prefs['feature_trackers'] == 'y' && (!isset($params['showtracker']) || $params['showtracker'] != 'n')) {
    $ret["items"]["trackers"]["label"] = tra("new tracker items");
    $ret["items"]["trackers"]["cname"] = "slvn_trackers_menu";

    $query = "select `itemId`, `trackerId`, `created`, `lastModif`  from `tiki_tracker_items` where `lastModif`>? order by `lastModif` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    global $cachelib;
    require_once('lib/cache/cachelib.php');
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user,$res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
           $ret["items"]["trackers"]["list"][$count]["href"]  = "tiki-view_tracker_item.php?itemId=" . $res["itemId"];
           $ret["items"]["trackers"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["lastModif"]);
	   
	   //routine to verify field in tracker that should appear as label
	   $cacheKey = 'trackerItemLabel'.$res['itemId'];
	   if (!$cachelib->isCached($cacheKey)) {
	       $query = "select `fieldId` from `tiki_tracker_fields` where `isMain` = ? and `trackerId` = ? order by `position`";
	       $fieldId = $tikilib->getOne($query, array('y',$res['trackerId']));
	       $query = "select `value` from `tiki_tracker_item_fields` where `fieldId` = ? and `itemId` = ?";
	       $label = $tikilib->getOne($query, array($fieldId,$res['itemId']));

	       $cachelib->cacheItem($cacheKey, $label);
	   } else {
	       $label = $cachelib->getCached($cacheKey);
	   }
	   $ret["items"]["trackers"]["list"][$count]["label"] = $label;
           $count++;
        }
    }
    $ret["items"]["trackers"]["count"] = $count;
  }

  if ($prefs['feature_calendar'] == 'y') {    
    $ret["items"]["calendar"]["label"] = tra("new calendar events");
    $ret["items"]["calendar"]["cname"] = "slvn_calendar_menu";

    $query = "select `calitemId`, `calendarId`, `created`, `lastmodif`, `name` from `tiki_calendar_items` where `lastmodif`>? order by `lastmodif` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        if ($userlib->user_has_perm_on_object($user, $res['calendarId'], 'calendar', 'tiki_p_view_calendar')) {
	   $ret["items"]["calendar"]["list"][$count]["href"]  = 'tiki-calendar_edit_item.php?viewcalitemId='.$res['calitemId'];
           $ret["items"]["calendar"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["lastmodif"]);
	   $ret["items"]["calendar"]["list"][$count]["label"] = $res['name'];
           $count++;
        }
    }
    $ret["items"]["calendar"]["count"] = $count;
  }

  //get the total of items
  $ret["cant"] = 0;
  foreach ($ret["items"] as $item) {
	  $ret["cant"] += $item["count"];
  }

  return $ret;

}
}

$slvn_info = since_last_visit_new($user, $module_params);
$smarty->assign('slvn_info', $slvn_info);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
