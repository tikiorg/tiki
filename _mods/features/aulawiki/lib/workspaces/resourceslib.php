<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
class WorkspaceResourcesLib extends TikiDB {
	var $db; // The PEAR db object used to access the database
	function WorkspaceResourcesLib($db) {
		$this->TikiDB($db);
	}

	function get_category_by_name($parentId, $catName) {
		$query = "select * from tiki_categories where parentId=? and name=?";
		$result = $this->query($query, array ($parentId, $catName));
		$res = $result->fetchRow();
		return $res;
	}

	function create_object($name, $desc, $type, $parentCategoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$type = str_replace(" ", "", $type);
		$funcname = "create_".$type;
		return $this-> $funcname ($name, $desc, $parentCategoryId, $categlib);
	}

	function redirect($id, $name, $type) {
		$type = str_replace(" ", "", $type);
		$funcname = "redirect_".$type;
		$this-> $funcname ($id, $name);
	}

	function redirect_wikipage($id, $name) {
		global $tikilib;
		header("location: "."./tiki-editpage.php?page=".$name."&objectCreated=".$name);
		die;
	}
	function redirect_article($id, $name) {
		global $tikilib;
		header("location: ./tiki-edit_submission.php?subId=".$id);
		die;
	}
	function redirect_blog($id, $name) {
		global $tikilib;
		header("location: "."./tiki-edit_blog.php?blogId=".$id."&objectCreated=".$name);
		die;
	}
	function redirect_category($id, $name) {

	}
	function redirect_structure($id, $name) {
		global $tikilib;
		header("location: "."./tiki-editpage.php?page=".$name."&objectCreated=".$name);
		die;
	}
	function redirect_forum($id, $name) {
		global $tikilib;
		header("location: "."./tiki-admin_forums.php?forumId=".$id."&objectCreated=".$name);
		die;
	}
	function redirect_imagegallery($id, $name) {
		global $tikilib;
		header("location: "."./tiki-galleries.php?edit_mode=1&galleryId=".$id."&objectCreated=".$name);
		die;
	}

	function redirect_filegallery($id, $name) {
		global $tikilib;
		header("location: "."./tiki-file_galleries.php?edit_mode=1&galleryId=".$id."&objectCreated=".$name);
		die;
	}

	function redirect_quiz($id, $name) {
		global $tikilib;
		header("location: ./tiki-edit_quiz.php?&quizId=".$id);
		die;
	}

	function redirect_faq($id, $name) {
		global $tikilib;
		header("location: ./tiki-list_faqs.php?find=".$name."&search=find&sort_mode=created_desc&faqId=".$id);
		die;
	}
	
	function redirect_calendar($id, $name) {
		global $tikilib;
		header("location: ./tiki-admin_calendars.php?find=".$name."&search=find&sort_mode=created_desc&calendarId=".$id);
		die;
	}

	function redirect_tracker($id, $name) {
		global $tikilib;
		header("location: ./tiki-admin_trackers.php?trackerId=".$id);
		die;
	}
	
	function redirect_survey($id, $name) {
		global $tikilib;
		header("location: ./tiki-admin_surveys.php?offset=0&sort_mode=created_desc&surveyId=".$id);
		die;
	}
	
	function redirect_sheet($id, $name) {
		global $tikilib;
		header("location: ./tiki-sheets.php?offset=0&sort_mode=title_desc&edit_mode=1&sheetId=".$id);
		die;
	}
	
	/******************************************************************/
	/*                      Admin URLs                          */
	/******************************************************************/
	function get_url_admin($id, $name, $type) {
		$type = str_replace(" ", "", $type);
		$funcname = "get_url_admin_".$type;
		return $this-> $funcname ($id, $name);
	}

	function get_url_admin_wikipage($id, $name) {
		return "./tiki-editpage.php?page=".$name;
	}

	function get_url_admin_article($id, $name) {
		return "./tiki-edit_submission.php?subId=".$id;
	}
	function get_url_admin_blog($id, $name) {
		return "./tiki-edit_blog.php?blogId=".$id;
	}

	function get_url_admin_category($id, $name) {

	}
	function get_url_admin_structure($id, $name) {
		return "./tiki-index.php?page_ref_id=".$id."&objectCreated=".$name;
	}

	function get_url_admin_forum($id, $name) {
		return "./tiki-admin_forums.php?find=".$name."&search=find&forumId=".$id;
	}

