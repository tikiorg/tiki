<?php

function refresh_search_index() {
  // first write close the session. refreshing can take a huge amount of time
  session_write_close();

  // check if we have to run. Run every n-th click:
  global $search_refresh_rate;
  //$search_refresh_rate=1; //debug
  list($usec, $sec) = explode(" ",microtime());
  srand (ceil($sec+100*$usec));
  if(rand(1,$search_refresh_rate)==1) {
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
      $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"f_gal on\n");fclose($fpd);
      $locs[]="random_refresh_imggals";
      $locs[]="random_refresh_img";
    }


    // comments can be everywhere?
    $locs[]="random_refresh_index_comments";
    // some refreshes to enhance the refreshing stats
    $locs[]="refresh_index_oldest";
    //print_r($locs);
    $location=$locs[rand(0,count($locs)-1)];
    // random refresh
    //echo "$location";
    call_user_func ($location);
  }
}

function random_refresh_imggals() {
  global $feature_galleries;
  global $tikilib;
  $cant=$tikilib->getOne("select count(*) from `tiki_galleries`",array());
  if($cant>0) {
    $query="select * from `tiki_galleries`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]);
    insert_index($words,"imggal",$res["galleryId"]);
  }
}

function random_refresh_img() {
  global $feature_galleries;
  global $tikilib;
  $cant=$tikilib->getOne("select count(*) from `tiki_images`",array());
  if($cant>0) {
    $query="select * from `tiki_images`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]);
    insert_index($words,"img",$res["imageId"]);
  }
}

function random_refresh_index_comments() {
  //find random forum comment
  global $tikilib;
  // get random comment
  $cant=$tikilib->getOne("select count(*) from `tiki_comments`",array());
  if($cant>0) {
    $query="select * from `tiki_comments`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["title"]." ".$res["data"]." ".$res["summary"]);
    insert_index($words,$res["objectType"].'comment',$res["threadId"]);
  }
}

function random_refresh_index_blogs() {
  global $tikilib;
  // get random blog 
  $cant=$tikilib->getOne("select count(*) from `tiki_blogs`",array());
  if($cant>0) {
    $query="select * from `tiki_blogs`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["title"]." ".$res["user"]." ".$res["description"]);
    insert_index($words,'blog',$res["blogId"]);
  }
}

function random_refresh_index_dir_cats() {
  global $tikilib;
  // get random directory ctegory
  $cant=$tikilib->getOne("select count(*) from `tiki_directory_categories`",array());
  if($cant>0) {
    $query="select * from `tiki_directory_categories`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]);
    insert_index($words,'dir_cat',$res["categId"]);
  }
}

function random_refresh_index_dir_sites() {
  global $tikilib;
  // get random directory ctegory
  $cant=$tikilib->getOne("select count(*) from `tiki_directory_sites`",array());
  if($cant>0) {
    $query="select * from `tiki_directory_sites`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]);
    insert_index($words,'dir_site',$res["siteId"]);
  }
}

function random_refresh_index_faqs() {
  global $tikilib;
  // get random faq 
  $cant=$tikilib->getOne("select count(*) from `tiki_faqs`",array());
  if($cant>0) {
    $query="select * from `tiki_faqs`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["title"]." ".$res["description"]);
    insert_index($words,'faq',$res["faqId"]);
  }
}

function random_refresh_index_faq_questions() {
  global $tikilib;
  // get random faq   
  $cant=$tikilib->getOne("select count(*) from `tiki_faq_questions`",array());
  if($cant>0) {
    $query="select * from `tiki_faq_questions`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["question"]." ".$res["answer"]);
    insert_index($words,'faq_question',$res["questionId"]);
  }
}

function random_refresh_index_blog_posts() {
  global $tikilib;
  // get random blog 
  $cant=$tikilib->getOne("select count(*) from `tiki_blog_posts`",array());
  if($cant>0) {
    $query="select * from `tiki_blog_posts`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["title"]." ".$res["user"]." ".$res["data"]);
    insert_index($words,'blog_post',$res["postId"]);
  }
}


