<?php

class TikiCalendarLib extends TikiLib {

  function TikiCalendarLib($db) {
		$this->db = $db;
	}

	function list_tiki_items($tikiobj, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
		global $user;
		$ret = array();

		$res = $dstart = '';
		
		$dc = $this->get_date_converter($user);
		foreach ($tikiobj as $tik) {
			switch ($tik) {
			case "wiki":
				$query = "select * from `tiki_actionlog` where (`lastModif`>? and `lastModif`<?) and `objectType`='wiki page'";
				$result = $this->query($query,array($tstart,$tstop));

		//header('Content-type: text/plain');var_dump($result);die;
				while ($res = $result->fetchRow()) {
					$res['lastModif'] = $dc->getDisplayDateFromServerDate($res['lastModif']); /* server time -> user time */
					$dstart2 = mktime(0, 0, 0, date("m", $res['lastModif']), date("d", $res['lastModif']), date("Y", $res['lastModif']));
					$tstart2 = date("Hi", $res["lastModif"]);
					$quote = "<i>" . tra("by"). " " . $res["user"] . "</i><br />" . str_replace('"', "'", $res["comment"]);
					$ret["$dstart2"][] = array(
						"visible" => "y",
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart2,
						"start" => $res['lastModif'],
						"type" => "wiki",
						"url" => "tiki-index.php?page=" . $res['object'],
						"name" => $res['object'] . " " . tra($res["action"]),
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
						"visible" => "y",
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "wiki page",
						"url" => "tiki-index.php?page=" . urlencode($res["pageName"]). "&amp;comzone=show#comments",
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
						"visible" => "y",
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
						"visible" => "y",
						"calitemId" => "",
						"calname" => "",
						"prio" => "",
						"time" => $tstart,
						"start" => $res['created'],
						"type" => "art",
						"url" => "tiki-read_article.php?articleId=" . $res["articleId"],
						"name" => $res["title"],
						"head" => "<b>" . date("H:i", $res["created"]). "</b> " . tra("in"). " <b>" . $res["topicName"]. "</b>",
						"description" => "<i>" . tra("by"). " " . $res["authorName"] . "</i><br />" . str_replace('"', "'", $tikilib->parse_data($res["heading"]))
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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
						"visible" => "y",
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

}
global $dbTiki;
$tikicalendarlib = new TikiCalendarLib($dbTiki);

?>