	function get_url_admin_imagegallery($id, $name) {
		return "./tiki-galleries.php?find=".$name."&search=find&edit_mode=1&galleryId=".$id;
	}

	function get_url_admin_filegallery($id, $name) {
		return "./tiki-file_galleries?find=".$name."&search=find&edit_mode=1&galleryId=".$id;
	}

	function get_url_admin_calendar($id, $name) {
		return "./tiki-admin_calendars.php?find=".$name."&search=find&calendarId=".$id;
	}

	function get_url_admin_assignments($id, $name) {
		return "./tiki-workspaces_view_module.php?module=workspaces_assignments_admin&workspaceId=".$id;
	}

	function get_url_admin_workspace($id, $name) {
		return "./tiki-workspaces_admin.php?find=".$name."&search=find&edit=".$id;
	}

	function get_url_admin_quiz($id, $name) {
		return "./tiki-edit_quiz.php?find=".$name."&search=find&quizId=".$id;
	}

	function get_url_admin_faq($id, $name) {
		return "./tiki-list_faqs.php?find=".$name."&search=find&faqId=".$id;
	}
	
	function get_url_admin_tracker($id, $name) {
		return "./tiki-admin_trackers.php?trackerId=".$id;
	}
	
	function get_url_admin_survey($id, $name) {
		return "./tiki-admin_surveys.php?offset=0&sort_mode=created_desc&surveyId=".$id;
	}
	
	function get_url_admin_sheet($id, $name) {
		return "./tiki-sheets.php?offset=0&sort_mode=title_desc&edit_mode=1&sheetId=".$id;
	}

	/******************************************************************/
	/*                      Remove URLs                          */
	/******************************************************************/
	function get_url_remove($id, $type) {
		$type = str_replace(" ", "", $type);
		$funcname = "get_url_remove_".$type;
		return $this-> $funcname ($id);
	}

	function get_url_remove_wikipage($id) {
		return "./tiki-removepage.php?version=last&page=".$id;
	}

	function get_url_remove_article($id) {
		return "./tiki-list_submissions.php?remove=".$id;
	}
	function get_url_remove_blog($id) {
		return "./tiki-list_blogs.php?remove=".$id;
	}

	function get_url_remove_category($id) {
		return "./tiki-admin_categories.php?removeCat=".$id;

	}
	function get_url_remove_structure($id) {
		return "./tiki-admin_structures.php?rremovex=".$id;
	}

	function get_url_remove_forum($id) {
		return "./tiki-admin_forums.php?remove=".$id;
	}

	function get_url_remove_imagegallery($id) {
		return "./tiki-galleries.php?removegal=".$id;
	}

	function get_url_remove_filegallery($id) {
		return "./tiki-file_galleries.php?removegal=".$id;
	}

	function get_url_remove_calendar($id) {
		return "./tiki-admin_calendars.php?drop=".$id;
	}

	function get_url_remove_assignments($id) {
		return "./tiki-workspaces_view_module.php?module=aulawiki_assignments_admin&workspaceId=".$id;
	}

	function get_url_remove_workspace($id) {
		return "./tiki-workspaces_admin.php?viewWS=0&delete=".$id;
	}

	function get_url_remove_quiz($id) {
		return "./tiki-edit_quiz.php?&remove=".$id;
	}

	function get_url_remove_faq($id) {
		return "./tiki-list_faqs.php?&remove=".$id;
	}

	function get_url_remove_tracker($id) {
		return "./tiki-admin_trackers.php?offset=0&sort_mode=created_desc&remove=".$id;
	}
	
	function get_url_remove_survey($id) {
		return "./tiki-admin_surveys.php?offset=0&sort_mode=created_desc&remove=".$id;
	}
	
	function get_url_remove_sheet($id) {
		return "./tiki-sheets.php?offset=0&sort_mode=title_desc&removesheet=".$id;
	}
	
	function create_resource($type, $name, $desc, $parentCategoryId, $resourceId = null) {
		$type = str_replace(" ", "", $type);
		$funcname = "create_".$type;
		return $this-> $funcname ($name, $desc, $parentCategoryId, $resourceId);
	}

	function remove_resource($type, $name, $categoryId) {
		$type = str_replace(" ", "", $type);
		$funcname = "remove_".$type;
		return $this-> $funcname ($name, $categoryId);
	}

