<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
include_once('lib/reportslib.php');

/**
 * Class that handles all blog operations
 *
 * @uses TikiDb_Bridge
 * @package
 * @version
 * @license LGPL. See licence.txt for more details
 */
class BlogLib extends TikiDb_Bridge
{
	/**
	 * List all blogs
	 *
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string @sort_mode
	 * @param string $find
	 * @param string $ref
	 * @param string $with
	 *
	 * @return array
	 */
	function list_blogs($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $ref='', $with = '')
	{
		global $tikilib, $categlib;
		if (!$categlib) require_once 'lib/categories/categlib.php';
		$bindvars = array();
		$join = '';
		$where = '';

		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin($jail, 'blog', '`tiki_blogs`.`blogId`', $join, $where, $bindvars);
		}	

		if ($find) {
			$findesc = '%' . $find . '%';
			$where .= ' and (`tiki_blogs`.`title` like ? or `tiki_blogs`.`description` like ?) ';
			$bindvars = array_merge($bindvars, array($findesc, $findesc));
		}
		if (isset($with['showlastpost'])) {
			$query = "SELECT tb.*, tbp.`postId`, tbp.`created` as postCreated, tbp.`user` as postUser, tbp.`title` as postTitle, tbp.`data` as postData FROM `tiki_blogs` tb, `tiki_blog_posts` tbp $join where tb.`blogId` = tbp.`blogId` and tbp.`created` = (select max(`created`) from `tiki_blog_posts` tbp2 where tbp2.`blogId`=tb.`blogId` order by `created` desc) $where order by tb.".$this->convertSortMode($sort_mode);
		} else {
			$query = "select * from `tiki_blogs` $join WHERE 1=1 $where order by `tiki_blogs`." . $this->convertSortMode($sort_mode); 
		}
		$result = $this->fetchAll($query, $bindvars);

