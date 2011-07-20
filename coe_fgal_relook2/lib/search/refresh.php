<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function refresh_search_index() {
    // *DON'T* *DO* *THIS*
    // I just tested, and session_write_close loses the database connection on
    // my machine!  None of the indexing can succeed after that!
    // WAS: first write close the session. refreshing can take a huge amount of time
    // WAS session_write_close();

  // check if we have to run. Run every n-th click:
  global $prefs;
  list($usec, $sec) = explode(" ",microtime());
  srand (ceil($sec+100*$usec));
  if($prefs['search_refresh_rate'] > 0 && rand(1,$prefs['search_refresh_rate'])==1) {
 //    print "<pre>refreshing</pre>\n";


    // require_once('lib/search/refresh-functions.php');
    // get a random location
    $locs=array();
    if ($prefs['feature_wiki'] == 'y') $locs[]="random_refresh_index_wiki";
    if ($prefs['feature_forums'] == 'y') $locs[]="random_refresh_index_forum";
    if ($prefs['feature_trackers'] == 'y') {
      $locs[]="random_refresh_index_trackers";
      $locs[]="random_refresh_index_tracker_items";
    }
    if ($prefs['feature_articles'] == 'y') $locs[]="random_refresh_index_articles";
    if ($prefs['feature_blogs']== 'y') {
      $locs[]="random_refresh_index_blogs";
      $locs[]="random_refresh_index_blog_posts";
      }
    if ($prefs['feature_faqs'] == 'y') {
      $locs[]="random_refresh_index_faqs";
      $locs[]="random_refresh_index_faq_questions";
    }
    if ($prefs['feature_directory'] == 'y') {
      $locs[]="random_refresh_index_dir_cats";
      $locs[]="random_refresh_index_dir_sites";
    }
    if ($prefs['feature_galleries'] == 'y') {
      $locs[]="random_refresh_imggals";
      $locs[]="random_refresh_img";
    }
    if ($prefs['feature_file_galleries'] == "y") {
      $locs[]="random_refresh_filegal";
      $locs[]="random_refresh_file";
    }

    // comments can be everywhere?
    $locs[]="random_refresh_index_comments";
    // some refreshes to enhance the refreshing stats
    $locs[]="refresh_index_oldest";
    // print_r($locs);
    $location=$locs[rand(0,count($locs)-1)];
    // random refresh
    // echo "location to index: $location";
    call_user_func ($location);
  }
}
