<?php

class SearchLib extends TikiLib {
	function SearchLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to SearchLib constructor");
		}

		$this->db = $db;
	}

	function register_search($words) {
		$words = addslashes($words);

		$words = preg_split("/\s/", $words);

		foreach ($words as $word) {
			$word = trim($word);

			$cant = $this->getOne("select count(*) from `tiki_search_stats` where `term`=?",array($word));

			if ($cant) {
				$query = "update `tiki_search_stats` set `hits`= `hits` + 1 where `term`=?";
			} else {
				$query = "insert into `tiki_search_stats` (`term`,`hits`) values (?,1)";
			}

			$result = $this->query($query,array($word));
		}
	}

	function &find($where,$words,$offset, $maxRecords) {
	  return($this->find_exact($where,$words,$offset, $maxRecords));
	}

	function &find_exact($where,$words,$offset, $maxRecords) {
	  $words=preg_split("/[\W]+/",$words,-1,PREG_SPLIT_NO_EMPTY);
	  if (count($words)>0) {
	  switch($where) {
	    case "wikis":
	      return $this->find_exact_wiki($words,$offset, $maxRecords);
	      break;
	    case "forums":
	      return $this->find_exact_forums($words,$offset, $maxRecords);
	      break;
	    case "articles":
	      return $this->find_exact_articles($words,$offset, $maxRecords);
	      break;
	    case "blogs":
	      return $this->find_exact_blogs($words,$offset, $maxRecords);
	      break;
	    case "posts":
	      return $this->find_exact_blog_posts($words,$offset, $maxRecords);
	      break;
	    default:
	      return $this->find_exact_all($words,$offset, $maxRecords);
	      break;
	  }
	  }
	}

	function &find_exact_all($words,$offset, $maxRecords) {
	  $wikiresults=$this->find_exact_wiki($words,$offset, $maxRecords);
	  $artresults=$this->find_exact_articles($words,$offset, $maxRecords);
	  $forumresults=$this->find_exact_forums($words,$offset, $maxRecords);
	  $blogresults=$this->find_exact_blogs($words,$offset, $maxRecords);
	  $blogpostsresults=$this->find_exact_blog_posts($words,$offset, $maxRecords);


	  //merge the results
	  $res=array();
	  $res["data"]=array_merge($wikiresults["data"],$artresults["data"],
	  		$blogresults["data"],
			$blogpostsresults["data"],$forumresults["data"]);
	  $res["cant"]=$wikiresults["cant"]+$artresults["cant"]+
	  		$blogresults["cant"]+
			$blogpostsresults["cant"]+$forumresults["cant"];
	  return ($res);
	}

	function &find_exact_blogs($words,$offset, $maxRecords) {
          global $feature_blogs;
          if ($feature_blogs == 'y') {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                b.`description`,b.`hits`,b.`lastModif`,b.`title` from
                `tiki_searchindex` s, `tiki_blogs` b where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog' and
                s.`page`=b.`blogId`";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_blogs` b where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog' and
                s.`page`=b.`blogId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_blog.php?blogId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["title"],
                'location' => $res["location"],
                'data' => substr($res["description"],0,250),
                'hits' => $res["hits"],
                'lastModif' => $res["lastModif"],
                'href' => $href,
                'relevance' => $res["hits"]
              );
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }


        function &find_exact_blog_posts($words,$offset, $maxRecords) {
          global $feature_blogs;
          if ($feature_blogs == 'y') {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                bp.`data`,b.`hits`,b.`title` as `btitle`,bp.`created`,b.`title`,b.`blogId` from
                `tiki_searchindex` s, `tiki_blogs` b ,`tiki_blog_posts` bp where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog_post' and
                s.`page`=bp.`postId` and
		bp.`blogId`=b.`blogId`";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_blog_posts` bp where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog_post' and
                s.`page`=bp.`postId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_blog_post.php?blogId=".urlencode($res["blogId"])."&amp;postId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["btitle"]."::".$res["title"],
                'location' => tra("Blog post"),
                'data' => substr($res["data"],0,250),
                'hits' => $res["hits"],
                'lastModif' => $res["created"],
                'href' => $href,
                'relevance' => $res["hits"]
              );
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

	function &find_exact_articles($words,$offset, $maxRecords) {
	  global $feature_articles;
	  if ($feature_articles  == 'y') {
	    $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	    	a.`heading`,a.`reads`,a.`publishDate`,a.`title` from
		`tiki_searchindex` s, `tiki_articles` a where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='article' and
		s.`page`=a.`articleId`";
	    $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_articles` a where `searchword` in
	     	(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='article' and
		s.`page`=a.`articleId`";
	    $cant=$this->getOne($querycant,$words);
	    $ret=array();
	    while ($res = $result->fetchRow()) {
	      $href = "tiki-read_article.php?articleId=".urlencode($res["page"]);
	      $ret[] = array(
	        'pageName' => $res["title"],
	        'location' => $res["location"],
		'data' => substr($res["heading"],0,250),
		'hits' => $res["reads"],
		'lastModif' => $res["publishDate"],
		'href' => $href,
		'relevance' => $res["reads"]
              );
	    }
	    return array('data' => $ret,'cant' => $cant);
	  } else {
	    return array('data' => array(),'cant' => 0);
	  }
	}
	
	function &find_exact_wiki($words,$offset, $maxRecords) {
	  global $feature_wiki;
	  if ($feature_wiki == 'y') {
	  $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	  	p.`data`, p.`hits`, p.`lastModif` from
	        `tiki_searchindex` s, `tiki_pages` p  where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='wiki' and
		s.`page`=p.`pageName`";
	  $result=$this->query($query,$words,$maxRecords,$offset);

	  $querycant="select count(*) from `tiki_searchindex` s, `tiki_pages` p where
	  	`searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='wiki' and
		s.`page`=p.`pageName`";
	  $cant=$this->getOne($querycant,$words);

	  $ret=array();
          while ($res = $result->fetchRow()) {
            $href = "tiki-index.php?page=".urlencode($res["page"]);
            $ret[] = array(
              'pageName' => $res["page"],
	      'location' => $res["location"],
              'data' => substr($res["data"],0,250),
              'hits' => $res["hits"],
              'lastModif' => $res["lastModif"],
              'href' => $href,
              'relevance' => $res["count"]
            );
          }

          return array('data' => $ret,'cant' => $cant);
	  } else {
	  return array('data' => array(),'cant' => 0);
	  }
        }

        function &find_exact_forums($words,$offset, $maxRecords) {
          global $feature_forums;
          if ($feature_forums== 'y') {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`description`,f.`hits`,f.`lastPost`,f.`name` from
                `tiki_searchindex` s, `tiki_forums` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='forum' and
                s.`page`=f.`forumId`";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_forums` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='forum' and
                s.`page`=f.`forumId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_forum.php?forumId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["name"],
                'location' => $res["location"],
                'data' => substr($res["description"],0,250),
                'hits' => $res["hits"],
                'lastModif' => $res["lastPost"],
                'href' => $href,
                'relevance' => $res["hits"]
              );
            }
            $fcommres=$this->find_exact_forumcomments($words,$offset, $maxRecords);
            return array('data' => array_merge($ret,$fcommres["data"]),'cant' => $cant+$fcommres["cant"]);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

	function &find_exact_forumcomments($words,$offset, $maxRecords) {
	  global $feature_forums;
	  if ($feature_forums == 'y') {
	  $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	  	f.`data`,f.`hits`,f.`commentDate`,f.`object`,f.`title` from
		`tiki_searchindex` s, `tiki_comments` f where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='forumcomment' and
		s.`page`=f.`threadId`";
	  $result=$this->query($query,$words,$maxRecords,$offset);

	  $querycant="select count(*) from `tiki_searchindex` s, `tiki_comments` f where `searchword` in
	  	(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='forumcomment' and
		s.`page`=f.`threadId`";
	  $cant=$this->getOne($querycant,$words);
	  $ret=array();
	  while ($res = $result->fetchRow()) {
	    $href = "tiki-view_forum_thread.php?comments_parentId=".urlencode($res["page"])."&amp;forumId=".urlencode($res["object"]);
	    $ret[] = array(
	      'pageName' => $res["title"],
	      'location' => 'forum comment',
	      'data' => substr($res["data"],0,250),
	      'hits' => $res["hits"],
	      'lastModif' => $res["commentDate"],
	      'href' => $href,
	      'relevance' => $res["count"]
	    );
	  }
	  return array('data' => $ret,'cant' => $cant);
	  }else {
	  return array('data' => array(),'cant' => 0);
	  }
	}


} # class SearchLib

?>
