<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

class BlogLib extends TikiLib {
	function BlogLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to BlogsLib constructor");
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

	function send_trackbacks($id, $trackbacks) {
		// Split to get each URI
		$tracks = explode(',', $trackbacks);

		$ret = array();
		// Foreach URI
		$post_info = $this->get_post($id);
		$blog_info = $this->get_blog($post_info['blogId']);
		//Build uri for post
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$uri = httpPrefix(). str_replace('tiki-blog_post',
			'tiki-view_blog_post', $parts['path']). '?postId=' . $id . '&amp;blogId=' . $post_info['blogId'];
		include ("lib/snoopy/Snoopy.class.inc");
		$snoopy = new Snoopy;

		foreach ($tracks as $track) {
			@$fp = fopen($track, 'r');

			if ($fp) {
				$data = '';

				while (!feof($fp)) {
					$data .= fread($fp, 32767);
				}

				fclose ($fp);
				preg_match("/trackback:ping=(\"|\'|\s*)(.+)(\"|\'\s)/", $data, $reqs);

				if (!isset($reqs[2]))
					return $ret;

				@$fp = fopen($reqs[2], 'r');

				if ($fp) {
					fclose ($fp);

					$submit_url = $reqs[2];
					$submit_vars["url"] = $uri;
					$submit_vars["blog_name"] = $blog_info['title'];
					$submit_vars["title"] = $post_info['title'] ? $post_info['title'] : date("d/m/Y [h:i]", $post_info['created']);
					$submit_vars["title"] .= ' ' . tra('by'). ' ' . $post_info['user'];
					$submit_vars["excerpt"] = substr($post_info['data'], 0, 200);
					$snoopy->submit($submit_url, $submit_vars);
					$back = $snoopy->results;

					if (!strstr('<error>1</error>', $back)) {
						$ret[] = $track;
					}
				}
			}
		}

		return $ret;
	}

	function add_trackback_from($postId, $url, $title = '', $excerpt = '', $blog_name = '') {
		if (!$this->getOne("select count(*) from `tiki_blog_posts` where `postId`=?",array($postId)))
			return false;

		$tbs = $this->get_trackbacks_from($postId);
		$aux = array(
			'title' => $title,
			'excerpt' => $excerpt,
			'blog_name' => $blog_name
		);

		$tbs[$url] = $aux;
		$st = serialize($tbs);
		$query = "update `tiki_blog_posts` set `trackbacks_from`=? where `postId`=?";
		$this->query($query,array($st,$postId));
		return true;
	}

	function get_trackbacks_from($postId) {
		$st = $this->db->getOne("select `trackbacks_from` from `tiki_blog_posts` where `postId`=?",array($postId));

		return unserialize($st);
	}

	function get_trackbacks_to($postId) {
		$st = $this->db->getOne("select `trackbacks_to` from `tiki_blog_posts` where `postId`=?",array($postId));

		return unserialize($st);
	}

	function clear_trackbacks_from($postId) {
		$empty = serialize(array());

		$query = "update `tiki_blog_posts` set `trackbacks_from` = ? where `postId`=?";
		$this->query($query,array($empty,$postId));
	}

	function clear_trackbacks_to($postId) {
		$empty = serialize(array());

		$query = "update `tiki_blog_posts` set `trackbacks_to` = ? where `postId`=?";
		$this->query($query,array($empty,$postId));
	}

	function add_blog_hit($blogId) {
		global $count_admin_pvs;

		global $user;

		if ($count_admin_pvs == 'y' || $user != 'admin') {
			$query = "update `tiki_blogs` set `hits` = `hits`+1 where `blogId`=?";

			$result = $this->query($query,array($blogId));
		}

		return true;
	}

	function insert_post_image($postId, $filename, $filesize, $filetype, $data) {

		$query = "insert into `tiki_blog_posts_images`(`postId`,`filename`,`filesize`,`filetype`,`data`)
    values(?,?,?,?,?)";
		$this->query($query,array($postId,$filename,$filesize,$filetype,$data));
	}

	function get_post_image($imgId) {
		$query = "select * from `tiki_blog_posts_images` where `imgId`=?";

		$result = $this->query($query,array($imgId));
		$res = $result->fetchRow();
		return $res;
	}