	function create_category($name, $desc, $parentCategoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$categ = $this->get_category($parentCategoryId, $name);

		if (isset($categ) && $categ["name"]==$name){
			return $categ["categId"];
		}
		$categId = $categlib->add_category($parentCategoryId, $name, $desc);
		return $categId;
	}

	/*
		function update_workspace_category($oldcatId,$code, $name, $parentId, $parentCategoryId, $categlib) {
			if (!isset ($parentCategoryId) || $parentCategoryId == "") {
				$parentCategoryId = 0;
				if (isset ($parentId) && $parentId != "") {
					$parentws = $this->get_workspace_by_id($parentId);
					$parentCategoryId = $parentws["categoryId"];
				}
			}
			$categlib->update_category($oldcatId, $code, $name, $parentCategoryId);
			return $oldcatId;
		}
		function remove_workspace_category($categoryId, $categlib) {
			$categlib->remove_category($categoryId);
			return true;
		}
	*/
	//TODO: Poder asociar diferentes conjuntos de assignments a un mismo workspace
	function create_assignments($name, $desc, $parentCategoryId, $resourceId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $dbTiki;
		global $tikilib;

		$assignments = $this->get_category_object($parentCategoryId, $name, "assignments");
		if (isset ($assignments) && $assignments["name"] == $name) {
			return $assignments["objId"];
		}
		$idCatObj = $categlib->add_categorized_object("assignments", $resourceId, $desc, $name, "tiki-workspaces_view_module.php?module=aulawiki_assignments_admin&workspaceId=".$resourceId);
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $resourceId;

	}

	function remove_assignments($name, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$assignments = $this->get_category_object($categoryId, $name, "assignments");
		if (isset ($assignments) && $assignments["name"] == $name) {
			$objCats = $categlib->get_object_categories("assignments",$assignments["objId"]);
			if(count($objCats)==1){
				global $dbTiki;
				global $tikilib;
				include_once ('lib/aulawiki/assignmentslib.php');
				$assignmentsLib = new AssignmentsLib($dbTiki);
				$assignmentsLib->del_all_assignments($assignments["objId"]);
			}
			//$categlib->uncategorize_object("assignments", $assignments["objId"]);
		}
	}

	function create_blog($name, $desc, $parentCategoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $dbTiki;
		global $tikilib;

		$blogId = '';
		$blog = $this->get_category_object($parentCategoryId, $name, "blog");
		if (isset ($blog) && $blog["name"] == $name) {
			return $blog["objId"];
		}

		$head = '{include file="tiki-workspaces_blog.tpl"}';
		include_once ('lib/blogs/bloglib.php');
		$bloglib2 = new BlogLib($dbTiki);
		global $user;
		$blogId = $bloglib2->replace_blog($name, $desc, $user, 'y', 10, $blogId, $head, 'y', 'y', 'y', 'y');

		//Categorizar blog de problemas

		$categlib->uncategorize_object("blog", $blogId);
		$idCatObj = $categlib->add_categorized_object("blog", $blogId, $desc, $name, "tiki-view_blog.php?blogId=".$blogId);
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $blogId;
	}

	function remove_blog($name, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$blog = $this->get_category_object($categoryId, $name, "blog");
		if (isset ($blog) && $blog["name"] == $name) {
			$objCats = $categlib->get_object_categories("blog",$blog["objId"]);
			if(count($objCats)==1){
				global $dbTiki;
				global $tikilib;
				include_once ('lib/blogs/bloglib.php');
				$bloglib2 = new BlogLib($dbTiki);
				$blogId = $bloglib2->remove_blog($blog["objId"]);
			}
		}
	}

	function create_calendar($name, $desc, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $dbTiki;
		include_once ('lib/calendar/calendarlib.php');
		$calendarlib2 = new CalendarLib($dbTiki);

		$calendario = $this->get_category_object($categoryId, $name, "calendar");
		if (isset ($calendario) && $calendario["name"] == $name) {
			return $calendario["objId"];
		}
		$customflags["customlanguages"] = 'n';
		$customflags["customlocations"] = 'y';
		$customflags["customparticipants"] = 'y';
		$customflags["customcategories"] = 'y';
		$customflags["custompriorities"] = 'y';
		$customflags["customsubscription"] = 'n';
		$customflags["personal"] = "n";

		$calendarioId = $calendarlib2->set_calendar(null, 'admin', $name, $desc, $customflags);

		//Categorizar calendario
		$categlib->uncategorize_object("calendar", $calendarioId);
		$idCatObj = $categlib->add_categorized_object("calendar", $calendarioId, $desc, $name, "tiki-calendar.php?calendarId=".$calendarioId."&calIds[]=".$calendarioId."&viewmode=month");
		$categlib->categorize($idCatObj, $categoryId);
		return $calendarioId;
	}

