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

	function list_calendars($offset=0,$maxRecords=-1,$sort_mode='created desc',$find='')
	{
		$sort_mode = str_replace("_"," ",$sort_mode);
		if ($find) {
			$mid = "where name like '%".$find."%'";
		} else {
			$mid = '';
		}
		$query = "select * from tiki_calendars $mid order by $sort_mode limit $offset,$maxRecords";
		$result = $this->query($query);
		$ret = array();
		while ($r = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$res[] = $r;
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
			$groupscond = implode("' or groupname='",$groups);
			$query = "select calendarId from tiki_calendars where (groupname='$groupscond' or public='y') and (visible='y' or user='$user')";
		} else {
			$query = "select calendarId from tiki_calendars where (groupname='Anonymous' or public='y') and (visible='y')";
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

	function set_calendar($calendarId,$user,$group,$name,$description,$public,$visible)
	{
		$name = addslashes(strip_tags($name));
		$description = addslashes(strip_tags($description));
		$now = time();
		if ($calendarId > 0) {
			// modification of a calendar
			$query = "update tiki_calendars set name='$name', groupname='$group', description='$description', ";
			$query.= "lastmodif=$now, public='$public', visible='$visible' where calendarId=$calendarId";
			$result = $this->query($query);
		} else {
			// create a new calendar
			$query = "insert into tiki_calendars (name,groupname,description,created,lastmodif,public,visible) ";
			$query.= "values ('$name','$group','$description',$now,$now,'$public','$visible')";
			$result = $this->query($query);
			$calendarId = mysql_insert_id();
		}
		return $calendarId;
	}

	function get_calendar($calendarId)
	{
		$query = "select * from tiki_calendars where calendarId=$calendarId";
		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
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
			$where[] = "calendarId=$calendarId";	
		}
		$cond = "(".implode(" or ",$where).")";
		$cond.= " and (start>$tstart or end<$tstop)"; 
		$query = "select calitemId, name, description, start, end from tiki_calendar_items where ($cond) ";
		$result = $this->query($query);
		$ret = Array();
		while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$dstart = mktime(0,0,0,date("m",$res['start']),date("d",$res['start']),date("Y",$res['start']));
			$tstart = date("Hi",$res["start"]);
			$ret["$dstart"][] = array(
				"calitemId" => $res["calitemId"],
				"time" => $tstart,
				"type" => "calitem",
				"url" => "tiki-calendar.php?todate=$dstart&editmode=1&calitemId=".$res["calitemId"],
				"name" => $res["name"],
				"descriptionhead" => date("H:i",$res["start"])."-".$res["name"],
				"descriptionbody" => str_replace("\n|\r","",$res["description"])
			);
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
						$ret["$dstart"][] = array(
							"calitemId" => "",
							"time" => $tstart,
							"type" => "wiki",
							"url" => "tiki-index.php?page=".$res["pageName"],
							"name" => $res["pageName"]." ".tra($res["action"]),
							"descriptionhead" => date("H:i",$res["lastModif"])."-".$res["pageName"]." <i>".$res["action"]."</i>",
							"descriptionbody" => tra("by")." ".$res["user"]." (".$res["ip"].")<br>".addslashes($res["comment"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"])." ".tra("in")." ".addslashes($res["galname"]),
							"descriptionbody" => tra("new image uploaded by")." ".$res["user"]
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["title"])." ".tra("by")." ".$res["authorName"],
							"descriptionbody" => addslashes($res["heading"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".$res["blogname"]." ".tra("by")." ".$res["user"],
							"descriptionbody" => tra("new post")." ".addslashes($res["postname"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"]),
							"descriptionbody" => tra("by")." ".$res["user"]." ".tra("in")." ".$res["forum"]
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"]),
							"descriptionbody" => addslashes($res["url"])."<br/>".addslashes($res["description"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"]),
							"descriptionbody" => tra("ul by")." ".addslashes($res["user"])."<br/>".addslashes($res["description"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["title"]),
							"descriptionbody" => addslashes($res["description"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"]),
							"descriptionbody" => addslashes($res["description"])
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"]),
							"descriptionbody" => tra("new item in tracker")
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
							"descriptionhead" => date("H:i",$res["created"])." - ".addslashes($res["name"]),
							"descriptionbody" => addslashes($res["description"])
						);
					}
					break;



			}
		}
		return $ret;
	}
	
	function get_item($calitemId)
	{
		$query = "select * from tiki_calendar_items where calitemId=$calitemId";
		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function set_item($user,$calitemId,$data)
	{
		
	}
	
	function drop_item($user,$calitemId)
	{

	}

	function list_locations($calendarId)
	{
		if ($calendarId > 0) {
			$query = "select callocId, name from tiki_calendar_locations where calendarId=$calendarId";
			$result = $this->query($query);
			$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		} else {
			$res = array();
		}
		return $res;
	}

	function list_categories($calendarId)
	{
		if ($calendarId > 0) {
			$query = "select calcatId, name from tiki_calendar_categories where calendarId=$calendarId";
			$result = $this->query($query);
			$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		} else {
			$res = array();
		}
		return $res;
	}
}

$calendarlib= new CalendarLib($dbTiki);
?>
