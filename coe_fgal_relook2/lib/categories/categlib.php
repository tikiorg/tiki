<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Categories support class
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $objectlib;require_once("lib/objectlib.php");

class CategLib extends ObjectLib
{

	/* Returns an array of categories which are descendants of the category with the given $categId. If no category is given, all categories are returned.
	Each category is similar to a tiki_categories record, but with the following additional fields:
		"categpath" is a string representing the path to the category in the category tree, ordered from the ancestor to the category. Each category is separated by "::". For example, "Tiki" could have categpath "Software::Free software::Tiki". If a category is given, it is considered the root of the category tree for building categpath.
		"tepath" is an array representing the path to the category in the category tree, ordered from the ancestor to the category. Each element is the name of the represented category.
		"children" is the number of categories the category has as children.
		"objects" is the number of objects directly in the category. 
	If $all is set to false, only first level children obtained. */
	function list_categs($categId=0, $showWS=false, $all = true) {
		$back = $this->get_all_categories_ext($showWS);

		if ($categId > 0) {
			$path = '';
			$back2 = array();
			foreach ($back as $cat) {
				if ($cat['categId'] == $categId)
					$path = $cat['categpath'].'::';
				else if (($all == true || $cat['parentId'] == $categId) && ($path != '' && strpos($cat['categpath'], $path) === 0)) {
					$cat['categpath'] = substr($cat['categpath'], strlen($path));
					$back2[] = $cat;
				}
			}
			return $back2;
		} else {
			return $back;
		}
	}
	/* Similar to list_categs, but gets info for the category ids themselves, not descendants
	 * $categIds can be an array.
	 * Specifiy a common ancestor category ID in $top to remove the top level from the category path
	 */
	function get_category_info($categIds, $showWS=false, $top=null) {
		$back = $this->get_all_categories_ext($showWS);
		$i = 0;
		$cut = '';
		foreach ($back as $cat) {
			$catkey = $cat['categId'];
			if (isset($top)) {
				if ($top == $cat['categId']) {
					$cut = $cat['categpath'].'::';
				} elseif ($cut != '' && strpos($cat['categpath'], $cut) === 0){
					$cat['categpath'] = substr($cat['categpath'], strlen($cut));
				}
			}
			$catlist["$catkey"] = $cat;
			$catlist["$catkey"]['order'] = $i;
			$i++;
		}
		if (is_array($categIds)) {
			foreach ($categIds as $ids) {
				$order = $catlist["$ids"]['order'];
				$catinfo[$order] = $catlist["$ids"];
			}
			ksort($catinfo);
			$catinfo = array_values($catinfo);
		} else {
			$catinfo[0] = $catlist["$categIds"];
		}
		return $catinfo;
	}

	function list_all_categories($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid, $showWS = false, $listOnlyWS = false) {
		$cats = $this->get_object_categories($type, $objid);

		if ($find) {
			$findesc = '%' . $find . '%';
			$bindvals=array($findesc,$findesc);
			$mid = " where (`name` like ? or `description` like ?)";
		} else {
		    $bindvals=array();
		    $mid = "";
		}
		
		global $prefs; if(!$prefs) require_once 'lib/setup/prefs.php';
		$exclude = $this->exclude_categs ($prefs['ws_container'], $find, $showWS);
		if (!empty($exclude)) $bindvals[] = $prefs['ws_container'];

		if ($listOnlyWS)
		{
		    $query = "select * from `tiki_categories` where `rootCategId`=?";
		    $query_cant = "select count(*) from `tiki_categories` where `rootCategId`=?";
		} else {
		    $query = "select * from `tiki_categories` $mid $exclude order by ".$this->convertSortMode($sort_mode);
		    $query_cant = "select count(*) from `tiki_categories` $mid $exclude";
		}
		
		$result = $this->query($query,$bindvals,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvals);
		$ret = array();

		while ($res = $result->fetchRow()) {
		  if (!empty($cats) && in_array($res["categId"], $cats)) {
				$res["incat"] = 'y';
			} else {
				$res["incat"] = 'n';
			}
      
			$catpath = $this->get_category_path($res["categId"]);
			$tepath = array();	
			foreach ($catpath as $cat) {
				$tepath[] = $cat['name'];
			}
			$categpath = implode("::",$tepath);
			$categpathforsort = implode("!!",$tepath); // needed to prevent cat::subcat to be sorted after cat2::subcat 
			$res["categpath"] = $categpath;
			$res["tepath"] = $tepath;
			$res["deep"] = count($tepath);
			$res['name'] = $this->get_category_name($res['categId']);
			global $userlib;
			if ($userlib->object_has_one_permission($res['categId'], 'category')) {
				$res['has_perm'] = 'y';
			} else {
				$res['has_perm'] = 'n';
			}
			$ret["$categpathforsort"] = $res;
		}

		ksort($ret);
		
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}
	
	//With this you can exclude certain types of categories (i.e ws)
	function exclude_categs ($excludeCategId, $find, $showWS = false)
	{
	    if ($excludeCategId)
	    {
	    	if ($showWS)
			{
				if ($find)
					$exclude = "and `rootCategId` = ?";
				else
					$exclude = "where `rootCategId` = ?";
			}
			else
			{
				if ($find)
						$exclude = "and `rootCategId` is NULL and `categId` != ?";
				else
						$exclude = "where `rootCategId` is NULL and `categId` != ?";
			}
	    }
	    else
			$exclude = "";
	    return $exclude;
	}

	function get_category_path_string($categId) {
		global $cachelib; include_once('lib/cache/cachelib.php');
		$categs = $this->get_category_cache();
		foreach ($categs as $cat) {
			if ($cat['categId'] == $categId) {
				return $cat['categpath'];
			}
		}
		return '';
	}

	// Returns an array of ancestors of the category with the given $categId and the given category itself.
	// The path is ordered starting from the category tree root and ending with the given category.
	// Each category is represented by an array with the category ID at index "categId" and the name at index "name".
	function get_category_path($categId) {
		$info = $this->get_category($categId);
		$i=999999;
		$path[$i--] = array('categId'=>$info["categId"],'name'=>$info["name"]);
		while ($info["parentId"] != 0) {
			$info = $this->get_category($info["parentId"]);
			$path[$i--] = array('categId'=>$info["categId"],'name'=>$info["name"]);
		}
		ksort($path);
		return array_values($path);
	}

	function get_category($categId) {
		if(!isset($this->category_cache) || !isset($this->category_cache[$categId])) {
			$this->update_category_cache($categId);
		}
		return $this->category_cache[$categId];
	}
	
	function get_category_id($name){
		$query = "select `categId` from `tiki_categories` where `name`=?";
		return $this->getOne($query,array((string)$name));
		
	
	}
	function get_category_name($categId,$real=false) {
	    if ( $categId==0 ) return 'Top';   
		$query = "select `name`,`parentId` from `tiki_categories` where `categId`=?";
		$result=$this->query($query,array((int) $categId)) ;
		$res = $result->fetchRow();
		if ($real) return $res['name'];
		if (preg_match('/^Tracker ([0-9]+)$/',$res['name'])) {
		    $trackerId=preg_replace('/^Tracker ([0-9]+)$/',"$1",$res['name']);
		    return $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?",array((int) $trackerId));
		}
		if (preg_match('/^Tracker Item ([0-9]+)$/',$res['name'])) {
		    global $trklib;require_once('lib/trackers/trackerlib.php');
		    $itemId=preg_replace('/^Tracker Item ([0-9]+)$/',"$1",$res['name']);
		    return $trklib->get_isMain_value(-1,$itemId);
		}
		return $res['name'];
	}
	