	function remove_calendar($name, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $dbTiki;
		include_once ('lib/calendar/calendarlib.php');
		$calendarlib2 = new CalendarLib($dbTiki);
		$calendario = $this->get_category_object($categoryId, $name, "calendar");

		if (isset ($calendario) && $calendario["name"] == $name) {
			$objCats = $categlib->get_object_categories("calendar",$calendario["objId"]);
			if(count($objCats)==1){
				$calendarlib2->drop_calendar($calendario["objId"]);
			}
		}
	}

	function create_wikipage($name, $description, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $tikilib;
		if (!$tikilib->page_exists($name)) {
			global $user;
			$tikilib->create_page($name, 0, '', date("U"), $description, $user, $_SERVER["REMOTE_ADDR"], $description);
			//Categorizar
			$categlib->uncategorize_object("wiki page", $name);
			$idCatObj = $categlib->add_categorized_object("wiki page", $name, $description, $name, "tiki-index.php?page=".$name);
			$categlib->categorize($idCatObj, $categoryId);
		}
		return $name;

	}

	function remove_wikipage($name, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $tikilib;
		if ($tikilib->page_exists($name)) {
			$objCats = $categlib->get_object_categories("wiki page",$name);
			if(count($objCats)==1){
				$tikilib->remove_all_versions($name);
			}
		}
	}

	function create_article($name, $description, $categoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/articles/artlib.php');
		global $user;

		$article = $this->get_category_object($categoryId, $name, "article");

		if (isset ($article) && $article["name"] == $name) {
			return $article["objId"];
		}
		$subid = $artlib->replace_submission($name, $user, null, 'n', '', 0, '', '', $description, '', date("U"), date("U"), $user, 0, 0, 0, null, '', $description, '', '', '', '', 'n');

		$categlib->uncategorize_object("article", $subid);
		$idCatObj = $categlib->add_categorized_object("article", $subid, $description, $name, "./tiki-read_article.php?articleId=".$subid);
		$categlib->categorize($idCatObj, $categoryId);
		return $subid;
	}

	function remove_structure($name, $categoryId) {
		$this->remove_wikipage($name, $categoryId);
	}

	function create_structure($name, $desc, $parentCategoryId) {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		global $dbTiki;
		include_once ('lib/structures/structlib.php');

		$structure = $this->get_category_object($parentCategoryId, $name, "structure");
		if (isset ($structure) && $structure["name"] == $name) {
			return $structure["objId"];
			;
		}
		$structure_id = $structlib->s_create_page(null, null, $name, $desc);

		//Categorizar pagina principal del curriculo
		$categlib->uncategorize_object("structure", $name);
		$idCatObj = $categlib->add_categorized_object("structure", $structure_id, $desc, $name, "./tiki-index.php?page_ref_id=".$structure_id);
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $name;
	}

	function create_forum($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $user;
		include_once ('lib/categories/categlib.php');
		include_once ("lib/commentslib.php");

		$forum = $this->get_category_object($parentCategoryId, $name, "forum");
		if (isset ($forum) && $forum["name"] == $name) {
			return $forum["objId"];
		}

		$commentslib = new Comments($dbTiki);
		$fid = $commentslib->replace_forum(null, $name, $desc, 'n', 120, $user, '', 'n', 'n', '2592000', 'n', '2592000', 20, 'commentDate_desc', 'commentDate_desc', '', 'y', 'y', 'y', 'y', 'y', 'y', 'y', '', 110, '', '', '', '', '', '', 'y', 'y', 'y', 'y', 'y', 'y', 'n', 'n', 'all_posted', '', '', 'n', 'att_no', 'db', '', '1000000', 'y');
		//Categorizar foro
		//$categlib->uncategorize_object("forum", $name);
		$idCatObj = $categlib->add_categorized_object("forum", $fid, $desc, $name, "./tiki-view_forum.php?forumId=".$fid);
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $fid;
	}