		$ret = array();
		$cant = 0;
		$nb = 0;
		$i = 0;
		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			global $user;
			if ($objperm = $tikilib->get_perm_object($res['blogId'], 'blog', '', false)) {
				if ( $objperm['tiki_p_read_blog'] == 'y' || ($ref == 'post' && $objperm['tiki_p_blog_post_view_ref'] == 'y') || ($ref == 'blog' && $objperm['tiki_p_blog_view_ref'] == 'y')) {
					++$cant;
					if ($maxRecords == - 1 || ($i >= $offset && $nb < $maxRecords)) {
						$ret[] = $res;
						++$nb;
					}
					++$i;
				}
			}
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}


	/**
	 * Return all blog information
	 *
	 * @param int $blogId
	 * @return array
	 */
	function get_blog($blogId)
	{
		global $tikilib, $prefs, $user, $categlib; if (!$categlib) require_once 'lib/categories/categlib.php'; 

		$bindvars = array();

		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin($jail, 'blog', '`tiki_blogs`.`blogId`', $join, $where, $bindvars);
		} else {
			$join = '';
			$where = '';
		}
		array_push( $bindvars, $blogId );
		if (!empty($where)) $where = '1=1 '.$where.' AND ';
		$query = "SELECT * FROM `tiki_blogs` $join WHERE $where `blogId`=?";
		$result = $this->query($query, $bindvars);
		if ($result->numRows()) {
			$res = $result->fetchRow();
		} else {
			return false;
		}

		if ($prefs['feature_score'] == 'y' && $user != $res['user']) {
			$tikilib->score_event($user, 'blog_read', $blogId);
			$tikilib->score_event($res['user'], 'blog_is_read', "$user:$blogId");
		}

		return $res;
	}

	/**
	 * Return a blog by its title
	 *
	 * @param string $blogTitle
	 * @return array or false if no blog is found
	 */
	function get_blog_by_title($blogTitle)
	{
		global $prefs, $user;

	 	// Avoiding select by name so as to avoid SQL injection problems.
		$query = "select `title`, `blogId` from `tiki_blogs`";
		$result = $this->fetchAll($query);
		if ( !empty($result) ) {
			foreach ( $result as $res ) {
				if( strtolower($res['title']) == strtolower($blogTitle) ) {
					return $this->get_blog($res['blogId']);
				}
			}
		}

		return false;
	}

	/**
	 * Returns an array of blogs that belong to the user with the given name,
	 * or which are public, if $include_public is set to true.
	 * A blog is represented by an array like a tiki_blogs record.
	 *
	 * @param string $user
	 * @param bool $include_public wheter or include public blogs (that belongs to other users)
	 * @return array
	 */
	function list_user_blogs($user, $include_public = false)
	{
		global $tikilib;

		$query = "select * from `tiki_blogs` where `user`=? ";
		$bindvars=array($user);
		if ($include_public) {
			$query .= " or `public`=?";
			$bindvars[]='y';
		}
		$query .= "order by `title` asc";
		$result = $this->fetchAll($query,$bindvars);
		$ret = array();

		//FIXME Perm::filter ?
		foreach ( $result as $res ) {
			if ($tikilib->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$ret[] = $res;
			}
		}
		return $ret;
	}

	/**
	 * Return a list of blogs that the user has permission to post
	 *
	 * @return array
	 */
	function list_blogs_user_can_post()
	{
		global $tikilib, $tiki_p_blog_admin, $user;
		$query = "select * from `tiki_blogs` order by `title` asc";
		$result = $this->fetchAll($query);
		$ret = array();

		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			if( (!empty($user) and $user == $res['user']) || $tiki_p_blog_admin == 'y' || $tikilib->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_blog_admin') || ($res['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_blog_post'))) 
				$ret[] = $res;
		}
		return $ret;
	}

	/**
	 * List all posts
	 *
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param int $filterByBlogId
	 * @param string $author
	 * @param string $ref
	 * @param int $date_min
	 * @param int $data_max
	 * @return array
	 */
	function list_posts($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $filterByBlogId = -1, $author='', $ref='', $date_min = 0, $date_max = 0)
	{
		global $tikilib;

		$authorized_blogs = $this->list_blogs(0, -1, 'created_desc', '', $ref);
		$permit_blogs = array();
		for ($i = 0; $i < $authorized_blogs["cant"] ; $i++) {
			$permit_blogs[] = $authorized_blogs["data"][$i]['blogId'];
		}

		if ($filterByBlogId >= 0) {
			// get posts for a given blogId:
			$mid = " where ( `blogId` = ? ) ";
			$bindvars = array($filterByBlogId);
		} else {
			// get posts from all blogs
			$mid = '';
			$bindvars = array();
		}

		if ($find) {
			$findesc = '%' . $find . '%';
			if ($mid == "") {
				$mid = " where ";
			} else {
				$mid .= " and ";
			}
			$mid .= " ( `data` like ? ) ";
			$bindvars[] = $findesc;
		}
		if ($date_min !== 0 || $date_max !== 0) {
			if ( $date_max <= 0 ) {
				// show articles published today
				$date_max = $tikilib->now;
			}
			if ($mid == '') {
				$mid = ' where ';
			} else {
				$mid .= ' and ';
			}
			$mid .= '(`created`>=? and `created`<=?)';
			$bindvars[] = $date_min;
			$bindvars[] = $date_max;
		}
		if (!empty($author)) {
			if ($mid == '') {
				$mid = ' where ';
			} else {
				$mid .= ' and ';
			}
			$mid .= 'user =?';
			$bindvars[] = $author;
		}

		$query = "select * from `tiki_blog_posts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` $mid";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		foreach ( $result as $res ) {
			$blogId = $res["blogId"];

			if ( ! in_array($blogId, $permit_blogs) ) {
				continue;
			}
			$query = "select `title` from `tiki_blogs` where `blogId`=?";
			$cant_com = $this->getOne("select count(*) from
					`tiki_comments` where `object`=? and `objectType` = ?",
					array((string) $res["postId"],'blog'));
			$res["comments"] = $cant_com;
			$res["blogTitle"] = $this->getOne($query,array((int)$blogId));
			$res["size"] = strlen($res["data"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	 * get_number_of_pages Returns the number of pages
	 *
	 * @param string $data
	 * @access public
	 * @return int number of pages
	 */
	function get_number_of_pages($data)
	{
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
	function get_page($data, $i)
	{
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
	function add_blog_hit($blogId)
	{
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_blogs` set `hits` = `hits`+1 where `blogId`=?";
			$result = $this->query($query, array((int) $blogId));
		}
		return true;
	}

	/**
	 * get_post_image Returns the image $imgId
	 *
	 * @param mixed $imgId
	 * @access public
	 * @return array all fields that are associated with an image in tiki_blog_post_images database table
	 */
	function get_post_image($imgId)
	{
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
	function get_post_images($postId)
	{
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
	function remove_post_image($imgId)
	{
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
	 * @param char[1] $use_title_in_post
	 * @param char[1] $use_description
	 * @param char[1] $use_breadcrumbs
	 * @param char[1] $use_author
	 * @param char[1] $use_find
	 * @param char[1] $allow_comments
	 * @param char[1] $show_avatar
	 * @param string $post_heading
	 * @param char[1] $show_related display related content on the bottom of each post
	 * @param int $related_max control the maximum number of related posts displayed per post
	 * @param int $use_excerpt use a post excerpt instead of the main content when listing posts of a blog
	 * @param int $created if 0 use $tikilib->now
	 * @param int $lastModif if 0 use $tikilib->now
	 * @access public
	 * @return int blogId
	 */
	function replace_blog($title, $description, $user, $public, $maxPosts, $blogId, 
						$heading, $use_title, $use_title_in_post, $use_description, $use_breadcrumbs, 
						$use_author, $add_date, $use_find, $allow_comments, $show_avatar, $alwaysOwner, 
						$post_heading, $show_related, $related_max, $use_excerpt, $created = 0, $lastModif = 0
	)  {
		//TODO: all the display parameters can be one single array parameter
		global $tikilib, $prefs;
		
		if ($lastModif == 0) {
			$lastModif = $tikilib->now;
		}
		
		if ($blogId) {
			$query = "update `tiki_blogs` set `title`=? ,`description`=?,`user`=?,`public`=?,`lastModif`=?,`maxPosts`=?,`heading`=?,`use_title`=?,`use_title_in_post`=?,`use_description`=?,`use_breadcrumbs`=?,`use_author`=?,`add_date`=?,`use_find`=?,`allow_comments`=?,`show_avatar`=?,`always_owner`=?, `post_heading`=?, `show_related`=?, `related_max`=?, `use_excerpt`=? where `blogId`=?";

			$result = $this->query($query, array($title, $description, $user, $public, $lastModif, $maxPosts, $heading, $use_title, $use_title_in_post, $use_description, $use_breadcrumbs, $use_author, $add_date, $use_find, $allow_comments, $show_avatar, $alwaysOwner, $post_heading, $show_related, $related_max, $use_excerpt, $blogId));
			$tikilib->object_post_save( array('type'=>'blog', 'object'=>$blogId), array('content'=>$heading) );
		} else {
			if ($created == 0) {
				$created = $tikilib->now;
			}
			
			$query = "insert into `tiki_blogs`(`created`,`lastModif`,`title`,`description`,`user`,`public`,`posts`,`maxPosts`,`hits`,`heading`,`use_title`,`use_title_in_post`,`use_description`,`use_breadcrumbs`,`use_author`,`add_date`,`use_find`,`allow_comments`,`show_avatar`,`always_owner`,`post_heading`, `show_related`, `related_max`, `use_excerpt`) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$result = $this->query($query, array($created, $lastModif, $title, $description, $user, $public, 0, (int) $maxPosts, 0, $heading, $use_title, $use_title_in_post, $use_description, $use_breadcrumbs, $use_author, $add_date, $use_find, $allow_comments, $show_avatar, $alwaysOwner, $post_heading, $show_related, $related_max, $use_excerpt));
			$query2 = "select max(`blogId`) from `tiki_blogs` where `lastModif`=?";
			$blogId = $this->getOne($query2, array($lastModif));

			if ($prefs['feature_score'] == 'y') {
				$tikilib->score_event($user, 'blog_new');
			}
			$tikilib->object_post_save(array('type'=>'blog', 'object'=>$blogId, 'description'=>$description, 'name'=>$title, 'href'=>"tiki-view_blog.php?blogId=$blogId"), array( 'content' => $heading ));
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('blogs', $blogId);

		return $blogId;
	}

	/**
	 * list_blog_posts Returns all the posts for the blog $blogId
	 *
	 * @param int $blogId
	 * @param bool $allowDrafts
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
	function list_blog_posts($blogId = 0, $allowDrafts = false, $offset = 0, 
							$maxRecords = -1, $sort_mode = 'created_desc', $find = '', 
							$date_min = '', $date_max = '', $approved = 'y'
	)  {
		global $tikilib, $tiki_p_admin_comments, $tiki_p_admin, $tiki_p_blog_admin, $tiki_p_blog_post, $user;
		global $commentslib; require_once('lib/comments/commentslib.php');
		
		if (!is_object($commentslib)) {
			$commentslib = new Comments();
		}

		$mid = array();
		$bindvars = array();

		$ownsblog = 'n';
		if ( $blogId > 0 ) {
			$mid[] = "tbp.`blogId`=?";
			$bindvars[] = (int)$blogId;

			$blog_data = $this->get_blog($blogId);
			if ($user && $user == $blog_data["user"]) {
				$ownsblog = 'y';
			}
		}
		$mid[] = "tbp.blogId = tb.blogId";

		if ( !$allowDrafts ){
			$mid[] = "`priv`!='y'";
		}else{
			// Private posts can be accessed on the following conditions:
			// user has tiki_p_admin or tiki_p_blog_admin or has written the post
			// If blog is configured with 'Allow other user to post in this blog', then also if user has tiki_p_blog_post or is owner of this blog
			if ( ($tiki_p_admin != 'y')
				  and ($tiki_p_blog_admin != 'y')
				  and ( (! isset($blog_data["public"])) || $blog_data["public"] != 'y' || $tiki_p_blog_post != 'y')
				  and ( !isset($blog_data["public"]) || $blog_data["public"] != 'y' || $ownsblog != 'y') ) {
				if ( isset($user) ) {
					$mid[] = "(tbp.`priv`!='y' or tbp.`user`=?)";
					$bindvars[] = "$user";
				} else {
					$mid[] = "tbp.`priv`!='y'";
				}
			}
		}

		if ( $find ) {
			$findesc = '%' . $find . '%';
			$mid[] = "(tbp.`data` like ? or tbp.`title` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		if ( $date_min ) {
			$mid[] = "tbp.`created`>=?";
			$bindvars[] = (int)$date_min;
		}
		if ( $date_max ) {
			$mid[] = "tbp.`created`<=?";
			$bindvars[] = (int)$date_max;
		}

		$mid = empty($mid) ? '' : 'where ' . implode(' and ', $mid);
		$query = "select tbp.*,tb.title as blogTitle from `tiki_blog_posts` as tbp, `tiki_blogs` as tb $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_blog_posts` as tbp, `tiki_blogs` as tb $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["comments"] = $commentslib->count_comments('blog post:' . $res['postId']);
			$res['pages'] = $this->get_number_of_pages($res['data']);
			$res['avatar'] = $tikilib->get_user_avatar($res['user']);

			if (isset($res['excerpt'])) {
				$res['excerpt'] = $tikilib->parse_data($res['excerpt']);
			}

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;

		return $retval;
	}

	/**
	 * list_blog_post_comments List all the comments in posts for all the blogs 
	 *
	 * @param string $approved
	 * @param int $maxRecords
	 * @access public
	 * @return void
	 */
	function list_blog_post_comments($approved = 'y', $maxRecords = -1)
	{
		global $user, $tikilib, $userlib, $tiki_p_admin, $tiki_p_blog_admin, $tiki_p_blog_post;

		// TODO: use commentslib instead of querying database directly
		$query = "SELECT b.`title`, b.`postId`, b.`priv`, blog.`user`, blog.`public`, c.`threadId`, c.`title` as commentTitle, c.`website`, `commentDate`, `userName` FROM `tiki_comments` c, `tiki_blog_posts` b, `tiki_blogs` blog WHERE `objectType`='post' AND b.`postId`=c.`object` AND blog.`blogId`=b.`blogId`";

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
			if ( $tikilib->user_has_perm_on_object($user, $res['postId'], 'post', 'tiki_p_read_blog') || $tikilib->user_has_perm_on_object($user, $res['postId'], 'post', 'tiki_p_blog_post_view_ref')) {

				// Private posts can be accessed on the following conditions:
				// user has tiki_p_admin or tiki_p_blog_admin or has written the post
				// If blog is configured with 'Allow other user to post in this blog', then also if user has tiki_p_blog_post or is owner of this blog
				if ( ($res['priv'] != 'y')
						or ($tiki_p_admin == 'y' )
						or ($tiki_p_blog_admin == 'y')
						or ( ($res["public"] == 'y') && ($user && $user == $res["user"]) )
						or ( ($res["public"] == 'y') && ($tiki_p_blog_post == 'y') ) ) {
					$ret[] = $res;
				}
			}
		}

		// just to distinct between user and anonymous (should be done in commentslib and not here)
		foreach ($ret as $key => $comment) {
			if (!$userlib->user_exists($comment['userName'])) {
				$ret[$key]['anonymous_name'] = $comment['userName'];
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
	function list_all_blog_posts($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $date = '')
	{

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

		$result = Perms::filter( array( 'type' => 'blog' ), 'object', $result, array( 'object' => 'blogId' ), array('read_blog', 'blog_view_ref') );

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
	 * @param string $excerpt
	 * @param string $user
	 * @param string $title
	 * @param string $contributions
	 * @param string $priv
	 * @param bool $is_wysiwyg
	 * @access public
	 * @return int postId
	 */
	function blog_post($blogId, $data, $excerpt, $user, $title = '', $contributions = '', $priv = 'n', $created = 0, $is_wysiwyg=FALSE)
	{
		// update tiki_blogs and call activity functions
		global $smarty, $tikilib, $prefs, $reportslib;

		$wysiwyg=$is_wysiwyg==TRUE?'y':'n';
		if(!$created) {
			$created = $tikilib->now;	
		}
		
		$data = strip_tags($data, '<a><b><i><h1><h2><h3><h4><h5><h6><ul><li><ol><br><p><table><tr><td><img><pre><strong>');
		$query = "insert into `tiki_blog_posts`(`blogId`,`data`,`excerpt`,`created`,`user`,`title`,`priv`,`wysiwyg`) values(?,?,?,?,?,?,?,?)";
		$result = $this->query($query, array((int) $blogId, $data, $excerpt, (int) $created, $user, $title, $priv, $wysiwyg));
		$query = "select max(`postId`) from `tiki_blog_posts` where `created`=? and `user`=?";
		$id = $this->getOne($query, array((int) $created, $user));
		$query = "update `tiki_blogs` set `lastModif`=?,`posts`=`posts`+1 where `blogId`=?";
		$result = $this->query($query, array((int) $created, (int) $blogId));
		$this->add_blog_activity($blogId);

		if ($prefs['feature_user_watches'] == 'y' or $prefs['feature_group_watches'] == 'y' ) {
			$nots = $tikilib->get_event_watches('blog_post', $blogId);
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
				$smarty->assign('mail_date', $tikilib->now);
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
			$tikilib->score_event($user, 'blog_post');
		}

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Posted', $blogId, 'blog', "blogId=$blogId&amp;postId=$id&amp;add=" . strlen($data) . "#postId$id", '', '', '', '', $contributions);
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('blog_posts', $id);

		$tikilib->object_post_save(array('type'=>'blog post', 'object'=>$id, 'description'=>substr($data, 0, 200), 'name'=>$title, 'href'=>"tiki-view_blog_post.php?postId=$id"), array('content' => $data));
		return $id;
	}

	/**
	 * remove_blog Removes a blog and all the posts of a blog
	 *
	 * @param int $blogId
	 * @access public
	 * @return boolean unconditionnal true
	 */
	function remove_blog($blogId)
	{
		global $tikilib;

		$query = "delete from `tiki_blogs` where `blogId`=?";

		$result = $this->query($query, array((int) $blogId));
		$query = "delete from `tiki_blog_posts` where `blogId`=?";
		$result = $this->query($query, array((int) $blogId));
		$tikilib->remove_object('blog', $blogId);

		return true;
	}

	/**
	 * remove_post Removes a post identified by $postId
	 *
	 * @param int $postId
	 * @access public
	 * @return boolean inconditionnal true
	 */
	function remove_post($postId)
	{
		global $tikilib;
		global $objectlib; require_once('lib/objectlib.php');

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

		/*
		 * TODO: this should be a method in freetaglib or maybe even better $tikilib->remove_object() should
		 * remove the relation between the object and the tags, no?
		 */
		// When a post is deleted, all freetags asociated must also be deleted
		$objectId = $objectlib->get_object_id('blog post', $postId);
		$query = "DELETE FROM `tiki_freetagged_objects` WHERE `objectId` = ?";
		$this->query($query,array((int) $objectId));

		$query = "delete from `tiki_blog_posts_images` where `postId`=?";
		$this->query($query, array((int) $postId));

		$tikilib->remove_object('blog post', $postId);

		return true;
	}

	/**
	 * get_post Returns the post identfied by $postId
	 *		Returns false if the post does not exist
	 *
	 * @param mixed $postId
	 * @param bool $adjacent wheter to return or not adjacent posts
	 * @access public
	 * @return The post
	 */
	function get_post($postId, $adjacent = false)
	{
		global $tikilib;

		$query = "select * from `tiki_blog_posts` where `postId`=?";
		$result = $this->query($query, array((int) $postId));
		if ($result->numRows()) {
			$res = $result->fetchRow();
			$res['avatar'] = $tikilib->get_user_avatar($res['user']);

			if ($adjacent) {
				$res['adjacent'] = $this->_get_adjacent_posts($res['blogId'], $res['created']);
			}
		} else {
			return false;
		}
		return $res;
	}

	/**
	 * Get post related content using $freetaglib->get_similar()
	 *
	 * @param int $postId
	 * @param int $maxResults
	 * @return array
	 */
	function get_related_posts($postId, $maxResults = 5)
	{
		global $freetaglib;
		$related_posts = $freetaglib->get_similar('blog post', $postId, $maxResults);

		// extract 'postId' from href to be able to use {self_link}
		foreach ($related_posts as $key => $post) {
			$related_posts[$key]['postId'] = str_replace('tiki-view_blog_post.php?postId=', '', $post['href']);
		}

		return $related_posts;
	}

	/**
	 * Get adjacent posts (previous and next by created date)
	 *
	 * @param int $blogId which blog the post belongs to
	 * @param int $created when the post was created
	 * @return array
	 */
	function _get_adjacent_posts($blogId, $created)
	{
		$res = array();

		$next_query = 'SELECT postId, title FROM `tiki_blog_posts` WHERE `blogId` = ? AND `created` > ? ORDER BY created ASC';
		$result = $this->fetchAll($next_query, array($blogId, $created), 1);
		$res['next'] = !empty($result[0]) ? $result[0] : null;

		$prev_query = 'SELECT postId, title FROM `tiki_blog_posts` WHERE `blogId` = ? AND `created` < ? ORDER BY created DESC';
		$result = $this->fetchAll($prev_query, array($blogId, $created), 1);
		$res['prev'] = !empty($result[0]) ? $result[0] : null;

		return $res;
	}

	/**
	 * Updates a blog post
	 *
	 * @param int $postId
	 * @param int $blogId
	 * @param string $data
	 * @param string $excerpt
	 * @param string $user
	 * @param string $title
	 * @param string $contributions
	 * @param string $priv
	 * @param bool $is_wysiwyg
	 * @access public
	 * @return void
	 */
	function update_post($postId, $blogId, $data, $excerpt, $user, $title = '', 
						$contributions = '', $priv='n', $created = 0, $is_wysiwyg=FALSE
	)  {
		global $tikilib, $prefs;

		$wysiwyg=$is_wysiwyg==TRUE?'y':'n';
		if ($prefs['feature_blog_edit_publish_date'] == 'y') {
			if(!$created) {
				$created = $tikilib->now;	
			}
			$query = "update `tiki_blog_posts` set `blogId`=?,`data`=?,`excerpt`=?,`created`=?,`user`=?,`title`=?, `priv`=?, `wysiwyg`=? where `postId`=?";
			$result = $this->query($query, array($blogId, $data, $excerpt, $created,$user, $title, $priv, $wysiwyg, $postId));
		} else {
			$query = "update `tiki_blog_posts` set `blogId`=?,`data`=?,`excerpt`=?,`user`=?,`title`=?, `priv`=?, `wysiwyg`=? where `postId`=?";
			$result = $this->query($query, array($blogId, $data, $excerpt, $user, $title, $priv, $wysiwyg, $postId));
		}
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Updated', $blogId, 'blog', "blogId=$blogId&amp;postId=$postId#postId$postId", '', '', '', '', $contributions);
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('blog_posts', $postId);

		$tikilib->object_post_save(array('type' => 'blog post', 'object' => $postId), array('content' => $data));
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
	function list_user_posts($user, $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '')
	{

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
	function add_blog_activity($blogId)
	{
		global $tikilib;

		//Caclulate activity, update tiki_blogs and purge activity table
		$today = $tikilib->make_time(0, 0, 0, $tikilib->date_format("%m"), $tikilib->date_format("%d"), $tikilib->date_format("%Y"));

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
	function get_title($blogId)
	{
		$query = 'select `title` from `tiki_blogs` where `blogId`=?';
		return $this->getOne($query, array((int)$blogId));
	}

	/**
	 * Return true if blog exist or false if not
	 *
	 * @param int $blogId
	 * @return bool true or false depending if blog exist or not
	 */
	function blog_exists($blogId)
	{
		$query = 'SELECT `blogId` FROM `tiki_blogs` WHERE `blogId`=?';

		if (is_null($this->getOne($query, array($blogId))))
			return false;
		else
			return true;
	}

	/**
	 * Check if a blog exists
	 *
	 * @param int $blogId
	 * @return bool true or false if blog exists or not
	 */
	function check_blog_exists($blogId)
	{
		global $smarty;

		if (!$this->blog_exists($blogId)) {
			$msg = tra('Blog cannot be found');
			$smarty->assign('msg', $msg);
			$smarty->display('error.tpl');
			die;
		}
	}

	/**
	 * Returns a list of posts that belongs to a particular blog
	 *
	 * @param int $blogId
	 * @return array list of post ids
	 */
	function get_blog_posts_ids($blogId)
	{
		$query = 'SELECT `postId` FROM `tiki_blog_posts` WHERE `blogId`=?';
		$result = $this->fetchMap($query, array($blogId));

		return array_keys($result);
	}
}

global $bloglib;
$bloglib = new BlogLib;
