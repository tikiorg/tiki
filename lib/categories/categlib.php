<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/categories/categlib.php,v 1.12 2003-08-11 07:06:54 redflo Exp $
 *
 * \brief Categiries support class
 *
 */
class CategLib extends TikiLib {
	function CategLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to CategLib constructor");
		}

		$this->db = $db;
	}

	function list_all_categories($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid) {
		$cats = $this->get_object_categories($type, $objid);

		if ($find) {
			$findesc = '%' . $find . '%';
			$bindvals=array($findesc,$findesc);

			$mid = " where (`name` like ? or `description` like ?)";
		} else {
		        $bindvals=array();
			$mid = "";
		}

		$query = "select * from `tiki_categories` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_categories` $mid";
		$result = $this->query($query,$bindvals,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvals);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if (in_array($res["categId"], $cats)) {
				$res["incat"] = 'y';
			} else {
				$res["incat"] = 'n';
			}

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_category_path_admin($categId) {
		$path = '';

		$info = $this->get_category($categId);
		$path = '<a class="categpath" href="tiki-admin_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a>';

		while ($info["parentId"] != 0) {
			$info = $this->get_category($info["parentId"]);

			$path
				= $path = '<a class="categpath" href="tiki-admin_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a>' . '>' . $path;
		}

		return $path;
	}

	function get_category_path_browse($categId) {
		$path = '';

		$info = $this->get_category($categId);
		$path
			= '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a>';

		while ($info["parentId"] != 0) {
			$info = $this->get_category($info["parentId"]);

			$path
				= $path = '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a>' . '>' . $path;
		}

		return $path;
	}

	function get_category($categId) {
		$query = "select * from `tiki_categories` where `categId`=?";

		$result = $this->query($query,array($categId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function remove_category($categId) {
		// Delete the category
		$query = "delete from `tiki_categories` where `categId`=?";

		$result = $this->query($query,array($categId));
		// Remove objects for this category
		$query = "select `catObjectId` from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query,array($categId));

		while ($res = $result->fetchRow()) {
			$object = $res["catObjectId"];

			$query2 = "delete from `tiki_categorized_objects` where `catObjectId`=?";
			$result2 = $this->query($query2,array($object));
		}

		$query = "delete from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query,array($categId));
		$query = "select `categId` from `tiki_categories` where `parentId`=?";
		$result = $this->query($query,array($categId));

		while ($res = $result->fetchRow()) {
			// Recursively remove the subcategory
			$this->remove_category($res["categId"]);
		}

		return true;
	}

	function update_category($categId, $name, $description, $parentId) {
		$query = "update `tiki_categories` set `name`=?, `parentId`=?, `description`=? where `categId`=?";
		$result = $this->query($query,array($name,$parentId,$description,$categId));
	}

	function add_category($parentId, $name, $description) {
		$query = "insert into `tiki_categories`(`name`,`description`,`parentId`,`hits`) values(?,?,?,?)";
		$result = $this->query($query,array($name,$description,$parentId,0));
	}

	function is_categorized($type, $objId) {

		$query = "select `catObjectId` from `tiki_categorized_objects` where `type`=? and `objId`=?";
		$bindvars=array($type,$objId);
		settype($bindvars["1"],"string");
		$result = $this->query($query,$bindvars);

		if ($result->numRows()) {
			$res = $result->fetchRow();

			return $res["catObjectId"];
		} else {
			return 0;
		}
	}

	function add_categorized_object($type, $objId, $description, $name, $href) {
		$description = strip_tags($description);

		$name = strip_tags($name);
		$now = date("U");
		$query = "insert into `tiki_categorized_objects`(`type`,`objId`,`description`,`name`,`href`,`created`,`hits`)
    values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($type,$objId,$description,$name,$href,$now,0));
		$query = "select `catObjectId` from `tiki_categorized_objects` where `created`=? and `type`=? and `objId`=?";
		$id = $this->getOne($query,array($now,$type,$objId));
		return $id;
	}

	function categorize($catObjectId, $categId) {
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query,array($catObjectId,$categId),-1,-1,false);
	        
		$query = "insert into `tiki_category_objects`(`catObjectId`,`categId`) values(?,?)";
		$result = $this->query($query,array($catObjectId,$categId));
	}

	function get_category_descendants($categId) {
		$query = "select `categId` from `tiki_categories` where `parentId`=?";

		$result = $this->query($query,array($categId));
		$ret = array($categId);

		while ($res = $result->fetchRow()) {
			$ret[] = $res["categId"];

			$aux = $this->get_category_descendants($res["categId"]);
			$ret = array_merge($ret, $aux);
		}

		$ret = array_unique($ret);
		return $ret;
	}

	function list_category_objects_deep($categId, $offset, $maxRecords, $sort_mode = 'pageName_asc', $find) {

		$des = $this->get_category_descendants($categId);
		if (count($des)>0) {
			$cond = "where tbl1.`categId` in (".str_repeat("?,",count($des)-1)."?)";
		} else {
			$cond = "";
		}

		if ($find) {
			$findesc = '%' . $find . '%';
			$des[]=$findesc;
			$des[]=$findesc;
			$mid = " and (name like ? or description like ?)";
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 $cond and tbl1.`catObjectId`=tbl2.`catObjectId` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select distinct tbl1.`catObjectId` from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 $cond and tbl1.`catObjectId`=tbl2.`catObjectId` $mid";
		$result = $this->query($query,$des,$maxRecords,$offset);
		$result2 = $this->query($query_cant,$des);
		$cant = $result2->numRows();
		$cant2
			= $this->getOne("select count(*) from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 $cond and tbl1.`catObjectId`=tbl2.`catObjectId` $mid",$des);
		$ret = array();
		$objs = array();

		while ($res = $result->fetchRow()) {
			if (!in_array($res["catObjectId"], $objs)) {
				$ret[] = $res;

				$objs[] = $res["catObjectId"];
			}
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$retval["cant2"] = $cant2;
		return $retval;
	}

	function list_category_objects($categId, $offset, $maxRecords, $sort_mode = 'pageName_asc', $find) {

		if ($find) {
			$findesc = '%' . $find . '%';
			$bindvars=array($categId,$findesc,$findesc);
			$mid = " and (name like ? or description like ?)";
		} else {
			$mid = "";
			$bindvars=array($categId);
		}

		$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`catObjectId`=tbl2.`catObjectId` and `categId`=? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select distinct tbl1.`catObjectId` from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`catObjectId`=tbl2.`catObjectId` and `categId`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$result2 = $this->query($query_cant,$bindvars);
		$cant = $result2->numRows();
		$cant2
			= $this->getOne("select count(*) from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`catObjectId`=tbl2.`catObjectId` and `categId`=? $mid",$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$retval["cant2"] = $cant2;
		return $retval;
	}

	function get_object_categories($type, $objId) {

		$query = "select `categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto
    where tco.`catObjectId`=tto.`catObjectId` and `type`=? and `objId`=?";
		//settype($objId,"string"); //objId is defined as varchar
		$bindvars=array($type,$objId);
		settype($bindvars["1"],"string");
		$result = $this->query($query,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["categId"];
		}

		return $ret;
	}

	function get_category_objects($categId) {
		// Get all the objects in a category
		$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`catObjectId`=tbl2.`catObjectId` and `categId`=?";

		$result = $this->query($query,array($categId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function remove_object_from_category($catObjectId, $categId) {
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";

		$result = $this->query($query,array($catObjectId,$categId));
		// If the object is not listed in any category then remove the object
		$query = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
		$cant = $this->getOne($query,array($catObjectId));

		if (!$cant) {
			$query = "delete from `tiki_categorized_objects` where `catObjectId`=?";

			$result = $this->query($query,array($catObjectId));
		}
	}

	// FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS ////
	function categorize_page($pageName, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$pageName_sl = addslashes($pageName);

		$catObjectId = $this->is_categorized('wiki page', $pageName_sl);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_page_info($pageName);

			$href = 'tiki-index.php?page=' . urlencode($pageName);
			$catObjectId
				= $this->add_categorized_object('wiki page', $pageName, substr($info["description"], 0, 200), $pageName, $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_quiz($quizId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('quiz', $quizId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_quiz($quizId);

			$href = 'tiki-take_quiz.php?quizId=' . $quizId;
			$catObjectId
				= $this->add_categorized_object('quiz', $quizId, substr($info["description"], 0, 200), $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_article($articleId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('article', $articleId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_article($articleId);

			$href = 'tiki-read_article.php?articleId=' . $articleId;
			$catObjectId = $this->add_categorized_object('article', $articleId, $info["heading"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_faq($faqId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('faq', $faqId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_faq($faqId);

			$href = 'tiki-view_faq.php?faqId=' . $faqId;
			$catObjectId = $this->add_categorized_object('faq', $faqId, $info["description"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_blog($blogId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('blog', $blogId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_blog($blogId);

			$href = 'tiki-view_blog.php?blogId=' . $blogId;
			$catObjectId = $this->add_categorized_object('blog', $blogId, $info["description"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_directory($directoryId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('directory', $directoryId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_directory($directoryId);

			$href = 'tiki-directory_browse.php?parent=' . $directoryId;
			$catObjectId = $this->add_categorized_object('directory', $directoryId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_gallery($galleryId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('image gallery', $galleryId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_gallery($galleryId);

			$href = 'tiki-browse_gallery.php?galleryId=' . $galleryId;
			$catObjectId = $this->add_categorized_object('image gallery', $galleryId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_file_gallery($galleryId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('file gallery', $galleryId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_file_gallery($galleryId);

			$href = 'tiki-list_file_gallery.php?galleryId=' . $galleryId;
			$catObjectId = $this->add_categorized_object('file gallery', $galleryId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_forum($forumId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('forum', $forumId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_forum($forumId);

			$href = 'tiki-view_forum.php?forumId=' . $forumId;
			$catObjectId = $this->add_categorized_object('forum', $forumId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}

	function categorize_poll($pollId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('poll', $pollId);

		if (!$catObjectId) {
			// The page is not cateorized
			$info = $this->get_poll($pollId);

			$href = 'tiki-poll_form.php?pollId=' . $pollId;
			$catObjectId = $this->add_categorized_object('poll', $pollId, $info["title"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
	}
	// FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS END ////
	function get_child_categories($categId) {
		$ret = array();

		$query = "select * from `tiki_categories` where `parentId`=?";
		$result = $this->query($query,array($categId));

		while ($res = $result->fetchRow()) {
			$id = $res["categId"];

			$query = "select count(*) from `tiki_categories` where `parentId`=?";
			$res["children"] = $this->getOne($query,array($id));
			$query = "select count(*) from `tiki_category_objects` where `categId`=?";
			$res["objects"] = $this->getOne($query,array($id));
			$ret[] = $res;
		}

		return $ret;
	}

	function get_all_categories() {
		$query = " select `name`,`categId`,`parentId` from `tiki_categories` order by `name`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	// Same as get_all_categories + it also get info about count of objects
	function get_all_categories_ext() {
		$ret = array();

		$query = "select * from `tiki_categories` order by `name`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$id = $res["categId"];

			$query = "select count(*) from `tiki_categories` where `parentId`=?";
			$res["children"] = $this->getOne($query,array($id));
			$query = "select count(*) from `tiki_category_objects` where `categId`=?";
			$res["objects"] = $this->getOne($query,array($id));
			$ret[] = $res;
		}

		return $ret;
	}
}

$categlib = new CategLib($dbTiki);

?>