	function remove_forum($name, $categoryId) {
		global $categlib;
		global $dbTiki;
		include_once ('lib/categories/categlib.php');

		$forum = $this->get_category_object($categoryId, $name, "forum");
		if (isset ($forum) && $forum["name"] == $name) {
			$objCats = $categlib->get_object_categories("forum",$forum["objId"]);
			if(count($objCats)==1){
				include_once ("lib/commentslib.php");
				$commentslib = new Comments($dbTiki);
				$fid = $commentslib->remove_forum($forum["objId"]);
			}
		}
	}

	function create_imagegallery($name, $desc, $parentCategoryId) {
		global $categlib;
		global $user;
		global $dbTiki;
		include_once ('lib/categories/categlib.php');
		include_once ("lib/imagegals/imagegallib.php");

		$galimg = $this->get_category_object($parentCategoryId, $name, "image gallery");
		if (isset ($galimg) && $galimg["name"] == $name) {
			return $galimg["objId"];
		}

		$imagegallib2 = new ImageGalsLib($dbTiki);

		$gid = $imagegallib2->replace_gallery(null, $name, $desc, '', $user, 5, 5, 80, 80, 'y', 'y', 'created', 'desc', 'first', -1, 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'n', 'o', 'n');

		//Categorizar image galery
		//$categlib->uncategorize_object("forum", $name);
		$idCatObj = $categlib->add_categorized_object("image gallery", $gid, $desc, $name, "./tiki-browse_gallery.php?galleryId=".$gid);
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $gid;
	}

	function remove_imagegallery($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ("lib/imagegals/imagegallib.php");
		global $imagegallib;
		$galimg = $this->get_category_object($categoryId, $name, "image gallery");
		if (isset ($galimg) && $galimg["name"] == $name) {
			$objCats = $categlib->get_object_categories("image gallery",$galimg["objId"]);
			if(count($objCats)==1){
				$imagegallib->remove_gallery($galimg["objId"]);
			}
		}
	}
	function create_filegallery($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $tikilib;
		include_once ('lib/filegals/filegallib.php');
		$filegallib = new FileGalLib($dbTiki);
		$galId = '';
		$filegal = $this->get_category_object($parentCategoryId, $name, "file gallery");
		if (isset ($filegal) && $filegal["name"] == $name) {
			return $filegal["objId"];
		}

		$galId = $filegallib->replace_file_gallery($galId, $name, $desc, "admin", 15, 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 2024);

		//Categorizar galeria de ficheros
		$categlib->uncategorize_object("file gallery", $galId);
		$idCatObj = $categlib->add_categorized_object("file gallery", $galId, $desc, $name, "./tiki-list_file_gallery.php?galleryId=$galId");
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $galId;
	}

	function remove_filegallery($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/filegals/filegallib.php');
		$filegallib = new FileGalLib($dbTiki);
		$filegal = $this->get_category_object($categoryId, $name, "file gallery");
		if (isset ($filegal) && $filegal["name"] == $name) {
			$objCats = $categlib->get_object_categories("file gallery",$filegal["objId"]);
			if(count($objCats)==1){
				$galId = $filegallib->remove_file_gallery($filegal["objId"]);
			}
		}
	}

	function create_quiz($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $tikilib;
		include_once ('lib/quizzes/quizlib.php');
		$quizlib = new QuizLib($dbTiki);
		
		$galId = '';
		$quiz = $this->get_category_object($parentCategoryId, $name, "quiz");
		if (isset ($quiz) && $quiz["name"] == $name) {
			return $quiz["objId"];
		}

		$qid = $quizlib->replace_quiz(null, $name, $desc, "n", "n", "y", "n", "n", "n", 10, "y", 60 * 60, date("U"), date("U"), "");

		//Categorizar galeria de ficheros
		$categlib->uncategorize_object("quiz", $qid);
		$idCatObj = $categlib->add_categorized_object("quiz", $qid, $desc, $name, "./tiki-take_quiz.php?quizId=$qid");
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $qid;
	}

	function remove_quiz($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/quizzes/quizlib.php');
		$quizlib = new QuizLib($dbTiki);
		$quiz = $this->get_category_object($categoryId, $name, "quiz");
		if (isset ($quiz) && $quiz["name"] == $name) {
			$objCats = $categlib->get_object_categories("quiz",$quiz["objId"]);
			if(count($objCats)==1){
				$quizlib->remove_quiz($quiz["objId"]);
			}
		}
	}

