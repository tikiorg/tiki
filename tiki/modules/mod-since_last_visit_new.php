<?php

function since_last_visit_new($user) {
  if (!$user) return false;

  global $tikilib;
  global $userlib;
  $ret = array();
  $ret["label"] = tra("Since your last visit");

  if (strpos($_SERVER["SCRIPT_NAME"],"tiki-calendar.php") && isset($_REQUEST["todate"]) && $_REQUEST["todate"]) {
    $last = $_REQUEST["todate"];
    $_SESSION["slvn_last_login"] = $last;
    $ret["label"] = tra("Changes")." ".tra("since");
  }
  else if (isset($_SESSION["slvn_last_login"])) {
    $last = $_SESSION["slvn_last_login"];
    $ret["label"] = tra("Changes")." ".tra("since");
  }
  else {
    $last = $tikilib->getOne("select `lastLogin`  from `users_users` where `login`=?",array($user));
    if (!$last) $last = time();
  }
  $ret["lastLogin"] = $last;
  //if (!isset($_SESSION["slvn_last_login"])) $_SESSION["slvn_last_login"] = $last;
  //$last = strtotime ("-2 week");

  if ($tikilib->get_preference("feature_wiki") == 'y' && $userlib->user_has_permission($user, "tiki_p_view")) {
    // && $tikilib->getOne("select count(*) from `tiki_pages` where `lastModif`>?",array((int)$last))!=0) {    
    $ret["items"]["pages"]["label"] = tra("wiki pages changed");
    $ret["items"]["pages"]["cname"] = "slvn_pages_menu";
    $query = "select `pageName`, `user`, `lastModif`  from `tiki_pages` where `lastModif`>? order by `lastModif` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        $ret["items"]["pages"]["list"][$count]["href"]  = "tiki-index.php?page=" . $res["pageName"];
        $ret["items"]["pages"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["lastModif"]) ." ". tra("by") ." ". $res["user"];
        $ret["items"]["pages"]["list"][$count]["label"] = $res["pageName"]; 
        $count++;
    }
    $ret["items"]["pages"]["count"] = $count;
  }

  if ($tikilib->get_preference("feature_articles") == 'y' && $userlib->user_has_permission($user, "tiki_p_read_article")) {    
    $ret["items"]["articles"]["label"] = tra("new articles");
    $ret["items"]["articles"]["cname"] = "slvn_articles_menu";

    if($userlib->user_has_permission($user, "tiki_p_edit_article")) {
      $query = "select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `created`>? and `expireDate`>?";
    }else {
      $query = "select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `publishDate`>? and `expireDate`>?";
    }
    $bindvars = array((int)$last);
    $bindvars[] = time();
    $result = $tikilib->query($query, $bindvars);

    $count = 0;
    while ($res = $result->fetchRow())
    {
        $ret["items"]["articles"]["list"][$count]["href"]  = "tiki-read_article.php?articleId=" . $res["articleId"];
        $ret["items"]["articles"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["publishDate"]) ." ". tra("by") ." ". $res["authorName"];
        $ret["items"]["articles"]["list"][$count]["label"] = $res["title"]; 
        $count++;
    }

    $ret["items"]["articles"]["count"] = $count;
  }

  if ($userlib->user_has_permission($user, "tiki_p_read_comments") &&
     ($tikilib->get_preference("feature_article_comments")         == 'y' ||
      $tikilib->get_preference("feature_blogposts_comments")       == 'y' ||
      $tikilib->get_preference("feature_blog_comments")            == 'y' ||
      $tikilib->get_preference("feature_faq_comments")             == 'y' ||
      $tikilib->get_preference("feature_file_galleries_comments")  == 'y' ||
      $tikilib->get_preference("feature_image_galleries_comments") == 'y' ||
      $tikilib->get_preference("feature_poll_comments")            == 'y' ||
      $tikilib->get_preference("feature_wiki_comments")            == 'y' ))
  {
    $ret["items"]["comments"]["label"] = tra("new comments");
    $ret["items"]["comments"]["cname"] = "slvn_comments_menu";
    $query = "select `object`,`objectType`,`title`,`commentDate`,`userName` from `tiki_comments` where `commentDate`>? order by `commentDate` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
      switch($res["objectType"]){
        case "article":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-read_article.php?articleId=" . $res["object"];
          break;
        case "post":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-view_blog_post.php?postId=" . $res["object"];
          break;
        case "blog":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-view_blog.php?blogId=" . $res["object"];
          break;
        case "faq":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-view_faq.php?faqId=" . $res["object"];
          break;
        case "file gallery":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-list_file_gallery.php?galleryId=" . $res["object"];
          break;
        case "image_gallery":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-browse_gallery.php?galleryId=" . $res["object"];
          break;
        case "poll":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-poll_results.php?pollId=" . $res["object"];
          break;
        case "wiki page":
          $ret["items"]["comments"]["list"][$count]["href"]
            = "tiki-index.php?page=" . $res["object"];
          break;
      }
      $ret["items"]["comments"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["commentDate"]) ." ". tra("by") ." ". $res["userName"];
      $ret["items"]["comments"]["list"][$count]["label"] = $res["title"]; 
      $count++;
    }
    $ret["items"]["comments"]["count"] = $count;
  }

  if ($tikilib->get_preference("feature_galleries") == 'y') {
    //image galleries
    $ret["items"]["imageGalleries"]["label"] = tra("new image galleries");
    $ret["items"]["imageGalleries"]["cname"] = "slvn_imageGalleries_menu";
    $query = "select `galleryId`,`name`,`created`,`user` from `tiki_galleries` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        $ret["items"]["imageGalleries"]["list"][$count]["href"]  = "tiki-browse_gallery.php?galleryId=" . $res["galleryId"];
        $ret["items"]["imageGalleries"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
        $ret["items"]["imageGalleries"]["list"][$count]["label"] = $res["name"]; 
        $count++;
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
        $ret["items"]["images"]["list"][$count]["href"]  = "tiki-browse_image.php?galleryId=" . $res["galleryId"]. "&imageId=" .$res["imageId"];
        $ret["items"]["images"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
        $ret["items"]["images"]["list"][$count]["label"] = $res["name"]; 
        $count++;
    }
    $ret["items"]["images"]["count"] = $count;
  }

  if ($tikilib->get_preference("feature_file_galleries") == 'y') {    
    //file galleries
    $ret["items"]["fileGalleries"]["label"] = tra("new file galleries");
    $ret["items"]["fileGalleries"]["cname"] = "slvn_fileGalleries_menu";
    $query = "select `galleryId`,`name`,`created`,`user` from `tiki_galleries` where `created`>? order by `created` desc";
    $result = $tikilib->query($query, array((int)$last));

    $count = 0;
    while ($res = $result->fetchRow())
    {
        $ret["items"]["fileGalleries"]["list"][$count]["href"]  = "tiki-list_file_gallery.php?galleryId=" . $res["galleryId"];
        $ret["items"]["fileGalleries"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
        $ret["items"]["fileGalleries"]["list"][$count]["label"] = $res["name"]; 
        $count++;
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
        $ret["items"]["files"]["list"][$count]["href"]  = "tiki-list_file_gallery.php?galleryId=" . $res["galleryId"];
        $ret["items"]["files"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
        $ret["items"]["files"]["list"][$count]["label"] = $res["name"]. " (".$res["filename"].")"; 
        $count++;
    }
    $ret["items"]["files"]["count"] = $count;
  }

  $ret["items"]["users"]["label"] = tra("new users");
  $ret["items"]["users"]["cname"] = "slvn_users_menu";
  $query = "select `login` from `users_users` where `registrationDate`>?";
  $result = $tikilib->query($query, array((int)$last));

  $count = 0;
  $slvn_tmp_href = $userlib->user_has_permission($user, "tiki_p_admin") ? "tiki-assignuser.php?assign_user=" : "tiki-user_information.php?view_user="; 
  while ($res = $result->fetchRow())
  {
      $ret["items"]["users"]["list"][$count]["href"]  = $slvn_tmp_href . $res["login"];
      $ret["items"]["users"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["registrationDate"]);
      $ret["items"]["users"]["list"][$count]["label"] = $res["login"]; 
      $count++;
  }
  $ret["items"]["users"]["count"] = $count;
  
  return $ret;
}

$slvn_info = since_last_visit_new($user);
$smarty->assign('slvn_info', $slvn_info);

?>