	function remove_category($categId) {
		global $cachelib; include_once('lib/cache/cachelib.php');

		$parentId=$this->get_category_parent($categId);
		$categoryName=$this->get_category_name($categId);
		$categoryPath=$this->get_category_path_string_with_root($categId);
		$description=$this->get_category_description($categId);
		$rootCategId=$this->get_category_rootCategId($categId);

		$query = "delete from `tiki_categories` where `categId`=?";
		$result = $this->query($query,array((int) $categId));
		$query = "select `catObjectId` from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query,array((int) $categId));

		while ($res = $result->fetchRow()) {
			$object = $res["catObjectId"];

			$query_cant = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
			$cant = $this->getOne($query_cant,array($object));
			if ($cant <= 1) {
			$query2 = "delete from `tiki_categorized_objects` where `catObjectId`=?";
			$result2 = $this->query($query2,array($object));
			}
		}
		
		// remove any permissions assigned to this category
		$type = 'category';
		$object = $type . $categId;
		$query = "delete from `users_objectpermissions` where `objectId`=? and `objectType`=?";
		$result = $this->query($query,array(md5($object),$type));

		$query = "delete from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query,array((int) $categId));
		$query = "select `categId` from `tiki_categories` where `parentId`=?";
		$result = $this->query($query,array((int) $categId));

		while ($res = $result->fetchRow()) {
			// Recursively remove the subcategory
			$this->remove_category($res["categId"]);
		}
		
		if (empty($rootCategId)) {
			$cachelib->invalidate('allcategs');
		}
		else {
			$cachelib->invalidate('allws');
		}
		
		$cachelib->empty_type_cache('fgals_perms');
		$cachelib->invalidate("allcategs$categId");

	
		$values= array("categoryId"=>$categId, "categoryName"=>$categoryName, "categoryPath"=>$categoryPath,
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category removed");		
		$this->notify($values);

		$this->remove_category_from_watchlists($categId);
					
		return true;
	}

	function update_category($categId, $name, $description, $parentId) {
		global $cachelib; include_once('lib/cache/cachelib.php');

		$oldCategory=$this->get_category($categId);
		$oldCategoryName=$oldCategory['name'];
		$oldCategoryPath=$this->get_category_path_string_with_root($categId);
		$oldDescription=$oldCategory['description'];
		$oldParentId=$oldCategory['parentId'];
		$oldParentName=$this->get_category_name($oldParentId);
		$rootCategId=$this->get_category_rootCategId($categId);

		$query = "update `tiki_categories` set `name`=?, `parentId`=?, `description`=? where `categId`=?";
		$result = $this->query($query,array($name,(int) $parentId,$description,(int) $categId));
		if (empty($rootCategId)) {
			$cachelib->invalidate('allcategs');
		}
		else {
			$cachelib->invalidate('allws');
		}
		$cachelib->empty_type_cache('fgals_perms');
		$cachelib->invalidate('childcategs'.$parentId);

		$this->update_category_cache($categId);
		$values= array("categoryId"=>$categId, "categoryName"=>$name, "categoryPath"=>$this->get_category_path_string_with_root($categId),
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category updated","oldCategoryName"=>$oldCategoryName, "oldCategoryPath"=>$oldCategoryPath,
			"oldDescription"=>$oldDescription, "oldParentId" => $parentId, "oldParentName" => $oldParentName);			
		$this->notify($values);		
	}

	function add_category($parentId, $name, $description, $rootCategId = null) {
		global $cachelib; include_once('lib/cache/cachelib.php');
		$query = "insert into `tiki_categories`(`name`,`description`,`parentId`,`hits`, `rootCategId`) values(?,?,?,?,?)";
		$result = $this->query($query,array($name,$description,(int) $parentId, 0, $rootCategId));
		$query = "select `categId` from `tiki_categories` where `name`=? and `parentId`=? order by `categId` desc";
		$id = $this->getOne($query,array($name,(int) $parentId));
		if (empty($rootCategId)) {
			$cachelib->invalidate('allcategs');
		}
		else {
			$cachelib->invalidate('allws');
		}
		$cachelib->empty_type_cache('fgals_perms');
		$cachelib->invalidate('childcategs'.$parentId);
		$values= array("categoryId"=>$id, "categoryName"=>$name, "categoryPath"=> $this->get_category_path_string_with_root($id),
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category created");		
		$this->notify($values);		 	
		return $id;
	}

	function is_categorized($type, $itemId) {
		if ( empty($itemId) ) return 0;

		global $cachelib; include_once('lib/cache/cachelib.php');
		if ( count( $this->get_category_cache() ) == 0 ) {
			return 0;
		}

		$query = "select o.`objectId` from `tiki_categorized_objects` c, `tiki_objects` o, `tiki_category_objects` tco where c.`catObjectId`=o.`objectId` and o.`type`=? and o.`itemId`=? and tco.`catObjectId`=c.`catObjectId`";
		$bindvars = array($type,$itemId);
		settype($bindvars["1"],"string");
		$result = $this->query($query,$bindvars);

		if ( $result->numRows() ) {
			$res = $result->fetchRow();
			return $res["objectId"];
		} else {
			return 0;
		}
	}

	function add_categorized_object($type, $itemId, $description, $name, $href) {
		global $cachelib; include_once('lib/cache/cachelib.php');

		$id = $this->add_object($type, $itemId, $description, $name, $href);
		
		$query = "select `catObjectId` from `tiki_categorized_objects` where `catObjectId`=?";
		if (!$this->getOne($query, array($id))) {
			$query = "insert into `tiki_categorized_objects` (`catObjectId`) values (?)";
			$this->query($query, array($id));
		}
		$cachelib->invalidate('allcategs');
		$cachelib->empty_type_cache('fgals_perms');
		return $id;
	}

	function categorize($catObjectId, $categId) {
		global $prefs;
		if (empty($categId)) {
			return;
		}
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query,array((int) $catObjectId,(int) $categId),-1,-1,false);
	        
		$query = "insert into `tiki_category_objects`(`catObjectId`,`categId`) values(?,?)";
		$result = $this->query($query,array((int) $catObjectId,(int) $categId));

		global $cachelib;
		$cachelib->invalidate("allcategs");
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			global $objectlib; include_once('lib/objectlib.php');
			$info = $objectlib->get_object_via_objectid($catObjectId);
			$logslib->add_action('Categorized', $info['itemId'], $info['type'], "categId=$categId");
		}
	}

	function uncategorize($catObjectId, $categId) {
		global $prefs;
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query,array((int) $catObjectId,(int) $categId),-1,-1,false);