	function create_faq($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $tikilib;
		include_once ('lib/faqs/faqlib.php');
		$faqlib = new FaqLib($dbTiki);
		$faq = $this->get_category_object($parentCategoryId, $name, "faq");
		if (isset ($faq) && $faq["name"] == $name) {
			return $faq["objId"];
		}

		$fid = $faqlib->replace_faq(null, $name, $desc, "y");

		//Categorizar galeria de ficheros
		$categlib->uncategorize_object("faq", $fid);
		$idCatObj = $categlib->add_categorized_object("faq", $fid, $desc, $name, "./tiki-view_faq.php?faqId=$fid");
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $fid;
	}

	function remove_faq($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/faqs/faqlib.php');
		$faqlib = new FaqLib($dbTiki);
		$faq = $this->get_category_object($categoryId, $name, "faq");
		if (isset ($faq) && $faq["name"] == $name) {
			$objCats = $categlib->get_object_categories("faq",$faq["objId"]);
			if(count($objCats)==1){
				$faqlib->remove_faq($faq["objId"]);
			}
		}
	}

	function create_tracker($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $tikilib;
		include_once ('lib/trackers/trackerlib.php');
		
		if($tikilib->get_preference('trk_with_mirror_tables') == 'y') {
			include_once ("lib/trackers/trkWithMirrorTablesLib.php");
			$trklib = new TrkWithMirrorTablesLib($dbTiki);
		}
		else {
			$trklib = new TrackerLib($dbTiki);
		}
		
		$tracker = $this->get_category_object($parentCategoryId, $name, "tracker");
		if (isset ($tracker) && $tracker["name"] == $name) {
			return $tracker["objId"];
		}
		$tracker_options["showCreated"] = 'y';
		$tracker_options["showStatus"] = 'y';
		$tracker_options["showStatusAdminOnly"] = 'y';
		$tracker_options["simpleEmail"] = 'n';
		$tracker_options["outboundEmail"] = '';
		$tracker_options["newItemStatus"] = 'y';
		$tracker_options["useRatings"] = 'y';
		$tracker_options["showRatings"] = 'y';
		$tracker_options["useComments"] = 'y';
		$tracker_options["showComments"] = 'y';
		$tracker_options["useAttachments"] = 'y';
		$tracker_options["showAttachments"] = 'y';
		$tracker_options["showLastModif"] = 'y';
		$tracker_options["defaultOrderDir"] = 'asc';
		$tracker_options["newItemStatus"] = '';
		$tracker_options["modItemStatus"] = '';
		$tracker_options["defaultOrderKey"] = '';
		$tracker_options["writerCanModify"] = 'y';
		$tracker_options["writerGroupCanModify"] = 'n';
		$tracker_options["defaultStatus"] = 'o';
		$trackid = $trklib->replace_tracker(null, $name, $desc,$tracker_options);

		//Categorizar galeria de ficheros
		$categlib->uncategorize_object("tracker", $trackid);
		$idCatObj = $categlib->add_categorized_object("tracker", $trackid, $desc, $name, "tiki-view_tracker.php?trackerId=$trackid");
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $trackid;
	}
	
	function remove_tracker($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/trackers/trackerlib.php');
				
		if($tikilib->get_preference('trk_with_mirror_tables') == 'y') {
			include_once ("lib/trackers/trkWithMirrorTablesLib.php");
			$trklib = new TrkWithMirrorTablesLib($dbTiki);
		}
		else {
			$trklib = new TrackerLib($dbTiki);
		}
		
		$tracker = $this->get_category_object($categoryId, $name, "tracker");
		if (isset ($tracker) && $tracker["name"] == $name) {
			$objCats = $categlib->get_object_categories("tracker",$tracker["objId"]);
			if(count($objCats)==1){
				$trklib->remove_tracker($tracker["objId"]);
			}
		}
	}
	
	function create_survey($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $tikilib;
		include_once ('lib/surveys/surveylib.php');
		$srvlib = new SurveyLib($dbTiki);
		$survey = $this->get_category_object($parentCategoryId, $name, "survey");
		if (isset ($survey) && $survey["name"] == $name) {
			return $survey["objId"];
		}

		$sid = $srvlib->replace_survey(null, $name, $desc, "o");
		//Categorizar galeria de ficheros
		$categlib->uncategorize_object("survey", $sid);
		$idCatObj = $categlib->add_categorized_object("survey", $sid, $desc, $name, "tiki-take_survey.php?surveyId=$sid");
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $sid;
	}
	
