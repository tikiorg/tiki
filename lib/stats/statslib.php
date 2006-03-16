<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class StatsLib extends TikiLib {
	function StatsLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to StatsLib constructor");
		}
		$this->db = $db;
	}

	function list_orphan_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc', $find = '', $onlyName=false) {
	        global $user;

		if ($sort_mode == 'size_desc') {
			$sort_mode = 'page_size_desc';
		}

		if ($sort_mode == 'size_asc') {
			$sort_mode = 'page_size_asc';
		}

		$old_sort_mode = '';

		if (in_array($sort_mode, array(
			'versions_desc',
			'versions_asc',
			'links_asc',
			'links_desc',
			'backlinks_asc',
			'backlinks_desc'
		))) {
			$old_offset = $offset;

			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user_desc';
			$offset = 0;
			$maxRecords = -1;
		}
		$bindvars = array();
		if ($find) {
			$mid = " and `pageName` like ? ";
			$bindvars[] = "%$find%";
		} else {
			$mid = "";
		}

		// If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		$query = "select `pageName`, `hits`, `page_size` as `len` ,`lastModif`, `user`, `ip`, `comment`, `version`, `flag` from `tiki_pages` tp left join `tiki_links` tl on tp.`pageName` = tl.`toPage` left join `tiki_structures` ts on  tp.`page_id`= ts.`page_id`where tl.`toPage` IS NULL and  `ts`.page_id IS NULL $mid order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,-1,0);
		$ret = array();
		$num_or = 0;

		while ($res = $result->fetchRow()) {
		   //WYSIWYCA
		   if($this->user_has_perm_on_object($user,$res["pageName"],'wiki page','tiki_p_view')) {
			$pageName = $res["pageName"];
				$num_or++;
				$aux = array();
				$aux["pageName"] = $pageName;
				$page = $aux["pageName"];
				$page_as = addslashes($page);
				$aux["hits"] = $res["hits"];
				$aux["lastModif"] = $res["lastModif"];
				$aux["user"] = $res["user"];
				$aux["ip"] = $res["ip"];
				$aux["len"] = $res["len"];
				$aux["comment"] = $res["comment"];
				$aux["version"] = $res["version"];
				$aux["flag"] = $res["flag"] == 'L' ? tra('locked') : tra('unlocked');
				if (!$onlyName) {
					$aux["versions"] = $this->getOne("select count(*) from `tiki_history` where `pageName`=?",array($page_as));
					$aux["links"] = $this->getOne("select count(*) from `tiki_links` where `fromPage`=?",array($page_as));
					$aux["backlinks"] = $this->getOne("select count(*) from `tiki_links` where `toPage`=?",array($page_as));
				}
				$ret[] = $aux;
		    }
		}

		// If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offse and old_maxRecords
		if ($old_sort_mode == 'versions_asc') {
			usort($ret, 'compare_versions');
		}

		if ($old_sort_mode == 'versions_desc') {
			usort($ret, 'r_compare_versions');
		}

		if ($old_sort_mode == 'links_desc') {
			usort($ret, 'compare_links');
		}

		if ($old_sort_mode == 'links_asc') {
			usort($ret, 'r_compare_links');
		}

		if ($old_sort_mode == 'backlinks_desc') {
			usort($ret, 'compare_backlinks');
		}

		if ($old_sort_mode == 'backlinks_asc') {
			usort($ret, 'r_compare_backlinks');
		}

		if (in_array($old_sort_mode, array(
			'versions_desc',
			'versions_asc',
			'links_asc',
			'links_desc',
			'backlinks_asc',
			'backlinks_desc'
		))) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $num_or;
		return $retval;
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
		$stats["downloads"] = $this->getOne("select sum(`downloads`) from `tiki_files`",array());
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
    $dayzero = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
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
		if ($days!=0) {
			$mid="WHERE `day` >= ".mktime(0, 0, 0, date("m"), date("d")-$days, date("Y"))." ";
		} else {
			$mid="";
		}
		$query="SELECT `object`, `type`, sum(`hits`) AS `hits` FROM `tiki_stats` ".$mid."GROUP BY `object`,`type` ORDER BY `hits` DESC";
		$result = $this->query($query,array(),$max,0);
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
		if ($days!=0) {
			$mid="AND `day` >= ".mktime(0, 0, 0, date("m"), date("d")-$days, date("Y"))." ";
		} else {
			$mid="";
		}
		$query_cant="SELECT sum(`hits`) AS `hits` FROM `tiki_stats` WHERE `object`=? AND `type`=?".$mid."GROUP BY `object`,`type`";
		$cant = $this->getOne($query_cant,array($object,$type));
		return $cant;
	}
	
	function get_daily_usage_chart_data($days=30) {
		if ($days!=0) {
			$mid="WHERE `day` >= ".mktime(0, 0, 0, date("m"), date("d")-$days, date("Y"))." ";
		} else {
			$mid="";
		}
		$query="SELECT `day`,sum(`hits`) AS `hits` FROM `tiki_stats` ".$mid."GROUP BY `day`";
		$result = $this->query($query,array(),-1,0);
		$data=array();
		while ($res = $result->fetchRow()) {
			$data['xdata'][]=date("Y/m/d",$res['day']);
			$data['ydata'][]=$res['hits'];
		}
		return $data;
	}
	
}
global $dbTiki;
$statslib = new StatsLib($dbTiki);

?>
