<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class ArtLib extends TikiLib {
	function ArtLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to ArtLib constructor");
		}

		$this->db = $db;
	}

	// 29-Jun-2003, by zaufi
	// The 2 functions below contain duplicate code
	// to remove <PRE> tags... (moreover I copy this code
	// from tikilib.php, and paste to artlib.php, bloglib.php
	// and wikilib.php)
	// TODO: it should be separate function to avoid
	// maintain 3 pieces... (but I don't know PHP and TIKI
	// architecture very well yet to make this :()

	//Special parsing for multipage articles
	function get_number_of_pages($data) {
		// Temporary remove <PRE></PRE> secions to protect
		// from broke <PRE> tags and leave well known <PRE>
		// behaviour (i.e. type all text inside AS IS w/o
		// any interpretation)
		$preparsed = array();

		preg_match_all("/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse);
		$idx = 0;

		foreach (array_unique($preparse[2])as $pp) {
			$key = md5($this->genPass());

			$aux["key"] = $key;
			$aux["data"] = $pp;
			$preparsed[] = $aux;
			$data = str_replace($preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data);
			$idx = $idx + 1;
		}

		$parts = explode("...page...", $data);
		return count($parts);
	}

	function get_page($data, $i) {
		// Temporary remove <PRE></PRE> secions to protect
		// from broke <PRE> tags and leave well known <PRE>
		// behaviour (i.e. type all text inside AS IS w/o
		// any interpretation)
		$preparsed = array();

		preg_match_all("/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse);
		$idx = 0;

		foreach (array_unique($preparse[2])as $pp) {
			$key = md5($this->genPass());

			$aux["key"] = $key;
			$aux["data"] = $pp;
			$preparsed[] = $aux;
			$data = str_replace($preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data);
			$idx = $idx + 1;
		}

		// Get slides
		$parts = explode("...page...", $data);

		if (substr($parts[$i - 1], 1, 5) == "<br/>")
			$ret = substr($parts[$i - 1], 6);
		else
			$ret = $parts[$i - 1];

		// Replace back <PRE> sections
		foreach ($preparsed as $pp)
			$ret = str_replace($pp["key"], "<pre>" . $pp["data"] . "</pre>", $ret);

		return $ret;
	}

	function approve_submission($subId) {
		$data = $this->get_submission($subId);

		if (!$data)
			return false;

		if (!$data["image_x"])
			$data["image_x"] = 0;

		if (!$data["image_y"])
			$data["image_y"] = 0;

		$this->replace_article($data["title"], $data["authorName"], $data["topicId"], $data["useImage"], $data["image_name"],
			$data["image_size"], $data["image_type"], $data["image_data"], $data["heading"], $data["body"], $data["publishDate"], $data["expireDate"],
			$data["author"], 0, $data["image_x"], $data["image_y"], $data["type"],  $data["topline"],  $data["subtitle"],  $data["linkto"],  $data["caption_image"],  
			$data["lang"], $data["rating"], $data['isfloat']);
		$this->remove_submission($subId);
	}

	function add_article_hit($articleId) {
		global $count_admin_pvs;

		global $user;

		if ($count_admin_pvs == 'y' || $user != 'admin') {
			$query = "update `tiki_articles` set `reads`=`reads`+1 where `articleId`=?";

			$result = $this->query($query,array($articleId));
		}

		return true;
	}

	function remove_article($articleId) {
		if ($articleId) {
			$query = "delete from `tiki_articles` where `articleId`=?";

			$result = $this->query($query,array($articleId));
			$this->remove_object('article', $articleId);
			return true;
		}
	}

	function remove_submission($subId) {
		if ($subId) {
			$query = "delete from `tiki_submissions` where `subId`=?";

			$result = $this->query($query,array((int) $subId));

			return true;
		}
	}

	function replace_submission($title, $authorName, $topicId, $useImage, $imgname, $imgsize, 
	$imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $user, $subId, $image_x, $image_y, $type, 
	$topline, $subtitle, $linkto, $image_caption, $lang, $rating = 0, $isfloat = 'n') {
		global $smarty;

		global $dbTiki;
		global $sender_email;
      if ($expireDate < $publishDate) {
         $expireDate = $publishDate;
      }
		if(empty($imgdata)) $imgdata='';
		include_once ('lib/notifications/notificationlib.php');
		$hash = md5($title . $heading . $body);
		$now = date("U");
		$query = "select `name` from `tiki_topics` where `topicId` = ?";
		$topicName = $this->getOne($query,array((int) $topicId));
		$size = strlen($body);

		if ($subId) {
			// Update the article
			$query = "update `tiki_submissions` set
                `title` = ?,
                `authorName` = ?,
                `topicId` = ?,
                `topicName` = ?,
                `size` = ?,
                `useImage` = ?,
                `isfloat` = ?,
                `image_name` = ?,
                `image_type` = ?,
                `image_size` = ?,
                `image_data` = ?,
                `image_x` = ?,
                `image_y` = ?,
                `heading` = ?,
                `body` = ?,
                `publishDate` = ?,
                `created` = ?,
                `author` = ? ,
                `type` = ?,
                `rating` = ?,
`topline`=?, `subtitle`=?, `linkto`=?,`image_caption`=?, `lang`=?
                where `subId` = ?";

			$result = $this->query($query,array($title,$authorName,(int) $topicId,$topicName,(int) $size,
			$useImage,$isfloat,$imgname,$imgtype,(int) $imgsize,$imgdata,(int) $image_x,(int) $image_y,
			$heading,$body,(int) $publishDate,(int) $now,$user,$type,(float) $rating,$topline, $subtitle, $linkto, $image_caption, $lang, (int) $subId));
		} else {
			// Insert the article
			$query = "insert into `tiki_submissions`(`title`,`authorName`,`topicId`,`useImage`,`image_name`,`image_size`,
	`image_type`,`image_data`,`publishDate`,`created`,`heading`,`body`,`hash`,`author`,`reads`,`votes`,`points`,
	`size`,`topicName`,`image_x`,`image_y`,`type`,`rating`,`isfloat`,`topline`, `subtitle`, `linkto`,`image_caption`, `lang`)
                         values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$result = $this->query($query,array($title,$authorName,(int) $topicId,$useImage,$imgname,(int) $imgsize,$imgtype,
	$imgdata,(int) $publishDate,(int) $now,$heading,$body,$hash,$user,0,0,0,(int) $size,$topicName,(int) $image_x,
	(int) $image_y,$type,(float) $rating,$isfloat,$topline, $subtitle, $linkto, $image_caption, $lang));
		}

		$query = "select max(`subId`) from `tiki_submissions` where `created` = ? and `title`=? and `hash`=?";
		$id = $this->getOne($query,array((int) $now,$title,$hash));
		$emails = $notificationlib->get_mail_events('article_submitted', '*');
		if (count($emails)) {
			include_once("lib/notifications/notificationemaillib.php");
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = httpPrefix(). $foo["path"];
			$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

			$smarty->assign('mail_user', $user);
			$smarty->assign('mail_title', $title);
			$smarty->assign('mail_heading', $heading);
			$smarty->assign('mail_body', $body);
			$smarty->assign('mail_date', date("U"));
			$smarty->assign('mail_machine', $machine);
			$smarty->assign('mail_subId', $id);
			sendEmailNotification($emails, "email", "submission_notification_subject.tpl", $_SERVER["SERVER_NAME"], "submission_notification.tpl");
		}

		return $id;
	}

	function add_topic($name, $imagename, $imagetype, $imagesize, $imagedata) {
		$now = date("U");

		$query = "insert into `tiki_topics`(`name`,`image_name`,`image_type`,`image_size`,`image_data`,`active`,`created`)
                     values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($name,$imagename,$imagetype,(int) $imagesize,$imagedata,'y',(int) $now));

		$query = "select max(`topicId`) from `tiki_topics` where `created`=? and `name`=?";
		$topicId = $this->getOne($query,array((int) $now,$name));
		return $topicId;
	}

	function remove_topic($topicId, $all=0) {
		$query = "delete from `tiki_topics` where `topicId`=?";

		$result = $this->query($query,array($topicId));

		if ($all == 1) {
			$query = "delete from `tiki_articles` where `topicId`=?";
			$result = $this->query($query,array($topicId));
		}
		else {
			$query = "update `tiki_articles` set `topicId`=?, `topicName`=? where `topicId`=?";
			$result = $this->query($query, array(NULL, NULL, $topicId));
		}

		return true;
	}

	function replace_topic_name($topicId, $name) {
		$query = "update `tiki_topics` set `name` = ? where
			`topicId` = ?";
		$result = $this->query($query, array($name, $topicId));

		return true;
	}

	function replace_topic_image($topicId, $imagename, $imagetype,
			$imagesize, $imagedata) {
		$topicId = (int)$topicId;
		$query = "update `tiki_topics` set `image_name` = ?,
			`image_type` = ?, `image_size` = ?,  `image_data` = ? 
				where `topicId` = ?";
		$result = $this->query($query, array($imagename, $imagetype,
					$imagesize, $imagedata, $topicId));

		return true;
	}

	function activate_topic($topicId) {
		$query = "update `tiki_topics` set `active`=? where `topicId`=?";

		$result = $this->query($query,array('y',$topicId));
	}

	function deactivate_topic($topicId) {
		$query = "update `tiki_topics` set `active`=? where `topicId`=?";

		$result = $this->query($query,array('n',$topicId));
	}

	function get_topic($topicId) {
		$query = "select `topicId`,`name`,`image_name`,`image_size`,`image_type` from `tiki_topics` where `topicId`=?";

		$result = $this->query($query,array($topicId));

		$res = $result->fetchRow();
		return $res;
	}

	function list_topics() {
		$query = "select `topicId`,`name`,`image_name`,`image_size`,`image_type`,`active` from `tiki_topics` order by `name`";

		$result = $this->query($query,array());

		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["subs"] = $this->getOne("select count(*) from `tiki_submissions` where `topicId`=?",array($res["topicId"]));

			$res["arts"] = $this->getOne("select count(*) from `tiki_articles` where `topicId`=?",array($res["topicId"]));
			$ret[] = $res;
		}

		return $ret;
	}

	function list_active_topics() {
		$query = "select * from `tiki_topics` where `active`=?";

		$result = $this->query($query,array('y'));

		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

// Article Type functions
	function add_type($type) {

/*
		if ($use_ratings == 'on') {$use_ratings = 'y';} else {$use_ratings = 'n';}
		if ($show_pre_publ == 'on') {$show_pre_publ = 'y';} else {$show_pre_publ = 'n';}
		if ($show_post_expire == 'on') {$show_post_expire = 'y';} else {$show_post_expire = 'n';}
		if ($heading_only == 'on') {$heading_only = 'y';} else {$heading_only = 'n';}
		if ($allow_comments == 'on') {$allow_comments = 'y';} else {$allow_comments = 'n';}
		if ($comment_can_rate_article == 'on') {$comment_can_rate_article = 'y';} else {$comment_can_rate_article = 'n';}		
		if ($show_image == 'on') {$show_image = 'y';} else {$show_image = 'n';}
		if ($show_avatar == 'on') {$show_avatar = 'y';} else {$show_avatar = 'n';}
		if ($show_author == 'on') {$show_author = 'y';} else {$show_author = 'n';}
		if ($show_pubdate == 'on') {$show_pubdate = 'y';} else {$show_pubdate = 'n';}
		if ($show_expdate == 'on') {$show_expdate = 'y';} else {$show_expdate = 'n';}
		if ($show_reads == 'on') {$show_reads = 'y';} else {$show_reads = 'n';}
		if ($show_size == 'on') {$show_size = 'y';} else {$show_size = 'n';}
		if ($creator_edit == 'on') {$creator_edit = 'y';} else {$creator_edit = 'n';}

		$query = "select count(*) from `tiki_article_types` where `type`=?";
		$rowcnt = $this->getOne($query,array($type));

		// if the type already exists, delete it first
		if ($rowcnt > 0) {
			$query = "delete from `tiki_article_types` where `type`=?";
			$result = $this->query($query,array($type));
		}
*/
		$result = $this->query("insert into `tiki_article_types`(`type`) values(?)",array($type));

		return true;
	}

	function edit_type($type, $use_ratings, $show_pre_publ, $show_post_expire, 
		$heading_only, $allow_comments, $comment_can_rate_article, $show_image, 
		$show_avatar, $show_author, $show_pubdate, $show_expdate, $show_reads, 
		$show_size, $show_topline, $show_subtitle, $show_linkto, $show_image_caption, $show_lang, $creator_edit) {
		if ($use_ratings == 'on') {$use_ratings = 'y';} else {$use_ratings = 'n';}
		if ($show_pre_publ == 'on') {$show_pre_publ = 'y';} else {$show_pre_publ = 'n';}
		if ($show_post_expire == 'on') {$show_post_expire = 'y';} else {$show_post_expire = 'n';}
		if ($heading_only == 'on') {$heading_only = 'y';} else {$heading_only = 'n';}
		if ($allow_comments == 'on') {$allow_comments = 'y';} else {$allow_comments = 'n';}
		if ($comment_can_rate_article == 'on') {$comment_can_rate_article = 'y';} else {$comment_can_rate_article = 'n';}		
		if ($show_image == 'on') {$show_image = 'y';} else {$show_image = 'n';}
		if ($show_avatar == 'on') {$show_avatar = 'y';} else {$show_avatar = 'n';}
		if ($show_author == 'on') {$show_author = 'y';} else {$show_author = 'n';}
		if ($show_pubdate == 'on') {$show_pubdate = 'y';} else {$show_pubdate = 'n';}
		if ($show_expdate == 'on') {$show_expdate = 'y';} else {$show_expdate = 'n';}
		if ($show_reads == 'on') {$show_reads = 'y';} else {$show_reads = 'n';}
		if ($show_size == 'on') {$show_size = 'y';} else {$show_size = 'n';}
		if ($show_topline == 'on') {$show_topline = 'y';} else {$show_topline = 'n';}
		if ($show_subtitle == 'on') {$show_subtitle = 'y';} else {$show_subtitle = 'n';}
		if ($show_linkto == 'on') {$show_linkto = 'y';} else {$show_linkto = 'n';}
		if ($show_image_caption == 'on') {$show_image_caption = 'y';} else {$show_image_caption = 'n';}
		if ($show_lang == 'on') {$show_lang = 'y';} else {$show_lang = 'n';}
		if ($creator_edit == 'on') {$creator_edit = 'y';} else {$creator_edit = 'n';}
		$query = "update `tiki_article_types` set
			`use_ratings` = ?,
			`show_pre_publ` = ?,
			`show_post_expire` = ?,
			`heading_only` = ?,
			`allow_comments` = ?,
			`comment_can_rate_article` = ?,
			`show_image` = ?,
			`show_avatar` = ?,
			`show_author` = ?,
			`show_pubdate` = ?,
			`show_expdate` = ?,
			`show_reads` = ?,
			`show_size` = ?,
			`show_topline` = ?,
			`show_subtitle` = ?,
			`show_linkto` = ?,
			`show_image_caption` = ?,
			`show_lang` = ?,
			`creator_edit` = ?
			where `type` = ?";
		$result = $this->query($query, array($use_ratings, $show_pre_publ, $show_post_expire, $heading_only, 
$allow_comments, $comment_can_rate_article, $show_image, $show_avatar, $show_author, $show_pubdate, 
$show_expdate, $show_reads, $show_size, $show_topline, $show_subtitle, $show_linkto, $show_image_caption, $show_lang, $creator_edit, $type));
	}

	function remove_type($type) {
		$query = "delete from `tiki_article_types` where `type`=?";
		$result = $this->query($query,array($type));
	}

	function get_type($type) {
		$query = "select * from `tiki_article_types` where `type`=?";

		$result = $this->query($query,array($type));

		$res = $result->fetchRow();
		return $res;
	}

	function list_types() {
		$query = "select * from `tiki_article_types`";
		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['article_cnt'] = $this->getOne("select count(*) from `tiki_articles` where `type` = ?",array($res['type']));
			$ret[] = $res;
		}

		return $ret;
	}
}

$artlib = new ArtLib($dbTiki);

?>
