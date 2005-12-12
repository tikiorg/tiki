<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
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

function random_refresh_index_comments( $times = 1 ) {
  //find random forum comment
  global $tikilib;

    for( $i = 1; $i <= $times; $i ++ )
    {
  // get random comment
  $cant = $tikilib->getOne("select count(*) from `tiki_comments`");
    // print "<pre>cant:";
    // print_r( $cant );
    // print "</pre>";
  if($cant>0) {
    $query="select * from `tiki_comments`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    // print "<pre>res:";
    // print_r( $res );
    // print "</pre>";
    $words=&search_index($res["title"]." ".$res["data"]." ".$res["summary"]);
    // print "<pre>words:";
    // print_r( $words );
    // print "</pre>";
    insert_index($words,$res["objectType"].'comment',$res["threadId"]);
  }
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
    $words=&search_index($res["title"]." ".$res["authorName"]." ".$res["heading"]." ".$res["body"]." ".$res["author"]." ".$res['topline'].' '.$res['subtitle']);
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
    $query="select f.`value`, f.`itemId`, f.`fieldId`
	from `tiki_tracker_item_fields` f, `tiki_tracker_fields` tf
	where tf.`type` in (?,?) and tf.`fieldId`=f.`fieldId`";
    $result=$tikilib->query($query,array("t","a"),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["value"]);
    insert_index($words,'trackeritem',$res["itemId"]."#".$res["fieldId"]);
  }
}

function random_refresh_index_wiki(){
  //find random wiki page
  global $tikilib;
  $rpages=$tikilib->get_random_pages(1);
  if(!empty($rpages["0"]))
    refresh_index_wiki($rpages["0"]);
}

function refresh_index_wiki_all() {
  global $tikilib;
  $pages=$tikilib->get_all_pages();
  foreach($pages as $page) {
    refresh_index_wiki($page['pageName']);
  }
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
  $pdata .= ' '.$page;
  $words=&search_index($pdata);
  insert_index($words,'wiki',$page);
}

function refresh_index_comments($threadId) {
    global $tikilib;

    if( isset( $threadId ) )
    {
    $query="select * from `tiki_comments` where `threadId` = ? ";
    $result = $tikilib->query( $query, array( $threadId ) );
    $res=$result->fetchRow();

    // print "<pre>res:";
    // print_r( $res );
    // print "</pre>";

    $words=&search_index($res["title"]." ".$res["data"]." ".$res["summary"]);
    // print "<pre>words:";
    // print_r( $words );
    // print "</pre>";

    insert_index($words,$res["objectType"].'comment',$res["threadId"]);
    }
}

function refresh_index_forum($page) {

}

function refresh_index_trackers() {
	global $tikilib;
	$query = "select v.`itemId`, v.`fieldId`, v.`value` from `tiki_tracker_item_fields` v, `tiki_tracker_fields` tf where v.`fieldId`=tf.`fieldId` and (tf.`type`='t' or tf.`type`= 'a')";
	$result = $tikilib->query($query);
	while ($res = $result->fetchRow()) {
		$words=&search_index($res["value"]);
		insert_index($words,'trackeritem',$res["itemId"]."#".$res["fieldId"]);
	}
}

function &search_index($data) {
  $data=strip_tags($data);
  // split into words
  $sstrings=preg_split("/[\s]+/",$data,-1,PREG_SPLIT_NO_EMPTY);
  // count words
  $words=array();
  foreach ($sstrings as $key=>$value) {
    if(!isset($words[strtolower($value)]))
      $words[strtolower($value)]=0;
    $words[strtolower($value)]++;
  }

  return $words;
}

function insert_index(&$words,$location,$page) {
  global $tikilib, $search_min_wordlength;
  $query="delete from `tiki_searchindex` where `location`=? and `page`=?";
  $tikilib->query($query,array($location,$page),-1,-1,false);

  $now= (int) date('U');
  foreach ($words as $key=>$value) {
    if (strlen($key)>$search_min_wordlength) {//todo: make min length configurable
      // todo: stopwords
      $query="insert into `tiki_searchindex`
    		(`location`,`page`,`searchword`,`count`,`last_update`)
		values(?,?,?,?,?)";
      $tikilib->query($query,array($location,$page,$key,(int) $value,$now),-1,-1,false);
    }
  }
}

function random_refresh_file(){
   global $tikilib;
   $cant=$tikilib->getOne("select count(*) from `tiki_files`",array());
   if($cant>0) {
     $query="select * from `tiki_files`";
     $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
     $info=$result->fetchRow();
     $words=&search_index($info["data"]." ".$info["description"]." ".$info["name"]);
     insert_index($words,"file",$info["fileId"]);
   }
}

function refresh_index_files() {
  global $tikilib;
  $result = $tikilib->query("select * from `tiki_files`", array());
  while ($info = $result->fetchRow()) {
      $words=&search_index($info['data'].' '.$info['description']." ".$info['name'], ' '.$info['search_data']);
      insert_index($words,"file",$info["fileId"]);
  }
}

function random_refresh_filegal() {
  global $feature_galleries;
  global $tikilib;
  $cant=$tikilib->getOne("select count(*) from `tiki_file_galleries`",array());
  if($cant>0) {
    $query="select * from `tiki_file_galleries`";
    $result=$tikilib->query($query,array(),1,rand(0,$cant-1));
    $res=$result->fetchRow();
    $words=&search_index($res["name"]." ".$res["description"]);
    insert_index($words,"filegal",$res["galleryId"]);
  }
}

?>
