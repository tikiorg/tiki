<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class StatsLib extends TikiLib {
	function StatsLib($db) {
		$this->TikiLib($db);
	}

	// obsolete, but keeped for compatibility purposes
	// use Tikilib::list_pages() instead
	function list_orphan_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc', $find = '', $onlyCant=false) {
		return $this->list_pages($offset, $maxRecords, $sort_mode, $find, '', true, true, true, true, false, '', $onlyCant);
	}

	function wiki_stats() {
		$stats = array();

		$stats["pages"] = $this->getOne("select count(*) from `tiki_pages`",array());
		$stats["versions"] = $this->getOne("select count(*) from `tiki_history`",array());

		if ($stats["pages"]) {
			$stats["vpp"] = $stats["versions"] / $stats["pages"];
		} else {
			$stats["vpp"] = 0;
		}
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_pages`",array());
		$or = $this->list_orphan_pages(0, -1, 'pageName_desc', '', true);
		$stats["orphan"] = $or["cant"];
		$links = $this->getOne("select count(*) from `tiki_links`",array());

		if ($stats["pages"]) {
			$stats["lpp"] = $links / $stats["pages"];
		} else {
			$stats["lpp"] = 0;
		}
		$stats["size"] = $this->getOne("select sum(`page_size`) from `tiki_pages`",array());

		if ($stats["pages"]) {
			$stats["bpp"] = $stats["size"] / $stats["pages"];
		} else {
			$stats["bpp"] = 0;
		}
		$stats["size"] = $stats["size"] / 1000000;
		return $stats;
	}

	function quiz_stats() {
		$this->compute_quiz_stats();
		$stats = array();
		$stats["quizzes"] = $this->getOne("select count(*) from `tiki_quizzes`",array());
		$stats["questions"] = $this->getOne("select count(*) from `tiki_quiz_questions`",array());
		if ($stats["quizzes"]) {
			$stats["qpq"] = $stats["questions"] / $stats["quizzes"];
		} else {
			$stats["qpq"] = 0;
		}
		$stats["visits"] = $this->getOne("select sum(`timesTaken`) from `tiki_quiz_stats_sum`",array());
		$stats["avg"] = $this->getOne("select avg(`avgavg`) from `tiki_quiz_stats_sum`",array());
		$stats["avgtime"] = $this->getOne("select avg(`avgtime`) from `tiki_quiz_stats_sum`",array());
		return $stats;
	}

	function image_gal_stats() {
		$stats = array();
		$stats["galleries"] = $this->getOne("select count(*) from `tiki_galleries`",array());
		$stats["images"] = $this->getOne("select count(*) from `tiki_images`",array());
		$stats["ipg"] = ($stats["galleries"] ? $stats["images"] / $stats["galleries"] : 0);
		$stats["size"] = $this->getOne("select sum(`filesize`) from `tiki_images_data` where `type`=?",array('o'));
		$stats["bpi"] = ($stats["images"] ? $stats["size"] / $stats["images"] : 0);
		$stats["size"] = $stats["size"] / 1000000;
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_galleries`",array());
		return $stats;
	}

	function file_gal_stats() {
		$stats = array();
		$stats["galleries"] = $this->getOne("select count(*) from `tiki_file_galleries`",array());
		$stats["files"] = $this->getOne("select count(*) from `tiki_files`",array());
		$stats["fpg"] = ($stats["galleries"] ? $stats["files"] / $stats["galleries"] : 0);
		$stats["size"] = $this->getOne("select sum(`filesize`) from `tiki_files`",array());
		$stats["size"] = $stats["size"] / 1000000;
		$stats["bpf"] = ($stats["galleries"] ? $stats["size"] / $stats["galleries"] : 0);
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_file_galleries`",array());
		$stats["hits"] = $this->getOne("select sum(`hits`) from `tiki_files`",array());
		return $stats;
	}

	function cms_stats() {
		$stats = array();

		$stats["articles"] = $this->getOne("select count(*) from `tiki_articles`",array());
		$stats["reads"] = $this->getOne("select sum(`nbreads`) from `tiki_articles`",array());
		$stats["rpa"] = ($stats["articles"] ? $stats["reads"] / $stats["articles"] : 0);
		$stats["size"] = $this->getOne("select sum(`size`) from `tiki_articles`",array());
		$stats["bpa"] = ($stats["articles"] ? $stats["size"] / $stats["articles"] : 0);
		$stats["topics"] = $this->getOne("select count(*) from `tiki_topics` where `active`=?",array('y'));
		return $stats;
	}

	function forum_stats() {
		$stats = array();
		$stats["forums"] = $this->getOne("select count(*) from `tiki_forums`",array());
		$stats["topics"] = $this->getOne( "select count(*) from `tiki_comments`,`tiki_forums` where `object`=".$this->sql_cast('`forumId`','string')." and `objectType`=? and `parentId`=?",array('forum',0));
		$stats["threads"] = $this->getOne( "select count(*) from `tiki_comments`,`tiki_forums` where `object`=".$this->sql_cast('`forumId`','string')." and `objectType`=? and `parentId`<>?",array('forum',0));
		$stats["tpf"] = ($stats["forums"] ? $stats["topics"] / $stats["forums"] : 0);
		$stats["tpt"] = ($stats["topics"] ? $stats["threads"] / $stats["topics"] : 0);
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_forums`",array());
		return $stats;
	}

	function blog_stats() {
		$stats = array();
		$stats["blogs"] = $this->getOne("select count(*) from `tiki_blogs`",array());
		$stats["posts"] = $this->getOne("select count(*) from `tiki_blog_posts`",array());
		$stats["ppb"] = ($stats["blogs"] ? $stats["posts"] / $stats["blogs"] : 0);
		$stats["size"] = $this->getOne("select sum(`data_size`) from `tiki_blog_posts`",array());
		$stats["bpp"] = ($stats["posts"] ? $stats["size"] / $stats["posts"] : 0);
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_blogs`",array());
		return $stats;
	}

	function poll_stats() {
		$stats = array();
		$stats["polls"] = $this->getOne("select count(*) from `tiki_polls`",array());
		$stats["votes"] = $this->getOne("select sum(`votes`) from `tiki_poll_options`",array());
		$stats["vpp"] = ($stats["polls"] ? $stats["votes"] / $stats["polls"] : 0);
		return $stats;
	}

	function faq_stats() {
		$stats = array();
		$stats["faqs"] = $this->getOne("select count(*) from `tiki_faqs`",array());
		$stats["questions"] = $this->getOne("select count(*) from `tiki_faq_questions`",array());
		$stats["qpf"] = ($stats["faqs"] ? $stats["questions"] / $stats["faqs"] : 0);
		return $stats;
	}

	function user_stats() {
		$stats = array();
		$stats["users"] = $this->getOne("select count(*) from `users_users`",array());
		$stats["bookmarks"] = $this->getOne("select count(*) from `tiki_user_bookmarks_urls`",array());
		$stats["bpu"] = ($stats["users"] ? $stats["bookmarks"] / $stats["users"] : 0);
		return $stats;
	}

	function site_stats() {
		$stats = array();
		$stats["started"] = $this->getOne("select min(`day`) from `tiki_pageviews`",array());
		$stats["days"] = $this->getOne("select count(*) from `tiki_pageviews`",array());
		$stats["pageviews"] = $this->getOne("select sum(`pageviews`) from `tiki_pageviews`");
		$stats["ppd"] = ($stats["days"] ? $stats["pageviews"] / $stats["days"] : 0);
		$stats["bestpvs"] = $this->getOne("select max(`pageviews`) from `tiki_pageviews`",array());
		$stats["bestday"] = $this->getOne("select `day` from `tiki_pageviews` where `pageviews`=?",array((int)$stats["bestpvs"]));
		$stats["worstpvs"] = $this->getOne("select min(`pageviews`) from `tiki_pageviews`",array());
		$stats["worstday"] = $this->getOne("select `day` from `tiki_pageviews` where `pageviews`=?",array((int)$stats["worstpvs"]));
		return $stats;
	}
	
	function stats_hit($object,$type,$id=NULL) {
		if (is_null($object) || is_null($type)) {
			$result=false;
			return $result;
		}
    $dayzero = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d"), $this->date_format("%Y"));
    if (!is_null($id)) {
    	$object=$id."?".$object;
    }
    $cant = $this->getOne("select count(*) from `tiki_stats` where `object`=? and `type`=? and `day`=?",array($object,$type,(int)$dayzero));
    if ($cant) {
        $query = "update `tiki_stats` set `hits`=`hits`+1 where `object`=? and `type`=? and `day`=?";
    } else {
        $query = "insert into `tiki_stats`(`object`,`type`, `day`,`hits`) values(?,?,?,1)";
    }

    $result = $this->query($query,array($object,$type,(int)$dayzero),-1,-1,false);
    return $result;
	}
	
	function best_overall_object_stats($max=20,$days=0) {
		$stats = array();
		$bindvars = array();
		if ($days!=0) {
			$mid="WHERE `day` >= ?";
			$bindvars[] = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d")-$days, $this->date_format("%Y"));
		} else {
			$mid="";
		}
		$query="SELECT `object`, `type`, sum(`hits`) AS `hits` FROM `tiki_stats` ".$mid." GROUP BY `object`,`type` ORDER BY `hits` DESC";
		$result = $this->query($query,$bindvars,$max,0);
		$i=0;
		while ($res = $result->fetchRow()) {
		  if (strpos($res["object"],"?")) {
		  	list($stats[$i]->ID,$stats[$i]->object)=explode("?",$res["object"],2);
		  } else {
		  	$stats[$i]->object=$res["object"];
		  	$stats[$i]->ID=$res["object"];
		  }
		  $stats[$i]->type=$res["type"];
		  $stats[$i]->hits=$res["hits"];
		  $i++;
		}
		return $stats;
	}
	
	function object_hits($object,$type,$days=0) {
		$bindvars = array($object,$type);
		if ($days!=0) {
			$mid="AND `day` >= ? ";
			$bindvars[] = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d")-$days, $this->date_format("%Y"));
		} else {
			$mid="";
		}
		$query_cant="SELECT sum(`hits`) AS `hits` FROM `tiki_stats` WHERE `object`=? AND `type`=? ".$mid." GROUP BY `object`,`type`";
		$cant = $this->getOne($query_cant,$bindvars);
		return $cant;
	}
	
	function get_daily_usage_chart_data($days=30) {
		$bindvars = array();
		if ($days!=0) {
			$mid="WHERE `day` >= ? ";
			$bindvars[] = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d")-$days, $this->date_format("%Y"));
		} else {
			$mid="";
		}
		$query="SELECT `day`,sum(`hits`) AS `hits` FROM `tiki_stats` ".$mid." GROUP BY `day`";
		$result = $this->query($query,$bindvars,-1,0);
		$data=array();
		while ($res = $result->fetchRow()) {
			$data['xdata'][]=$this->date_format("%Y/%m/%d",$res['day']);
			$data['ydata'][]=$res['hits'];
		}
		return $data;
	}
	
}
global $dbTiki;
$statslib = new StatsLib($dbTiki);

?>
