<?php
class CalendarLib extends TikiLib {

  function CalendarLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to CalendarLib constructor");  
    }
    $this->db = $db;  
  }

	function list_calendars($offset=0,$maxRecords=-1,$sort_mode='created desc',$find='',$hide=1)
	{
		$res = array();
		$mid = '';
		$sort_mode = str_replace("_"," ",$sort_mode);
		if ($find) {
			$mid = "where name like '%".$find."%'";
		}
		$query = "select * from tiki_calendars $mid order by $sort_mode limit $offset,$maxRecords";
		$result = $this->query($query);
		$res = array();
		while ($r = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$k = $r["calendarId"];
			$res["$k"] = $r;
		}
		return $res;
	}

	// give out an array with Ids viewable by $user
	function list_user_calIds()
	{
		global $user;
		if ($user) {
			global $userlib;
			$groups = $userlib->get_user_groups($user);
			// need to add something
			$query = "select calendarId from tiki_calendars where user='$user'";
		} else {
			$query = "select calendarId from tiki_calendars";
		}
		$result = $this->query($query);
		$ret = array();
		while ($r = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$res[] = $r['calendarId'];
		}
		return $res;
	}

	function list_cal_users($calendarId) {
		global $userlib;
		$groupname = $this->getOne("select groupname from tiki_calendars where calendarId=$calendarId");
		if ($groupname == "Anonymous") {
			return false;
		} else {
			$users = $userlib->get_group_users($groupname);
		}
		return $users;
	}

	function set_calendar($calendarId,$user,$name,$description,$customflags)
	{
		$name = addslashes(strip_tags($name));
		$description = addslashes(strip_tags($description));
		$now = time();
		if ($calendarId > 0) {
			// modification of a calendar
			$query = "update tiki_calendars set name='$name', user='$user', description='$description', ";
			foreach ($customflags as $k=>$v) {
				$query.= "$k='$v', ";
			}
			$query.= "lastmodif=$now  where calendarId=$calendarId";
			$result = $this->query($query);
		} else {
			// create a new calendar
			$query = "insert into tiki_calendars (name,user,description,created,lastmodif,".implode(",",array_keys($customflags)).") ";
			$query.= "values ('$name','$user','$description',$now,$now,'".implode("','",$customflags)."')";
			$result = $this->query($query);
			$calendarId = mysql_insert_id();
		}
		return $calendarId;
	}

	function get_calendar($calendarId)
	{
		
		$res = $this->query("select * from tiki_calendars where calendarId='$calendarId' ");
		return $res->fetchRow(DB_FETCHMODE_ASSOC);
	}

	function drop_calendar($calendarId)
	{
		$query = "delete from tiki_calendars where calendarId=$calendarId";
		$this->query($query);
	}

 	function list_items($calIds,$user,$tstart,$tstop,$offset,$maxRecords,$sort_mode,$find)
	{
		$where = array();
		foreach ($calIds as $calendarId) {
			$where[] = "i.calendarId=$calendarId";	
		}
		$cond = "(".implode(" or ",$where).")";
		#$cond.= " and (start>$tstart or end<$tstop)"; 
		$cond.= " and ((i.start > $tstart or i.end < $tstop) or (i.start < $tstart and i.end < $tstop))"; 
		$query = "select i.calitemId as calitemId, i.name as name, i.description as description, i.start as start, i.end as end, ";
		$query.= "i.url as url, i.status as status, i.priority  as priority, c.name as calname, i.calendarId as calendarId ";
		$query.= "from tiki_calendar_items as i left join tiki_calendars as c on i.calendarId=c.calendarId where ($cond) ";
		$result = $this->query($query);
		$ret = Array();
		while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$dstart = mktime(0,0,0,date("m",$res['start']),date("d",$res['start']),date("Y",$res['start']));
			$dend = mktime(0,0,0,date("m",$res['end']),date("d",$res['end']),date("Y",$res['end']));
			$tstart = date("Hi",$res["start"]);
			$tend = date("Hi",$res["end"]);
			for ($i=$dstart;$i<=$dend;$i=($i+(60*60*24))) {
				if ($dstart == $dend) {
					$head = date("H:i",$res["start"])." - ".date("H:i",$res["end"]);
				} elseif ($i == $dstart) {
					$head = date("H:i",$res["start"])." ...";
				} elseif ($i == $dend) {
					$head = " ... ".date("H:i",$res["end"]);
				} else {
					$head = " ... ".tra("continued")." ... ";
				}
				$ret["$i"][] = array(
					"result" => $res,
					"calitemId" => $res["calitemId"],
					"calname" => $res["calname"],
					"time" => $tstart,
					"type" => (string) $res["status"],
					"web" => $res["url"],
					"prio" => $res["priority"],
					"url" => "tiki-calendar.php?todate=$i&editmode=1&calitemId=".$res["calitemId"],
					"name" => $res["name"],
					"extra" => "<div align=right>... ".tra("click to edit"),
					"head" => $head,
					"description" => str_replace("\n|\r","",$res["description"])
				);
			}
		}
		return $ret;
	}
  
	function list_tiki_items($tikiobj,$user,$tstart,$tstop,$offset,$maxRecords,$sort_mode,$find)
	{
		$ret = array();
		$res = $dstart = '';
		foreach ($tikiobj as $tik) {
			switch ($tik) {
				
				case "wiki":
					$query = "select * from tiki_actionlog where (lastModif>$tstart and lastModif<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['lastModif']),date("d",$res['lastModif']),date("Y",$res['lastModif']));
						$tstart = date("Hi",$res["lastModif"]);
						$quote = "<i>".tra("by")." ".$res["user"]."</i><br/>".addslashes(str_replace('"',"'",$res["comment"]));
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"calname" => "",
							"prio" => "",
							"time" => $tstart,
							"type" => "wiki",
							"url" => "tiki-index.php?page=".$res["pageName"],
							"name" => $res["pageName"]." ".tra($res["action"]),
							"head" => "<b>".date("H:i",$res["lastModif"])."</b> ".tra("in")." <b>$tik</b>",
							"description" => str_replace("\n|\r","",$quote)
						);
					}
					break;
				
				case "gal":
					$query = "select i.imageId as imageid, i.created as created, i.user as user, i.name as name, ";
					$query.= "g.name as galname, g.galleryId as galid from tiki_images as i ";
					$query.= "left join tiki_galleries as g on g.galleryId=i.galleryId where (i.created>$tstart and i.created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "gal",
							"url" => "tiki-view_image.php?galleryId=".$res["galid"]."&imageId=".$res["imageid"],
							"name" => $res["name"],
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => tra("new image")." ".addslashes($res["name"])."<br/>".tra("in")." ".tra("image gallery")." ".addslashes($res["galname"])."<br/>".tra("new image uploaded by")." ".$res["user"]
						);
					}
					break;
				
				case "art":
					$query = "select articleId, title, heading, authorName, topicName, publishDate as created ";
					$query.= "from tiki_articles where (publishDate>$tstart and publishDate<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "art",
							"url" => "tiki-read_article.php?atricleId=".$res["articleId"],
							"name" => $res["title"],
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["title"])."</b> ".tra("by")." ".$res["authorName"]."<br/>".addslashes(str_replace('"',"'",$res["heading"]))
						);
					}
					break;
				
				case "blog":
					$query = "select p.created as created, p.user as user, p.title as postname, b.title as blogname, b.blogId as blogid ";
					$query.= "from tiki_blog_posts as p left join tiki_blogs as b on p.blogId=b.blogId where (p.created>$tstart and p.created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "blog",
							"url" => "tiki-view_blog.php?blogId=".$res["blogid"],
							"name" => $res["blogname"],
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".$res["blogname"]."</b> ".tra("by")." ".$res["user"]."<br/>".tra("new post")." ".addslashes($res["postname"])
						);
					}
					break;

				case "forum":
					$query = "select c.commentDate as created, c.threadId as threadId, c.userName as user, c.title as name, f.name as forum, f.forumId as forumId ";
					$query.= "from tiki_comments as c left join tiki_forums as f on c.object=md5(concat('forum',f.forumId)) where (c.commentDate>$tstart and c.commentDate<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "forum",
							"url" => "tiki-view_forum.php?forumId=".$res["forumId"],
							"name" => $res["name"],
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b> ".tra("by")." ".$res["user"]."<br/>".tra("in")." ".$res["forum"]
						);
					}
					break;

				case "dir":
					$query = "select siteId, created, name, description, url ";
					$query.= "from tiki_directory_sites where (created>$tstart and created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "dir",
							"url" => "tiki-directory_redirect.php?siteId=".$res["siteId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".addslashes($res["url"])."<br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;

				case "fgal":
					$query = "select f.created as created, f.user as user, f.name as name, f.description as description, g.galleryId as fgalId ";
					$query.= "from tiki_files as f left join tiki_file_galleries as g on f.galleryId=g.galleryId where (f.created>$tstart and f.created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "fgal",
							"url" => "tiki-list_file_gallery.php?galleryId=".$res["fgalId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".tra("uploaded by")." ".addslashes($res["user"])."<br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;

				case "faq":
					$query = "select faqId, created, title, description ";
					$query.= "from tiki_faqs where (created>$tstart and created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "faq",
							"url" => "tiki-view_faq.php?faqId=".$res["faqId"],
							"name" => str_replace("'","",$res["title"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["title"])."</b><br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;

				case "quiz":
					$query = "select quizId, created, name, description ";
					$query.= "from tiki_quizzes where (created>$tstart and created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "quiz",
							"url" => "tiki-take_quiz.php?quizId=".$res["quizId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;

				case "track":
					$query = "select i.itemId as itemId, i.created as created, t.name as name, t.trackerId as tracker ";
					$query.= "from tiki_tracker_items as i left join tiki_trackers as t on t.trackerId=i.trackerId where (i.created>$tstart and i.created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "track",
							"url" => "tiki-view_tracker_item.php?trackerId=".$res["tracker"]."&offset=0&sort_mode=created_desc&itemId=".$res["itemId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".tra("new item in tracker")
						);
					}
					break;

				case "surv":
					$query = "select surveyId, created, name, description ";
					$query.= "from tiki_surveys where (created>$tstart and created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "surv",
							"url" => "tiki-take_survey.php?surveyId=".$res["surveyId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;

				case "nl":
					$query = "select count(s.email) as count, FROM_UNIXTIME(s.subscribed,'%d') as d, max(s.subscribed) as day, s.nlId as nlId, n.name as name from tiki_newsletter_subscriptions as s ";
					$query.= "left join tiki_newsletters as n on n.nlId=s.nlId  where (subscribed>$tstart and subscribed<$tstop) group by s.nlId, d";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['day']),date("d",$res['day']),date("Y",$res['day']));
						$tstart = date("Hi",$res["day"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "nl",
							"url" => "tiki-newsletters.php?nlId=".$res["nlId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => " ... ".$res["count"],
							"descriptionbody" => tra("new subscriptions")
						);
					}
					break;

				case "eph":
					$query = "select publish as created, title as name, textdata as description ";
					$query.= "from tiki_eph where (publish>$tstart and publish<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "eph",
							"url" => "tiki-eph.php?day=".date("d",$res["created"])."&mon=".date("m",$res['created'])."&year=".date("Y",$res['created']),
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;

				case "chart":
					$query = "select chartId, created, title as name, description ";
					$query.= "from tiki_charts where (created>$tstart and created<$tstop)";
					$result = $this->query($query);
					while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
						$dstart = mktime(0,0,0,date("m",$res['created']),date("d",$res['created']),date("Y",$res['created']));
						$tstart = date("Hi",$res["created"]);
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "chart",
							"url" => "tiki-view_chart.php?chartId=".$res["chartId"],
							"name" => str_replace("'","",$res["name"]),
							"descriptionhead" => date("H:i",$res["created"]),
							"descriptionbody" => "<b>".addslashes($res["name"])."</b><br/>".addslashes(str_replace('"',"'",$res["description"]))
						);
					}
					break;



			}
		}
		return $ret;
	}
	
	function get_item($calitemId)
	{
		$query = "select i.calitemId as calitemId, i.calendarId as calendarId, i.user as user, i.start as start, i.end as end, t.name as calname, ";
		$query.= "i.locationId as locationId, l.name as locationName, i.categoryId as categoryId, c.name as categoryName, i.priority as priority, ";
		$query.= "i.status as status, i.url as url, i.lang as lang, i.name as name, i.description as description, i.created as created, i.lastModif as lastModif, ";
		$query.= "t.customlocations as customlocations, t.customcategories as customcategories, t.customlanguages as customlanguages, t.custompriorities as custompriorities, ";
		$query.= "t.customparticipants as customparticipants from tiki_calendar_items as i left join tiki_calendar_locations as l on i.locationId=l.callocId ";
		$query.= "left join tiki_calendar_categories as c on i.categoryId=c.calcatId left join tiki_calendars as t on i.calendarId=t.calendarId where calitemId=$calitemId";
		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$query = "select username, role from tiki_calendar_roles where calitemId=$calitemId order by role";
		$rezult = $this->query($query);
		$ppl = array();
		$org = array();
		while ($rez = $rezult->fetchRow(DB_FETCHMODE_ASSOC)) {
			if ($rez["role"] == '6') {
				$org[] = $rez["username"];
			} elseif ($rez["username"]) {
				$ppl[] = $rez["username"].":".$rez["role"];
			}
		}
		$res["participants"] = implode(',',$ppl);
		$res["organizers"] = implode(',',$org);
		return $res;
	}

	function set_item($user,$calitemId,$data)
	{
		if (!$data["locationId"] and !$data["newloc"]) {
			$data["newloc"] = tra("not specified");
		}
		if (trim($data["newloc"])) {
			$query = "replace into tiki_calendar_locations (calendarId,name) values (".$data["calendarId"].",'".trim($data["newloc"])."')";
			$this->query($query);
			$data["locationId"] = mysql_insert_id();
		}
		if (!$data["locationId"] and !$data["newcat"]) {
			$data["newcat"] = tra("not specified");
		}
		if (trim($data["newcat"])) {
			$query = "replace into tiki_calendar_categories (calendarId,name) values (".$data["calendarId"].",'".trim($data["newcat"])."')";
			$this->query($query);
			$data["categoryId"] = mysql_insert_id();
		}
		$roles = array();
		if ($data["organizers"]) {
			$orgs = split(',',$data["organizers"]);
			foreach ($orgs as $o) {
				$roles['6'][] = trim($o);
			}
		}
		if ($data["participants"]) {
			$parts = split(',',$data["participants"]);
			foreach ($parts as $pa) {
				$p = split('\:',trim($pa));
				if (isset($p[0]) and isset($p[1])) {
					$roles["$p[1]"][] = trim($p[0]); 
				}
			}
		}
		if ($calitemId) {
			$query = "update tiki_calendar_items set calendarId=".$data["calendarId"].", user='$user',";
			$query.= "start=".$data["start"].",end=".$data["end"].",locationId='".$data["locationId"]."',categoryId='".$data["categoryId"]."',";
			$query.= "priority=".$data["priority"].",status='".$data["status"]."',url='".$data["url"]."',";
			$query.= "lang='".$data["lang"]."',name='".$data["name"]."',description='".$data["description"]."',lastmodif=".time()." where calitemId=$calitemId";
			$result = $this->query($query);
		} else {
			$query = "insert into tiki_calendar_items (calendarId, user, start, end, locationId, categoryId, ";
			$query.= " priority, status, url, lang, name, description, created, lastmodif) values (";
			$query.= $data["calendarId"].",'".$data["user"]."',".$data["start"].",".$data["end"].",'".$data["locationId"]."','";
			$query.= $data["categoryId"]."',".$data["priority"].",'".$data["status"]."','".$data["url"]."','";
			$query.= $data["lang"]."','".$data["name"]."','".$data["description"]."',".time().",".time().")";
			$result = $this->query($query);
			$calitemId = mysql_insert_id();
		}
		if ($calitemId) {
			$query = "delete from tiki_calendar_roles where calitemId=$calitemId";
			$this->query($query);
		}
		foreach ($roles as $lvl=>$ro) {
			foreach ($ro as $r) {
				$query = "insert into tiki_calendar_roles (calitemId,username,role) values ($calitemId,'$r','$lvl')";
				$this->query($query);
			}
		}
		return $calitemId;
	}
	
	function drop_item($user,$calitemId)
	{
		if ($calitemId) {
			$query = "delete from tiki_calendar_items where calitemId=$calitemId";
			$this->query($query);
		}
	}

	function list_locations($calendarId)
	{
		$res = array();
		if ($calendarId > 0) {
			$query = "select callocId as locationId, name from tiki_calendar_locations where calendarId=$calendarId";
			$result = $this->query($query);
			while ($rez = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
				$res[] = $rez;
			}
		}
		return $res;
	}

	function list_categories($calendarId)
	{
		$res = array();
		if ($calendarId > 0) {
			$query = "select calcatId as categoryId, name from tiki_calendar_categories where calendarId=$calendarId";
			$result = $this->query($query);
			while ($rez = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
				$res[] = $rez;
			}
		}
		return $res;
	}

}

$calendarlib= new CalendarLib($dbTiki);
?>
