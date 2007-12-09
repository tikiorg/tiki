<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class RankLib extends TikiLib {
	function RankLib($db) {
		$this->TikiLib($db);
	}

	function wiki_ranking_top_pages($limit, $categ=array()) {
		global $user, $prefs;
		
		$bindvals = array();
		$mid = '';
		if ($categ) {
			$mid .= " INNER JOIN (`tiki_objects` as tob, `tiki_category_objects` as tco) ON (tp.`pageName` = tob.`itemId` and tob.`objectId` = tco.`catObjectId`) WHERE tob.`type` = 'wiki page' AND (tco.`categId` = ?";
			$bindvals[] = $categ[0]; 	
			for ($i = 1; $i < count($categ); $i++) {
				$mid .= " OR tco.`categId` = " . $categ[$i];
			}
			$mid .= ")";
		}
		
		if ($prefs['feature_wikiapproval'] == 'y') {
			if ($mid) {
				$mid .= " AND tp.`pageName` not like ?";
			} else {
				$mid .= " WHERE tp.`pageName` not like ?";	
			}
			$bindvals[] = $prefs['wikiapproval_prefix'] . '%';
		}
		
		$query = "select tp.`pageName`, tp.`hits` from `tiki_pages` tp $mid order by `hits` desc";

		$result = $this->query($query, $bindvals);
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user,$res['pageName'], 'wiki page', 'tiki_p_view')) {
				$aux['name'] = $res['pageName'];
				$aux['hits'] = $res['hits'];
				$aux['href'] = 'tiki-index.php?page=' . urlencode($res['pageName']);
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top pages");
		$retval["y"] = tra("Hits");
		$retval["type"] = "nb";
		return $retval;
	}

	function wiki_ranking_top_pagerank($limit, $categ=array()) {
		global $user, $prefs;
		$this->pageRank();

		$bindvals = array();
		$mid = '';
		if ($categ) {
			$mid .= " INNER JOIN (`tiki_objects` as tob, `tiki_category_objects` as tco) ON (tp.`pageName` = tob.`itemId` and tob.`objectId` = tco.`catObjectId`) WHERE tob.`type` = 'wiki page' AND (tco.`categId` = ?";
			$bindvals[] = $categ[0]; 	
			for ($i = 1; $i < count($categ); $i++) {
				$mid .= " OR tco.`categId` = " . $categ[$i];
			}
			$mid .= ")";
		}
		if ($prefs['feature_wikiapproval'] == 'y') {
			if ($mid) {
				$mid .= " AND tp.`pageName` not like ?";
			} else {
				$mid .= " WHERE tp.`pageName` not like ?";	
			}
			$bindvals[] = $prefs['wikiapproval_prefix'] . '%';
		}
		
		$query = "select tp.`pageName`, tp.`pageRank` from `tiki_pages` tp $mid order by `pageRank` desc";
		
		$result = $this->query($query, $bindvals);
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user,$res['pageName'], 'wiki page', 'tiki_p_view')) {
				$aux['name'] = $res['pageName'];
				$aux['hits'] = $res['pageRank'];
				$aux['href'] = 'tiki-index.php?page=' . urlencode($res['pageName']);
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Most relevant pages");
		$retval["y"] = tra("Relevance");
		$retval["type"] = "nb";
		return $retval;
	}

	function wiki_ranking_last_pages($limit, $categ=array()) {
		global $user, $prefs;
		
		$bindvals = array();
		$mid = '';
		if ($categ) {
			$mid .= " INNER JOIN (`tiki_objects` as tob, `tiki_category_objects` as tco) ON (tp.`pageName` = tob.`itemId` and tob.`objectId` = tco.`catObjectId`) WHERE tob.`type` = 'wiki page' AND (tco.`categId` = ?";
			$bindvals[] = $categ[0]; 	
			for ($i = 1; $i < count($categ); $i++) {
				$mid .= " OR tco.`categId` = " . $categ[$i];
			}
			$mid .= ")";
		}
		if ($prefs['feature_wikiapproval'] == 'y') {
			if ($mid) {
				$mid .= " AND tp.`pageName` not like ?";
			} else {
				$mid .= " WHERE tp.`pageName` not like ?";	
			}
			$bindvals[] = $prefs['wikiapproval_prefix'] . '%';
		}
		
		$query = "select tp.`pageName`,tp.`lastModif`,tp.`hits` from `tiki_pages` tp $mid order by `lastModif` desc";

		$result = $this->query($query, $bindvals);
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user,$res['pageName'], 'wiki page', 'tiki_p_view')) {
				$aux['name'] = $res['pageName'];
				$aux['hits'] = $res['lastModif'];
				$aux['href'] = 'tiki-index.php?page=' . urlencode($res['pageName']);
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki last pages");
		$retval["y"] = tra("Modified");
		$retval["type"] = "date";
		return $retval;
	}

	function forums_ranking_last_topics($limit, $forumId='') {
		global $user;
		if (is_array($forumId)) {
			$bindvars = $forumId;
			$mid = ' and tf.`forumId` in ('.implode(',',array_fill(0,count($forumId),'?')).')';
		} elseif (!empty($forumId)) {
			$bindvars=array((int) $forumId);
		    $mid = ' and tf.`forumId`=?';
		} else {
			$bindvars = array();
			$mid = '';
		}
		$query = "select * from
		`tiki_comments`,`tiki_forums` tf where
		`object`=".$this->sql_cast("`forumId`","string")." and `objectType` = 'forum' and
		`parentId`=0 $mid order by `commentDate` desc";

		$result = $this->query($query, $bindvars);
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
                  if ($this->user_has_perm_on_object($user, $res['forumId'], 'forum', 'tiki_p_forum_read')) {
				$aux['name'] = $res['name'] . ': ' . $res['title'];
				$aux['hits'] = $res['commentDate'];
				$aux['href'] = 'tiki-view_forum_thread.php?forumId=' . $res['forumId'] . '&amp;comments_parentId=' . $res['threadId'];
				$aux['date'] = $res['commentDate'];
				$aux['user'] = $res['userName'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Forums last topics");
		$retval["y"] = tra("Topic date");
		$retval["type"] = "date";
		return $retval;
	}

	function forums_ranking_last_posts($limit) {
		global $user;
		$query = "select * from
		`tiki_comments`,`tiki_forums` where
		`object`=".$this->sql_cast("`forumId`","string")." and `objectType` = 'forum'
		order by `commentDate` desc"; 
//todo(tibi) this will save a lot of time but after this step the not permitted forums will be filtered out. so you might end up with less than limit.
	//		$result = $this->query($query,array(),$limit);

		$result = $this->query($query,array());
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
                 if ($this->user_has_perm_on_object($user, $res['forumId'], 'forum', 'tiki_p_forum_read')) {
				$aux['name'] = $res['name'] . ': ' . $res['title'];
				$aux['hits'] = $this->get_long_datetime($res['commentDate']);
				$tmp = $res['parentId'];
				if ($tmp == 0) $tmp = $res['threadId'];
				$aux['href'] = 'tiki-view_forum_thread.php?forumId=' . $res['forumId'] . '&amp;comments_parentId=' . $tmp;
				$aux['date'] = $res['commentDate'];
				$aux['user'] = $res['userName'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Forums last posts");
		$retval["y"] = tra("Topic date");
		$retval["type"] = "date";
		return $retval;
	}

	function forums_ranking_most_read_topics($limit) {
		global $user;
		$query = "select
		tc.`hits`,tc.`title`,tf.`name`,tf.`forumId`,tc.`threadId`,tc.`object`
		from `tiki_comments` tc,`tiki_forums` tf where
		`object`=`forumId` and `objectType` = 'forum' and
		`parentId`=0 order by tc.`hits` desc";

		$result = $this->query($query,array());
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
                 if ($this->user_has_perm_on_object($user, $res['forumId'], 'forum', 'tiki_p_forum_read')) {
				$aux['name'] = $res['name'] . ': ' . $res['title'];
				$aux['hits'] = $res['hits'];
				$aux['href'] = 'tiki-view_forum_thread.php?forumId=' . $res['forumId'] . '&amp;comments_parentId=' . $res['threadId'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Forums most read topics");
		$retval["y"] = tra("Reads");
		$retval["type"] = "nb";
		return $retval;
	}

    function forums_top_posters($qty) {
        $query = "select `user`, `posts` from `tiki_user_postings` order by ".$this->convert_sortmode("posts_desc");
        $result = $this->query($query, array(),$qty);
        $ret = array();

        while ($res = $result->fetchRow()) {
            $aux["name"] = $res["user"];
	    $aux["posts"] = $res["posts"];
	    $ret[] = $aux;
        }

	$retval["data"] = $ret;

        return $retval;
    }


	function forums_ranking_top_topics($limit) {
		global $user;
		$query = "select
		tc.`average`,tc.`title`,tf.`name`,tf.`forumId`,tc.`threadId`,tc.`object`
		from `tiki_comments` tc,`tiki_forums` tf where
		`object`=`forumId` and `objectType` = 'forum' and
		`parentId`=0 order by tc.`average` desc";

		$result = $this->query($query,array());
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user, $res['forumId'], 'forum', 'tiki_p_forum_read')) {
				$aux['name'] = $res['name'] . ': ' . $res['title'];
				$aux['hits'] = $res['average'];
				$aux['href'] = 'tiki-view_forum_thread.php?forumId=' . $res['forumId'] . '&amp;comments_parentId=' . $res['threadId'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Forums best topics");
		$retval["y"] = tra("Score");
		$retval["type"] = "nb";
		return $retval;
	}

	function forums_ranking_most_visited_forums($limit) {
		global $user;
		$query = "select * from `tiki_forums` order by `hits` desc";

		$result = $this->query($query,array());
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user, $res['forumId'], 'forum', 'tiki_p_forum_read')) {
				$aux['name'] = $res['name'];
				$aux['hits'] = $res['hits'];
				$aux['href'] = 'tiki-view_forum.php?forumId=' . $res['forumId'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Forums most visited forums");
		$retval["y"] = tra("Visits");
		$retval["type"] = "nb";
		return $retval;
	}

	function forums_ranking_most_commented_forum($limit) {
		global $user;
		$query = "select * from `tiki_forums` order by `comments` desc";

		$result = $this->query($query,array());
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user, $res['forumId'], 'forum', 'tiki_p_forum_read')) {
				$aux['name'] = $res['name'];
				$aux['hits'] = $res['comments'];
				$aux['href'] = 'tiki-view_forum.php?forumId=' . $res['forumId'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Forums with most posts");
		$retval["y"] = tra("Posts");
		$retval["type"] = "nb";
		return $retval;
	}

	function gal_ranking_top_galleries($limit) {
		global $user;
		$query = "select * from `tiki_galleries` where `visible`=? order by `hits` desc";

		$result = $this->query($query,array('y'));
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$aux['name'] = $res['name'];
				$aux['hits'] = $res['hits'];
				$aux['href'] = 'tiki-browse_gallery.php?galleryId=' . $res['galleryId'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top galleries");
		$retval["y"] = tra("Visits");
		$retval["type"] = "nb";
		return $retval;
	}

	function filegal_ranking_top_galleries($limit) {
		global $user;
		$query = "select * from `tiki_file_galleries` where `visible`=? order by `hits` desc";

		$result = $this->query($query,array('y'),$limit,0);
		$ret = array();
		$count = 0;
		while (($res = $result->fetchRow()) && $count < $limit) {
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$aux['name'] = $res['name'];
				$aux['hits'] = $res['hits'];
				$aux['href'] = 'tiki-list_file_gallery.php?galleryId=' . $res['galleryId'];
				$ret[] = $aux;
				++$count;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top file galleries");
		$retval["y"] = tra("Visits");
		$retval["type"] = "nb";
		return $retval;
	}

	function gal_ranking_top_images($limit) {
		global $user;
		$query = "select `imageId`,`name`,`hits`, `galleryId` from `tiki_images` order by `hits` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$aux["name"] = $res["name"];
				$aux["hits"] = $res["hits"];
				$aux["href"] = 'tiki-browse_image.php?imageId=' . $res["imageId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top images");
		$retval["y"] = tra("Hits");
		$retval["type"] = "nb";
		return $retval;
	}

	function filegal_ranking_top_files($limit) {
		global $user;
		$query = "select `fileId`,`filename`,`downloads`, `galleryId` from `tiki_files` order by `downloads` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$aux["name"] = $res["filename"];
				$aux["hits"] = $res["downloads"];
				$aux["href"] = 'tiki-download_file.php?fileId=' . $res["fileId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top files");
		$retval["y"] = tra("Downloads");
		$retval["type"] = "nb";
		return $retval;
	}

	function gal_ranking_last_images($limit) {
		global $user;
		$query = "select `imageId`,`name`,`created`, `galleryId` from `tiki_images` order by `created` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$aux["name"] = $res["name"];
				$aux["hits"] = $res["created"];
				$aux["href"] = 'tiki-browse_image.php?imageId=' . $res["imageId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki last images");
		$retval["y"] = tra("Upload date");
		$retval["type"] = "date";
		return $retval;
	}

	function filegal_ranking_last_files($limit) {
		global $user;
		$query = "select `fileId`,`filename`,`created`, `galleryId` from `tiki_files` order by `created` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$aux["name"] = $res["filename"];
				$aux["hits"] = $res["created"];
				$aux["href"] = 'tiki-download_file.php?fileId=' . $res["fileId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki last files");
		$retval["y"] = tra("Upload date");
		$retval["type"] = "date";
		return $retval;
	}

	function cms_ranking_top_articles($limit) {
		global $user;
		$query = "select * from `tiki_articles` order by `nbreads` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['articleId'], 'article', 'tiki_p_read_article')) {
				$aux["name"] = $res["title"];
				$aux["hits"] = $res["nbreads"];
				$aux["href"] = 'tiki-read_article.php?articleId=' . $res["articleId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top articles");
		$retval["y"] = tra("Reads");
		$retval["type"] = "nb";
		return $retval;
	}

	function blog_ranking_top_blogs($limit) {
		global $user;
		$query = "select * from `tiki_blogs` order by `hits` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$aux["name"] = $res["title"];
				$aux["hits"] = $res["hits"];
				$aux["href"] = 'tiki-view_blog.php?blogId=' . $res["blogId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Most visited blogs");
		$retval["y"] = tra("Visits");
		$retval["type"] = "nb";
		return $retval;
	}

	function blog_ranking_top_active_blogs($limit) {
		global $user;
		$query = "select * from `tiki_blogs` order by `activity` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$aux["name"] = $res["title"];
				$aux["hits"] = $res["activity"];
				$aux["href"] = 'tiki-view_blog.php?blogId=' . $res["blogId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Most active blogs");
		$retval["y"] = tra("Activity");
		$retval["type"] = "nb";
		return $retval;
	}

	function blog_ranking_last_posts($limit) {
		global $user;
		$query = "select * from `tiki_blog_posts` order by `created` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$q = "select `title` from `tiki_blogs` where `blogId`=?";

				$name = $this->getOne($q,array($res["blogId"]));
				$aux["name"] = $name;
				$aux["hits"] = $res["created"];
				$aux["href"] = 'tiki-view_blog.php?blogId=' . $res["blogId"];
				$ret[] = $aux;
			}
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Blogs last posts");
		$retval["y"] = tra("Post date");
		$retval["type"] = "date";
		return $retval;
	}

	function wiki_ranking_top_authors($limit, $categ=array()) {
		global $user;
		
		$bindvals = array();
		$mid = '';
		if ($categ) {
			$mid .= " INNER JOIN (`tiki_objects` as tob, `tiki_category_objects` as tco) ON (tp.`pageName` = tob.`itemId` and tob.`objectId` = tco.`catObjectId`) WHERE tob.`type` = 'wiki page' AND (tco.`categId` = ?";
			$bindvals[] = $categ[0]; 	
			for ($i = 1; $i < count($categ); $i++) {
				$mid .= " OR tco.`categId` = " . $categ[$i];
			}
			$mid .= ")";
		}
		$query = "select distinct tp.`user`, count(*) as `numb` from `tiki_pages` tp $mid group by `user` order by ".$this->convert_sortmode("numb_desc");

		$result = $this->query($query,$bindvals,$limit,0);
		$ret = array();
		$retu = array();

		while ($res = $result->fetchRow()) {
			$ret["name"] = $res["user"];
			$ret["hits"] = $res["numb"];
			$ret["href"] = "tiki-user_information.php?view_user=".urlencode($res["user"]);
			$retu[] = $ret;
		}
		$retval["data"] = $retu;
		$retval["title"] = tra("Wiki top authors");
		$retval["y"] = tra("Pages");
		$retval["type"] = "nb";
		return $retval;
	}

	function cms_ranking_top_authors($limit) {
		$query = "select distinct `author`, count(*) as `numb` from `tiki_articles` group by `author` order by ".$this->convert_sortmode("numb_desc");

		$result = $this->query($query,array(),$limit,0);
		$ret = array();
		$retu = array();

		while ($res = $result->fetchRow()) {
			$ret["name"] = $res["author"];
			$ret["hits"] = $res["numb"];
			$ret["href"] = "tiki-user_information.php?view_user=".urlencode($res["author"]);
			$retu[] = $ret;
		}
		$retval["data"] = $retu;
		$retval["title"] = tra("Top article authors");
		$retval["y"] = tra("Articles");
		$retval["type"] = "nb";
		return $retval;
	}

}
global $dbTiki;
$ranklib = new RankLib($dbTiki);

?>
