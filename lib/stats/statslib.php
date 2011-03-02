<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class StatsLib extends TikiLib
{
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
		$stats["bpf"] = ($stats["files"] ? $stats["size"] / $stats["files"] : 0);
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
		$stats["topics"] = $this->getOne( "select count(*) from `tiki_comments`,`tiki_forums` where `object`=`forumId` and `objectType`=? and `parentId`=?",array('forum',0));
		$stats["threads"] = $this->getOne( "select count(*) from `tiki_comments`,`tiki_forums` where `object`=`forumId` and `objectType`=? and `parentId`<>?",array('forum',0));
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
		global $tikilib;
		$stats = array();
		$rows = $this->getOne("select count(*) from `tiki_pageviews`",array());
		if ($rows > 0) {
			//get max pageview number
			//sum by day as there are sometimes multiple unixstamps per day
			$max = $this->fetchAll("SELECT SUM(`pageviews`) AS views, `day` AS unixtime
									FROM `tiki_pageviews`
									GROUP BY FROM_UNIXTIME(`day`, '%Y-%m-%d')
									ORDER BY views DESC 
									LIMIT 1");
			$maxvar = $max[0]['views'];
			//get min pageview number
			$min = $this->fetchAll("SELECT SUM(`pageviews`) AS views, `day` AS unixtime
									FROM `tiki_pageviews`
									GROUP BY FROM_UNIXTIME(`day`, '%Y-%m-%d')
									ORDER BY views ASC 
									LIMIT 1");
			$minvar = $min[0]['views'];
			//pull all dates with max or min because there may be more than one for each
			$views = $this->fetchAll("SELECT SUM(`pageviews`) AS views, FROM_UNIXTIME(`day`, '%Y-%m-%d') AS date, `day` AS unixtime
									FROM `tiki_pageviews`
									GROUP BY FROM_UNIXTIME(`day`, '%Y-%m-%d')
									HAVING views = '$maxvar' OR views = '$minvar'
									ORDER BY date ASC");
			$start = $this->getOne("select min(`day`) from `tiki_pageviews`",array());
			$stats['started'] = $start;
			$stats['days'] = floor(($tikilib->now - $start)/86400);
			$stats['pageviews'] = $this->getOne("select sum(`pageviews`) from `tiki_pageviews`");
			$stats['ppd'] = sprintf("%.2f", ($stats['days'] ? $stats['pageviews'] / $stats['days'] : 0));
			$b = 0;
			$w = 0;
			//for each in case there's more than one max day and more than one min day
			foreach ($views as $view) {
				if ($view['views'] == $maxvar) {
					$stats['bestday'] .= $tikilib->get_long_date($view['unixtime']) . ' (' . $maxvar . ' ' . tra('pvs') . ')<br />';
					$b > 0 ? $stats['bestdesc'] = tra('Days with the most pageviews') : $stats['bestdesc'] = tra('Day with the most pageviews');
					$b++;
				} 
				if ($view['views'] == $minvar) {
					$stats['worstday'] .= $tikilib->get_long_date($view['unixtime']) . ' (' . $minvar . ' ' . tra('pvs') . ')<br />';
					$w > 0 ? $stats['worstdesc'] = tra('Days with the least pageviews') : $stats['worstdesc'] = tra('Day with the least pageviews');
					$w++;
				}
			}
		} else {
			$stats['started'] = tra('No pageviews yet');
			$stats['days'] = tra('n/a');
			$stats['pageviews'] = tra('n/a');
			$stats['ppd'] = tra('n/a');
			$stats['bestpvs'] = tra('n/a');
			$stats['bestday'] = tra('n/a');
			$stats['worstpvs'] = tra('n/a');
			$stats['worstday'] = tra('n/a');
		}
		return $stats;
	}
	
	function stats_hit($object, $type, $id = NULL) {
		if ( is_null($object) || is_null($type) ) {
			$result = false;
			return $result;
		}

		list($month, $day, $year) = explode(',', $this->date_format("%m,%d,%Y"));
		$dayzero = $this->make_time(0, 0, 0, $month, $day, $year);

		if ( ! is_null($id) ) {
			$object = $id."?".$object;
		}

		$cant = $this->getOne("select count(*) from `tiki_stats` where `object`=? and `type`=? and `day`=?", array($object, $type, (int)$dayzero));

		if ( $cant ) {
			$query = "update `tiki_stats` set `hits`=`hits`+1 where `object`=? and `type`=? and `day`=?";
		} else {
			$query = "insert into `tiki_stats` (`object`,`type`,`day`,`hits`) values(?,?,?,1)";
		}

		return $this->query($query, array($object, $type, (int)$dayzero), -1, -1, false);
	}
	
	function best_overall_object_stats($max=20, $days=0, $startDate=0, $endDate=0 ) {
		$stats = array();
		$bindvars = array();
		if ($days!=0) {
			$mid="WHERE `day` >= ?";
			$bindvars[] = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d")-$days, $this->date_format("%Y"));
		} else {
			$mid="WHERE `day` <> 'NULL' ";
		}
		
		if ($startDate) $mid .= " and `day` > '".$startDate."' ";
		if ($endDate) $mid .= " and `day` < '".$endDate."' ";

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
	
	function object_hits($object, $type, $days=0, $startDate=0, $endDate=0 ) {
		$bindvars = array($object,$type);
		if ($days!=0) {
			$mid="AND `day` >= ? ";
			$bindvars[] = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d")-$days, $this->date_format("%Y"));
		} else {
			$mid="";
		}

		if ($startDate) $mid .= " and `day` > '".$startDate."' ";
		if ($endDate) $mid .= " and `day` < '".$endDate."' ";

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
	/* transform a last period to a 2 dates */
	function period2dates($when) {
		global $tikilib, $prefs;
		$now = $tikilib->now;
		$sec = TikiLib::date_format("%s", $now);
		$min = TikiLib::date_format("%i", $now);
		$hour = TikiLib::date_format("%H", $now);
		$day = TikiLib::date_format("%d", $now);
		$month = TikiLib::date_format("%m", $now);
		$year = TikiLib::date_format("%Y", $now);
		switch ($when){
		case 'lasthour':
			$begin = $now - 60*60;
			break;
		case 'day':
			$begin = TikiLib::make_time(0, 0, 0, $month, $day, $year);
			break;
		case 'lastday':
			$begin = Tikilib::make_time($hour-24, $min, $sec, $month, $day, $year);
			break;
		case 'week':
			$iweek = TikiLib::date_format("%w", $now);// 0 for Sunday...
			if ($prefs['calendar_firstDayofWeek'] == 'user') {
				$firstDayofWeek = (int)tra('First day of week: Sunday (its ID is 0) - translators you need to localize this string!');
				if ( $firstDayofWeek < 1 || $firstDayofWeek > 6 ) {
					$firstDayofWeek = 0;
				} 
			} else {
				$firstDayofWeek = $prefs['calendar_firstDayofWeek'];
			}
			$iweek -= $firstDayofWeek;
			if ($iweek < 0) $iweek += 7;
			$begin = TikiLib::make_time(0, 0, 0, $month, $day-($iweek ), $year);
			break;
		case 'lastweek':
			$begin = Tikilib::make_time($hour, $min, $sec, $month, $day-7, $year);
			break;
		case 'month':
			$begin = TikiLib::make_time(0, 0, 0, $month, 1, $year);
			break;
		case 'lastmonth':
			$begin = TikiLib::make_time($hour, $min, $sec, $month-1, $day, $year);
			break;			
		case 'year':
			$begin = TikiLib::make_time(0, 0, 0, 1, 1, $year);
			break;
		case 'lastyear':
			$begin = TikiLib::make_time($hour, $min, $sec, $month, $day, $year-1);
			break;
		default :
			$begin = $now;
			break;
		}
		return array((int)$begin, (int)$now);
	}
	/* count the number of created or modified for this day, this month, this year */
	function count_this_period($table = 'tiki_pages', $column ='created', $when='daily', $parentColumn ='', $parentId='') {
		$bindvars = $this->period2dates($when);
		$where = '';
		if (!empty($parentColumn) && !empty($parentId)) {
			$where = " and `$parentColumn` = ?";
			$bindvars[] = (int)$parentId;
		}
		$query = "select count(*) from `$table` where `$column` >= ? and `$column` <= ? $where";
		$count = $this->getOne($query, $bindvars);
		return $count;
	}
	/* count the number of viewed for this day, this month, this year */
	function hit_this_period($type='wiki', $when='daily') {
		$bindvars = $this->period2dates($when);
		$bindvars[1] = $type;
		$query = "select sum(`hits`)from `tiki_stats` where `day` >=? and `type`=?";
		$count = $this->getOne($query, $bindvars);
		if ($count == '')  {
			$count = 0;
		}
		return $count;
	}
	
}
global $dbTiki;
$statslib = new StatsLib;
