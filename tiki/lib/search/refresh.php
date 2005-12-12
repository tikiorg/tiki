<?php

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
  global $search_refresh_rate;
  //$search_refresh_rate=1; //debug
    ///iprint "<pre>rate: $search_refresh_rate.</pre>\n";
  list($usec, $sec) = explode(" ",microtime());
  srand (ceil($sec+100*$usec));
  if($search_refresh_rate > 0 && rand(1,$search_refresh_rate)==1) {
    // print "<pre>refreshing</pre>\n";

    require_once(dirname(__FILE__).'/refresh-functions.php');
    // get a random location
    $locs=array();
    global $feature_wiki;
    if ($feature_wiki == 'y') $locs[]="random_refresh_index_wiki";
    global $feature_forums;
    if ($feature_forums == 'y') $locs[]="random_refresh_index_forum";
    global $feature_trackers;
    if ($feature_trackers == 'y') {
      $locs[]="random_refresh_index_trackers";
      $locs[]="random_refresh_index_tracker_items";
    }
    global $feature_articles;
    if ($feature_articles == 'y') $locs[]="random_refresh_index_articles";
    global $feature_blogs;
    if ($feature_blogs== 'y') {
      $locs[]="random_refresh_index_blogs";
      $locs[]="random_refresh_index_blog_posts";
      }
    global $feature_faqs;
    if ($feature_faqs == 'y') {
      $locs[]="random_refresh_index_faqs";
      $locs[]="random_refresh_index_faq_questions";
    }
    global $feature_directory;
    if ($feature_directory == 'y') {
      $locs[]="random_refresh_index_dir_cats";
      $locs[]="random_refresh_index_dir_sites";
    }
    global $feature_galleries;
    if ($feature_galleries == 'y') {
      $locs[]="random_refresh_imggals";
      $locs[]="random_refresh_img";
    }
    global $feature_file_galleries;
    if ($feature_file_galleries == "y") {
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

?>
