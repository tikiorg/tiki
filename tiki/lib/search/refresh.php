<?php

function refresh_search_index() {
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"refresh_search_index()\n");fclose($fpd);
  // first write close the session. refreshing can take a huge amount of time
  session_write_close();

  // check if we have to run. Run every n-th click:
  $n=5; //todo: make it configurable
  srand (microtime());
  if(rand(1,$n)==1) {
    // get a random location
    $locs=array("wiki","forum","trackers","oldest"); // to be continued
    $location=$locs[rand(0,count($locs))];
    // random refresh
    switch ($location) {
      case "wiki":
        random_refresh_index_wiki();
	break;
      case "forum":
        random_refresh_index_forum();
	break;
      case "trackers":
        random_refresh_index_trackers();
	break;
      case "oldest":
        refresh_index_oldest();
	break;
    }

  }
}

function random_refresh_index_forum() {
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"random_refresh_index_forum\n");fclose($fpd);

}

function random_refresh_index_trackers() {
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"random_refresh_index_trackers\n");fclose($fpd);

}

function random_refresh_index_wiki(){
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"random_refresh_index_wiki\n");fclose($fpd);
  //find random wiki page
  global $tikilib;
  $rpages=$tikilib->get_random_pages(1);
  if(!empty($rpages["0"]))
    refresh_index_wiki($rpages["0"]);
}


function refresh_index_oldest(){
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"refresh_index_oldest()\n");fclose($fpd);
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
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"refresh_index_wiki($page)\n");fclose($fpd);
  global $tikilib;
  $info = $tikilib->get_page_info($page);
  $pdata=$tikilib->parse_data($info["data"]);
  $pdata.=" ".$tikilib->parse_data($info["description"]);
  $words=&search_index($pdata);
  insert_index($words,'wiki',$page);
}

function refresh_index_forum($page) {
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"refresh_index_forum($page)\n");fclose($fpd);

}

function refresh_index_trackers($page) {
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"refresh_index_trackers($page)\n");fclose($fpd);

}

function &search_index($data) {
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"search_index()\n");fclose($fpd);
  $data=strip_tags($data);
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"parsing data: $data \n");fclose($fpd);
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
  $fpd=fopen("/tmp/tikidebug",'a');fwrite($fpd,"insert_index(...,$location,$page)\n");fclose($fpd);
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
      $tikilib->query($query,array($location,$page,$key,(int) $value,$now));
    }
  }

}

?>
