<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class SearchLib extends TikiLib {
	function SearchLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to SearchLib constructor");
		}

		$this->db = $db;
		$this->wordlist_cache = array(); // for caching queries to the LRU-cache-list.
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
	  $exact=$this->find_exact($where,$words,$offset, $maxRecords);
	  $part=$this->find_part($where,$words,$offset, $maxRecords);
	  $res=array();
	  $res["data"]=array_merge($exact["data"],$part["data"]);
	  $res["cant"]=$exact["cant"]+$part["cant"];
	  return $res;
	}


        function &find_part($where,$words,$offset, $maxRecords) {
          $words=preg_split("/[\W]+/",$words,-1,PREG_SPLIT_NO_EMPTY);
          if (count($words)>0) {
          switch($where) {
            case "wikis":
              return $this->find_part_wiki($words,$offset, $maxRecords);
              break;
            case "forums":
              return $this->find_part_forums($words,$offset, $maxRecords);
              break;
            case "articles":
              return $this->find_part_articles($words,$offset, $maxRecords);
              break;
            case "blogs":
              return $this->find_part_blogs($words,$offset, $maxRecords);
              break;
            case "posts":
              return $this->find_part_blog_posts($words,$offset, $maxRecords);
              break;
            case "faqs":
              return $this->find_part_faqs($words,$offset, $maxRecords);
              break;
            case "directory":
              return $this->find_part_directory($words,$offset, $maxRecords);
              break;
            case "galleries":
              return $this->find_part_imggals($words,$offset, $maxRecords);
              break;
            case "images":
              return $this->find_part_img($words,$offset, $maxRecords);
              break;
            case "trackers":
              return $this->find_part_trackers($words,$offset, $maxRecords);
              break;

            default:
              return $this->find_part_all($words,$offset, $maxRecords);
              break;
          }
          }
        }
	
	function &refresh_lru_wordlist($syllable) {
		global $search_max_syllwords;
		global $search_lru_length;
		global $search_lru_purge_rate;
		// delete from wordlist and lru list
		$this->query("delete from `tiki_searchwords` where `syllable`=?",array($syllable),-1,-1,false);
		$this->query("delete from `tiki_searchsyllable` where `syllable`=?",array($syllable),-1,-1,false);
		// search the searchindex - can take long time
		$ret=array();
		if (!isset($search_max_syllwords))
			$search_max_syllwords = 100;
		$query="select `searchword`, sum(`count`) as `cnt` from `tiki_searchindex`
			where `searchword` like ? group by `searchword` order by `cnt` desc";
		$result=$this->query($query,array('%'.$syllable.'%'),$search_max_syllwords); // search_max_syllwords: how many different searchwords that contain the syllable are taken into account?. Sortet by number of occurences.
		while ($res = $result->fetchRow()) {
			$ret[]=$res["searchword"];
		}
		// cache this long running query
		foreach($ret as $searchword) {
			$this->query("insert into `tiki_searchwords` (`syllable`,`searchword`) values (?,?)",array($syllable,$searchword),-1,-1,false);
			}
		// set lru list parameters
		$now=time();
		$this->query("insert into `tiki_searchsyllable`(`syllable`,`lastUsed`,`lastUpdated`) values (?,?,?)",
			array($syllable,(int) $now,(int) $now));
		
		// at random rate: check length of lru list and purge these that
		// have not been used for long time. This is what a lru list 
		// basically does
		list($usec, $sec) = explode(" ",microtime());
		srand (ceil($sec+100*$usec));
		if(rand(1,$search_lru_purge_rate)==1) {
			$lrulength=$this->getOne("select count(*) from `tiki_searchsyllable`",array());
			if ($lrulength > $search_lru_length) { // only purge if lru list is long.
				//purge oldest
				$diff=$lrulength-$search_lru_length;
				$oldwords=array();
				$query="select `syllable` from `tiki_searchsyllable` order by `lastUsed` asc";
				$result=$this->query($query,array(),$diff);
				while ($res = $result->fetchRow()) {
					//we probably cannot delete now. to avoid database deadlocks
					//we save the words and delete later
					$oldwords[]=$res["syllable"];
				}
				foreach($oldwords as $oldword) {
					$this->query("delete from `tiki_searchwords` where `syllable`=?",array($oldword),-1,-1,false);
					$this->query("delete from `tiki_searchsyllable` where `syllable`=?",array($oldword),-1,-1,false);
				}

			}
		}
		return $ret;
	}

	function &get_lru_wordlist($syllable) {
		if(!isset($this->wordlist_cache[$syllable])) {
        		$query="select `searchword` from `tiki_searchwords` where `syllable`=?";
        		$result=$this->query($query,array($syllable));
        		while ($res = $result->fetchRow()) {
        			$this->wordlist_cache[$syllable][]=$res["searchword"];
        		}
		}
		return $this->wordlist_cache[$syllable];
	}

	function &get_wordlist_from_syllables($syllables) {
		$ret=array();
		global $search_syll_age;
		foreach($syllables as $syllable) {
		  //Have a look at the lru list (tiki_searchsyllable)
		  $bindvars=array($syllable);
		  $age=time()-$this->getOne("select `lastUpdated` from `tiki_searchsyllable` where `syllable`=?",$bindvars);
		  if(!$age || $age>($search_syll_age*3600)) {// older than search_syll_age hours
		  	$a=$this->refresh_lru_wordlist($syllable);
			$ret=array_merge($ret,$a);
		  } else {

		  	// get wordlist
			$ret=array_merge($ret,$this->get_lru_wordlist($syllable));
		  }

		  // update lru list status
		  $now=time();
		  $this->query("update `tiki_searchsyllable` set `lastUsed`=? where `syllable`=?",array((int) $now,$syllable));
		}
		return $ret;
	}

	function &find_part_wiki($words,$offset, $maxRecords) {
		return $this->find_exact_wiki($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
	}

        function &find_part_articles($words,$offset, $maxRecords) {
                return $this->find_exact_articles($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_forums($words,$offset, $maxRecords) {
                return $this->find_exact_forums($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_blogs($words,$offset, $maxRecords) {
                return $this->find_exact_blogs($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_blog_posts($words,$offset, $maxRecords) {
                return $this->find_exact_blog_posts($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_faqs($words,$offset, $maxRecords) {
                return $this->find_exact_faqs($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_directory($words,$offset, $maxRecords) {
                return $this->find_exact_directory($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_imggals($words,$offset, $maxRecords) {
                return $this->find_exact_imggals($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_img($words,$offset, $maxRecords) {
                return $this->find_exact_img($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function &find_part_trackers($words,$offset, $maxRecords) {
                return $this->find_exact_trackers($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }



        function &find_part_all($words,$offset, $maxRecords) {
          $wikiresults=$this->find_part_wiki($words,$offset, $maxRecords);
          $artresults=$this->find_part_articles($words,$offset, $maxRecords);
          $forumresults=$this->find_part_forums($words,$offset, $maxRecords);
          $blogresults=$this->find_part_blogs($words,$offset, $maxRecords);
          $blogpostsresults=$this->find_part_blog_posts($words,$offset, $maxRecords);
          $faqresults=$this->find_part_faqs($words,$offset, $maxRecords);
          $dirresults=$this->find_part_directory($words,$offset, $maxRecords);
          $imggalsresults=$this->find_part_imggals($words,$offset, $maxRecords);
          $imgresults=$this->find_part_img($words,$offset, $maxRecords);
          $trackerresults=$this->find_part_trackers($words,$offset, $maxRecords);

          //merge the results
          $res=array();
          $res["data"]=array_merge($wikiresults["data"],$artresults["data"],
                        $blogresults["data"],$faqresults["data"],
                        $blogpostsresults["data"],$forumresults["data"],
                        $dirresults["data"],$imggalsresults["data"],
                        $imgresults["data"],$trackerresults["data"]);
          $res["cant"]=$wikiresults["cant"]+$artresults["cant"]+
                        $blogresults["cant"]+$faqresults["cant"]+
                        $blogpostsresults["cant"]+$forumresults["cant"]+
                        $dirresults["cant"]+$imggalsresults["cant"]+
                        $imgresults["cant"]+$trackerresults["cant"];
          return ($res);
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
	    case "faqs":
	      return $this->find_exact_faqs($words,$offset, $maxRecords);
	      break;
	    case "directory":
	      return $this->find_exact_directory($words,$offset, $maxRecords);
	      break;
            case "galleries":
              return $this->find_exact_imggals($words,$offset, $maxRecords);
              break;
            case "images":
              return $this->find_exact_img($words,$offset, $maxRecords);
              break;
	    case "trackers":
	      return $this->find_exact_trackers($words,$offset, $maxRecords);
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
	  $faqresults=$this->find_exact_faqs($words,$offset, $maxRecords);
	  $dirresults=$this->find_exact_directory($words,$offset, $maxRecords);
	  $imggalsresults=$this->find_exact_imggals($words,$offset, $maxRecords);
	  $imgresults=$this->find_exact_img($words,$offset, $maxRecords);
	  $trackerresults=$this->find_exact_trackers($words,$offset, $maxRecords);

	  //merge the results
	  $res=array();
	  $res["data"]=array_merge($wikiresults["data"],$artresults["data"],
	  		$blogresults["data"],$faqresults["data"],
			$blogpostsresults["data"],$forumresults["data"],
			$dirresults["data"],$imggalsresults["data"],
			$imgresults["data"],$trackerresults["data"]);
	  $res["cant"]=$wikiresults["cant"]+$artresults["cant"]+
	  		$blogresults["cant"]+$faqresults["cant"]+
			$blogpostsresults["cant"]+$forumresults["cant"]+
			$dirresults["cant"]+$imggalsresults["cant"]+
			$imgresults["cant"]+$trackerresults["cant"];
	  return ($res);
	}


        function &find_exact_trackers($words,$offset, $maxRecords) {
	  global $feature_trackers;
          if ($feature_trackers == 'y' && count($words) >0 ) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                t.`description`,t.`lastModif`,t.`name` from
                `tiki_searchindex` s, `tiki_trackers` t where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='tracker' and
                ".$this->sql_cast("s.`page`","int")."=t.`trackerId`";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_trackers` t where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='tracker' and
                ".$this->sql_cast("s.`page`","int")."=t.`trackerId`";
            $cant1=$this->getOne($querycant,$words);
            $ret1=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_tracker.php?trackerId=".urlencode($res["page"]);
              $ret1[] = array(
                'pageName' => $res["name"],
                'location' => tra("Tracker"),
                'data' => substr($res["description"],0,250),
                'hits' => tra("Unknown"),
                'lastModif' => $res["lastModif"],
                'href' => $href,
                'relevance' => 1
              );
            }
	    
	    //tracker items
	    $ret2=array();
	    $cant2=0;
	    if ($cant1 < $offset+$maxRecords) {

	      //new offset and maxRecords
	      $offset-=$cant1;
	      if ($offset < 0) {
	        $maxRecords+=$offset;
		$offset=0;
	      }

	      $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	          t.`lastModif`,t.`trackerId` from 
	  	  `tiki_searchindex` s, `tiki_tracker_items` t where `searchword` in
	  	  (".implode(',',array_fill(0,count($words),'?')).") and
		  s.`location`='trackeritem' and
		  ".$this->sql_cast("s.`page`","int")."=t.`itemId`";
	      $result=$this->query($query,$words,$maxRecords,$offset);
	      $querycant="select count(*) from `tiki_searchindex` s, `tiki_tracker_items` t where `searchword` in
	          (".implode(',',array_fill(0,count($words),'?')).") and
		  s.`location`='trackeritem' and
		  ".$this->sql_cast("s.`page`","int")."=t.`itemId`";
	      $cant2=$this->getOne($querycant,$words);
	      while ($res = $result->fetchRow()) {
	        $href = "tiki-view_tracker_item.php?trackerId=".urlencode($res["trackerId"])."&amp;itemId=".urlencode($res["page"]);
	        $ret2[] = array(
	          'pageName' => $res["page"],
		  'location' => tra("Trackeritem"),
		  'data' => tra("Unknown"),
		  'hits' => tra("Unknown"),
		  'lastModif' => $res["lastModif"],
		  'href' => $href,
		  'relevance' => 1
	        );
	      }
	    }
	    $ret=array();
	    $ret["data"]=array_merge($ret1,$ret2);
	    $ret["cant"]=$cant1+$cant2;
	    return $ret;

          } else {
            return array('data' => array(),'cant' => 0);
          }
        }



        function &find_exact_imggals($words,$offset, $maxRecords) {
          global $feature_galleries;
          if ($feature_galleries == 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                g.`description`,g.`hits`,g.`lastModif`,g.`name` from
                `tiki_searchindex` s, `tiki_galleries` g where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='imggal' and
                ".$this->sql_cast("s.`page`","int")."=g.`galleryId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_galleries` g where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='imggal' and
                ".$this->sql_cast("s.`page`","int")."=g.`galleryId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-browse_gallery.php?galleryId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["name"],
                'location' => tra("Image Gallery"),
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

        function &find_exact_img($words,$offset, $maxRecords) {
          global $feature_galleries;
          if ($feature_galleries == 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                g.`description`,g.`hits`,g.`created`,g.`name` from
                `tiki_searchindex` s, `tiki_images` g where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='img' and
                ".$this->sql_cast("s.`page`","int")."=g.`imageId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_images` g where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='img' and
                ".$this->sql_cast("s.`page`","int")."=g.`imageId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-browse_image.php?imageId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["name"],
                'location' => tra("Image"),
                'data' => substr($res["description"],0,250),
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

	function &find_exact_blogs($words,$offset, $maxRecords) {
          global $feature_blogs;
          if ($feature_blogs == 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                b.`description`,b.`hits`,b.`lastModif`,b.`title` from
                `tiki_searchindex` s, `tiki_blogs` b where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog' and
                ".$this->sql_cast("s.`page`","int")."=b.`blogId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_blogs` b where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog' and
                ".$this->sql_cast("s.`page`","int")."=b.`blogId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_blog.php?blogId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["title"],
                'location' => tra("Blog"),
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
          if ($feature_blogs == 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                bp.`data`,b.`hits`,b.`title` as `btitle`,bp.`created`,b.`title`,b.`blogId` from
                `tiki_searchindex` s, `tiki_blogs` b ,`tiki_blog_posts` bp where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog_post' and
                ".$this->sql_cast("s.`page`","int")."=bp.`postId` and
		bp.`blogId`=b.`blogId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_blog_posts` bp where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog_post' and
                ".$this->sql_cast("s.`page`","int")."=bp.`postId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_blog_post.php?blogId=".urlencode($res["blogId"])."&amp;postId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["btitle"],
                'location' => tra("Blog")."::".$res["title"],
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
	  if ($feature_articles  == 'y'  && count($words) >0) {
	    $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	    	a.`heading`,a.`reads`,a.`publishDate`,a.`title` from
		`tiki_searchindex` s, `tiki_articles` a where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='article' and
		".$this->sql_cast("s.`page`","int")."=a.`articleId` order by `reads` desc";
	    $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_articles` a where `searchword` in
	     	(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='article' and
		".$this->sql_cast("s.`page`","int")."=a.`articleId`";
	    $cant=$this->getOne($querycant,$words);
	    $ret=array();
	    while ($res = $result->fetchRow()) {
	      $href = "tiki-read_article.php?articleId=".urlencode($res["page"]);
	      $ret[] = array(
	        'pageName' => $res["title"],
	        'location' => tra("Article"),
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
	  if ($feature_wiki == 'y'  && count($words) >0) {
	  $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	  	p.`data`, p.`hits`, p.`lastModif` from
	        `tiki_searchindex` s, `tiki_pages` p  where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='wiki' and
		s.`page`=p.`pageName` order by `count` desc";
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
	      'location' => tra("Wiki"),
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

        function &find_exact_directory($words,$offset, $maxRecords) {
          global $feature_directory;
          if ($feature_directory== 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                d.`description`,d.`hits`,d.`name` from
                `tiki_searchindex` s, `tiki_directory_categories` d where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='dir_cat' and
                ".$this->sql_cast("s.`page`","int")."=d.`categId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_directory_categories` d where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='dir_cat' and
                ".$this->sql_cast("s.`page`","int")."=d.`categId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-directory_browse.php?parent=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["name"],
                'location' => tra("Directory category"),
                'data' => substr($res["description"],0,250),
                'hits' => $res["hits"],
                'lastModif' => time(), //not determinable
                'href' => $href,
                'relevance' => $res["hits"]
              );
            }
            $dsiteres=$this->find_exact_directory_sites($words,$offset, $maxRecords);
            return array('data' => array_merge($ret,$dsiteres["data"]),'cant' => $cant+$dsiteres["cant"]);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

        function &find_exact_directory_sites($words,$offset, $maxRecords) {
          global $feature_directory;
          if ($feature_directory== 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                d.`description`,d.`hits`,d.`name`,d.`lastModif`,cs.`categId` from
                `tiki_searchindex` s, `tiki_directory_sites` d ,`tiki_category_sites` cs where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='dir_site' and
                ".$this->sql_cast("s.`page`","int")."=d.`siteId` and 
		cs.`siteId`=d.`siteId`
		order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_directory_sites` d , `tiki_category_sites` cs where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='dir_site' and
                ".$this->sql_cast("s.`page`","int")."=d.`siteId` and
		cs.`siteId`=d.`siteId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-directory_browse.php?parent=".urlencode($res["categId"]);
              $ret[] = array(
                'pageName' => $res["name"],
                'location' => tra("Directory"),
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


	function &find_exact_faqs($words,$offset, $maxRecords) {
          global $feature_faqs;
          if ($feature_faqs== 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`description`,f.`hits`,f.`created`,f.`title` from
                `tiki_searchindex` s, `tiki_faqs` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='faq' and
                ".$this->sql_cast("s.`page`","int")."=f.`faqId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_faqs` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='faq' and
                ".$this->sql_cast("s.`page`","int")."=f.`faqId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_faq.php?faqId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["title"],
                'location' => tra("FAQ"),
                'data' => substr($res["description"],0,250),
                'hits' => $res["hits"],
                'lastModif' => $res["created"],
                'href' => $href,
                'relevance' => $res["hits"]
              );
            }
            $fquesres=$this->find_exact_faqquestions($words,$offset, $maxRecords);
            return array('data' => array_merge($ret,$fquesres["data"]),'cant' => $cant+$fquesres["cant"]);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

        function &find_exact_faqquestions($words,$offset, $maxRecords) {
          global $feature_faqs;
          if ($feature_faqs== 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`question`,faq.`hits`,faq.`created`,faq.`title`,f.`answer`,f.`faqId` from
                `tiki_searchindex` s, `tiki_faqs` faq, `tiki_faq_questions` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='faq_question' and
                ".$this->sql_cast("s.`page`","int")."=f.`questionId` and
		f.`faqId`=faq.`faqId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_faqs` faq, `tiki_faq_questions` f  where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='faq_question' and
                ".$this->sql_cast("s.`page`","int")."=f.`questionId` and
                f.`faqId`=faq.`faqId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_faq.php?faqId=".urlencode($res["faqId"])."#".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => substr($res["question"],0,40),
                'location' => tra("FAQ")."::".$res["title"],
                'data' => substr($res["answer"],0,250),
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


        function &find_exact_forums($words,$offset, $maxRecords) {
          global $feature_forums;
          if ($feature_forums== 'y'  && count($words) >0) {
            $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`description`,f.`hits`,f.`lastPost`,f.`name` from
                `tiki_searchindex` s, `tiki_forums` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='forum' and
                ".$this->sql_cast("s.`page`","int")."=f.`forumId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $querycant="select count(*) from `tiki_searchindex` s, `tiki_forums` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='forum' and
                ".$this->sql_cast("s.`page`","int")."=f.`forumId`";
            $cant=$this->getOne($querycant,$words);
            $ret=array();
            while ($res = $result->fetchRow()) {
              $href = "tiki-view_forum.php?forumId=".urlencode($res["page"]);
              $ret[] = array(
                'pageName' => $res["name"],
                'location' => tra("Forum"),
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
	  if ($feature_forums == 'y'  && count($words) >0) {
	  $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	  	f.`data`,f.`hits`,f.`commentDate`,f.`object`,f.`title`,fo.`name` from
		`tiki_searchindex` s, `tiki_comments` f,`tiki_forums` fo where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='forumcomment' and
		".$this->sql_cast("s.`page`","int")."=f.`threadId` and
		fo.`forumId`=".$this->sql_cast("f.`object`","int")." order by `count` desc";
	  $result=$this->query($query,$words,$maxRecords,$offset);

	  $querycant="select count(*) from `tiki_searchindex` s, `tiki_comments` f ,`tiki_forums` fo where `searchword` in
	  	(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='forumcomment' and
		".$this->sql_cast("s.`page`","int")."=f.`threadId` and
		fo.`forumId`=".$this->sql_cast("f.`object`","int")." order by `count` desc";
	  $cant=$this->getOne($querycant,$words);
	  $ret=array();
	  while ($res = $result->fetchRow()) {
	    $href = "tiki-view_forum_thread.php?comments_parentId=".urlencode($res["page"])."&amp;forumId=".urlencode($res["object"]);
	    $ret[] = array(
	      'pageName' => $res["title"],
	      'location' => tra("Forum")."::".$res["name"],
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
