<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class CalendarLib extends TikiLib {
	function CalendarLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to CalendarLib constructor");
		}
		$this->db = $db;
	}

	function list_calendars($offset = 0, $maxRecords = -1, $sort_mode = 'name_asc', $find = '') {
		$mid = '';
		$res = array();
		$bindvars = array();
		if ($find) {
			$mid = "where `name` like ?";
			$bindvars[] = '%'.$find.'%';
		}
		$query = "select * from `tiki_calendars` $mid order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = "select count(*) from `tiki_calendars` $mid";
		$cant = $this->getOne($query_cant,$bindvars);

		$res = array();
		while ($r = $result->fetchRow()) {
			$k = $r["calendarId"];
			$res["$k"] = $r;
		}
		$retval["data"] = $res;
		$retval["cant"] = $cant;
		return $retval;
	}

	// give out an array with Ids viewable by $user
	/*Was never used
	function list_user_calIds() {
		global $user;
		if ($user) {
			global $userlib;
			$groups = $userlib->get_user_groups($user);
			// need to add something
			$query = "select `calendarId` from `tiki_calendars` where `user`=?";
			$bindvars=array($user);
		} else {
			$query = "select `calendarId` from `tiki_calendars`";
			$bindvars=array();
		}
		$result = $this->query($query,$bindvars);
		$res = array();
		while ($r = $result->fetchRow()) {
			$res[] = $r['calendarId'];
		}
		return $res;
	} */

	function set_calendar($calendarId, $user, $name, $description, $customflags=array()) {
		$name = strip_tags($name);
		$description = strip_tags($description);
		$now = time();
		if ($calendarId > 0) {
			// modification of a calendar
			$query = "update `tiki_calendars` set `name`=?, `user`=?, `description`=?, ";
			$bindvars = array($name,$user,$description);
			foreach ($customflags as $k => $v) {
				$query .= "`$k`=?, ";
				$bindvars[]=$v;
			}
			$query .= "`lastmodif`=?  where `calendarId`=?";
			$bindvars[] = $now;
			$bindvars[] = $calendarId;
			$result = $this->query($query,$bindvars);
		} else {
			// create a new calendar
			$query = "insert into `tiki_calendars` (`name`,`user`,`description`,`created`,`lastmodif`,`" . implode("`,`", array_keys($customflags)). "`) ";
			$query.= "values (?,?,?,?,?," . implode(",", array_fill(0,count($customflags),"?")). ")";
			$bindvars=array($name,$user,$description,$now,$now);
			foreach ($customflags as $k => $v) $bindvars[]=$v;
			$result = $this->query($query,$bindvars);
			$calendarId=$this->GetOne("select `calendarId` from `tiki_calendars` where `created`=?",array($now));
		}
		return $calendarId;
	}

	function get_calendar($calendarId) {
		$res = $this->query("select * from `tiki_calendars` where `calendarId`=?",array((int)$calendarId));
		return $res->fetchRow();
	}

	function drop_calendar($calendarId) {
		$query = "delete from `tiki_calendars` where `calendarId`=?";
		$this->query($query,array($calendarId));
		$query = "delete from `tiki_calendar_items` where `calendarId`=?";
		$this->query($query,array($calendarId));
	}

	/* tsart ans tstop are in user time - the data base is in server time */
	function list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='') {
		global $user, $tikilib;
		$dc = $tikilib->get_date_converter($user);
		$tstart = $dc->getServerDateFromDisplayDate($tstart);/* user time -> server time */;
		$tstop = $dc->getServerDateFromDisplayDate($tstop);
		$where = array();
		$bindvars=array();
		$time = new timer;
		$time->start();
		foreach ($calIds as $calendarId) {
			$where[] = "i.`calendarId`=?";
			$bindvars[] = (int)$calendarId;
		}

		$cond = "(" . implode(" or ", $where). ")";
		$cond .= " and ((i.`start` > ? and i.`end` < ?) or (i.`start` < ? and i.`end` > ?))";
		$bindvars[] = (int)$tstart;
		$bindvars[] = (int)$tstop;
		$bindvars[] = (int)$tstop;
		$bindvars[] = (int)$tstart;

		$cond .= " and ((c.`personal`='y' and i.`user`=?) or c.`personal` != 'y')";
		$bindvars[] = $user;

		$query = "select i.`calitemId` as `calitemId`, i.`name` as `name`, i.`description` as `description`, i.`start` as `start`, i.`end` as `end`, ";
		$query .= "i.`url` as `url`, i.`status` as `status`, i.`priority`  as `priority`, c.`name` as `calname`, i.`calendarId` as `calendarId`, ";
		$query .= "i.`locationID` as `locationID`, i.`categoryID` as `categoryID`, i.`evId` as `evId` ";
		$query .= "from `tiki_calendar_items` as i left join `tiki_calendars` as c on i.`calendarId`=c.`calendarId` where ($cond)  order by ".$this->convert_sortmode("$sort_mode");
		$result = $this->query($query,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[]=$this->get_item($res["calitemId"]);
		}
		return $ret;
	}

	function list_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='') {
		global $user, $tikilib, $tiki_p_change_events;
		$ret = array();
		$list = $this->list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find);
		foreach ($list as $res) {
			$mloop = date("m", $res['startUser']);
			$dloop = date("d", $res['startUser']);
			$yloop = date("Y", $res['startUser']);
			$dstart = mktime(0, 0, 0, $mloop, $dloop, $yloop);
			$dend = mktime(0, 0, 0, date("m", $res['endUser']), date("d", $res['endUser']), date("Y", $res['endUser']));
			$tstart = date("Hi", $res["startUser"]);
			$tend = date("Hi", $res["endUser"]);
			for ($i = $dstart; $i <= $dend; $i = mktime(0, 0, 0, $mloop, ++$dloop, $yloop)) {
				/* $head is in user time */
				if ($dstart == $dend) {
					$head = date("H:i", $res["startUser"]). " - " . date("H:i", $res["endUser"]);
				} elseif ($i == $dstart) {
					$head = date("H:i", $res["startUser"]). " ...";
				} elseif ($i == $dend) {
					$head = " ... " . date("H:i", $res["endUser"]);
				} else {
					$head = " ... " . tra("continued"). " ... ";
				}

				/* $i is timestamp unix of the beginning of a day */
				$ret["$i"][] = array(
					"result" => $res,
					"calitemId" => $res["calitemId"],
					"calname" => $res["calname"],
					"time" => $tstart, /* user time */
					"end" => $tend, /* user time */
					"type" => $res["status"],
					"web" => $res["url"],
					"prio" => $res["priority"],
					"location" => $res["locationName"],
					"category" => $res["categoryName"],
					"url" => "tiki-calendar.php?todate=$i&amp;editmode=1&amp;calitemId=" . $res["calitemId"],
					"name" => $res["name"],
					"evId" => $res["evId"],
					"head" => $head,
					"parsedDescription" => $tikilib->parse_data($res["description"]),
					"description" => str_replace("\n|\r", "", $res["description"]),
					"calendarId" => $res['calendarId']
				);
			}
		}
		return $ret;
	}

	function list_tiki_items($tikiobj, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
		global $tikilib, $user;
		$ret = array();

		$res = $dstart = '';
		$dc = $tikilib->get_date_converter($user);
		foreach ($tikiobj as $tik) {
			switch ($tik) {
			case "wiki":
				$query = "select * from `tiki_actionlog` where (`lastModif`>? and `lastModif`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['lastModif'] = $dc->getDisplayDateFromServerDate($res['lastModif']); /* server time -> user time */
					$dstart2 = mktime(0, 0, 0, date("m", $res['lastModif']), date("d", $res['lastModif']), date("Y", $res['lastModif']));
					$tstart2 = date("Hi", $res["lastModif"]);
					$quote = "<i>" . tra("by"). " " . $res["user"] . "</i><br />" . str_replace('"', "'", $res["comment"]);
					$ret["$dstart2"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart2,
						"start" => $res['lastModif'],
						"type" => "wiki",
						"url" => "tiki-index.php?page=" . $res["pageName"],
						"name" => $res["pageName"] . " " . tra($res["action"]),
						"head" => "<b>" . date("H:i", $res["lastModif"]). "</b> " . tra("in"). " <b>".str_replace("\n|\r", "", addslashes($tik))."</b>",
						"description" => str_replace("\n|\r", "", $quote)
					);
				}
				$query = "select c.`commentDate` as created, c.`threadId` as threadId, c.`userName` as user, c.`title` as name, c.`object` as pageName ";
				$query.= "from `tiki_comments` as c where c.`objectType` = ? ";
				$query.= "and (c.`commentDate`>? and c.`commentDate`<?)";
				$result = $this->query($query,array('wiki page',$tstart,$tstop));
				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "wiki page",
						"url" => "tiki-index.php?page=" . $res["pageName"]. "&amp;comzone=show#comments",
						"name" => $res["name"],
						"head" => "<b>" . date("H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["pageName"]. "</b>",
						"description" => "<i>" . tra("by"). " " . $res["user"] . "</i>"
					);
						
				}
				break;

			case "gal":
				$query = "select i.`imageId` as `imageid`, i.`created` as `created`, i.`user` as `user`, i.`name` as `name`, ";
				$query.= "g.`name` as `galname`, g.`galleryId` as `galid` from `tiki_images` as i ";
				$query.= "left join `tiki_galleries` as g on g.`galleryId`=i.`galleryId` where (i.`created`>? and i.`created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "gal",
						"url" => "tiki-browse_image.php?galleryId=" . $res["galid"] . "&amp;imageId=" . $res["imageid"],
						"name" => $res["name"],
						"head" => "<b>" . date("H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["galname"]. "</b>",
						"description" => tra("new image uploaded by"). " " . $res["user"]
					);
				}
				break;

			case "art":
				$query = "select `articleId`, `title`, `heading`, `authorName`, `topicName`, `publishDate` as `created` ";
				$query.= "from `tiki_articles` where (`publishDate`>? and `publishDate`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "art",
						"url" => "tiki-read_article.php?articleId=" . $res["articleId"],
						"name" => $res["title"],
						"head" => "<b>" . date("H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["topicName"]. "</b>",
						"description" => "<i>" . tra("by"). " " . $res["authorName"] . "</i><br />" . str_replace('"', "'", $res["heading"])
					);
				}
				break;

			case "blog":
				$query = "select p.`created` as `created`, p.`user` as `user`, p.`title` as `postname`, b.`title` as `blogname`, b.`blogId` as `blogid` ";
				$query.= "from `tiki_blog_posts` as p left join `tiki_blogs` as b on p.`blogId`=b.`blogId` where (p.`created`>? and p.`created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "blog",
						"url" => "tiki-view_blog.php?blogId=" . $res["blogid"],
						"name" => $res["blogname"] . " :: " . $res["postname"],
						"head" => "<b>" . date( "H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["blogname"]. "</b>",
						"description" => "<i>" . tra("by"). " " . $res["user"] . "</i>"
					);
				}
				break;

			case "forum":
				// the left join brings back wiki comments
				//$query = "select c.`commentDate` as created, c.`threadId` as threadId, c.`userName` as user, c.`title` as name, f.`name` as forum, f.`forumId` as forumid, c.`parentId` as parentId ";
				//$query.= "from `tiki_comments` as c left join `tiki_forums` as f on c.`object`=f.`forumId` and c.`objectType` = ? ";
				//$query.= "where (c.`commentDate`>? and c.`commentDate`<?)";
				$query = "select c.`commentDate` as `created`, c.`threadId` as `threadId`, c.`userName` as `user`, c.`title` as `name`, f.`name` as `forum`, f.`forumId` as `forumid`, c.`parentId` as `parentId` ";
				$query.= "from `tiki_comments` as c, `tiki_forums` as f where c.`object`=f.`forumId`and c.`objectType` = ? ";
				$query.= "and (c.`commentDate`>? and c.`commentDate`<?)";
				$result = $this->query($query,array('forum',$tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					if ($res["parentId"] == 0) {
						$res["parentId"] = $res["threadId"];
						$anchor = "";
					} else
						$anchor = "#threadId".$res['threadId'];
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "forum",
						"url" => "tiki-view_forum_thread.php?forumId=" . $res["forumid"]."&amp;comments_parentId=".$res["parentId"].$anchor ,
						"name" => $res["name"],
						"head" => "<b>" . date("H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["forum"]. "</b>",
						"description" => "<i>" . tra("by"). " " . $res["user"] . "</i>"
					);
						
				}
				break;

			case "dir":
				$query = "select `siteId`, `created`, `name`, `description`, `url` ";
				$query.= "from `tiki_directory_sites` where (`created`>? and `created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "dir",
						"url" => "tiki-directory_redirect.php?siteId=" . $res["siteId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => addslashes($res["url"]). "<br />" . str_replace('"', "'", $res["description"])
					);
				}
				break;

			case "fgal":
				$query = "select f.`created` as `created`, f.`user` as `user`, f.`name` as `name`, f.`description` as `description`, g.`galleryId` as `fgalId`, g.`name` as `fgalname` ";
				$query.= "from `tiki_files` as f left join `tiki_file_galleries` as g on f.`galleryId`=g.`galleryId` where (f.`created`>? and f.`created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "fgal",
						"url" => "tiki-list_file_gallery.php?galleryId=" . $res["fgalId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["fgalname"]. "</b>",
						"description" => "<i>" . tra("uploaded by"). " " . addslashes($res["user"]). "</i><br />" . str_replace('"', "'", $res["description"])
					);
				}
				break;

			case "faq":
				$query = "select `faqId`, `created`, `title`, `description` ";
				$query .= "from `tiki_faqs` where (`created`>? and `created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "faq",
						"url" => "tiki-view_faq.php?faqId=" . $res["faqId"],
						"name" => str_replace("'", "", $res["title"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => str_replace('"', "'", $res["description"])
					);
				}
				break;

			case "quiz":
				$query = "select `quizId`, `created`, `name`, `description` ";
				$query.= "from `tiki_quizzes` where (`created`>? and `created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "quiz",
						"url" => "tiki-take_quiz.php?quizId=" . $res["quizId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => str_replace('"', "'", $res["description"])
					);
				}
				break;

			case "track":
				$query = "select i.`itemId` as `itemId`, i.`created` as `created`, t.`name` as `name`, t.`trackerId` as `tracker` ";
				$query.= "from `tiki_tracker_items` as i left join `tiki_trackers` as t on t.`trackerId`=i.`trackerId` where (i.`created`>? and i.`created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "track",
						"url" => "tiki-view_tracker_item.php?trackerId=" . $res["tracker"] . "&amp;offset=0&amp;sort_mode=created_desc&amp;itemId=" . $res["itemId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => tra("new item in tracker")
					);
				}
				break;

			case "surv":
				$query = "select `surveyId`, `created`, `name`, `description` ";
				$query.= "from `tiki_surveys` where (`created`>? and `created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "surv",
						"url" => "tiki-take_survey.php?surveyId=" . $res["surveyId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => str_replace('"', "'", $res["description"])
					);
				}

				break;

			case "nl":
				$query = "select count(s.`email`) as count, FROM_UNIXTIME(s.`subscribed`,'%d') as d, max(s.`subscribed`) as day, s.`nlId` as `nlId`, n.`name` as `name` ";
				$query.= " from `tiki_newsletter_subscriptions` as s left join `tiki_newsletters` as n ";
				$query.= " on n.`nlId`=s.`nlId`  where (`subscribed`>? and `subscribed`<?) group by s.`nlId`, d";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['day'] = $dc->getDisplayDateFromServerDate($res['day']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['day']), date("d", $res['day']), date("Y", $res['day']));
					$tstart = date("Hi", $res["day"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['day'],
						"type" => "nl",
						"url" => "tiki-newsletters.php?nlId=" . $res["nlId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => " ... " . $res["count"],
						"description" => tra("new subscriptions")
					);
				}
				break;

			case "eph":
				$query = "select `publish` as `created`, `title` as `name`, `textdata` as `description` ";
				$query.= "from `tiki_eph` where (`publish`>? and `publish`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "eph",
						"url" => "tiki-eph.php?day=" . date("d", $res["created"]). "&amp;mon=" . date("m", $res['created']). "&amp;year=" . date("Y", $res['created']),
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => str_replace('"', "'", $res["description"])
					);
				}
				break;

			case "chart":
				$query = "select `chartId`, `created`, `title` as `name`, `description` ";
				$query .= "from `tiki_charts` where (`created`>? and `created`<?)";
				$result = $this->query($query,array($tstart,$tstop));

				while ($res = $result->fetchRow()) {
					$res['created'] = $dc->getDisplayDateFromServerDate($res['created']); /* server time -> user time */
					$dstart = mktime(0, 0, 0, date("m", $res['created']), date("d", $res['created']), date("Y", $res['created']));
					$tstart = date("Hi", $res["created"]);
					$ret["$dstart"][] = array(
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "chart",
						"url" => "tiki-view_chart.php?chartId=" . $res["chartId"],
						"name" => str_replace("'", "", $res["name"]),
						"head" => "<b>" . date("H:i", $res["created"]). "</b>",
						"description" => str_replace('"', "'", $res["description"])
					);
				}
				break;
			}
		}
		return $ret;
	}

	function get_item($calitemId) {
		global $user, $tikilib;
		$query = "select i.`calitemId` as `calitemId`, i.`calendarId` as `calendarId`, i.`user` as `user`, i.`start` as `start`, i.`end` as `end`, t.`name` as `calname`, ";
		$query.= "i.`locationId` as `locationId`, l.`name` as `locationName`, i.`categoryId` as `categoryId`, c.`name` as `categoryName`, i.`priority` as `priority`, i.`evId` as `evId`, ";
		$query.= "i.`status` as `status`, i.`url` as `url`, i.`lang` as `lang`, i.`name` as `name`, i.`description` as `description`, i.`created` as `created`, i.`lastmodif` as `lastModif`, ";
		$query.= "t.`customlocations` as `customlocations`, t.`customcategories` as `customcategories`, t.`customlanguages` as `customlanguages`, t.`custompriorities` as `custompriorities`, ";
		$query.= "t.`customparticipants` as `customparticipants`, t.`customevents` as `customevents`  from `tiki_calendar_items` as i left join `tiki_calendar_locations` as l on i.`locationId`=l.`callocId` ";
		$query.= "left join `tiki_calendar_categories` as c on i.`categoryId`=c.`calcatId` left join `tiki_calendars` as t on i.`calendarId`=t.`calendarId` where `calitemId`=?";
		$result = $this->query($query,array((int)$calitemId));
		$res = $result->fetchRow();
		$query = "select `username`, `role` from `tiki_calendar_roles` where `calitemId`=? order by `role`";
		$rezult = $this->query($query,array((int)$calitemId));
		$ppl = array();
		$org = array();

		while ($rez = $rezult->fetchRow()) {
			if ($rez["role"] == '6') {
				$org[] = $rez["username"];
			} elseif ($rez["username"]) {
				$ppl[] = $rez["username"] . ":" . $rez["role"];
			}
		}

		$res["participants"] = implode(',', $ppl);
		$res["organizers"] = implode(',', $org);
		$dc = $tikilib->get_date_converter($user);
		$res["startUser"] = $dc->getDisplayDateFromServerDate($res["start"]); /* user time */
		$res["endUser"] = $dc->getDisplayDateFromServerDate($res["end"]);
		return $res;
	}

	function set_item($user, $calitemId, $data) {
		global $tikilib, $user;
		$dc = $tikilib->get_date_converter($user);
		$data['start'] = $dc->getServerDateFromDisplayDate($data['start']);/* user time -> server time */
		$data['end'] = $dc->getServerDateFromDisplayDate($data['end']);
		if (!$data["locationId"] and !$data["newloc"]) {
			$data["newloc"] = tra("not specified");
		}

		if (trim($data["newloc"])) {
			$bindvars=array((int)$data["calendarId"],trim($data["newloc"]));
			$query = "delete from `tiki_calendar_locations` where `calendarId`=? and `name`=?";
			$this->query($query,$bindvars,-1,-1,false);
			$query = "insert into `tiki_calendar_locations` (`calendarId`,`name`) values (?,?)";
			$this->query($query,$bindvars);
			$data["locationId"] = $this->GetOne("select `callocId` from `tiki_calendar_locations` where `calendarId`=? and `name`=?",$bindvars);
		}

		if (!$data["categoryId"] and !$data["newcat"]) {
			$data["newcat"] = tra("not specified");
		}

		if (trim($data["newcat"])) {
			$query = "delete from `tiki_calendar_categories` where `calendarId`=? and `name`=?";
			$bindvars=array((int)$data["calendarId"],trim($data["newcat"]));
			$this->query($query,$bindvars,-1,-1,false);
			$query = "insert into `tiki_calendar_categories` (`calendarId`,`name`) values (?,?)";
			$this->query($query,$bindvars);
			$data["categoryId"] = $this->GetOne("select `calcatId` from `tiki_calendar_categories` where `calendarId`=? and `name`=?",$bindvars);
		}

		$roles = array();
		if ($data["organizers"]) {
			$orgs = split(',', $data["organizers"]);
			foreach ($orgs as $o) {
				$roles['6'][] = trim($o);
			}
		}
		if ($data["participants"]) {
			$parts = split(',', $data["participants"]);
			foreach ($parts as $pa) {
				$p = split('\:', trim($pa));
				if (isset($p[0])and isset($p[1])) {
					$roles["$p[1]"][] = trim($p[0]);
				}
			}
		}

		if ($calitemId) {
			$query = "update `tiki_calendar_items` set `calendarId`=?,`user`=?,`start`=?,`end`=? ,`locationId`=? ,`categoryId`=?,`priority`=?,`status`=?,`url`=?,";
			$query.= "`lang`=?,`name`=?,`description`=?,`evId`=?,`lastmodif`=? where `calitemId`=?";
			$bindvars=array((int)$data["calendarId"],$user,(int)$data["start"],(int)$data["end"],(int)$data["locationId"],(int)$data["categoryId"],(int)$data["priority"],
			                $data["status"],$data["url"],$data["lang"],$data["name"],$data["description"],(int)$data["evId"],(int)time(),(int)$calitemId);
			$result = $this->query($query,$bindvars);
		} else {
			$now=time();
			$query = "insert into `tiki_calendar_items` (`calendarId`, `user`, `start`, `end`, `locationId`, `categoryId`,`evId`,  ";
			$query.= " `priority`, `status`, `url`, `lang`, `name`, `description`, `created`, `lastmodif`) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array((int)$data["calendarId"],$user,(int)$data["start"],(int)$data["end"],(int)$data["locationId"],(int)$data["categoryId"],(int)$data["evId"],$data["priority"],$data["status"],$data["url"],$data["lang"],$data["name"],$data["description"],(int)$now,(int)$now);
			$result = $this->query($query,$bindvars);
			$calitemId = $this->GetOne("select `calitemId` from `tiki_calendar_items` where `calendarId`=? and `created`=?",array($data["calendarId"],$now));
		}

		if ($calitemId) {
			$query = "delete from `tiki_calendar_roles` where `calitemId`=?";
			$this->query($query,array($calitemId));
		}

		foreach ($roles as $lvl => $ro) {
			foreach ($ro as $r) {
				$query = "insert into `tiki_calendar_roles` (`calitemId`,`username`,`role`) values (?,?,'?')";
				$this->query($query,array($calitemId,$r,$lvl));
			}
		}
		return $calitemId;
	}

	function drop_item($user, $calitemId) {
		if ($calitemId) {
			$query = "delete from `tiki_calendar_items` where `calitemId`=?";
			$this->query($query,array($calitemId));
		}
	}

	function list_locations($calendarId) {
		$res = array();
		if ($calendarId > 0) {
			$query = "select `callocId` as `locationId`, `name` from `tiki_calendar_locations` where `calendarId`=? order by `name`";
			$result = $this->query($query,array($calendarId));
			while ($rez = $result->fetchRow()) {
				$res[] = $rez;
			}
		}
		return $res;
	}

	function list_categories($calendarId) {
		$res = array();
		if ($calendarId > 0) {
			$query = "select `calcatId` as `categoryId`, `name` from `tiki_calendar_categories` where `calendarId`=? order by `name`";
			$result = $this->query($query,array($calendarId));
			while ($rez = $result->fetchRow()) {
				$res[] = $rez;
			}
		}
		return $res;
	}
	
	// Returns the last $maxrows of modified events for an
	// optional $calendarId
	function last_modif_events($maxrows = -1, $calendarId = 0) {
		
		if ($calendarId > 0) {
			$cond = "where `calendarId` = ? ";
			$bindvars = array($calendarId);
		} else {
			$cond = '';
			$bindvars = array();
		}
				
		$query = "select `start`, `name`, `calitemId`, `calendarId`, `user`, `lastModif` from `tiki_calendar_items` ".$cond."order by ".$this->convert_sortmode('lastModif_desc');
	
		$result = $this->query($query,$bindvars,$maxrows,0);
		
		$ret = array();
		
		while ($res = $result->fetchRow()) {
		    $ret[] = $res;
		}
		
		return $ret;
	}
}

$calendarlib = new CalendarLib($dbTiki);

?>
