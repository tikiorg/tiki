<?php

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once('lib/reportslib.php');

/**
 * Class that handles all blog operations
 *
 * @uses TikiLib
 * @package
 * @version
 * @license LGPL. See licence.txt for more details
 */
class BlogLib extends TikiLib {

	/**
	 * get_number_of_pages Returns the number of pages
	 *
	 * @param string $data
	 * @access public
	 * @return int number of pages
	 */
	function get_number_of_pages($data) {
		$parts = explode("...page...", $data);
		return count($parts);
	}

	/**
	 * get_page Returns a spcific page of a post
	 *
	 * @param string $data
	 * @param int $i
	 * @access public
	 * @return string the page $i of the post
	 */
	function get_page($data, $i) {
		$parts = explode("...page...", $data);

		$ret = $parts[$i - 1];
		if (substr($parts[$i - 1], 1, 5) == "<br/>") $ret = substr($parts[$i - 1], 6);
		if (substr($parts[$i - 1], 1, 6) == "<br />") $ret = substr($parts[$i - 1], 7);

		return $ret;
	}

	/**
	 * add_blog_hit Add a hit for the blog $blogId
	 *
	 * @param int $blogId
	 * @access public
	 * @return boolean unconditionnal true
	 */
	function add_blog_hit($blogId) {
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_blogs` set `hits` = `hits`+1 where `blogId`=?";
			$result = $this->query($query, array((int) $blogId));
		}
		return true;
	}

	/**
	 * insert_post_image Add an image to a post
	 *
	 * @param int $postId
	 * @param string $filename
	 * @param int $filesize
	 * @param string $filetype
	 * @param blob $data
	 * @access public
	 * @return void
	 */
	function insert_post_image($postId, $filename, $filesize, $filetype, $data) {
		$query = "insert into `tiki_blog_posts_images`(`postId`,`filename`,`filesize`,`filetype`,`data`) values(?,?,?,?,?)";
		$this->query($query, array($postId, $filename, $filesize, $filetype, $data));
	}

	/**
	 * get_post_image Returns the image $imgId
	 *
	 * @param mixed $imgId
	 * @access public
	 * @return array all fields that are associated with an image in tiki_blog_post_images database table
	 */
	function get_post_image($imgId) {
		$query = "select * from `tiki_blog_posts_images` where `imgId`=?";
		$result = $this->query($query, array($imgId));
		$res = $result->fetchRow();
		return $res;
	}

	/**
	 * get_post_images Returns all the images joined to a post
	 *
	 * @param int $postId
	 * @access public
	 * @return array with the permalink and the absolute link for each image
	 */
	function get_post_images($postId) {
		global $tikilib;
		$query = "select `postId`,`filename`,`filesize`,`imgId` from `tiki_blog_posts_images` where `postId`=?";

		$result = $this->query($query, array((int) $postId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$imgId = $res['imgId'];
			$res['link'] = "<img src='tiki-view_blog_post_image.php?imgId=$imgId' border='0' alt='image' />";
			$parts = parse_url($_SERVER['REQUEST_URI']);
			$path = str_replace('tiki-blog_post.php', 'tiki-view_blog_post_image.php', $parts['path']);
			$res['absolute'] = $tikilib->httpPrefix(). $path . "?imgId=$imgId";
			$ret[] = $res;
		}

		return $ret;
	}

	/**
	 * remove_post_image Removes an image
	 *
	 * @param int $imgId
	 * @access public
	 * @return void
	 */
	function remove_post_image($imgId) {
		$query = "delete from `tiki_blog_posts_images` where `imgId`=?";

		$this->query($query, array($imgId));
	}

	/**
	 * replace_blog Change the attributes of a blog
	 *
	 * @param string $title
	 * @param swtring $description
	 * @param string $user
	 * @param char[1] $public
	 * @param int $maxPosts
	 * @param int $blogId
	 * @param string $heading
	 * @param char[1] $use_title
	 * @param char[1] $use_find
	 * @param char[1] $allow_comments
	 * @param char[1] $show_avatar
	 * @access public
	 * @return int blogId
	 */
	function replace_blog($title, $description, $user, $public, $maxPosts, $blogId, $heading, $use_title, $use_find, $allow_comments, $show_avatar) {
		global $prefs;
		if ($blogId) {
			$query = "update `tiki_blogs` set `title`=? ,`description`=?,`user`=?,`public`=?,`lastModif`=?,`maxPosts`=?,`heading`=?,`use_title`=?,`use_find`=?,`allow_comments`=?,`show_avatar`=? where `blogId`=?";

			$result = $this->query($query, array($title, $description, $user, $public, $this->now, $maxPosts, $heading, $use_title, $use_find, $allow_comments, $show_avatar, $blogId));
		} else {
			$query = "insert into `tiki_blogs`(`created`,`lastModif`,`title`,`description`,`user`,`public`,`posts`,`maxPosts`,`hits`,`heading`,`use_title`,`use_find`,`allow_comments`,`show_avatar`) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$result = $this->query($query, array((int) $this->now, (int) $this->now, $title, $description, $user, $public, 0, (int) $maxPosts, 0, $heading, $use_title, $use_find, $allow_comments, $show_avatar));
			$query2 = "select max(`blogId`) from `tiki_blogs` where `lastModif`=?";
			$blogId = $this->getOne($query2, array((int) $this->now));

			if ($prefs['feature_score'] == 'y') {
				$this->score_event($user, 'blog_new');
			}
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('blogs', $blogId);
		}
		return $blogId;
	}

	/**
	 * list_blog_posts Returns al the posts for the blog $blogId
	 *
	 * @param int $blogId
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param string $date_min
	 * @param string $date_max
	 * @param string $approved
	 * @access public
	 * @return array posts
	 */
	function list_blog_posts($blogId = 0, $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $date_min = '', $date_max = '', $approved = 'y') {
		global $tiki_p_admin_comments;

		$mid = array();
		$bindvars = array();

		if ( $blogId > 0 ) {
			$mid[] = "`blogId`=?";
			$bindvars[] = (int)$blogId;
		}

		if ( $find ) {
			$findesc = '%' . $find . '%';
			$mid[] = "(`data` like ? or `title` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		if ( $date_min ) {
			$mid[] = "`created`>=?";
			$bindvars[] = (int)$date_min;
		}
		if ( $date_max ) {
			$mid[] = "`created`<=?";
			$bindvars[] = (int)$date_max;
		}
		$mid = empty($mid) ? '' : 'where ' . implode(' and ', $mid);

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		$cant_com_query = "select count(*) from `tiki_comments` where `object`=? and `objectType` = 'post'";
		if ( $tiki_p_admin_comments != 'y' ) {
			$cant_com_query .= ' and `approved`=?';
		} else {
			$approved = NULL;
		}

		while ($res = $result->fetchRow()) {
			$cant_com_vars = array((int)$res['postId']);
			if ( $approved !== NULL ) $cant_com_vars[] = $approved;
			$cant_com = $this->getOne($cant_com_query, $cant_com_vars);
			$res["comments"] = $cant_com;
			$res['pages'] = $this->get_number_of_pages($res['data']);
			$res['avatar'] = $this->get_user_avatar($res['user']);		
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;

		return $retval;
	}

	/**
	 * list_blog_post_comments List all the comments for a post
	 *
	 * @param string $approved
	 * @param int $maxRecords
	 * @access public
	 * @return void
	 */
	function list_blog_post_comments($approved = 'y', $maxRecords = -1) {
		global $user;

		$query = "SELECT b.`title`, b.`postId`, c.`threadId`, c.`title` as commentTitle, `commentDate`, `userName` FROM `tiki_comments` c, `tiki_blog_posts` b WHERE `objectType`='post' AND b.`postId`=c.`object`";

		$bindvars = array();
		$globalperms = Perms::get();
		if ( !$globalperms->admin_comment ) {
			$query .= ' AND `approved`=?';
			$bindvars[] = $approved;
		} else {
			$approved = NULL;
		}

		$query .= " ORDER BY `commentDate` desc";
		$result = $this->query($query, $bindvars, $maxRecords);

		$ret = array();
		while ( $res = $result->fetchRow() ) {
			if ( $this->user_has_perm_on_object($user, $res['postId'], 'post', 'tiki_p_read_blog') ) {

				/// check if the blog post is marked private
				$priv = ( $res2 = $this->get_post($res['postId']) ) ? $res2['priv'] : '';

				if ( $priv != 'y' || ( $user && $user == $res2["user"] ) || $tiki_p_blog_admin == 'y' ) {
					$ret[] = $res;
				}
			}
		}

		return array('data' => $ret, 'cant' => count($ret));
	}

	/**
	 * list_all_blog_posts Returns all the posts filtered by $date and $find
	 *
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param string $date
	 * @access public
	 * @return void
	 */
	function list_all_blog_posts($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $date = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`data` like ?) ";
			$bindvars = array($findesc);
		} else {
			$mid = "";
			$bindvars = array();
		}

		if ($date) {
			$bindvars[] = $date;
			if ($mid) {
				$mid .= " and `created`<=? ";
			} else {
				$mid .= " where `created`<=? ";
			}
		}

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		$result = Perms::filter( array( 'type' => 'blog' ), 'object', $result, array( 'object' => 'blogId' ), 'read_blog' );

		global $prefs;
		foreach( $result as $res ) {
			$query2 = "select `title` from `tiki_blogs` where `blogId`=?";
			$title = $this->getOne($query2, array($res["blogId"]));
			$res["blogtitle"] = $title;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	 * blog_post Stores a blog post
	 *
	 * @param int $blogId
	 * @param string $data
	 * @param string $user
	 * @param string $title
	 * @param string $contributions
	 * @param string $priv
	 * @access public
	 * @return int postId
	 */
	function blog_post($blogId, $data, $user, $title = '', $contributions = '', $priv = 'n') {
		// update tiki_blogs and call activity functions
		global $smarty, $tikilib, $prefs, $reportslib;

		$data = strip_tags($data, '<a><b><i><h1><h2><h3><h4><h5><h6><ul><li><ol><br><p><table><tr><td><img><pre>');
		$query = "insert into `tiki_blog_posts`(`blogId`,`data`,`created`,`user`,`title`,`priv`) values(?,?,?,?,?,?)";
		$result = $this->query($query, array((int) $blogId, $data, (int) $this->now, $user, $title, $priv));
		$query = "select max(`postId`) from `tiki_blog_posts` where `created`=? and `user`=?";
		$id = $this->getOne($query, array((int) $this->now, $user));
		$query = "update `tiki_blogs` set `lastModif`=?,`posts`=`posts`+1 where `blogId`=?";
		$result = $this->query($query, array((int) $this->now, (int) $blogId));
		$this->add_blog_activity($blogId);

		if ($prefs['feature_user_watches'] == 'y') {
			$nots = $this->get_event_watches('blog_post', $blogId);
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}

			if ($prefs['feature_daily_report_watches'] == 'y') {
				$query = "select `title` from `tiki_blogs` where `blogId`=?";
				$blogTitle = $this->getOne($query, array((int)$blogId));
				$reportslib->makeReportCache($nots, array("event"=>'blog_post', "blogId"=>$blogId, "blogTitle"=>$blogTitle, "postId"=>$id, "user"=>$user));
			}
			
			if (count($nots)) {
				include_once("lib/notifications/notificationemaillib.php");
				$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
				$query = "select `title` from `tiki_blogs` where `blogId`=?";
				$blogTitle = $this->getOne($query, array((int)$blogId));
				$smarty->assign('mail_title', $blogTitle);
				$smarty->assign('mail_post_title', $title);
				$smarty->assign('mail_blogid', $blogId);
				$smarty->assign('mail_postid', $id);
				$smarty->assign('mail_date', $this->now);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_data', $data);

				if ($prefs['feature_contribution'] == 'y' && !empty($contributions)) {
					global $contributionlib; include_once('lib/contribution/contributionlib.php');
					$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
				}
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $tikilib->httpPrefix(). $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1)
					unset ($parts[count($parts) - 1]);
				$smarty->assign('mail_machine_raw', $tikilib->httpPrefix(). implode('/', $parts));
				sendEmailNotification($nots, "watch", "user_watch_blog_post_subject.tpl", $_SERVER["SERVER_NAME"], "user_watch_blog_post.tpl");
			}
		}

		if ($prefs['feature_score'] == 'y') {
			$this->score_event($user, 'blog_post');
		}

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Posted', $blogId, 'blog', "blogId=$blogId&amp;postId=$id&amp;add=" . strlen($data) . "#postId$id", '', '', '', '', $contributions);
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('blog_posts', $id);
		}

		return $id;
	}

	/**
	 * remove_blog Removes a blog and all the posts of a blog
	 *
	 * @param int $blogId
	 * @access public
	 * @return boolean unconditionnal true
	 */
	function remove_blog($blogId) {
		$query = "delete from `tiki_blogs` where `blogId`=?";

		$result = $this->query($query, array((int) $blogId));
		$query = "delete from `tiki_blog_posts` where `blogId`=?";
		$result = $this->query($query, array((int) $blogId));
		$this->remove_object('blog', $blogId);

		return true;
	}

	/**
	 * remove_post Removes a post identified by $postId
	 *
	 * @param int $postId
	 * @access public
	 * @return boolean inconditionnal true
	 */
	function remove_post($postId) {
		$query = "select `blogId`, `data` from `tiki_blog_posts` where `postId`=?";
		$result = $this->query($query, array((int) $postId));
		if ($res = $result->fetchRow()) {
			$blogId = $res['blogId'];
		} else {
			$blogId = 0;
		}

		global $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$param = "blogId=$blogId&amp;postId=$postId";
			if ($blogId)
				$param .= "&amp;del=" . strlen($res['data']);
			$logslib->add_action('Removed', $blogId, 'blog', $param);
		}
		if ($blogId) {
			$query = "delete from `tiki_blog_posts` where `postId`=?";

			$result = $this->query($query, array((int) $postId));
			$query = "update `tiki_blogs` set `posts`=`posts`-1 where `blogId`=?";
			$result = $this->query($query, array((int) $blogId));
		}

		$query = "delete from `tiki_blog_posts_images` where `postId`=?";
		$this->query($query, array((int) $postId));

		$this->remove_object('blog post', $postId);

		return true;
	}

	/**
	 * get_post Returns the post identfied by $postId
	 *		Returns false if the post does not exist
	 *
	 * @param mixed $postId
	 * @access public
	 * @return The post
	 */
	function get_post($postId) {
		$query = "select * from `tiki_blog_posts` where `postId`=?";
		$result = $this->query($query, array((int) $postId));
		if ($result->numRows()) {
			$res = $result->fetchRow();
		} else {
			return false;
		}
		return $res;
	}

	/**
	 * Updates a blog post
	 *
	 * @param int $postId
	 * @param int $blogId
	 * @param string $data
	 * @param string $user
	 * @param string $title
	 * @param string $contributions
	 * @param string $old_data
	 * @param string $priv
	 * @access public
	 * @return void
	 */
	function update_post($postId, $blogId, $data, $user, $title = '', $contributions = '', $old_data = '', $priv='n') {
		global $prefs;
		$query = "update `tiki_blog_posts` set `blogId`=?,`data`=?,`user`=?,`title`=?, `priv`=? where `postId`=?";
		$result = $this->query($query, array($blogId, $data, $user, $title, $priv, $postId));
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Updated', $blogId, 'blog', "blogId=$blogId&amp;postId=$postId#postId$postId", '', '', '', '', $contributions);
		}
		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('blog_posts', $postId);
		}
	}

	/**
	 * list_user_posts Returns all the posts from a user
	 *
	 * @param string $user login name of the user
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @access public
	 * @return void
	 */
	function list_user_posts($user, $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `user`=? and (`data` like ?) ";
			$bindvars = array($user, $findesc);
		} else {
			$mid = ' where `user`=? ';
			$bindvars = array($user);
		}

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	 * add_blog_activity
	 *
	 * @param mixed $blogId
	 * @access public
	 * @return void
	 */
	function add_blog_activity($blogId) {

		//Caclulate activity, update tiki_blogs and purge activity table
		$today = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d"), $this->date_format("%Y"));

		$day0 = $today - (24 * 60 * 60);
		$day1 = $today - (2 * 24 * 60 * 60);
		$day2 = $today - (3 * 24 * 60 * 60);
		// Purge old activity
		$query = "delete from `tiki_blog_activity` where `day`<?";
		$result = $this->query($query, array((int) $day2));
		
		// Register new activity
		$query = "select count(*) from `tiki_blog_activity` where `blogId`=? and `day`=?";
		$result = $this->getOne($query, array((int) $blogId, (int)$today));

		if ($result) {
			$query = "update `tiki_blog_activity` set `posts`=`posts`+1 where `blogId`=? and `day`=?";
		} else {
			$query = "insert into `tiki_blog_activity`(`blogId`,`day`,`posts`) values(?,?,1)";
		}

		$result = $this->query($query, array((int) $blogId, (int) $today));
		// Calculate activity
		$query = "select `posts` from `tiki_blog_activity` where `blogId`=? and `day`=?";
		$vtoday = $this->getOne($query, array((int) $blogId, (int) $today));
		$day0 = $this->getOne($query, array((int) $blogId, (int) $day0));
		$day1 = $this->getOne($query, array((int) $blogId, (int) $day1));
		$day2 = $this->getOne($query, array((int) $blogId, (int) $day2));
		$activity = (2 * $vtoday) + ($day0)+(0.5 * $day1) + (0.25 * $day2);
		// Update tiki_blogs with activity information
		$query = "update `tiki_blogs` set `activity`=? where `blogId`=?";
		$result = $this->query($query, array($activity, (int) $blogId));
	}
	
	/**
	 * Returns the title of the blog "blogId"
	 *
	 * @param int $blogId
	 * @access public
	 * @return string the title of the blog
	 */
	function get_title($blogId) {
		$query = 'select `title` from `tiki_blogs` where `blogId`=?';
		return $this->getOne($query, array((int)$blogId));
	}
}
$bloglib = new BlogLib;
