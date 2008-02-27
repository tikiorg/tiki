<?php
// $Id: searchlib.php,v 1.38.2.3 2008-02-27 15:18:47 nyloth Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class SearchLib extends TikiLib {
	function SearchLib($db) {
		$this->TikiLib($db);
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

	function find($where,$words,$offset, $maxRecords, $fulltext='', $filter='') {
	  $exact=$this->find_exact($where,$words,$offset, $maxRecords, $filter);
	  $part=$this->find_part($where,$words,$offset, $maxRecords, $filter);
          if (count($part)) foreach ($part["data"] as $p) {
            $same = false;
            foreach ($exact["data"] as $e) {
              if ($p["pageName"] == $e["pageName"] && $p["location"] == $e["location"] && $p['href'] == $e['href']) { // need to check also on href for img
                $same = true;
                break;
              }
            }
            if (!$same) {
              array_push($exact["data"], $p);
              $exact["cant"]++;
            }
          }
	  return $exact;
	}


        function find_part($where,$words,$offset, $maxRecords, $filter='') {
          $words=preg_split("/[\s]+/",$words,-1,PREG_SPLIT_NO_EMPTY);
          if (count($words)>0) {
          switch($where) {
            case "wikis":
              return $this->find_part_wiki($words,$offset, $maxRecords);
              break;
            case "forums":
              return $this->find_part_forums($words,$offset, $maxRecords, $filter);
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
            case "files":
              return $this->find_part_files($words,$offset, $maxRecords);
              break;

            default:
              return $this->find_part_all($words,$offset, $maxRecords);
              break;
          }
          }
        }

	function refresh_lru_wordlist($syllable) {
		global $prefs;
		// delete from wordlist and lru list
		$this->query("delete from `tiki_searchwords` where `syllable`=?",array($syllable),-1,-1,false);
		$this->query("delete from `tiki_searchsyllable` where `syllable`=?",array($syllable),-1,-1,false);
		// search the searchindex - can take long time
		$ret=array();
		if (!isset($prefs['search_max_syllwords']))
			$prefs['search_max_syllwords'] = 100;
		$query="select `searchword`, sum(`count`) as `cnt` from `tiki_searchindex`
			where `searchword` like ? group by `searchword` order by `cnt` desc";
		$result=$this->query($query,array('%'.$syllable.'%'),$prefs['search_max_syllwords']); // search_max_syllwords: how many different searchwords that contain the syllable are taken into account?. Sortet by number of occurences.
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
		if(rand(1,$prefs['search_lru_purge_rate'])==1) {
			$lrulength=$this->getOne("select count(*) from `tiki_searchsyllable`",array());
			if ($lrulength > $prefs['search_lru_length']) { // only purge if lru list is long.
				//purge oldest
				$diff=$lrulength-$prefs['search_lru_length'];
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

	function get_lru_wordlist($syllable) {
		if(!isset($this->wordlist_cache[$syllable])) {
		        $this->wordlist_cache[$syllable] = array();
        		$query="select `searchword` from `tiki_searchwords` where `syllable`=?";
        		$result=$this->query($query,array($syllable));
        		while ($res = $result->fetchRow()) {
        			$this->wordlist_cache[$syllable][]=$res["searchword"];
        		}
		}
		return $this->wordlist_cache[$syllable];
	}

	function get_wordlist_from_syllables($syllables) {
		$ret=array();
		global $prefs;
		foreach($syllables as $syllable) {
		  //Have a look at the lru list (tiki_searchsyllable)
		  $bindvars=array($syllable);
		  $age=time()-$this->getOne("select `lastUpdated` from `tiki_searchsyllable` where `syllable`=?",$bindvars);
		  if(!$age || $age>($prefs['search_syll_age']*3600)) {// older than search_syll_age hours
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

	function find_part_wiki($words,$offset, $maxRecords) {
		return $this->find_exact_wiki($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
	}

        function find_part_articles($words,$offset, $maxRecords) {
                return $this->find_exact_articles($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_forums($words,$offset, $maxRecords, $filter='') {
                return $this->find_exact_forums($this->get_wordlist_from_syllables($words),$offset, $maxRecords, $filter);
        }

        function find_part_blogs($words,$offset, $maxRecords) {
                return $this->find_exact_blogs($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_blog_posts($words,$offset, $maxRecords) {
                return $this->find_exact_blog_posts($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_faqs($words,$offset, $maxRecords) {
                return $this->find_exact_faqs($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_directory($words,$offset, $maxRecords) {
                return $this->find_exact_directory($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_imggals($words,$offset, $maxRecords) {
                return $this->find_exact_imggals($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_img($words,$offset, $maxRecords) {
                return $this->find_exact_img($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

        function find_part_trackers($words,$offset, $maxRecords) {
                return $this->find_exact_trackers($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }

      function find_part_files($words,$offset, $maxRecords) {
                return $this->find_exact_files($this->get_wordlist_from_syllables($words),$offset, $maxRecords);
        }



        function find_part_all($words,$offset, $maxRecords) {

		global $prefs, $tiki_p_view, $tiki_p_view_directory, $tiki_p_view_image_gallery, $tiki_p_view_file_gallery, $tiki_p_read_article, $tiki_p_forum_read, $tiki_p_read_blog, $tiki_p_view_faqs, $tiki_p_view_trackers, $tiki_p_download_files;

			if ($prefs['feature_wiki'] == 'y' && $tiki_p_view == 'y') {
				$wikiresults=$this->find_part_wiki($words,$offset, $maxRecords);
			} else {
				$wikiresults['data'] = array();
				$wikiresults['cant'] = 0;
			}
			if ($prefs['feature_articles'] == 'y' && $tiki_p_read_article == 'y') {
				$artresults=$this->find_part_articles($words,$offset, $maxRecords);
			} else {
				$artresults['data'] = array();
				$artresults['cant'] = 0;
			}
			if ($prefs['feature_forums'] == 'y' && $tiki_p_forum_read == 'y') {
				$forumresults=$this->find_part_forums($words,$offset, $maxRecords);
			} else {
				$forumresults['data'] = array();
				$forumresults['cant'] = 0;
			}
			if ($prefs['feature_blogs'] == 'y' && $tiki_p_read_blog == 'y') {
				$blogresults=$this->find_part_blogs($words,$offset, $maxRecords);
			} else {
				$blogresults['data'] = array();
				$blogresults['cant'] = 0;
			}
			if ($prefs['feature_blogs'] == 'y' && $tiki_p_read_blog == 'y') {
				$blogpostsresults=$this->find_part_blog_posts($words,$offset, $maxRecords);
			} else {
				$blogpostsresults['data'] = array();
				$blogpostsresults['cant'] = 0;
			}
			if ($prefs['feature_faqs'] == 'y' && $tiki_p_view_faqs == 'y') {
				$faqresults=$this->find_part_faqs($words,$offset, $maxRecords);
			} else {
				$faqresults['data'] = array();
				$faqresults['cant'] = 0;
			}
			if ($prefs['feature_directory'] == 'y' && $tiki_p_view_directory == 'y') {
				$dirresults=$this->find_part_directory($words,$offset, $maxRecords);
			} else {
				$dirresults['data'] = array();
				$dirresults['cant'] = 0;
			}
			if ($prefs['feature_galleries'] == 'y' && $tiki_p_view_image_gallery == 'y') {
				$imggalsresults=$this->find_part_imggals($words,$offset, $maxRecords);
			} else {
				$imggalsresults['data'] = array();
				$imggalsresults['cant'] = 0;
			}
			if ($prefs['feature_galleries'] == 'y' && $tiki_p_view_image_gallery == 'y') {
				$imgresults=$this->find_part_img($words,$offset, $maxRecords);
			} else {
				$imgresults['data'] = array();
				$imgresults['cant'] = 0;
			}
			if ($prefs['feature_trackers'] == 'y' && $tiki_p_view_trackers == 'y') {
				$trackerresults=$this->find_part_trackers($words,$offset, $maxRecords);
			} else {
				$trackerresults['data'] = array();
				$trackerresults['cant'] = 0;
			}
			if ($prefs['feature_file_galleries'] == 'y' && $tiki_p_download_files == 'y') {
				$fileresults=$this->find_part_files($words,$offset, $maxRecords);
			} else {
				$fileresults['data'] = array();
				$fileresults['cant'] = 0;
			}

		  /* // check if feature is enabled before searching
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
          */

          //merge the results
          $res=array();
          $res["data"]=array_merge($wikiresults["data"],$artresults["data"],
                        $blogresults["data"],$faqresults["data"],
                        $blogpostsresults["data"],$forumresults["data"],
                        $dirresults["data"],$imggalsresults["data"],
                        $imgresults["data"],$trackerresults["data"],
				$fileresults["data"]);
          $res["cant"]=$wikiresults["cant"]+$artresults["cant"]+
                        $blogresults["cant"]+$faqresults["cant"]+
                        $blogpostsresults["cant"]+$forumresults["cant"]+
                        $dirresults["cant"]+$imggalsresults["cant"]+
                        $imgresults["cant"]+$trackerresults["cant"]+
				$fileresults["cant"];
          return ($res);
        }

		function find_exact($where,$words,$offset, $maxRecords, $filter='') {
	  $words=preg_split("/[\s]+/",$words,-1,PREG_SPLIT_NO_EMPTY);
	  if (count($words)>0) {
	  switch($where) {
	    case "wikis":
	      return $this->find_exact_wiki($words,$offset, $maxRecords);
	      break;
	    case "forums":
	      return $this->find_exact_forums($words,$offset, $maxRecords, $filter);
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
	    case "files":
	      return $this->find_exact_files($words,$offset, $maxRecords);
	      break;

	    default:
	      return $this->find_exact_all($words,$offset, $maxRecords);
	      break;
	  }
	  }
	}

	function find_exact_all($words,$offset, $maxRecords) {
		global $prefs, $tiki_p_view, $tiki_p_view_directory, $tiki_p_view_image_gallery, $tiki_p_view_file_gallery, $tiki_p_read_article, $tiki_p_forum_read, $tiki_p_read_blog, $tiki_p_view_faqs, $tiki_p_view_trackers, $tiki_p_download_files;

		if ($prefs['feature_wiki'] == 'y' && $tiki_p_view == 'y') {
			$wikiresults=$this->find_exact_wiki($words,$offset, $maxRecords);
		} else {
			$wikiresults['data'] = array();
			$wikiresults['cant'] = 0;
		}
		if ($prefs['feature_articles'] == 'y' && $tiki_p_read_article == 'y') {
			$artresults=$this->find_exact_articles($words,$offset, $maxRecords);
		} else {
			$artresults['data'] = array();
			$artresults['cant'] = 0;
		}
		if ($prefs['feature_forums'] == 'y' && $tiki_p_forum_read == 'y') {
			$forumresults=$this->find_exact_forums($words,$offset, $maxRecords);
		} else {
			$forumresults['data'] = array();
			$forumresults['cant'] = 0;
		}
		if ($prefs['feature_blogs'] == 'y' && $tiki_p_read_blog == 'y') {
			$blogresults=$this->find_exact_blogs($words,$offset, $maxRecords);
		} else {
			$blogresults['data'] = array();
			$blogresults['cant'] = 0;
		}
		if ($prefs['feature_blogs'] == 'y' && $tiki_p_read_blog == 'y') {
			$blogpostsresults=$this->find_exact_blog_posts($words,$offset, $maxRecords);
		} else {
			$blogpostsresults['data'] = array();
			$blogpostsresults['cant'] = 0;
		}
		if ($prefs['feature_faqs'] == 'y' && $tiki_p_view_faqs == 'y') {
			$faqresults=$this->find_exact_faqs($words,$offset, $maxRecords);
		} else {
			$faqresults['data'] = array();
			$faqresults['cant'] = 0;
		}
		if ($prefs['feature_directory'] == 'y' && $tiki_p_view_directory == 'y') {
			$dirresults=$this->find_exact_directory($words,$offset, $maxRecords);
		} else {
			$dirresults['data'] = array();
			$dirresults['cant'] = 0;
		}
		if ($prefs['feature_galleries'] == 'y' && $tiki_p_view_image_gallery == 'y') {
			$imggalsresults=$this->find_exact_imggals($words,$offset, $maxRecords);
		} else {
			$imggalsresults['data'] = array();
			$imggalsresults['cant'] = 0;
		}
		if ($prefs['feature_galleries'] == 'y' && $tiki_p_view_image_gallery == 'y') {
			$imgresults=$this->find_exact_img($words,$offset, $maxRecords);
		} else {
			$imgresults['data'] = array();
			$imgresults['cant'] = 0;
		}
		if ($prefs['feature_trackers'] == 'y' && $tiki_p_view_trackers == 'y') {
			$trackerresults=$this->find_exact_trackers($words,$offset, $maxRecords);
		} else {
			$trackerresults['data'] = array();
			$trackerresults['cant'] = 0;
		}
		if ($prefs['feature_file_galleries'] == 'y' && $tiki_p_download_files == 'y') {
			$fileresults=$this->find_exact_files($words,$offset, $maxRecords);
		} else {
			$fileresults['data'] = array();
			$fileresults['cant'] = 0;
		}

	  /* // should check if feature is enabled before searching
	  $artresults=$this->find_exact_articles($words,$offset, $maxRecords);
	  $forumresults=$this->find_exact_forums($words,$offset, $maxRecords);
	  $blogresults=$this->find_exact_blogs($words,$offset, $maxRecords);
	  $blogpostsresults=$this->find_exact_blog_posts($words,$offset, $maxRecords);
	  $faqresults=$this->find_exact_faqs($words,$offset, $maxRecords);
	  $dirresults=$this->find_exact_directory($words,$offset, $maxRecords);
	  $imggalsresults=$this->find_exact_imggals($words,$offset, $maxRecords);
	  $imgresults=$this->find_exact_img($words,$offset, $maxRecords);
	  $trackerresults=$this->find_exact_trackers($words,$offset, $maxRecords);
	  */

	  //merge the results
	  $res=array();
	  $res["data"]=array_merge($wikiresults["data"],$artresults["data"],
	  		$blogresults["data"],$faqresults["data"],
			$blogpostsresults["data"],$forumresults["data"],
			$dirresults["data"],$imggalsresults["data"],
			$imgresults["data"],$trackerresults["data"],
			$fileresults["data"]);
	  $res["cant"]=$wikiresults["cant"]+$artresults["cant"]+
	  		$blogresults["cant"]+$faqresults["cant"]+
			$blogpostsresults["cant"]+$forumresults["cant"]+
			$dirresults["cant"]+$imggalsresults["cant"]+
			$imgresults["cant"]+$trackerresults["cant"]+
			$fileresults["cant"];
	  return ($res);
	}


        function find_exact_trackers($words,$offset, $maxRecords) {
		global $prefs, $tiki_p_view_trackers_pending, $tiki_p_view_trackers_closed, $tikilib, $user, $trklib;
		include_once("lib/trackers/trackerlib.php");
          if ($prefs['feature_trackers'] == 'y' && count($words) >0 ) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                t.`description`,t.`lastModif`,t.`name` from
                `tiki_searchindex` s, `tiki_trackers` t where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='tracker' and
                ".$this->sql_cast("s.`page`","int")."=t.`trackerId`";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant1=0;
            $ret1=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res["page"],'tracker','tiki_p_view_trackers')) {
              ++$cant1;
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

	      $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
	          t.`lastModif`,t.`trackerId`, t.`status` from
	  	  `tiki_searchindex` s, `tiki_tracker_items` t  where `searchword` in
	  	  (".implode(',',array_fill(0,count($words),'?')).") and
		  s.`location`='trackeritem' and
		  s.`page` like concat(t.`itemId`,'#%')";
	      $result=$this->query($query,$words,$maxRecords,$offset);
	      $cant2=0;
	      while ($res = $result->fetchRow()) {
	       if($this->user_has_perm_on_object($user,$res['trackerId'],'tracker','tiki_p_view_trackers') &&
			($res["status"] == 'o' || ($res["status"] == 'p'  && $tiki_p_view_trackers_pending == "y") || ($res["status"] == 'c'  && $tiki_p_view_trackers_closed == "y"))) {
              ++$cant2;
		  list($itemId, $fieldId) = split("#", $res["page"]);
	        $href = "tiki-view_tracker_item.php?trackerId=".urlencode($res["trackerId"])."&amp;itemId=".urlencode($itemId);
	        $ret2[] = array(
	          'pageName' => "(#".$itemId.") ".$trklib->get_isMain_value($res["trackerId"], $res["page"]),
		  'location' => tra("Trackeritem"),
		  'data' => "",// we don't have the fieldId(s) of the item to select the data
		  'hits' => tra("Unknown"),
		  'lastModif' => $res["lastModif"],
		  'href' => $href,
		  'relevance' => 1
	        );
	       }
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



        function find_exact_imggals($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_galleries'] == 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                g.`description`,g.`hits`,g.`lastModif`,g.`name` from
                `tiki_searchindex` s, `tiki_galleries` g where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='imggal' and
                ".$this->sql_cast("s.`page`","int")."=g.`galleryId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res['page'],'image gallery','tiki_p_view_image_gallery')) {
              ++$cant;
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
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

        function find_exact_img($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_galleries'] == 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                g.`description`,g.`hits`,g.`created`,g.`name`, g.`galleryId` from
                `tiki_searchindex` s, `tiki_images` g, `tiki_galleries` gal where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='img' and 
                ".$this->sql_cast("s.`page`","int")."=g.`imageId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     // gallery system are only for admin - img has the gallery perm
	     if(($res['galleryId'] == 0 && $tiki_p_admin == 'y') || ($res['galleryId'] != 0 && $this->user_has_perm_on_object($user,$res['galleryId'],'image gallery','tiki_p_view_image_gallery'))) {
              ++$cant;
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
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

	function find_exact_blogs($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_blogs'] == 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                b.`description`,b.`hits`,b.`lastModif`,b.`title` from
                `tiki_searchindex` s, `tiki_blogs` b where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog' and
                ".$this->sql_cast("s.`page`","int")."=b.`blogId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res['page'],'blog','tiki_p_read_blog')) {
              ++$cant;
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
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }


        function find_exact_blog_posts($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_blogs'] == 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                bp.`data`,b.`hits`,b.`title` as `btitle`,bp.`created`,b.`title`,b.`blogId` from
                `tiki_searchindex` s, `tiki_blogs` b ,`tiki_blog_posts` bp where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='blog_post' and
                ".$this->sql_cast("s.`page`","int")."=bp.`postId` and
		bp.`blogId`=b.`blogId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res['blogId'],'blog','tiki_p_read_blog')) {
              ++$cant;
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
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

	function find_exact_articles($words,$offset, $maxRecords) {
	  global $prefs, $user;
	  if ($prefs['feature_articles']  == 'y'  && count($words) >0) {
	    $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
	    	a.`heading`,a.`nbreads`,a.`publishDate`,a.`title` from
		`tiki_searchindex` s, `tiki_articles` a where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='article' and
		".$this->sql_cast("s.`page`","int")."=a.`articleId` order by `nbreads` desc";
	    $result=$this->query($query,$words,$maxRecords,$offset);
	    $cant=0;
	    $ret=array();
	    while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res['page'],'article','tiki_p_read_article')) {
		++$cant;
	      $href = "tiki-read_article.php?articleId=".urlencode($res["page"]);
	      $ret[] = array(
	        'pageName' => $res["title"],
	        'location' => tra("Article"),
		'data' => substr($res["heading"],0,250),
		'hits' => $res["nbreads"],
		'lastModif' => $res["publishDate"],
		'href' => $href,
		'relevance' => $res["nbreads"]
              );
	     }
	    }
	    return array('data' => $ret,'cant' => $cant);
	  } else {
	    return array('data' => array(),'cant' => 0);
	  }
	}

	function find_exact_wiki($words,$offset, $maxRecords) {
	  global $prefs, $user, $tikilib;
	  if ($prefs['feature_wiki'] == 'y'  && count($words) >0) {
	  $query = "select distinct s.`page`, s.`location`, s.`last_update`, sum(s.`count`) as `count`, p.`data`, p.`hits`, p.`lastModif`, p.`is_html` 
		from `tiki_searchindex` s, `tiki_pages` p  
		where `searchword` in (".implode(',',array_fill(0,count($words),'?')).") 
			and s.`location`='wiki' and s.`page`=p.`pageName`
		group by s.`page`, s.`location`, s.`last_update`, p.`data`, p.`hits`, p.`lastModif`
		order by `count` desc";
	  $result=$this->query($query,$words,$maxRecords,$offset);
	  $cant=0;
	  $ret=array();
          while ($res = $result->fetchRow()) {
	   if($this->user_has_perm_on_object($user,$res["page"],'wiki page','tiki_p_view')) {
            $href = "tiki-index.php?page=".urlencode($res["page"]);
            ++$cant;
            $ret[] = array(
              'pageName' => $res["page"],
              'location' => tra("Wiki"),
              'data' => $tikilib->get_snippet($res['data'], $res['is_html']),
              'hits' => $res["hits"],
              'lastModif' => $res["lastModif"],
              'href' => $href,
              'relevance' => $res["count"]
            );
	   }
          }

          return array('data' => $ret,'cant' => $cant);
	  } else {
	  return array('data' => array(),'cant' => 0);
	  }
        }

        function find_exact_directory($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_directory']== 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                d.`description`,d.`hits`,d.`name` from
                `tiki_searchindex` s, `tiki_directory_categories` d where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='dir_cat' and
                ".$this->sql_cast("s.`page`","int")."=d.`categId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res["page"],'directory','tiki_p_view_directory')) {
              ++$cant;
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
            }
            $dsiteres=$this->find_exact_directory_sites($words,$offset, $maxRecords);
            return array('data' => array_merge($ret,$dsiteres["data"]),'cant' => $cant+$dsiteres["cant"]);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

        function &find_exact_directory_sites($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_directory']== 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                d.`description`,d.`hits`,d.`name`,d.`lastModif`,cs.`categId` from
                `tiki_searchindex` s, `tiki_directory_sites` d ,`tiki_category_sites` cs where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='dir_site' and
                ".$this->sql_cast("s.`page`","int")."=d.`siteId` and
		cs.`siteId`=d.`siteId`
		order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     // only permissions on directory - have to find out first?
	     if($this->user_has_perm_on_object($user,$res["page"],'directory','tiki_p_view_directory')) {
              ++$cant;
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
            }
            $return = array('data' => $ret,'cant' => $cant);
          } else {
            $return = array('data' => array(),'cant' => 0);
          }
          return $return;
        }


	function find_exact_faqs($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_faqs']== 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`description`,f.`hits`,f.`created`,f.`title` from
                `tiki_searchindex` s, `tiki_faqs` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='faq' and
                ".$this->sql_cast("s.`page`","int")."=f.`faqId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res["page"],'faq','tiki_p_view_faqs')) {
              ++$cant;
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
            }
            $fquesres=$this->find_exact_faqquestions($words,$offset, $maxRecords);
            return array('data' => array_merge($ret,$fquesres["data"]),'cant' => $cant+$fquesres["cant"]);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

        function find_exact_faqquestions($words,$offset, $maxRecords) {
          global $prefs, $user;
          if ($prefs['feature_faqs']== 'y'  && count($words) >0) {
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`question`,faq.`hits`,faq.`created`,faq.`title`,f.`answer`,f.`faqId` from
                `tiki_searchindex` s, `tiki_faqs` faq, `tiki_faq_questions` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='faq_question' and
                ".$this->sql_cast("s.`page`","int")."=f.`questionId` and
		f.`faqId`=faq.`faqId` order by `hits` desc";
            $result=$this->query($query,$words,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res["faqId"],'faq','tiki_p_view_faqs')) {
              ++$cant;
              $href = "tiki-view_faq.php?faqId=".urlencode($res["faqId"]);
              $ret[] = array(
                'pageName' => substr($res["question"],0,40),
                'location' => tra("FAQ")."::".$res["title"],
                'data' => substr($res["answer"],0,250),
                'hits' => $res["hits"],
                'lastModif' => $res["created"],
                'href' => $href,
		'anchor' => '#q'.urlencode($res['page']),
                'relevance' => $res["hits"]
              );
	     }
            }
            return array('data' => $ret,'cant' => $cant);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }


        function find_exact_forums($words,$offset, $maxRecords, $filter='') {
          global $prefs, $user;
          if ($prefs['feature_forums']== 'y'  && count($words) >0) {
			$bindvars = $words;
			if (!empty($filter) && !empty($filter['forumId'])) {
				$mid = ' and f.`forumId`=? ';
				$bindvars[] = $filter['forumId'];
			} else {
				$mid = '';
			}
            $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
                f.`description`,f.`hits`,f.`lastPost`,f.`name` from
                `tiki_searchindex` s, `tiki_forums` f where `searchword` in
                (".implode(',',array_fill(0,count($words),'?')).") and
                s.`location`='forum' and
                ".$this->sql_cast("s.`page`","int")."=f.`forumId` $mid order by `hits` desc";
            $result=$this->query($query,$bindvars,$maxRecords,$offset);
            $cant=0;
            $ret=array();
            while ($res = $result->fetchRow()) {
	     if($this->user_has_perm_on_object($user,$res["page"],'forum','tiki_p_forum_read')) {
              ++$cant;
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
            }
            $fcommres=$this->find_exact_forumcomments($words,$offset, $maxRecords, $filter);
            return array('data' => array_merge($ret,$fcommres["data"]),'cant' => $cant+$fcommres["cant"]);
          } else {
            return array('data' => array(),'cant' => 0);
          }
        }

	function find_exact_forumcomments($words,$offset, $maxRecords, $filter) {
	  global $prefs, $user;
	  if ($prefs['feature_forums'] == 'y'  && count($words) >0) {
	  $bindvars = $words;
	  if (!empty($filter) && !empty($filter['forumId'])) {
		$mid = ' and fo.`forumId`=? ';
		$bindvars[] = $filter['forumId'];
	  } else {
		$mid = '';
	  }
	  $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`,
	  	f.`data`,f.`hits`,f.`commentDate`,f.`object`,f.`title`,fo.`name` from
		`tiki_searchindex` s, `tiki_comments` f,`tiki_forums` fo where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='forumcomment' and
		".$this->sql_cast("s.`page`","int")."=f.`threadId` and
		fo.`forumId`=".$this->sql_cast("f.`object`","int")." $mid order by `count` desc";
	  $result=$this->query($query,$bindvars,$maxRecords,$offset);
	  $cant=0;
	  $ret=array();
	  while ($res = $result->fetchRow()) {
	   if($this->user_has_perm_on_object($user,$res["object"],'forum','tiki_p_forum_read')) {
          ++$cant;
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
	  }
	  return array('data' => $ret,'cant' => $cant);
	  }else {
	  return array('data' => array(),'cant' => 0);
	  }
	}
	function find_exact_files($words,$offset, $maxRecords) {
	  global $prefs, $user;
	  if ($prefs['feature_file_galleries'] == 'y'  && count($words) >0) {
	  $query="select distinct s.`page`, s.`location`, s.`last_update`, s.`count`, f.`search_data`,
	  	f.`data`,f.`lastModif`, f.`filename`, f.`hits`, f.`description`, f.`name`, g.`name` as `galName` from
		`tiki_searchindex` s, `tiki_files` f, `tiki_file_galleries` g  where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`location`='file' and f.`galleryId`= g.`galleryId` and f.`archiveId`=0 and 
		".$this->sql_cast("s.`page`","int")."=f.`fileId` order by `count` desc";
	  $result=$this->query($query,$words,$maxRecords,$offset);

	  $cant = 0;
	  $ret = array();
	  while ($res = $result->fetchRow()) {
	   if($this->user_has_perm_on_object($user,$res["page"],'file gallery','tiki_p_download_files') && $this->user_has_perm_on_object($user,$res["page"],'file gallery','tiki_p_view_file_gallery')) {
	    ++$cant;
	    $href = "tiki-download_file.php?fileId=".urlencode($res["page"]);
	    $ret[] = array(
	      'pageName' => $res["name"]? $res["name"]: $res["filename"],
	      'location' => tra("File Gallery").":".$res["galName"],
	      'data' => $res["description"], //$res["search_data"] can be messy
	      'hits' => $res["hits"],
	      'lastModif' => $res["lastModif"],
	      'href' => $href,
	      'relevance' => $res["count"]
	    );
	   }
	  }
	  return array('data' => $ret,'cant' => $cant);
	  }else {
	  return array('data' => array(),'cant' => 0);
	  }
	}



} # class SearchLib

?>