		global $cachelib;
		$cachelib->invalidate("allcategs");
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			global $objectlib; include_once('lib/objectlib/php');
			$info = $objectlib->get_object_via_objectid($catObjectId);
			$logslib->add_action('Uncategorized', $info['itemId'], $info['type'], "categId=$categId");
		}	}

	function get_category_descendants($categId) {
		global $user,$userlib;
		$query = "select `categId` from `tiki_categories` where `parentId`=?";

		$result = $this->query($query,array((int) $categId));
		$ret = array($categId);

		while ($res = $result->fetchRow()) {
			$ret[] = $res["categId"];
			$aux = $this->get_category_descendants($res["categId"]);
			$ret = array_merge($ret, $aux);
		}

		$ret = array_unique($ret);
		return array_values( $ret );
	}

	// Returns a hash indicating which permission is needed for viewing an object of desired type.
	function map_object_type_to_permission() {
	    return array('wiki page' => 'tiki_p_view',
			 'wiki' => 'tiki_p_view',
			 'forum' => 'tiki_p_forum_read',
	    	 'forum post' => 'tiki_p_forum_read',
			 'image gallery' => 'tiki_p_view_image_gallery',
			 'file gallery' => 'tiki_p_view_file_gallery',
			 'tracker' => 'tiki_p_view_trackers',
			 'blog' => 'tiki_p_read_blog',
			 'blog post' => 'tiki_p_read_blog',
			 'quiz' => 'tiki_p_take_quiz',

			 // overhead - we are checking individual permission on types below, but they
			 // can't have individual permissions, although they can be categorized.
			 // should they have permissions too?
			 'poll' => 'tiki_p_vote_poll',
			 'survey' => 'tiki_p_take_survey',
			 'directory' => 'tiki_p_view_directory',
			 'faq' => 'tiki_p_view_faqs',
			 'sheet' => 'tiki_p_view_sheet',

			 // these ones are tricky, because permission type is for container, not object itself.
			 // I think we need to refactor permission schemes for them to be wysiwyca - lfagundes
			 //
			 // by now they're not showing, list_category_objects needs support for ignoring permissions
			 // for a type.
			 'article' => 'tiki_p_read_article',
			 'submission' => 'tiki_p_approve_submission',
			 'image' => 'tiki_p_view_image_gallery',
			 'calendar' => 'tiki_p_view_calendar',
			 'file' => 'tiki_p_download_files',
			 'trackeritem' => 'tiki_p_view_trackers',
			 
			 // newsletters can't be categorized, although there's some code in tiki-admin_newsletters.php
			 // 'newsletter' => ?,
			 // 'events' => ?,
			 );
	}

	function list_category_objects($categId, $offset, $maxRecords, $sort_mode='pageName_asc', $type='', $find='', $deep=false, $and=false) {
		global $userlib, $prefs;
		if ($prefs['feature_sefurl'] == 'y') {include_once('tiki-sefurl.php');}
		if ($prefs['feature_trackers'] == 'y') {global $trklib;require_once('lib/trackers/trackerlib.php');}
	    
	    // Build the condition to restrict which categories objects must be in to be returned.
	    $join = '';
	    if (is_array($categId) && $and) {
			$categId = $this->get_jailed( $categId );
			$i = count($categId);
			$bindWhere = $categId;
			foreach ($categId as $c) {
				if (--$i)
					$join .= " INNER JOIN tiki_category_objects tco$i on (tco$i.`catObjectId`=o.`catObjectId` and tco$i.`categId`=?) ";
			}
			$where = ' AND c.`categId`=? ';
	   } elseif (is_array($categId)) {
			$bindWhere = $categId;
			if ($deep) {
				foreach ($categId as $c) {
					$bindWhere = array_merge($bindWhere, $this->get_category_descendants($c));
				}				
			}

			$bindWhere = $this->get_jailed( $bindWhere );
			$bindWhere[] = -1;

			$where = " AND c.`categId` IN (".str_repeat("?,",count($bindWhere)-1)."?)";
	    } else {
			if ($deep) {
				$bindWhere = $this->get_category_descendants($categId);
				$bindWhere[] = $categId;
				$bindWhere = $this->get_jailed( $bindWhere );
				$bindWhere[] = -1;
				$where = " AND c.`categId` IN (".str_repeat("?,",count($bindWhere)-1)."?)";
			} else {
				$bindWhere = array($categId);
				$where = ' AND c.`categId`=? ';
			}
	    }

	        // Restrict results by keyword
		if ($find) {
			$findesc = '%' . $find . '%';
			$bindWhere[]=$findesc;
			$bindWhere[]=$findesc;
			$where .= " AND (`name` LIKE ? OR `description` LIKE ?)";
		} 
		if (!empty($type)) {
			if (array($type)) {
				$where .= ' AND `type` in ('.implode(',',array_fill(0,count($type),'?')).')';
				$bindWhere = array_merge($bindWhere, $type);
			} else {
				$where .= ' AND `type` =? ';
				$bindWhere[] = $type;
			}
		}

		$bindVars = $bindWhere;

		$orderBy = '';
		if ($sort_mode) {
			if ($sort_mode != 'shuffle') {
				$orderBy = " ORDER BY ".$this->convertSortMode($sort_mode);
			}
		}

		// Fetch all results as was done before, but only do it once
		$query_cant = "SELECT DISTINCT c.*, o.* FROM `tiki_category_objects` c, `tiki_categorized_objects` co, `tiki_objects` o WHERE c.`catObjectId`=o.`objectId` AND o.`objectId`=co.`catObjectId` $where";
		$query = $query_cant . $orderBy;
		$result = $this->fetchAll($query,$bindVars);
		$cant = count($result);

		if ($sort_mode == 'shuffle') {
			shuffle($ret);
		}

		return $this->filter_object_list($result, $cant, $offset, $maxRecords);
	}
		
	private function filter_object_list($result, $cant, $offset, $maxRecords) {
		global $user, $prefs;
		$permMap = $this->map_object_type_to_permission();
		$groupList = $this->get_user_groups($user);

		// Filter based on permissions
		$contextMap = array( 'type' => 'type', 'object' => 'itemId' );
		$contextMapMap = array_fill_keys( array_keys( $permMap ), $contextMap );
		$result = Perms::mixedFilter( array(), 'type', 'object', $result, $contextMapMap, $permMap );
		
		if( $maxRecords == -1 ) {
			$maxRecords = $cant;
		}

		// Capture only the required portion
		$result = array_slice( $result, $offset, $maxRecords );

		$ret = array();
		$objs = array();

		foreach( $result as $res ) {
			if (!in_array($res['catObjectId'].'-'.$res['categId'], $objs)) { // same object and same category
				if (preg_match('/trackeritem/',$res['type'])&&$res['description']=='') {
					global $trklib; include_once('lib/trackers/trackerlib.php');
					$trackerId=preg_replace('/^.*trackerId=([0-9]+).*$/','$1',$res['href']);
					$res['name']=$trklib->get_isMain_value($trackerId,$res['itemId']);
					$filed=$trklib->get_field_id($trackerId,"description");
					$res['description']=$trklib->get_item_value($trackerId,$res['itemId'],$filed);
					if (empty($res['description'])) {
						$res['description']=$this->getOne("select `name` from `tiki_trackers` where `trackerId`=?",array((int) $trackerId));
					}
				}
				if ($prefs['feature_sefurl'] == 'y') {
					$type = $res['type'] == 'wiki page'? 'wiki': $res['type'];
					$res['sefurl'] = filter_out_sefurl($res['href'], $smarty, $type);
				}
				if (empty($res['name'])) {
					$res['name'] = '#' . $res['itemId'];
				}
				$ret[] = $res;
				$objs[] = $res['catObjectId'].'-'.$res['categId'];
			}
		}

		return array(
			"data" => $ret,
			"cant" => $cant,
		);
	}

	function list_orphan_objects($offset, $maxRecords, $sort_mode) {
		$orderClause = $this->convertSortMode($sort_mode);

		$common = "
			FROM
				tiki_objects
				LEFT JOIN tiki_category_objects ON objectId = catObjectId
			WHERE
				catObjectId IS NULL
			ORDER BY $orderClause
			";

		$query = "SELECT objectId catObjectId, 0 categId, type, itemId, name, href $common";
		$queryCount = "SELECT COUNT(*) $common";
		
		$result = $this->fetchAll($query, array(), $maxRecords, $offset);
		$count = $this->getOne($queryCount);

		return $this->filter_object_list($result, $count, $offset, $maxRecords);
	}

	// get the parent categories of an object
	function get_object_categories($type, $itemId,$parentId=-1, $jailed = true) {
		$ret = array();
		if (!$itemId)
			return $ret;
		if ($parentId == -1){
			$query = "select `categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto, `tiki_objects` o
				where tco.`catObjectId`=tto.`catObjectId` and o.`objectId`=tto.`catObjectId` and o.`type`=? and `itemId`=?";
			//settype($itemId,"string"); //itemId is defined as varchar
			$bindvars = array("$type",$itemId);
		} else {
			$query = "select tc.`categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto, `tiki_objects` o,`tiki_categories` tc
    		where tco.`catObjectId`=tto.`catObjectId` and o.`objectId`=tto.`catObjectId` and o.`type`=? and `itemId`=? and tc.`parentId` = ? and tc.`categId`=tco.`categId`";
			$bindvars = array("$type",$itemId,(int)$parentId);
		}
		$result = $this->query($query,$bindvars);
		while ($res = $result->fetchRow()) {
			$ret[] = $res["categId"];
		}

		if( $jailed ) {
			return $this->get_jailed( $ret );
		} else {
			return $ret;
		}
	}

	// Get all the objects in a category
	// filter = array('table'=>, 'join'=>, 'filter'=>, 'bindvars'=>)
	function get_category_objects($categId, $type=null, $filter = null) {
		$bindVars[] = (int)$categId;
		if (!empty($type)) {
			$where = ' and o.`type`=?';
			$bindVars[] = $type;
		} else {
			$where = '';
		}
		if (!empty($filter)) {
			$from = ',`'.$filter['table'].'` ft';
			$where .= ' and o.`itemId`=ft.`'.$filter['join'].'` and ft.`'.$filter['filter'].'`=?';
			$bindVars[] .= $filter['bindvars'];
		} else {
			$from = '';
		}
		$query = "select * from `tiki_category_objects` c,`tiki_categorized_objects` co, `tiki_objects` o $from where c.`catObjectId`=co.`catObjectId` and co.`catObjectId`=o.`objectId` and c.`categId`=?".$where;
		return $this->fetchAll($query, $bindVars);
	}

	// Removes the object with the given identifer from the category with the given identifier
	function remove_object_from_category($catObjectId, $categId) {
		$this->remove_object_from_categories($catObjectId, array($categId));
	}

	// Removes the object with the given identifer from the categories specified in the $categIds array. The array contains category identifiers.
	function remove_object_from_categories($catObjectId, $categIds) {
		if (!empty($categIds)) {
			global $cachelib; include_once('lib/cache/cachelib.php');
			$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId` in (".implode(',',array_fill(0,count($categIds),'?')).")";
			$result = $this->query($query,array_merge(array($catObjectId), $categIds));
			$query = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
			$cant = $this->getOne($query,array((int) $catObjectId));
			if (!$cant) {
				$query = "delete from `tiki_categorized_objects` where `catObjectId`=?";
				$result = $this->query($query,array((int) $catObjectId));
			}
			$cachelib->invalidate('allcategs');
			$cachelib->empty_type_cache('fgals_perms');
		}
	}

	// FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS ////
	function categorize_any( $type, $identifier, $categId )
	{
		switch( $type )
		{
		case 'wiki page':
		case 'wikipage':
		case 'wiki_page':
			return $this->categorize_page( $identifier, $categId );
		case 'tracker':
			return $this->categorize_tracker( $identifier, $categId );
		case 'quiz':
			return $this->categorize_quiz( $identifier, $categId );
		case 'article':
			return $this->categorize_article( $identifier, $categId );
		case 'faq':
			return $this->categorize_faq( $identifier, $categId );
		case 'blog':
			return $this->categorize_blog( $identifier, $categId );
		case 'directory':
			return $this->categorize_directory( $identifier, $categId );
		case 'gallery':
		case 'gal':
			return $this->categorize_gallery( $identifier, $categId );
		case 'file_gallery':
		case 'file gallery':
		case 'fgal':
			return $this->categorize_file_gallery( $identifier, $categId );
		case 'forum':
			return $this->categorize_forum( $identifier, $categId );
		case 'poll':
			return $this->categorize_poll( $identifier, $categId );
		case 'calendar':
			return $this->categorize_calendar( $identifier, $categId );
		case 'trackeritem':
			return $this->categorize_trackeritem($identifier, $categId);
		}
	}

	// Categorize the Wiki page with the given name in the categories specified in the second parameter. $categIds can be a category ID or an array of category IDs.
	function categorize_page($pageName, $categIds) {
		// Check if we already have this object in the tiki_categorized_objects page

		$catObjectId = $this->is_categorized('wiki page', $pageName);

		if (!$catObjectId) {
			// The page is not cateorized
			if (!($info = $this->get_page_info($pageName)))
				return;
			$href = 'tiki-index.php?page=' . urlencode($pageName);
			$catObjectId = $this->add_categorized_object('wiki page', $pageName, substr($info["description"], 0, 200), $pageName, $href);
		}

		if (!is_array($categIds)) $categIds=array($categIds);
		foreach($categIds as $categId) {
			$this->categorize($catObjectId, $categId);
		}

		return $catObjectId;
	}
	
	function categorize_tracker($trackerId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page

		$catObjectId = $this->is_categorized('tracker', $trackerId);

		if (!$catObjectId) {
			global $trklib; include_once('lib/trackers/trackerlib.php');
			$info = $trklib->get_tracker($trackerId);

			$href = 'tiki-view_tracker.php?trackerId=' . $trackerId;
			$catObjectId = $this->add_categorized_object('tracker', $trackerId, substr($info["description"], 0, 200),$info["name"] , $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
	}

	function categorize_trackeritem($itemId, $categId) {
		$catObjectId = $this->is_categorized('trackeritem', $itemId);

		if (!$catObjectId) {
			global $trklib; include_once('lib/trackers/trackerlib.php');
			$info = $trklib->get_tracker_item($itemId);
			$href = "tiki-view_tracker_item.php?itemId=$itemId&trackerId=".$info['trackerId'];
			$name = $trklib->get_isMain_value($info['trackerId'], $itemId);
			$catObjectId = $this->add_categorized_object('trackeritem', $itemId, '',$name , $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
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
		return $catObjectId;
	}

	function categorize_article($articleId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('article', $articleId);

		if (!$catObjectId) {
			global $artlib; require_once 'lib/articles/artlib.php';
			// The page is not cateorized
			$info = $artlib->get_article($articleId);

			$href = 'tiki-read_article.php?articleId=' . $articleId;
			$catObjectId = $this->add_categorized_object('article', $articleId, $info["heading"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
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
		return $catObjectId;
	}

	function categorize_blog($blogId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('blog', $blogId);

		if (!$catObjectId) {
			global $bloglib; require_once('lib/blogs/bloglib.php');
			// The page is not cateorized
			$info = $bloglib->get_blog($blogId);

			$href = 'tiki-view_blog.php?blogId=' . $blogId;
			$catObjectId = $this->add_categorized_object('blog', $blogId, $info["description"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
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
		return $catObjectId;
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
		return $catObjectId;
	}

	function categorize_file_gallery($galleryId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('file gallery', $galleryId);

		if (!$catObjectId) {
			$filegallib = TikiLib::lib('filegal');

			// The page is not cateorized
			$info = $filegallib->get_file_gallery($galleryId);

			$href = 'tiki-list_file_gallery.php?galleryId=' . $galleryId;
			$catObjectId = $this->add_categorized_object('file gallery', $galleryId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
	}

	function categorize_forum($forumId, $categId) {
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('forum', $forumId);
				
		if (!$catObjectId) {

			global $commentslib;
			if (!is_object($commentslib)) {
				require_once('lib/comments/commentslib.php');
				$commentslib = new Comments;
			}
			// The page is not cateorized
			$info = $commentslib->get_forum($forumId);

			$href = 'tiki-view_forum.php?forumId=' . $forumId;
			$catObjectId = $this->add_categorized_object('forum', $forumId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
	}

	function categorize_poll($pollId, $categId) {
		global $polllib;
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('poll', $pollId);
		if (!$catObjectId) {
			if (!is_object($polllib)) {
				require_once('lib/polls/polllib_shared.php');
			}
			// The page is not cateorized
			$info = $polllib->get_poll($pollId);

			$href = 'tiki-poll_form.php?pollId=' . $pollId;
			$catObjectId = $this->add_categorized_object('poll', $pollId, $info["title"], $info["title"], $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
	}
	
	function categorize_calendar($calendarId, $categId) {
		global $calendarlib;
		// Check if we already have this object in the tiki_categorized_objects page
		$catObjectId = $this->is_categorized('calendar', $calendarId);
		if (!$catObjectId) {
			if (!is_object($calendarlib)) {
				require_once('lib/calendar/calendarlib.php');
			}
			// The page is not cateorized
			$info = $calendarlib->get_calendar($calendarId);

			$href = 'tiki-calendar.php?calId=' . $calendarId;
			$catObjectId = $this->add_categorized_object('calendar', $calendarId, $info["description"], $info["name"], $href);
		}

		$this->categorize($catObjectId, $categId);
		return $catObjectId;
	}

	// FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS END ////
	
	/*Set $all_descends to true to get all descendent categories, otherwise only first level children
	 * Should consider combining with list_categs
	 */
	function get_child_categories($categId, $all_descends = false) {
		global $cachelib; include_once('lib/cache/cachelib.php');
		global $prefs;
		if (!$categId) $categId = "0"; // avoid wrong cache
		if ($all_descends) {
			$cachekey = "allchildcategs$categId";
		} else {
			$cachekey = "childcategs$categId";
		}
		if( ! $ret = $cachelib->getSerialized("$cachekey") ) {
			$ret = $this->list_categs($categId, false, $all_descends);
			$cachelib->cacheItem($cachekey,serialize($ret));
		}
		if ($prefs['feature_multilingual'] == 'y' && $prefs['language'] != 'en') {
			foreach ($ret as $key=>$res) {
				$ret[$key]['name'] = tra($res['name']);
			}
		}
		return $ret;
	}
	function get_viewable_child_categories($categId, $all_descends = false) {
		$alls = $this->get_child_categories($categId, $all_descends);
		if (empty($alls)) {
			return $alls;
		}
		return Perms::filter( array( 'type' => 'category' ), 'object', $alls, array( 'object' => 'categId' ), 'view_category' );
	}

	function get_all_categories($showWS = false) {
		global $cachelib; include_once('lib/cache/cachelib.php');
	/*
		// inhibited because allcateg_ext is cached now
		$query = " select `name`,`categId`,`parentId` from `tiki_categories` order by `name`";
		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
	*/
		return $this->get_all_categories_ext($showWS);
	}

	/* Returns an array of categories and caches it in cache item "allcategs".
	Each category is similar to a tiki_categories record, but with the following additional fields:
		"categpath" is a string representing the path to the category in the category tree, ordered from the ancestor to the category. Each category is separated by "::". For example, "Tiki" could have categpath "Software::Free software::Tiki".
		"tepath" is an array representing the path to the category in the category tree, ordered from the ancestor to the category. Each element is the name of the represented category.
		"children" is the number of categories the category has as children.
		"objects" is the number of objects directly in the category. */
	function build_cache($showWS = false) {
		global $cachelib; include_once('lib/cache/cachelib.php');
		$ret = array();
		
		global $prefs; if(!$prefs) require_once 'lib/setup/prefs.php';
		$exclude = $this->exclude_categs ($prefs['ws_container'], "", $showWS);
		$query = "select * from `tiki_categories` $exclude order by `name` asc, `categId` desc";
		if (!empty($prefs['ws_container']))
			$bindvals = array($prefs['ws_container']);
		else
			$bindvals = array();
		$result = $this->query($query,$bindvals);

		while ($res = $result->fetchRow()) {
			$id = $res["categId"];
			$catpath = $this->get_category_path($id);
			$tepath = array();
			foreach ($catpath as $cat) {
				$tepath[] = $cat['name'];
			}
			$categpath = implode("::",$tepath);
			$categpathforsort = implode("!!",$tepath); // needed to prevent cat::subcat to be sorted after cat2::subcat
			$res["categpath"] = $categpath;
			$res["tepath"] = $tepath;
			$query = "select count(*) from `tiki_categories` where `parentId`=?";
			$res["children"] = $this->getOne($query,array($id));
			$query = "select count(*) from `tiki_category_objects` where `categId`=?";
			$res["objects"] = $this->getOne($query,array($id));
			$ret[$categpathforsort] = $res;
		}
		ksort($ret);
		$ret = array_values($ret);
		if ($showWS)
			$cachelib->cacheItem("allws",serialize($ret));
		else
			$cachelib->cacheItem("allcategs",serialize($ret));
		return $ret;
	}

	private function get_category_cache() {
		global $cachelib;
		if( ! $ret = $cachelib->getSerialized("allcategs") ) {
			$ret = $this->build_cache(false);
		}

		return $ret;
	}

	// Same as get_all_categories + it also get info about count of objects
	function get_all_categories_ext($showWS = false) {

		global $cachelib; include_once('lib/cache/cachelib.php');
		if ($showWS) {
			if( ! $ret = $cachelib->getSerialized("allws") ) {
				$ret = $this->build_cache(true);
			}
		} else {
			$ret = $this->get_category_cache();
		}

		if( $jail = $this->get_jail() ) {
			$prefilter = $ret;
			$ret = array();

			foreach( $prefilter as $res ) {
				if( in_array( $res['categId'], $jail ) ) {
					$ret[] = $res;
				}
			}
		}

		return $ret;
	}

	function get_all_categories_respect_perms($user, $perm, $showWS = false) {
		$result = $this->get_all_categories_ext($showWS);
		return Perms::filter( array( 'type' => 'category' ), 'object', $result, array( 'object' => 'categId' ), $perm );
	}

	
	// get categories related to a link. For Whats related module.
	function get_link_categories($link) {
		$ret=array();
		$parsed=parse_url($link);
		$urlPath = preg_split("#\/#",$parsed["path"]);
		$parsed["path"]=end($urlPath);
		if(!isset($parsed["query"])) return($ret);
		/* not yet used. will be used to get the "base href" of a page
		$params=array();
		$a = explode('&', $parsed["query"]);
		for ($i=0; $i < count($a);$i++) {
			$b = preg_split('/=/', $a[$i]);
			$params[htmlspecialchars(urldecode($b[0]))]=htmlspecialchars(urldecode($b[1]));
		}
		*/
		$query="select distinct co.`categId` from `tiki_objects` o, `tiki_categorized_objects` cdo, `tiki_category_objects` co  where o.`href`=? and cdo.`catObjectId`=co.`catObjectId` and o.`objectId` = cdo.`catObjectId`";
		$result=$this->query($query,array($parsed["path"]."?".$parsed["query"]));
		while ($res = $result->fetchRow()) {
		  $ret[]=$res["categId"];
		}
		return($ret);
	}

	// input is a array of category id's and return is a array of 
	// maxRows related links with description
	function get_related($categories,$maxRows=10) {
		global $tiki_p_admin;
		if(count($categories)==0) return (array());
		$quarr=implode(",",array_fill(0,count($categories),'?'));
		$query="select distinct o.`type`, o.`description`, o.`itemId`,o.`href` from `tiki_objects` o, `tiki_categorized_objects` cdo, `tiki_category_objects` co  where co.`categId` in (".$quarr.") and co.`catObjectId`=cdo.`catObjectId` and o.`objectId`=cdo.`catObjectId`";
		$result=$this->query($query,$categories);
		$ret=array();
		if ($tiki_p_admin != 'y')
			$permMap = $this->map_object_type_to_permission();
		while ($res = $result->fetchRow()) {
			if ($tiki_p_admin == 'y' || $this->user_has_perm_on_object($user, $res['itemId'], $res['type'], $permMap[$res['type']])) {
				if (empty($res["description"])) {
					$ret[$res["href"]]=$res["type"].": ".$res["itemId"];
				} else {
					$ret[$res["href"]]=$res["type"].": ".$res["description"];
				}
			}
		}
		if (count($ret)>$maxRows) {
			$ret2=array();
			$rand_keys = array_rand ($ret,$maxRows);
			foreach($rand_keys as $value) {
				$ret2[$value]=$ret[$value];
			}
			return($ret2);
		}
		return($ret);
	}
	
	// combines the two functions above
	function get_link_related($link,$maxRows=10) {
		return ($this->get_related($this->get_link_categories($link),$maxRows));
	}
	
	// Moved from tikilib.php
	function uncategorize_object($type, $id) {
		// Fixed query. -rlpowell
		$query = "select `catObjectId` from `tiki_categorized_objects` c, `tiki_objects` o where o.`objectId`=c.`catObjectId` and o.`type`=? and o.`itemId`=?";
		$catObjectId = $this->getOne($query, array((string) $type,(string) $id));

		if ($catObjectId) {
		    $query = "delete from `tiki_category_objects` where `catObjectId`=?";
		    $result = $this->query($query,array((int) $catObjectId));
			// must keep tiki_categorized object because poll or ... can use it
	    
		    // Refresh categories
		    global $cachelib; include_once('lib/cache/cachelib.php');
		    $cachelib->invalidate('allcategs');
        $cachelib->empty_type_cache('fgals_perms');
		}
    }

    // Moved from tikilib.php
    function get_categorypath($cats, $include_excluded=false) {
			global $smarty, $prefs;

			if ($include_excluded == false) {
				$excluded = preg_split('/,/', $prefs['categorypath_excluded']);
				$cats = array_diff($cats, $excluded);
			}			
			
			$catpath = '';
			foreach ($cats as $categId) {
				$catp = array();
				$info = $this->get_category($categId);
				if ($include_excluded == false && !in_array($info['categId'], $excluded)) {
					$catp[$info['categId']] = $info['name'];
				}
				while ($info["parentId"] != 0) {
					$info = $this->get_category($info["parentId"]);
					if ($include_excluded == false && !in_array($info['categId'], $excluded)) {
						$catp[$info['categId']] = $info['name'];
					}
				}
				$smarty->assign('catp',array_reverse($catp,true));
				$catpath .= $smarty->fetch('categpath.tpl');
			}
			return $catpath;
    }
    
    //Moved from tikilib.php
    // ###trebly:B01229:Test change $sort to name_asc : pb which other case than listcats
   // function get_categoryobjects($catids,$types="*",$sort='created_desc',$split=true,$sub=false,$and=false, $maxRecords = 500) {
  function get_categoryobjects($catids,$types="*",$sort='name_asc',$split=true,$sub=false,$and=false, $maxRecords = 500) {
		global $smarty, $prefs;

		$typetokens = array(
			"article" => "article",
			"blog" => "blog",
			"directory" => "directory",
			"faq" => "faq",
			"fgal" => "file gallery",
			"forum" => "forum",
			"igal" => "image gallery",
			"newsletter" => "newsletter",
			"poll" => "poll",
			"quiz" => "quiz",
			"survey" => "survey",
			"tracker" => "tracker",
			"wiki" => "wiki page",
			"calendar" => "calendar",
			"img" => "image"
		);	//get_strings tra("article");tra("blog");tra("directory");tra("faq");tra("file gallery");tra("forum");tra("image gallery");tra("newsletter");
			//get_strings tra("poll");tra("quiz");tra("survey");tra("tracker");tra("wiki page");tra("image");tra("calendar");
			
		$typetitles = array(
			"article" => "Articles",
			"blog" => "Blogs",
			"directory" => "Directories",
			"faq" => "FAQs",
			"file gallery" => "File Galleries",
			"forum" => "Forums",
			"image gallery" => "Image Galleries",
			"newsletter" => "Newsletters",
			"poll" => "Polls",
			"quiz" => "Quizzes",
			"survey" => "Surveys",
			"tracker" => "Trackers",
			"wiki page" => "Wiki",
			"calendar" => "Calendar",
			"image" => "Image"
		);

		$out = "";
		$listcat = $allcats = array();
		$title = '';
		$find = "";
		$offset = 0;
		$firstpassed = false;
		$typesallowed = array();
		if ($and) {
			$split = false;
		}
		if ($types == '*') {
			$typesallowed = array_keys($typetitles);
		} elseif (strpos($types,'+')) {
			$alltypes = preg_split('/\+/',$types);
			foreach ($alltypes as $t) {
				if (isset($typetokens["$t"])) {
					$typesallowed[] = $typetokens["$t"];
				} elseif (isset($typetitles["$t"])) {
					$typesallowed[] = $t;
				}
			}
		} elseif (isset($typetokens["$types"])) {
			$typesallowed = array($typetokens["$types"]);
		} elseif (isset($typetitles["$types"])) {
			$typesallowed = array($types);
		}
		// ###trebly:B01229:Test of a title of the lists
		$out=$smarty->fetch("categobjects_title.tpl");
		foreach ($catids as $id) {
			$titles["$id"] = $this->get_category_name($id);
			$objectcat = array();
			$objectcat = $this->list_category_objects($id, $offset, $and? -1: $maxRecord, $sort, $types == '*'? '': $typesallowed, $find, $sub);

			$acats = $andcat = array();
			foreach ($objectcat["data"] as $obj) {
				$type = $obj["type"];
				if (substr($type,0,7) == 'tracker') $type = 'tracker';
				if (($types == '*') || in_array($type,$typesallowed)) {
					if ($split or !$firstpassed) {
						$listcat["$type"][] = $obj;
						$cats[] = $type.'.'.$obj['name'];
					} elseif ($and) {
						if (in_array($type.'.'.$obj['name'], $cats)) {
							$andcat["$type"][] = $obj;
							$acats[] = $type.'.'.$obj['name'];
						}
					} else {
						if (!in_array($type.'.'.$obj['name'], $cats)) {
							$listcat["$type"][] = $obj;
							$cats[] = $type.'.'.$obj['name'];
						}
					}
				}
			}
			if ($split) {
				$smarty->assign("id", $id);
				$smarty->assign("titles", $titles);
				$smarty->assign("listcat", $listcat);
				$smarty->assign("one", count($listcat));
				// ###trebly:B01229 test sur display des objets de même catégorie
				//$out .= echo('<br />Titre de la liste des objets de catégories <br />').$smarty->fetch("categobjects.tpl");
				$out .= $smarty->fetch("categobjects.tpl");
				$listcat = array();
				$titles = array();
				$cats = array();
			} elseif ($and and $firstpassed) {
				$listcat = $andcat;
				$cats = $acats;
			}
			$firstpassed = true;
		}
		if (!$split) {
			$smarty->assign("id", $id);
			$smarty->assign("titles", $titles);
			$smarty->assign("listcat", $listcat);
			$smarty->assign("one", count($listcat));
			$out = $smarty->fetch("categobjects.tpl");
		}
		return $out;
	}
	
	// Returns an array representing the last $maxRecords objects in the category with the given $categId of the given type, ordered by decreasing creation date. By default, objects of all types are returned.
	// Each array member is a string-indexed array with fields catObjectId, categId, type, name and href.
    function last_category_objects($categId, $maxRecords, $type="") {
		$mid = "and `categId`=?";
		$bindvars = array((int)$categId);
		if ($type) {
		    $mid.= " and `type`=?";
		    $bindvars[] = $type;
		}
		$sort_mode = "created_desc";
		$query = "select co.`catObjectId`, `categId`, `type`, `name`, `href` from `tiki_category_objects` co, `tiki_categorized_objects` cdo, `tiki_objects` o where co.`catObjectId`=cdo.`catObjectId` and o.`objectId`=cdo.`catObjectId` $mid order by o.".$this->convertSortMode($sort_mode);
		$ret = $this->fetchAll($query,$bindvars,$maxRecords,0);

		return array('data'=> $ret);
    }

    // Gets a list of categories that will block objects to be seen by user, recursive
    function list_forbidden_categories($parentId=0, $parentAllowed='', $perm='tiki_p_view_categorized') {
	global $user, $userlib;
	if (empty($parentAllowed)) {
	    global $tiki_p_view_categorized;
	    $parentAllowed = $tiki_p_view_categorized;
	}

	$query = "select `categId` from `tiki_categories` where `parentId`=?";
	$result = $this->query($query, array($parentId));

	$forbidden = array();

	while ($row = $result->fetchRow()) {
	    $child = $row['categId'];
	    if ($userlib->object_has_one_permission($child, 'category')) {
		if ($userlib->object_has_permission($user, $child, 'category', $perm)) {
		    $forbidden = array_merge($forbidden, $this->list_forbidden_categories($child, 'y', $perm));
		} else {
		    $forbidden[] = $child;
		    $forbidden = array_merge($forbidden, $this->list_forbidden_categories($child, 'n', $perm));
		}
	    } else {
		if ($parentAllowed != 'y') {
		    $forbidden[] = $child;
		}
		$forbidden = array_merge($forbidden, $this->list_forbidden_categories($child, $parentAllowed, $perm));
	    }
	}
	return $forbidden;
    }
	function approve_submission($subId, $articleId) {
		$query = "update `tiki_objects` set `type`= ?, `itemId`= ?, `href`=? where `itemId` = ? and `type`= ?";
		$this->query($query, array('article', (int)$articleId, "tiki-read_article.php?articleId=$articleId", (int)$subId, 'submission'));
	}
	/* build the portion of list join if filter by category
	 * categId can be a simple value, a list of values=>or between categ, array('AND'=>list values) for an AND
	 */
	function getSqlJoin($categId, $objType, $sqlObj, &$fromSql, &$whereSql, &$bindVars, $type = '?') {
		static $callno = 0;
		$callno++;
		$fromSql .= " inner join `tiki_objects` co$callno";
		$whereSql .= " AND co$callno.`type`=$type AND co$callno.`itemId`= $sqlObj ";
		if( $type == '?' ) {
			$bind = array($objType);
		} else {
			$bind = array();
		}
		if (isset( $categId['AND'] ) && is_array($categId['AND'])) {
			$categId['AND'] = $this->get_jailed( $categId['AND'] );
			$i = 0;
			foreach ($categId['AND'] as $c) {
				$fromSql .= " inner join `tiki_category_objects` t{$callno}co$i ";
				$whereSql .= " AND t{$callno}co$i.`categId`= ?  AND co$callno.`objectId`=t{$callno}co$i.`catObjectId` ";
				++$i;
			}
			$bind = array_merge($bind, $categId['AND']);
		} elseif (is_array($categId)) {
			$categId = $this->get_jailed( $categId );
			$fromSql .= " inner join `tiki_category_objects` tco$callno ";
			$whereSql .= " AND co$callno.`objectId`=tco$callno.`catObjectId` ";
			$whereSql .= "AND tco$callno.`categId` IN (".implode(',',array_fill(0,count($categId),'?')).')';
			$bind = array_merge($bind, $categId);
		} else {
			$fromSql .= " inner join `tiki_category_objects` tco$callno ";
			$whereSql .= " AND co$callno.`objectId`=tco$callno.`catObjectId` ";
			$whereSql .= " AND tco$callno.`categId`= ? ";
			$bind[] = $categId;
		}
		if (is_array($bindVars))
			$bindVars = array_merge($bindVars, $bind);
		else
			$bindVars = $bind;
	} 		
	function exist_child_category($parentId, $name) {
		$query = 'select `categId` from `tiki_categories` where `parentId`=? and `name`=?';
		return ($this->getOne($query, array((int)$parentId, $name)));
	}

	/**
	 * Sets watch entries for the given user and category. 
	 */
	function watch_category($user, $categId, $categName) {
		global $tikilib;		
		if ($categId != 0) {        
	        $name = $this->get_category_path_string_with_root($categId);
	        $tikilib->add_user_watch($user, 'category_changed', $categId, 'Category', $name, 
				"tiki-browse_categories.php?parentId=".$categId."&deep=off");		
		}	                         
	}


	/**
	 * Sets watch entries for the given user and category. Also includes
	 * all descendant categories for which the user has view permissions.
	 */
	function watch_category_and_descendants($user, $categId, $categName) {
		global $tikilib;
		
		if ($categId != 0) {
	        $tikilib->add_user_watch($user, 'category_changed', $categId, 'Category', $categName, 
				"tiki-browse_categories.php?parentId=".$categId."&deep=off");
		}
                         
		$descendants = $this->get_category_descendants($categId);
		foreach ($descendants as $descendant) {
			if ($descendant != 0 && $this->has_view_permission($user,$descendant)) {
				$name = $this->get_category_path_string_with_root($descendant);
				$tikilib->add_user_watch($user, 'category_changed', $descendant, 'Category', $name, 
					"tiki-browse_categories.php?parentId=".$descendant."&deep=off");
			}
		}		
	}
	
	function group_watch_category_and_descendants($group, $categId, $categName = NULL, $top = true) {
		global $tikilib, $descendants; 
		
		if ($categId != 0 && $top == true) {
	        $tikilib->add_group_watch($group, 'category_changed', $categId, 'Category', $categName, 
				"tiki-browse_categories.php?parentId=".$categId."&deep=off");
		}
		$descendants = $this->get_category_descendants($categId);
		if ($top == false) {
			$length = count($descendants);
			$descendants = array_slice($descendants, 1, $length, true);
		}		
		foreach ($descendants as $descendant) {
			if ($descendant != 0) {
				$name = $this->get_category_path_string_with_root($descendant);
				$tikilib->add_group_watch($group, 'category_changed', $descendant, 'Category', $name, 
					"tiki-browse_categories.php?parentId=".$descendant."&deep=off");
			}
		}		
	}


	/**
	 * Removes the watch entry for the given user and category.
	 */
	function unwatch_category($user, $categId) {
		global $tikilib;		
		
		$tikilib->remove_user_watch($user, 'category_changed', $categId, 'Category' );
	}


	/**
	 * Removes the watch entry for the given user and category. Also
	 * removes all entries for the descendants of the category.
	 */
	function unwatch_category_and_descendants($user, $categId) {
		global $tikilib;		
		
		$tikilib->remove_user_watch($user, 'category_changed', $categId, 'Category');
		$descendants = $this->get_category_descendants($categId);
		foreach ($descendants as $descendant) {
			$tikilib->remove_user_watch($user, 'category_changed', $descendant, 'Category');
		}
	}
	
	function group_unwatch_category_and_descendants($group, $categId, $top = true) {
		global $tikilib, $descendants;	
			
		if ($categId != 0 && $top == true) {
			$tikilib->remove_group_watch($group, 'category_changed', $categId, 'Category');
		}
		$descendants = $this->get_category_descendants($categId);
		if ($top == false) {
			$length = count($descendants);
			$descendants = array_slice($descendants, 1, $length, true);
		}		
		foreach ($descendants as $descendant) {
			if ($descendant != 0) {
				$tikilib->remove_group_watch($group, 'category_changed', $descendant, 'Category');
			}
		}
	}

	/**
	 * Removes the category from all watchlists.
	 */
	 function remove_category_from_watchlists($categId) {
	 	$query = 'delete from `tiki_user_watches` where `object`=? and `type`=?';
	 	$this->query($query, array((int) $categId, 'Category'));
	 	$query = 'delete from `tiki_group_watches` where `object`=? and `type`=?';
	 	$this->query($query, array((int) $categId, 'Category'));
	 }
	
	
	/**
	 * Returns the path of the given category as a String in the format:
	 * "Root Category (TOP) > 1st Subcategory > 2nd Subcategory::..."	
	 */	
	function get_category_path_string_with_root($categId) {		
		$path = $this->get_category_path($categId);
		$name = '';
		$tepath = array ();
		$tepath[] = "Top";
		foreach ($path as $pathelem) {
			$tepath[] = $pathelem['name'];
		}
		$name = implode(" > ", $tepath);
		return $name;
	}

	/**
	 * Returns the description of the category.	
	 */	
	function get_category_description($categId) {
		$query = "select `description` from `tiki_categories` where `categId`=?";
		return $this->getOne($query,array((int) $categId));
	}

	/**
	 * Returns the parentId of the category.	
	 */	
	function get_category_parent($categId) {
		$query = "select `parentId` from `tiki_categories` where `categId`=?";
		return $this->getOne($query,array((int) $categId));
	}
	
	/**
	* Returns the rootCategId of the category (useful to see whether a category is a WS or a common category)
	*/
	function get_category_rootCategId ($categId) {
		$query = "select `rootCategId` from `tiki_categories` where `categId`=?";
		return $this->getOne($query,array((int) $categId));
	}


	/**
	 * Returns true if the given user has view permission for the category.
	 */
	function has_view_permission($user, $categoryId) {
		return Perms::get( array( 'type' => 'category', 'object' => $categoryId ) )->view_category;
	}

	/**
	 * Returns true if the given user has edit permission for the category.
	 */
	function has_edit_permission($user, $categoryId) {
		global $userlib;
		return ($userlib->user_has_permission($user,'tiki_p_admin')
				|| ($userlib->user_has_permission($user,'tiki_p_edit') && !$userlib->object_has_one_permission($categoryId,"category"))				 
				|| $userlib->object_has_permission($user, $categoryId, "category", "tiki_p_edit") 
				);
	}
	
	/**
	 * Notify the users, watching this category, about changes.
	 * The Array $values contains a selection of the following items:
	 * categoryId, categoryName, categoryPath, description, parentId, parentName, action
	 * oldCategoryName, oldCategoryPath, oldDescription, oldParendId, oldParentName,
	 * objectName, objectType, objectUrl 
	 */
	function notify ($values) {					
		global $prefs;
        
        if ($prefs['feature_user_watches'] == 'y') {        	       
			include_once('lib/notifications/notificationemaillib.php');			
          	$foo = parse_url($_SERVER["REQUEST_URI"]);          	
          	$machine = $this->httpPrefix( true ). dirname( $foo["path"]);          	
          	$values['event']="category_changed";          	
          	sendCategoryEmailNotification($values);          	
        }
	}

	/**
	 * Updates the information of the category that is stored in the cache.
	 */
	function update_category_cache($categId) {	   
		$query = "select * from `tiki_categories` where `categId`=?";
		$result = $this->query($query,array((int) $categId));
		if (!$result->numRows()) {
		   $this->category_cache[$categId] = false;
		}
		$this->category_cache[$categId] = $result->fetchRow();
	}

	/**
	 * Returns a categorized object.
	 */
	function get_categorized_object($cat_type, $cat_objid) {
	    global $objectlib;
		return $objectlib->get_object($cat_type, $cat_objid);		
	}

	/**
	 * Returns a categorized object, identified via the $cat_objid.
	 */
	function get_categorized_object_via_category_object_id($cat_objid) {
	    global $objectlib;
		return $objectlib->get_object_via_objectid($cat_objid);		
	}
	
	/**
	 * Returns the categories that contain the object and are in the user's watchlist.
	 */
	function get_watching_categories($objId, $objType, $user) {					
		global $tikilib;
		
		$categories=$this->get_object_categories($objType, $objId);
		$watchedCategories=$tikilib->get_user_watches($user,"category_changed");		
		$result=array();
		foreach ($categories as $cat) {						
			foreach ($watchedCategories as $wc ) {				
				if ( $wc['object'] == $cat) {									
					$result[]=$cat;	
				}
			}			
		}
		return $result;
	}

	function update_object_categories($categories, $objId, $objType, $desc='', $name='', $href='', $managedCategories = null) {
		global $prefs, $user, $userlib;
		
		//Dirty hack to remove the Slash at the end of the ID (Why is there a slash?! Bug is reportet.)
		if (!empty($categories)) {
			foreach($categories as $key=>$category) {
				if($category{strlen($category)-1}=="/")
					$categories[$key]=substr($category, 0, -1);
			}
		}
		
		if (empty($categories)) {
			$forcedcat = $userlib->get_user_group_default_category($user);
			if ( !empty($forcedcat) ) {
				$categories[] = $forcedcat;
			}
		}

		require_once 'lib/core/Category/Manipulator.php';
		$manip = new Category_Manipulator( $objType, $objId );
		$manip->setNewCategories( $categories ? $categories : array() );

		if( is_array( $managedCategories ) ) {
			$manip->setManagedCategories( $managedCategories );
		}

		if( $default = unserialize( $prefs['category_defaults'] ) ) {
			foreach( $default as $constraint ) {
				$manip->addRequiredSet( $constraint['categories'], $constraint['default'] );
			}
		}

		$this->applyManipulator( $manip, $objType, $objId, $desc, $name, $href );

		if( $prefs['category_i18n_sync'] != '' && $prefs['feature_multilingual'] == 'y' ) {
			global $multilinguallib; require_once 'lib/multilingual/multilinguallib.php';
			$targetCategories = $this->get_object_categories( $objType, $objId, -1, false );

			if( $objType == 'wiki page' ) {
				$translations = $multilinguallib->getTranslations( $objType, $this->get_page_id_from_name( $objId ), $objId );
				$objectIdKey = 'objName';
			} else {
				$translations = $multilinguallib->getTranslations( $objType, $objId );
				$objectIdKey = 'objId';
			}
			
			$subset = $prefs['category_i18n_synced'];
			if( is_string( $subset ) ) {
				$subset = unserialize( $subset );
			}

			foreach( $translations as $tr ) {
				if (!empty($tr[$objectIdKey]) && $tr[$objectIdKey] != $objId) {
					$manip = new Category_Manipulator( $objType, $tr[$objectIdKey] );
					$manip->setNewCategories( $targetCategories );
					$manip->overrideChecks();
	
					if( $prefs['category_i18n_sync'] == 'whitelist' ) {
						$manip->setManagedCategories( $subset );
					} elseif( $prefs['category_i18n_sync'] == 'blacklist' ) {
						$manip->setUnmanagedCategories( $subset );
					}
	
					$this->applyManipulator( $manip, $objType, $tr[$objectIdKey] );
				}
			}
		}

		if ($prefs['feature_user_watches'] == 'y' && !empty($new_categories)) {
			foreach ($new_categories as $categId) {			
		   		$category = $this->get_category($categId);
				$values = array('categoryId'=>$categId, 'categoryName'=>$category['name'], 'categoryPath'=>$this->get_category_path_string_with_root($categId),
					'description'=>$category['description'], 'parentId'=>$category['parentId'], 'parentName'=>$this->get_category_name($category['parentId']),
					'action'=>'object entered category', 'objectName'=>$name, 'objectType'=>$objType, 'objectUrl'=>$href);		
				$this->notify($values);								
			}
		}
		if ($prefs['feature_user_watches'] == 'y' && !empty($removed_categories)) {
			foreach ($removed_categories as $categId) {
				$category = $this->get_category($categId);	
				$values= array('categoryId'=>$categId, 'categoryName'=>$category['name'], 'categoryPath'=>$this->get_category_path_string_with_root($categId),
					'description'=>$category['description'], 'parentId'=>$category['parentId'], 'parentName'=>$this->get_category_name($category['parentId']),
				 	'action'=>'object leaved category', 'objectName'=>$name, 'objectType'=>$objType, 'objectUrl'=>$href);
				$this->notify($values);								
			}
		}
	}

	private function applyManipulator( $manip, $objType, $objId, $desc='', $name='', $href='' ) {
		$old_categories = $this->get_object_categories($objType, $objId, -1, false);
		$manip->setCurrentCategories( $old_categories );

		$new_categories = $manip->getAddedCategories();
		$removed_categories = $manip->getRemovedCategories();

		$this->add_object($objType, $objId, $desc, $name, $href);
		if (empty($new_categories) and empty($removed_categories)) { //nothing changed
			return;
		}

		if (! $catObjectId = $this->is_categorized($objType, $objId) ) {
			$catObjectId = $this->add_categorized_object($objType, $objId, $desc, $name, $href);
		}

		global $prefs;
		if ($prefs["category_autogeocode_within"]) {
			$geocats = $this->get_child_categories($prefs["category_autogeocode_within"], true);
		} else {
			$geocats = false;
		}

		foreach ($new_categories as $category) {
			$this->categorize($catObjectId, $category);
			// Auto geocode if feature is on
			if ($geocats) {
				foreach ($geocats as $g) {
					if ($category == $g["categId"]) {
						$geonames = explode('::', $g["name"]);
						$geonames = array_reverse($geonames);
						$geoloc = implode(',', $geonames);
						global $geolib;
						if (!is_object($geolib)) {
							include_once('lib/geo/geolib.php');
						}
						$geocode = $geolib->geocode($geoloc);
						if ($geocode) {
							global $attributelib;
							if (!is_object($attributelib)) {
								include_once('lib/attributes/attributelib.php');	
							}
							if ($prefs["category_autogeocode_replace"] != 'y') {
								$attributes = $attributelib->get_attributes( $objType, $objId );
								if ( !isset($attributes['tiki.geo.lon']) || !isset($attributes['tiki.geo.lat']) ) {
									$geonotexists = true;
								}
							}
							if ($prefs["category_autogeocode_replace"] == 'y' || isset($geonotexists) && $geonotexists) {
								if ($prefs["category_autogeocode_fudge"] == 'y') {
									$geocode = $geolib->geofudge($geocode);
								}
								$attributelib->set_attribute($objType, $objId, 'tiki.geo.lon', $geocode["lon"]);
								$attributelib->set_attribute($objType, $objId, 'tiki.geo.lat', $geocode["lat"]);
								if ($objType == 'trackeritem') {
									$geolib->setTrackerGeo($objId, $geocode);
								}
							}
						}
						break;
					}
				}
			}
		}

		foreach ($removed_categories as $category) {
			$this->uncategorize($catObjectId, $category);
		}
	}

	function findRoots( $categories ) {
		$candidates = array();

		foreach( $categories as $cat ) {
			$id = $cat['parentId'];
			$candidates[$id] = true;
		}

		foreach( $categories as $cat ) {
			unset( $candidates[ $cat['categId'] ] );
		}

		return array_keys( $candidates );
	}

	function get_jailed( $categories ) {
		if( $jail = $this->get_jail() ) {
			return array_values( array_intersect( $categories, $jail ) );
		} else {
			return $categories;
		}
	}

	// Returns the categories a new object should be in by default, that is none in general, or the perspective categories if the user is in a perspective.
	function get_default_categories() {
		global $prefs;
		if( $this->get_jail() ) {
			// Default categories are not the entire jail including the sub-categories but only the "root" categories
			return is_array($prefs['category_jail'])? $prefs['category_jail']: array($prefs['category_jail']);
		} else {
			return array();
		}
	}

	// Returns an array containing the ids of the passed $objects present in any of the passed $categories.
	function filter_objects_categories($objects, $categories) {
		$query="SELECT `catObjectId` from `tiki_category_objects` where `catObjectId` in (".implode(',', array_fill(0,count($objects),'?')).")";				
		if ($categories) {
			$query .= " and `categId` in (".implode(',', array_fill(0,count($categories),'?')).")";
		}	
		$result = $this->query($query, array_merge($objects, $categories));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[]=$res["catObjectId"];
		}
		return $ret;
	}
	// unassign all objects from a category
	function unassign_all_objects($categId) {
		$query = 'delete from  `tiki_category_objects` where `categId`=?';
		$this->query($query, array((int)$categId));
	}
	//move all objects from a categ to anotehr one
	function move_all_objects($from, $to) {
		$query = 'update `tiki_category_objects` set `categId`=? where `categId`=?';
		$this->query($query, array((int)$to, (int)$from));
	}
	//assign all objects of a categ to another one
	function assign_all_objects($from, $to) {
		$query = 'insert ignore `tiki_category_objects` (`catObjectId`, `categId`) select `catObjectId`, ? from `tiki_category_objects` where `categId`=?';
		$this->query($query, array((int)$to, (int)$from));
	}
	// generate category tree for use in various places (like categorize_list.php)
	function generate_cat_tree($categories, $canchangeall = false, $forceincat = array()) {
		global $smarty;
		include_once ('lib/tree/categ_browse_tree.php');
		$tree_nodes = array();
		$roots = $this->findRoots( $categories );
		foreach ($categories as $c) {
			if (isset($c['name']) || $c['parentId'] != 0) {
				// if used for purposes such as find, should be able to "change" all cats
				if ($canchangeall) {
					$c['canchange'] = true;
				}
				// if used in find, should force incat to check those that have been selected
				if (in_array($c['categId'], $forceincat)) {
					$c['incat'] = 'y';
				}
				$smarty->assign( 'category_data', $c );
				$tree_nodes[] = array(
					'id' => $c['categId'],
					'parent' => $c['parentId'],
					'data' => $smarty->fetch( 'category_tree_entry.tpl' ),
				);
				if (in_array( $c['parentId'], $roots )) {
					$tree_nodes[count($tree_nodes) - 1]['data'] = $tree_nodes[count($tree_nodes) - 1]['data'];
				}
			}
		}
		$tm = new CatBrowseTreeMaker("categorize");
		$res = '';
		foreach( $roots as $root ) {
			$res .= $tm->make_tree($root, $tree_nodes);
		}
		return $res;
	}
}
$categlib = new CategLib;
