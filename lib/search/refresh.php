<?php

function refresh_search_index() {
  // first write close the session. refreshing can take a huge amount of time
  session_write_close();

  // check if we have to run. Run every n-th click:
  $n=50; //todo: make it configurable
  //$n=1; //debug
  list($usec, $sec) = explode(" ",microtime());
  srand (ceil($sec+100*$usec));
  if(rand(1,$n)==1) {
    // get a random location
    $locs=array();
    global $feature_wiki;
    if ($feature_wiki == 'y') $locs[]="random_refresh_index_wiki";
    global $feature_forums;
    if ($feature_forums == 'y') $locs[]="random_refresh_index_forum";
    global $feature_trackers;
    if ($feature_trackers == 'y') $locs[]="random_refresh_index_trackers";
    global $feature_articles;
    if ($feature_articles == 'y') $locs[]="random_refresh_index_articles";
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
  
}

function random_refresh_index_trackers() {

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