	function remove_survey($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/surveys/surveylib.php');
		
		$srvlib = new SurveyLib($dbTiki);
		$survey = $this->get_category_object($categoryId, $name, "survey");
		if (isset ($survey) && $survey["name"] == $name) {
			$objCats = $categlib->get_object_categories("survey",$survey["objId"]);
			if(count($objCats)==1){
				$srvlib->remove_survey($survey["objId"]);
			}
		}
	}
	
	function create_sheet($name, $desc, $parentCategoryId) {
		global $categlib;
		global $dbTiki;
		global $tikilib;
		include_once ('lib/sheet/grid.php');
		
		$sheetlib = &new SheetLib( $tikilib->db );
		$sheet = $this->get_category_object($parentCategoryId, $name, "sheet");
		if (isset ($sheet) && $sheet["name"] == $name) {
			return $sheet["objId"];
		}
		
		global $user;
		$gid = $sheetlib->replace_sheet(null, $name, $desc, $user );
		$sheetlib->replace_layout($gid, 'default', 0, 0 );
		
		//Categorizar galeria de ficheros
		$categlib->uncategorize_object("sheet", $gid);
		$idCatObj = $categlib->add_categorized_object("sheet", $gid, $desc, $name, "tiki-view_sheets.php?sheetId=$gid");
		$categlib->categorize($idCatObj, $parentCategoryId);
		return $gid;
	}
	
	function remove_sheet($name, $categoryId) {
		global $dbTiki;
		global $tikilib;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		include_once ('lib/sheet/grid.php');
		
		$sheetlib = &new SheetLib( $tikilib->db );
		$sheet = $this->get_category_object($categoryId, $name, "sheet");
		if (isset ($sheet) && $sheet["name"] == $name) {
			$objCats = $categlib->get_object_categories("sheet",$sheet["objId"]);
			if(count($objCats)==1){
				$sheetlib->remove_sheet($sheet["objId"]);
			}
		}
	}
	
	function remove_category_objects($categId){
		$resources = $this->get_category_objects($categId);
		if (isset ($resources) && $resources != "" && count($resources) > 0) {
			foreach ($resources as $key => $resource) {
				if ($resource["type"]!="workspace"){
					$data = $this->remove_resource($resource["type"],$resource["name"], $categId);
				}
			}
		}
	}
	
	function get_category_objects($categId, $name = null, $type = "*") {
			// Get all the objects in a category
	/*$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`catObjectId`=tbl2.`catObjectId` and `categId`=?";
		$params = array ((int) $categId);
		if (isset ($name)) {
			$query = $query." and `name`=?";
			$params[] = $name;
		}
		if ($type != "*") {
			$query = $query." and `type`=?";
			$params[] = $type;
		}
		$result = $this->query($query, $params);
		$ret = array ();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;*/
		global $categlib;
		global $dbTiki;
		include_once ('lib/categories/categlib.php');
		$categlib2 = new CategLib($dbTiki);
		//$ret = $categlib->list_category_objects($categId, 0, 10000, 'name_asc', $type, $name,false);
		$ret = $categlib2->get_category_objects($categId);

		$objects = array();
		foreach ($ret as $key => $object) {
				if ((!isset($type) || $type=="" || $type==null || $type=="*" || $object["type"]==$type) && (!isset($name) || $name=="" || $object["name"]==$name)){
					if(isset($object["itemId"])){
							$object["objId"]=$object["itemId"];
					}
					$objects[] = $object;
				}
		}
		return $objects;
	}

	function get_category_object($categId, $name = null, $type = "*") {
		$objects = $this->get_category_objects($categId, $name, $type);
		if (isset ($objects) && $objects != "" && count($objects) > 0) {
			return $objects[0];
		} else {
			return null;
		}
	}
	
	function get_category($categId, $name = null) {
		global $categlib;
		global $dbTiki;
		include_once ('lib/categories/categlib.php');
		$categlib2 = new CategLib($dbTiki);
		if (isset($categId) &&  isset($name)){
			$childs = $categlib2->get_child_categories($categId);
			if (isset($childs)){
				foreach ($childs as $key => $category) {
					if($category["name"]==$name){
						return $category;
					}
				}
			}
		}
		return null;
	}
}
?>