	function get_post_images($postId) {
		$query = "select `postId`,`filename`,`filesize`,`imgId` from `tiki_blog_posts_images` where `postId`=?";

		$result = $this->query($query,array((int) $postId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$imgId = $res['imgId'];

			$res['link'] = "<img src='tiki-view_blog_post_image.php?imgId=$imgId' border='0' alt='image' />";
			$parts = parse_url($_SERVER['REQUEST_URI']);
			$path = str_replace('tiki-blog_post.php', 'tiki-view_blog_post_image.php', $parts['path']);
			$res['absolute'] = httpPrefix(). $path . "?imgId=$imgId";
			$ret[] = $res;
		}

		return $ret;
	}

	function remove_post_image($imgId) {
		$query = "delete from `tiki_blog_posts_images` where `imgId`=?";

		$this->query($query,array($imgId));
	}

	function replace_blog($title, $description, $user, $public, $maxPosts, $blogId, $heading, $use_title, $use_find,
		$allow_comments, $show_avatar) {
		$now = date("U");

		if ($blogId) {
			$query = "update `tiki_blogs` set `title`=? ,`description`=?,`user`=?,`public`=?,`lastModif`=?,`maxPosts`=?,`heading`=?,`use_title`=?,`use_find`=?,`allow_comments`=?,`show_avatar`=? where `blogId`=?";

			$result = $this->query($query,array($title,$description,$user,$public,$now,$maxPosts,$heading,$use_title,$use_find,$allow_comments,$show_avatar,$blogId));
		} else {
			$query = "insert into `tiki_blogs`(`created`,`lastModif`,`title`,`description`,`user`,`public`,`posts`,`maxPosts`,`hits`,`heading`,`use_title`,`use_find`,`allow_comments`,`show_avatar`)
                       values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$result = $this->query($query,array((int) $now,(int) $now,$title,$description,$user,$public,0,(int) $maxPosts,0,$heading,$use_title,$use_find,$allow_comments,$show_avatar));
			$query2 = "select max(`blogId`) from `tiki_blogs` where `lastModif`=?";
			$blogId = $this->getOne($query2,array((int) $now));
		}

		return $blogId;
	}

	function list_blog_posts($blogId, $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $date = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `blogId`=? and (`data` like ?) ";
			$bindvars = array((int)$blogId,$findesc);
		} else {
			$mid = " where `blogId`=? ";
			$bindvars = array((int) $blogId);
		}

		if ($date) {
			$mid .= " and  `created`<=? ";
			$bindvars[]=(int) $date;
		}

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$cant_com = $this->getOne("select count(*)
			from `tiki_comments` where
			`object`=? and `objectType` = 'post'", array(
			$res["postId"] ) );

			$res["comments"] = $cant_com;
			if($res['trackbacks_from']!=null)
				$res['trackbacks_from'] = unserialize($res['trackbacks_from']);

			if (!is_array($res['trackbacks_from']))
				$res['trackbacks_from'] = array();