function random_refresh_index_articles() {
  global $tikilib;
  // get random article
  $cant=$tikilib->getOne("select count(*) from `tiki_articles`",array());
  if($cant>0) {
    $query="select * from `tiki_articles`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["title"]." ".$res["authorName"]." ".$res["heading"]." ".$res["body"]." ".$res["author"]);
    insert_index($words,'article',$res["articleId"]);
  }
}


function random_refresh_index_forum() {
  global $tikilib;
  // get random forum
  $cant=$tikilib->getOne("select count(*) from `tiki_forums`",array());
  if($cant>0) {
    $query="select * from `tiki_forums`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]." ".$res["moderator"]);
    insert_index($words,'forum',$res["forumId"]);
  }
}

function random_refresh_index_trackers() {
  global $tikilib;
  $cant=$tikilib->getOne("select count(*) from `tiki_trackers`",array());
  if($cant>0) {
    $query="select * from `tiki_trackers`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]);
    insert_index($words,'tracker',$res["trackerId"]);
  }
}

function random_refresh_index_tracker_items() {
  global $tikilib;
  $cant=$tikilib->getOne("select count(*) from `tiki_tracker_item_fields` f, `tiki_tracker_fields` tf 
	where tf.`type` in (?,?) and tf.`fieldId`=f.`fieldId`",array("t","a"));
  if($cant>0) {
    $query="select f.`value`, f.`itemId` 
	from `tiki_tracker_item_fields` f, `tiki_tracker_fields` tf
	where tf.`type` in (?,?) and tf.`fieldId`=f.`fieldId`";
    $result=$tikilib->query($query,array("t","a"),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["value"]);
    insert_index($words,'trackeritem',$res["itemId"]);
  }
}

function random_refresh_index_wiki(){
  //find random wiki page
  global $tikilib;
  $rpages=$tikilib->get_random_pages(1);
  if(!empty($rpages["0"]))
    refresh_index_wiki($rpages["0"]);
}


function refresh_index_oldest(){
  global $tikilib;
  $min = $tikilib->getOne("select min(`last_update`) from `tiki_searchindex`",array());
  $result = $tikilib->query("select `location`,`page` from `tiki_searchindex` where `last_update`=?",array($min),1);
  $res = $result->fetchRow();
  switch($res["location"]) {
    case "wiki":
      refresh_index_wiki($res["page"]);
      break;
    case "forum":
      refresh_index_forum($res["page"]);
      break;
    case "trackers":
      refresh_index_trackers($res["page"]);
      break;
  }
}

function refresh_index_wiki($page) {
  global $tikilib;
  $info = $tikilib->get_page_info($page);
  $pdata=$tikilib->parse_data($info["data"]);
  $pdata.=" ".$tikilib->parse_data($info["description"]);
  $words=&search_index($pdata);
  insert_index($words,'wiki',$page);
}

function refresh_index_forum($page) {

}

function refresh_index_trackers($page) {

}

function &search_index($data) {
  $data=strip_tags($data);
  // split into words
  $sstrings=preg_split("/[\W]+/",$data,-1,PREG_SPLIT_NO_EMPTY);
  // count words
  $words=array();
  foreach ($sstrings as $key=>$value) {
    if(!isset($words[strtolower($value)]))
      $words[strtolower($value)]=0;
    $words[strtolower($value)]++;
  }

  return($words);
}

function insert_index(&$words,$location,$page) {
  global $tikilib;
  $query="delete from `tiki_searchindex` where `location`=? and `page`=?";
  $tikilib->query($query,array($location,$page),-1,-1,false);

  $now= (int) date('U');
  foreach ($words as $key=>$value) {
    if (strlen($key)>3) {//todo: make min length configurable
      // todo: stopwords
      $query="insert into `tiki_searchindex`
    		(`location`,`page`,`searchword`,`count`,`last_update`)
		values(?,?,?,?,?)";
      $tikilib->query($query,array($location,$page,$key,(int) $value,$now),-1,-1,false);
    }
  }

}

?>
