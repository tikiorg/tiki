<?php

class StatsLib extends TikiLib {

  function StatsLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to StatsLib constructor");  
    }
    $this->db = $db;  
  }
  
    function list_orphan_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc',$find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($sort_mode == 'size desc') {
      $sort_mode = ' length(data) desc';
    }
    if($sort_mode == 'size asc') {
      $sort_mode = ' length(data) asc';
    }
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('versions desc','versions asc','links asc','links desc','backlinks asc','backlinks desc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }

    if($find) {
      $mid=" where pageName like '%".$find."%' ";
    } else {
      $mid="";
    }

    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select pageName, hits, length(data) as len ,lastModif, user, ip, comment, version, flag from tiki_pages $mid order by $sort_mode limit 0,-1";
    $query_cant = "select count(*) from tiki_pages $mid";
    $result = $this->query($query);
    $result_cant = $this->query($query_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    $num_or = 0;
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $pageName = $res["pageName"];
      $queryc = "select count(*) from tiki_links where toPage='$pageName'";
      $cant = $this->getOne($queryc);
      if($cant==0) {
        $num_or++;
        $aux = Array();
        $aux["pageName"] = $pageName;
        $page = $aux["pageName"];
        $aux["hits"] = $res["hits"];
        $aux["lastModif"] = $res["lastModif"];
        $aux["user"] = $res["user"];
        $aux["ip"] = $res["ip"];
        $aux["len"] = $res["len"];
        $aux["comment"] = $res["comment"];
        $aux["version"] = $res["version"];
        $aux["flag"] = $res["flag"] == 'y' ? tra('locked') : tra('unlocked');
        $aux["versions"] = $this->getOne("select count(*) from tiki_history where pageName='$page'");
        $aux["links"] = $this->getOne("select count(*) from tiki_links where fromPage='$page'");
        $aux["backlinks"] = $this->getOne("select count(*) from tiki_links where toPage='$page'");
        $ret[] = $aux;
      }
    }
    // If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offse and old_maxRecords
    if($old_sort_mode == 'versions asc') {
      usort($ret,'compare_versions');
    }
    if($old_sort_mode == 'versions desc') {
      usort($ret,'r_compare_versions');
    }
    if($old_sort_mode == 'links desc') {
      usort($ret,'compare_links');
    }
    if($old_sort_mode == 'links asc') {
      usort($ret,'r_compare_links');
    }
    if($old_sort_mode == 'backlinks desc') {
      usort($ret,'compare_backlinks');
    }
    if($old_sort_mode == 'backlinks asc') {
      usort($ret,'r_compare_backlinks');
    }
    if(in_array($old_sort_mode,Array('versions desc','versions asc','links asc','links desc','backlinks asc','backlinks desc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $num_or;
    return $retval;
  }


  
  function wiki_stats()
  {
    $stats=Array();
    $stats["pages"]=$this->getOne("select count(*) from tiki_pages");
    $stats["versions"]=$this->getOne("select count(*) from tiki_history");
    if($stats["pages"]) $stats["vpp"]=$stats["versions"]/$stats["pages"]; else $stats["vpp"]=0;
    $stats["visits"]=$this->getOne("select sum(hits) from tiki_pages");
    $or = $this->list_orphan_pages(0,-1, 'pageName_desc','');
    $stats["orphan"]=$or["cant"];
    $links = $this->getOne("select count(*) from tiki_links");
    if($stats["pages"]) $stats["lpp"]=$links/$stats["pages"]; else $stats["lpp"]=0;
    $stats["size"] = $this->getOne("select sum(length(data)) from tiki_pages");
    if($stats["pages"]) $stats["bpp"]=$stats["size"]/$stats["pages"]; else $stats["bpp"]=0;
    $stats["size"] = $stats["size"]/1000000;
    return $stats;
  }
  
  function quiz_stats()
  {
    $this->compute_quiz_stats();
    $stats=Array();
    $stats["quizzes"]=$this->getOne("select count(*) from tiki_quizzes");
    $stats["questions"]=$this->getOne("select count(*) from tiki_quiz_questions");
    if($stats["quizzes"]) $stats["qpq"]=$stats["questions"]/$stats["quizzes"]; else $stats["qpq"]=0;
    $stats["visits"]=$this->getOne("select sum(timesTaken) from tiki_quiz_stats_sum");
    $stats["avg"]=$this->getOne("select avg(avgavg) from tiki_quiz_stats_sum");
    $stats["avgtime"]=$this->getOne("select avg(avgtime) from tiki_quiz_stats_sum");
    return $stats;
  }
  
  function image_gal_stats()
  {
    $stats=Array();
    $stats["galleries"]=$this->getOne("select count(*) from tiki_galleries");
    $stats["images"]=$this->getOne("select count(*) from tiki_images");
    $stats["ipg"] = ($stats["galleries"]?$stats["images"]/$stats["galleries"]:0);
    $stats["size"] = $this->getOne("select sum(filesize) from tiki_images_data where type='o'");
    //$stats["bpi"] = ($stats["galleries"]?$stats["size"]/$stats["galleries"]:0);
    $stats["bpi"] = ($stats["images"]?$stats["size"]/$stats["images"]:0);
    $stats["size"] = $stats["size"]/1000000;
    $stats["visits"] = $this->getOne("select sum(hits) from tiki_galleries");
    return $stats;
  }
  
  function file_gal_stats()
  {
    $stats=Array();
    $stats["galleries"]=$this->getOne("select count(*) from tiki_file_galleries");
    $stats["files"]=$this->getOne("select count(*) from tiki_files");
    $stats["fpg"] = ($stats["galleries"]?$stats["files"]/$stats["galleries"]:0);
    $stats["size"] = $this->getOne("select sum(filesize) from tiki_files");
    $stats["size"] = $stats["size"]/1000000;
    $stats["bpf"] = ($stats["galleries"]?$stats["size"]/$stats["galleries"]:0);
    $stats["visits"] = $this->getOne("select sum(hits) from tiki_file_galleries");
    $stats["downloads"] = $this->getOne("select sum(downloads) from tiki_files");
    return $stats;
  }
  
  function cms_stats()
  {
    $stats=Array();
    $stats["articles"]=$this->getOne("select count(*) from tiki_articles");
    $stats["reads"]=$this->getOne("select sum(reads) from tiki_articles");
    $stats["rpa"]=($stats["articles"]?$stats["reads"]/$stats["articles"]:0);
    $stats["size"] = $this->getOne("select sum(size) from tiki_articles");
    $stats["bpa"]=($stats["articles"]?$stats["size"]/$stats["articles"]:0);
    $stats["topics"]=$this->getOne("select count(*) from tiki_topics where active='y'");
    return $stats;
  }
  
  function forum_stats()
  {
    $stats=Array();
    $stats["forums"]=$this->getOne("select count(*) from tiki_forums");
    $stats["topics"]=$this->getOne("select count(*) from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and parentId=0");
    $stats["threads"]=$this->getOne("select count(*) from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and parentId<>0");
    $stats["tpf"]=($stats["forums"]?$stats["topics"]/$stats["forums"]:0);
    $stats["tpt"]=($stats["topics"]?$stats["threads"]/$stats["topics"]:0);
    $stats["visits"]=$this->getOne("select sum(hits) from tiki_forums");
    return $stats;
  }
  
  function blog_stats()
  {
    $stats=Array();
    $stats["blogs"]=$this->getOne("select count(*) from tiki_blogs");
    $stats["posts"]=$this->getOne("select count(*) from tiki_blog_posts");
    $stats["ppb"]=($stats["blogs"]?$stats["posts"]/$stats["blogs"]:0);
    $stats["size"]=$this->getOne("select sum(length(data)) from tiki_blog_posts");
    $stats["bpp"]=($stats["posts"]?$stats["size"]/$stats["posts"]:0);
    $stats["visits"]=$this->getOne("select sum(hits) from tiki_blogs");
    return $stats;
  }
  
  function poll_stats()
  {
    $stats=Array();
    $stats["polls"]=$this->getOne("select count(*) from tiki_polls");
    $stats["votes"]=$this->getOne("select sum(votes) from tiki_poll_options");
    $stats["vpp"]=($stats["polls"]?$stats["votes"]/$stats["polls"]:0);
    return $stats;
  }

  function faq_stats()
  {
    $stats=Array();
    $stats["faqs"]=$this->getOne("select count(*) from tiki_faqs");
    $stats["questions"]=$this->getOne("select count(*) from tiki_faq_questions");
    $stats["qpf"]=($stats["faqs"]?$stats["questions"]/$stats["faqs"]:0);
    return $stats;
  }

  function user_stats()
  {
    $stats=Array();
    $stats["users"]=$this->getOne("select count(*) from users_users");
    $stats["bookmarks"]=$this->getOne("select count(*) from tiki_user_bookmarks_urls");
    $stats["bpu"]=($stats["users"]?$stats["bookmarks"]/$stats["users"]:0);
    return $stats;
  }

  function site_stats()
  {
    $stats=Array();
    $stats["started"] = $this->getOne("select min(day) from tiki_pageviews");
    $stats["days"]=$this->getOne("select count(*) from tiki_pageviews");
    $stats["pageviews"]=$this->getOne("select sum(pageviews) from tiki_pageviews");
    $stats["ppd"]=($stats["days"]?$stats["pageviews"]/$stats["days"]:0);
    $stats["bestpvs"]=$this->getOne("select max(pageviews) from tiki_pageviews");
    $stats["bestday"]=$this->getOne("select day from tiki_pageviews where pageviews=".$stats["bestpvs"]);
    $stats["worstpvs"]=$this->getOne("select min(pageviews) from tiki_pageviews");
    $stats["worstday"]=$this->getOne("select day from tiki_pageviews where pageviews=".$stats["worstpvs"]);
    return $stats;
  }
}

$statslib= new StatsLib($dbTiki);
?>