			$res['trackbacks_from_count'] = count(array_keys($res['trackbacks_from']));
			if($res['trackbacks_to']!=null)
				$res['trackbacks_to'] = unserialize($res['trackbacks_to']);
			$res['trackbacks_to_count'] = count($res['trackbacks_to']);
			$res['pages'] = $this->get_number_of_pages($res['data']);
	        $res['avatar'] = $this->get_user_avatar($res['user']);		
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_all_blog_posts($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $date = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`data` like ?) ";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		if ($date) {
			$bindvars[]=$date;
			if ($mid) {
				$mid .= " and  `created`<=? ";
			} else {
				$mid .= " where `created`<=? ";
			}
		}

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query2 = "select `title` from `tiki_blogs` where `blogId`=?";

			$title = $this->getOne($query2,array($res["blogId"]));
			$res["blogtitle"] = $title;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function blog_post($blogId, $data, $user, $title = '', $trackbacks = '') {
		// update tiki_blogs and call activity functions
		global $smarty;

		global $feature_user_watches;
		global $sender_email;
		$tracks = serialize(explode(',', $trackbacks));
		$data = strip_tags($data, '<a><b><i><h1><h2><h3><h4><h5><h6><ul><li><ol><br><p><table><tr><td><img><pre>');
		$now = date("U");
		$query = "insert into `tiki_blog_posts`(`blogId`,`data`,`created`,`user`,`title`,`trackbacks_from`,`trackbacks_to`) values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int) $blogId,$data,(int) $now,$user,$title,serialize(array()),serialize(array())));
		$query = "select max(`postId`) from `tiki_blog_posts` where `created`=? and `user`=?";
		$id = $this->getOne($query,array((int) $now,$user));
		// Send trackbacks recovering only successful trackbacks
		$trackbacks = serialize($this->send_trackbacks($id, $trackbacks));
		// Update post with trackbacks successfully sent
		$query = "update `tiki_blog_posts` set `trackbacks_from`=?, `trackbacks_to` = ? where `postId`=?";
		$this->query($query,array(serialize(array()),$trackbacks,(int) $id));
		$query = "update `tiki_blogs` set `lastModif`=?,`posts`=`posts`+1 where `blogId`=?";
		$result = $this->query($query,array((int) $now,(int) $blogId));
		$this->add_blog_activity($blogId);

		if ($feature_user_watches == 'y') {
			$nots = $this->get_event_watches('blog_post', $blogId);
			if (count($nots)) {
				include_once("lib/notifications/notificationemaillib.php");
				$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
				$query = "select `title` from `tiki_blogs` where `blogId`=?";
				$blogTitle = $this->getOne($query, array((int)$blogId));
				$smarty->assign('mail_title', $blogTitle);
				$smarty->assign('mail_blogid', $blogId);
				$smarty->assign('mail_postid', $id);
				$smarty->assign('mail_date', date("U"));
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_data', $data);
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = httpPrefix(). $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1)
					unset ($parts[count($parts) - 1]);
				$smarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
				sendEmailNotification($nots, "watch", "user_watch_blog_post_subject.tpl", $_SERVER["SERVER_NAME"], "user_watch_blog_post.tpl");
				//@mail($not['email'], tra('Blog post'). ' ' . $blogTitle, $mail_data, "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
			}
		}

		return $id;
	}

	function remove_blog($blogId) {
		$query = "delete from `tiki_blogs` where `blogId`=?";

		$result = $this->query($query,array((int) $blogId));
		$query = "delete from `tiki_blog_posts` where `blogId`=?";
		$result = $this->query($query,array((int) $blogId));
		$this->remove_object('blog', $blogId);
		return true;
	}

	function remove_post($postId) {
		$query = "select `blogId` from `tiki_blog_posts` where `postId`=?";

		$blogId = $this->getOne($query,array((int) $postId));

		if ($blogId) {
			$query = "delete from `tiki_blog_posts` where `postId`=?";

			$result = $this->query($query,array((int) $postId));
			$query = "update `tiki_blogs` set `posts`=`posts`-1 where `blogId`=?";
			$result = $this->query($query,array((int) $blogId));
		}

		$query = "delete from `tiki_blog_posts_images` where `postId`=?";
		$this->query($query,array((int) $postId));
		return true;
	}

	function get_post($postId) {
		$query = "select * from `tiki_blog_posts` where `postId`=?";

		$result = $this->query($query,array((int) $postId));

		if ($result->numRows()) {
			$res = $result->fetchRow();
			
			if (!$res['trackbacks_from'] || $res['trackbacks_from']===null)
				$res['trackbacks_from'] = serialize(array());

			if (!$res['trackbacks_to'] || $res['trackbacks_to']===null)
				$res['trackbacks_to'] = serialize(array());

			$res['trackbacks_from_count'] = count(array_keys(unserialize($res['trackbacks_from'])));
			$res['trackbacks_from'] = unserialize($res['trackbacks_from']);
			$res['trackbacks_to'] = unserialize($res['trackbacks_to']);
			$res['trackbacks_to_count'] = count($res['trackbacks_to']);
		} else {
			return false;
		}

		return $res;
	}

	function update_post($postId, $data, $user, $title = '', $trackbacks = '') {
		$trackbacks = serialize($this->send_trackbacks($postId, $trackbacks));
		$query = "update `tiki_blog_posts` set `trackbacks_to`=?,`data`=?,`user`=?,`title`=? where `postId`=?";
		$result = $this->query($query,array($trackbacks,$data,$user,$title,$postId));
	}

	function list_user_posts($user, $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `user`=? and (`data` like ?) ";
			$bindvars=array($user,$findesc);
		} else {
			$mid = ' where `user`=? ';
			$bindvars=array($user);
		}

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function add_blog_activity($blogId) {

		//Caclulate activity, update tiki_blogs and purge activity table
		$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

		$day0 = $today - (24 * 60 * 60);
		$day1 = $today - (2 * 24 * 60 * 60);
		$day2 = $today - (3 * 24 * 60 * 60);
		// Purge old activity
		$query = "delete from `tiki_blog_activity` where `day`<?";
		$result = $this->query($query,array((int) $day2));
		// Register new activity
		$query = "select count(*) from `tiki_blog_activity` where `blogId`=? and `day`=?";
		$result = $this->getOne($query,array((int) $blogId,(int)$today));

		if ($result) {
			$query = "update `tiki_blog_activity` set `posts`=`posts`+1 where `blogId`=? and `day`=?";
		} else {
			$query = "insert into `tiki_blog_activity`(`blogId`,`day`,`posts`) values(?,?,1)";
		}

		$result = $this->query($query,array((int) $blogId,(int) $today));
		// Calculate activity
		$query = "select `posts` from `tiki_blog_activity` where `blogId`=? and `day`=?";
		$vtoday = $this->getOne($query,array((int) $blogId,(int) $today));
		$day0 = $this->getOne($query,array((int) $blogId,(int) $day0));
		$day1 = $this->getOne($query,array((int) $blogId,(int) $day1));
		$day2 = $this->getOne($query,array((int) $blogId,(int) $day2));
		$activity = (2 * $vtoday) + ($day0)+(0.5 * $day1) + (0.25 * $day2);
		// Update tiki_blogs with activity information
		$query = "update `tiki_blogs` set `activity`=? where `blogId`=?";
		$result = $this->query($query,array($activity,(int) $blogId));
	}
}

$bloglib = new BlogLib($dbTiki);

?>
