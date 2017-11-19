<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class StatsLib extends TikiLib
{
	/**
	 *  Check if the prerequisites for recording a statistics hit are fulfilled
	 */
	public static function is_stats_hit()
	{
		global $prefs, $user;
		return $prefs['feature_stats'] === 'y' && ( $prefs['count_admin_pvs'] === 'y' || $user != 'admin' );
	}

	// obsolete, but keeped for compatibility purposes
	// use Tikilib::list_pages() instead
	/**
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param bool $onlyCant
	 * @return array
	 */
	public function list_orphan_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc', $find = '', $onlyCant = false)
	{
		return $this->list_pages($offset, $maxRecords, $sort_mode, $find, '', true, true, true, true, false, '', $onlyCant);
	}

	/**
	 * @return array
	 */
	public function wiki_stats()
	{
		$stats = [];

		$stats["pages"] = $this->getOne("select count(*) from `tiki_pages`", []);
		$stats["versions"] = $this->getOne("select count(*) from `tiki_history`", []);

		if ($stats["pages"]) {
			$stats["vpp"] = $stats["versions"] / $stats["pages"];
		} else {
			$stats["vpp"] = 0;
		}
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_pages`", []);
		$or = $this->list_orphan_pages(0, -1, 'pageName_desc', '', true);
		$stats["orphan"] = $or["cant"];
		$links = $this->getOne("select count(*) from `tiki_links`", []);

		if ($stats["pages"]) {
			$stats["lpp"] = $links / $stats["pages"];
		} else {
			$stats["lpp"] = 0;
		}
		$stats["size"] = $this->getOne("select sum(`page_size`) from `tiki_pages`", []);

		if ($stats["pages"]) {
			$stats["bpp"] = $stats["size"] / $stats["pages"];
		} else {
			$stats["bpp"] = 0;
		}
		$stats["size"] = $stats["size"] / 1000000;
		return $stats;
	}

	/**
	 * @return array
	 */
	public function quiz_stats()
	{
		TikiLib::lib('quiz')->compute_quiz_stats();

		$stats = [];
		$stats["quizzes"] = $this->getOne("select count(*) from `tiki_quizzes`", []);
		$stats["questions"] = $this->getOne("select count(*) from `tiki_quiz_questions`", []);
		if ($stats["quizzes"]) {
			$stats["qpq"] = $stats["questions"] / $stats["quizzes"];
		} else {
			$stats["qpq"] = 0;
		}
		$stats["visits"] = $this->getOne("select sum(`timesTaken`) from `tiki_quiz_stats_sum`", []);
		$stats["avg"] = $this->getOne("select avg(`avgavg`) from `tiki_quiz_stats_sum`", []);
		$stats["avgtime"] = $this->getOne("select avg(`avgtime`) from `tiki_quiz_stats_sum`", []);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function image_gal_stats()
	{
		$stats = [];
		$stats["galleries"] = $this->getOne("select count(*) from `tiki_galleries`", []);
		$stats["images"] = $this->getOne("select count(*) from `tiki_images`", []);
		$stats["ipg"] = ($stats["galleries"] ? $stats["images"] / $stats["galleries"] : 0);
		$stats["size"] = $this->getOne("select sum(`filesize`) from `tiki_images_data` where `type`=?", ['o']);
		$stats["bpi"] = ($stats["images"] ? $stats["size"] / $stats["images"] : 0);
		$stats["size"] = $stats["size"] / 1000000;
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_galleries`", []);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function file_gal_stats()
	{
		$stats = [];
		$stats["galleries"] = $this->getOne("select count(*) from `tiki_file_galleries`", []);
		$stats["files"] = $this->getOne("select count(*) from `tiki_files`", []);
		$stats["fpg"] = ($stats["galleries"] ? $stats["files"] / $stats["galleries"] : 0);
		$stats["size"] = $this->getOne("select sum(`filesize`) from `tiki_files`", []);
		$stats["size"] = $stats["size"] / 1000000;
		$stats["bpf"] = ($stats["files"] ? $stats["size"] / $stats["files"] : 0);
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_file_galleries`", []);
		$stats["hits"] = $this->getOne("select sum(`hits`) from `tiki_files`", []);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function cms_stats()
	{
		$stats = [];

		$stats["articles"] = $this->getOne("select count(*) from `tiki_articles`", []);
		$stats["reads"] = $this->getOne("select sum(`nbreads`) from `tiki_articles`", []);
		$stats["rpa"] = ($stats["articles"] ? $stats["reads"] / $stats["articles"] : 0);
		$stats["size"] = $this->getOne("select sum(`size`) from `tiki_articles`", []);
		$stats["bpa"] = ($stats["articles"] ? $stats["size"] / $stats["articles"] : 0);
		$stats["topics"] = $this->getOne("select count(*) from `tiki_topics` where `active`=?", ['y']);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function forum_stats()
	{
		$stats = [];
		$stats["forums"] = $this->getOne("select count(*) from `tiki_forums`", []);
		$stats["topics"] = $this->getOne(
			"select count(*) from `tiki_comments`,`tiki_forums`" .
			" where `object`=`forumId` and `objectType`=? and `parentId`=?",
			['forum',0]
		);
		$stats["threads"] = $this->getOne(
			"select count(*) from `tiki_comments`,`tiki_forums`" .
			" where `object`=`forumId` and `objectType`=? and `parentId`<>?",
			['forum',0]
		);
		$stats["tpf"] = ($stats["forums"] ? $stats["topics"] / $stats["forums"] : 0);
		$stats["tpt"] = ($stats["topics"] ? $stats["threads"] / $stats["topics"] : 0);
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_forums`", []);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function blog_stats()
	{
		$stats = [];
		$stats["blogs"] = $this->getOne("select count(*) from `tiki_blogs`", []);
		$stats["posts"] = $this->getOne("select count(*) from `tiki_blog_posts`", []);
		$stats["ppb"] = ($stats["blogs"] ? $stats["posts"] / $stats["blogs"] : 0);
		$stats["size"] = $this->getOne("select sum(`data_size`) from `tiki_blog_posts`", []);
		$stats["bpp"] = ($stats["posts"] ? $stats["size"] / $stats["posts"] : 0);
		$stats["visits"] = $this->getOne("select sum(`hits`) from `tiki_blogs`", []);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function poll_stats()
	{
		$stats = [];
		$stats["polls"] = $this->getOne("select count(*) from `tiki_polls`", []);
		$stats["votes"] = $this->getOne("select sum(`votes`) from `tiki_poll_options`", []);
		$stats["vpp"] = ($stats["polls"] ? $stats["votes"] / $stats["polls"] : 0);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function faq_stats()
	{
		$stats = [];
		$stats["faqs"] = $this->getOne("select count(*) from `tiki_faqs`", []);
		$stats["questions"] = $this->getOne("select count(*) from `tiki_faq_questions`", []);
		$stats["qpf"] = ($stats["faqs"] ? $stats["questions"] / $stats["faqs"] : 0);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function user_stats()
	{
		$stats = [];
		$stats["users"] = $this->getOne("select count(*) from `users_users`", []);
		$stats["bookmarks"] = $this->getOne("select count(*) from `tiki_user_bookmarks_urls`", []);
		$stats["bpu"] = ($stats["users"] ? $stats["bookmarks"] / $stats["users"] : 0);
		return $stats;
	}

	/**
	 * @return array
	 */
	public function site_stats()
	{
		$tikilib = TikiLib::lib('tiki');
		$stats = [];
		$rows = $this->getOne("select count(*) from `tiki_pageviews`", []);

		if ($rows > 0) {
			//get max pageview number
			//sum by day as there are sometimes multiple unixstamps per day
			$max = $this->fetchAll(
				"SELECT SUM(`pageviews`) AS views, `day` AS unixtime" .
				" FROM `tiki_pageviews`" .
				" GROUP BY FROM_UNIXTIME(`day`, '%Y-%m-%d'), day" .
				" ORDER BY views DESC" .
				" LIMIT 1"
			);
			$maxvar = $max[0]['views'];

			//get min pageview number
			$min = $this->fetchAll(
				"SELECT SUM(`pageviews`) AS views, `day` AS unixtime" .
				" FROM `tiki_pageviews`" .
				" GROUP BY FROM_UNIXTIME(`day`, '%Y-%m-%d'), day" .
				" ORDER BY views ASC" .
				" LIMIT 1"
			);
			$minvar = $min[0]['views'];

			//pull all dates with max or min because there may be more than one for each
			$views = $this->fetchAll(
				"SELECT SUM(`pageviews`) AS views, FROM_UNIXTIME(`day`, '%Y-%m-%d') AS date, `day` AS unixtime" .
				" FROM `tiki_pageviews`" .
				" GROUP BY FROM_UNIXTIME(`day`, '%Y-%m-%d'), day" .
				" HAVING SUM(`pageviews`) = '$maxvar' OR SUM(`pageviews`) = '$minvar'" .
				" ORDER BY date ASC"
			);

			$start = $this->getOne("select min(`day`) from `tiki_pageviews`", []);
			$stats['started'] = $start;
			$stats['days'] = floor(($tikilib->now - $start) / 86400);
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
					$w > 0 ? $stats['worstdesc'] = tra('Days with the fewest pageviews') : $stats['worstdesc'] = tra('Day with the fewest pageviews');
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

	/**
	 * @param $object
	 * @param $type
	 * @param null $id
	 * @return bool
	 */
	public function stats_hit($object, $type, $id = null)
	{
		if (empty($object) || empty($type) || ! StatsLib::is_stats_hit()) {
			return false;
		}

		list($month, $day, $year) = explode(',', $this->date_format("%m,%d,%Y"));
		$dayzero = $this->make_time(0, 0, 0, $month, $day, $year);

		if (! is_null($id)) {
			$object = $id . "?" . $object;
		}

		$cant = $this->getOne(
			"select count(*) from `tiki_stats` where `object`=? and `type`=? and `day`=?",
			[$object, $type, (int) $dayzero]
		);

		if ($cant) {
			$query = "update `tiki_stats` set `hits`=`hits`+1 where `object`=? and `type`=? and `day`=?";
		} else {
			$query = "insert into `tiki_stats` (`object`,`type`,`day`,`hits`) values(?,?,?,1)";
		}

		return $this->query($query, [$object, $type, (int) $dayzero], -1, -1, false);
	}

	/**
	 * @param int $max
	 * @param int $days
	 * @param int $startDate
	 * @param int $endDate
	 * @return array
	 */
	public function best_overall_object_stats($max = 20, $days = 0, $startDate = 0, $endDate = 0)
	{
		$stats = [];
		$bindvars = [];
		if ($days != 0) {
			$mid = "WHERE `day` >= ?";
			$bindvars[] = $this->make_time(
				0,
				0,
				0,
				$this->date_format("%m"),
				$this->date_format("%d") - $days,
				$this->date_format("%Y")
			);
		} else {
			$mid = "WHERE `day` <> 'NULL' ";
		}

		if ($startDate) {
			$mid .= " and `day` > '" . $startDate . "' ";
		}
		if ($endDate) {
			$mid .= " and `day` < '" . $endDate . "' ";
		}

		$query = "SELECT `object`, `type`, sum(`hits`) AS `hits` FROM `tiki_stats` " .
							$mid .
							" GROUP BY `object`,`type` ORDER BY `hits` DESC";
		$result = $this->query($query, $bindvars, $max, 0);
		$i = 0;

		while ($res = $result->fetchRow()) {
			if (strpos($res["object"], "?")) {
				list($stats[$i]->ID,$stats[$i]->object) = explode("?", $res["object"], 2);
			} else {
				$stats[$i]->object = $res["object"];
				$stats[$i]->ID = $res["object"];
			}
			$stats[$i]->type = $res["type"];
			$stats[$i]->hits = $res["hits"];
			$i++;
		}
		return $stats;
	}

	/**
	 * @param $object
	 * @param $type
	 * @param int $days
	 * @param int $startDate
	 * @param int $endDate
	 * @return mixed
	 */
	public function object_hits($object, $type, $days = 0, $startDate = 0, $endDate = 0)
	{
		$bindvars = [$object, $type];
		if ($days != 0) {
			$mid = "AND `day` >= ? ";
			$bindvars[] = $this->make_time(
				0,
				0,
				0,
				$this->date_format("%m"),
				$this->date_format("%d") - $days,
				$this->date_format("%Y")
			);
		} else {
			$mid = '';
		}

		if ($startDate) {
			$mid .= " and `day` > '" . $startDate . "' ";
		}
		if ($endDate) {
			$mid .= " and `day` < '" . $endDate . "' ";
		}

		$query_cant = "SELECT sum(`hits`) AS `hits` FROM `tiki_stats` WHERE `object`=? AND `type`=? " .
										$mid .
										" GROUP BY `object`,`type`";
		$cant = $this->getOne($query_cant, $bindvars);
		return $cant;
	}

	/**
	 * @param int $days
	 * @return array
	 */
	public function get_daily_usage_chart_data($days = 30)
	{
		$bindvars = [];

		if ($days != 0) {
			$mid = "WHERE `day` >= ? ";
			$bindvars[] = $this->make_time(
				0,
				0,
				0,
				$this->date_format("%m"),
				$this->date_format("%d") - $days,
				$this->date_format("%Y")
			);
		} else {
			$mid = "";
		}

		$query = "SELECT `day`,sum(`hits`) AS `hits` FROM `tiki_stats` " . $mid . " GROUP BY `day`";
		$result = $this->query($query, $bindvars, -1, 0);
		$data = [];

		while ($res = $result->fetchRow()) {
			$data['xdata'][] = $this->date_format("%Y/%m/%d", $res['day']);
			$data['ydata'][] = $res['hits'];
		}

		return $data;
	}

	/**
	 * Transform a last period to a 2 dates
	 *
	 */
	public function period2dates($when)
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');
		$now = $tikilib->now;
		$sec = TikiLib::date_format("%s", $now);
		$min = TikiLib::date_format("%i", $now);
		$hour = TikiLib::date_format("%H", $now);
		$day = TikiLib::date_format("%d", $now);
		$month = TikiLib::date_format("%m", $now);
		$year = TikiLib::date_format("%Y", $now);
		switch ($when) {
			case 'lasthour':
				$begin = $now - 60 * 60;
				break;

			case 'day':
				$begin = TikiLib::make_time(0, 0, 0, $month, $day, $year);
				break;

			case 'lastday':
				$begin = Tikilib::make_time($hour - 24, $min, $sec, $month, $day, $year);
				break;

			case 'week':
				$iweek = TikiLib::date_format("%w", $now);// 0 for Sunday...
				$calendarlib = TikiLib::lib('calendar');
				$firstDayofWeek = $calendarlib->firstDayofWeek();
				$iweek -= $firstDayofWeek;
				if ($iweek < 0) {
					$iweek += 7;
				}
				$begin = TikiLib::make_time(0, 0, 0, $month, $day - ($iweek ), $year);
				break;

			case 'lastweek':
				$begin = Tikilib::make_time($hour, $min, $sec, $month, $day - 7, $year);
				break;

			case 'month':
				$begin = TikiLib::make_time(0, 0, 0, $month, 1, $year);
				break;

			case 'lastmonth':
				$begin = TikiLib::make_time($hour, $min, $sec, $month - 1, $day, $year);
				break;

			case 'year':
				$begin = TikiLib::make_time(0, 0, 0, 1, 1, $year);
				break;

			case 'lastyear':
				$begin = TikiLib::make_time($hour, $min, $sec, $month, $day, $year - 1);
				break;

			default:
				$begin = $now;
				break;
		}
		return [(int) $begin, (int) $now];
	}

	/**
	 * count the number of created or modified for this day, this month, this year
	 *
	 */
	public function count_this_period($table = 'tiki_pages', $column = 'created', $when = 'daily', $parentColumn = '', $parentId = '')
	{
		$bindvars = $this->period2dates($when);
		$where = '';
		if (! empty($parentColumn) && ! empty($parentId)) {
			$where = " and `$parentColumn` = ?";
			$bindvars[] = (int) $parentId;
		}
		$query = "select count(*) from `$table` where `$column` >= ? and `$column` <= ? $where";
		$count = $this->getOne($query, $bindvars);
		return $count;
	}

	/**
	 *  count the number of viewed for this day, this month, this year
	 *
	 */
	public function hit_this_period($type = 'wiki', $when = 'daily')
	{
		$bindvars = $this->period2dates($when);
		$bindvars[1] = $type;
		$query = "select sum(`hits`) from `tiki_stats` where `day` >=? and `type`=?";
		$count = $this->getOne($query, $bindvars);
		if ($count == '') {
			$count = 0;
		}
		return $count;
	}

	public function add_pageview()
	{
		$dayzero = $this->make_time(
			0,
			0,
			0,
			$this->date_format("%m", $this->now),
			$this->date_format("%d", $this->now),
			$this->date_format("%Y", $this->now)
		);

		$conditions = ['day' => (int) $dayzero,];

		$pageviews = $this->table('tiki_pageviews');
		$cant = $pageviews->fetchCount($conditions);

		if ($cant) {
			$pageviews->update(['pageviews' => $pageviews->increment(1),], $conditions);
		} else {
			$pageviews->insert(['day' => (int) $dayzero,'pageviews' => 1,]);
		}
	}

	/**
	 * @param $days
	 * @return array
	 */
	public function get_pv_chart_data($days)
	{
		$now = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d"), $this->date_format("%Y"));
		$dfrom = 0;
		if ($days != 0) {
			$dfrom = $now - ($days * 24 * 60 * 60);
		}

		$query = "select `day`, `pageviews` from `tiki_pageviews` where `day`<=? and `day`>=?";
		$result = $this->fetchAll($query, [(int) $now, (int) $dfrom]);
		$ret = [];
		$n = ceil(count($result) / 10);
		$i = 0;
		$xdata = [];
		$ydata = [];

		foreach ($result as $res) {
			if ($i % $n == 0) {
				$xdata[] = $this->date_format("%e %b", $res["day"]);
			} else {
				$xdata = '';
			}
			$ydata[] = $res["pageviews"];
		}
		$ret['xdata'] = $xdata;
		$ret['ydata'] = $ydata;
		return $ret;
	}
}
