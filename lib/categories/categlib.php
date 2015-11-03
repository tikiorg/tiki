<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Categories support class
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$objectlib = TikiLib::lib('object');

class CategLib extends ObjectLib
{
	private $parentCategories = array();

	// Returns a string representing the specified category's path.
	// The path includes all parent categories ordered from the root to the category's parent, and the category itself.
	// The string is a double colon (::) separated concatenation of category names.
	// Returns the empty string if the specified category does not exist.
	function get_category_path_string($categId)
	{
		$category = $this->get_category($categId);
		if ($category) {
			return $category['categpath'];
		} else {
			return '';
		}
	}

	/**
	 * Returns the path of the given category as a String in the format:
	 * "Root Category (TOP) > 1st Subcategory > 2nd Subcategory::..."
	 */
	function get_category_path_string_with_root($categId)
	{
		$category = $this->get_category($categId);
		$tepath = array ('Top');
		foreach ( (array) $category['tepath'] as $pathelem) {
			$tepath[] = $pathelem;
		}
		return implode(" > ", $tepath);
	}

	// Returns false if the category is not found.
	// WARNING: permissions and the category filter are not considered.
	function get_category($categId)
	{
		if (!is_numeric($categId)) {
			throw new Exception('Invalid category identifier');
		}
		$categories = $this->getCategories(array('identifier' => (int) $categId), false, false);
		return empty($categories) ? false : $categories[$categId];
	}

	function get_category_id($name)
	{
		$query = "select `categId` from `tiki_categories` where `name`=?";
		return $this->getOne($query, array((string)$name));


	}
	function get_category_name($categId,$real=false)
	{
		if ( $categId === 'orphan') return tr('None');
	    if ( $categId==0 ) return tr('Top');
		$query = "select `name`,`parentId` from `tiki_categories` where `categId`=?";
		$result=$this->query($query, array((int) $categId));
		$res = $result->fetchRow();
		if ($real) return $res['name'];
		if (preg_match('/^Tracker ([0-9]+)$/', $res['name'])) {
		    $trackerId=preg_replace('/^Tracker ([0-9]+)$/', "$1", $res['name']);
		    return $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?", array((int) $trackerId));
		}
		if (preg_match('/^Tracker Item ([0-9]+)$/', $res['name'])) {
		    $trklib = TikiLib::lib('trk');
		    $itemId=preg_replace('/^Tracker Item ([0-9]+)$/', "$1", $res['name']);
		    return $trklib->get_isMain_value(-1, $itemId);
		}
		return $res['name'];
	}

	// WARNING: This removes not only the specified category, but also all its descendants.
	function remove_category($categId)
	{
		$cachelib = TikiLib::lib('cache');

		$parentId=$this->get_category_parent($categId);
		$categoryName=$this->get_category_name($categId);
		$categoryPath=$this->get_category_path_string_with_root($categId);
		$description=$this->get_category_description($categId);

		$query = "delete from `tiki_categories` where `categId`=?";
		$result = $this->query($query, array((int) $categId));
		$query = "select `catObjectId` from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query, array((int) $categId));

		while ($res = $result->fetchRow()) {
			$object = $res["catObjectId"];

			$query_cant = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
			$cant = $this->getOne($query_cant, array($object));
			if ($cant <= 1) {
			$query2 = "delete from `tiki_categorized_objects` where `catObjectId`=?";
			$result2 = $this->query($query2, array($object));
			}
		}

		// remove any permissions assigned to this category
		$type = 'category';
		$object = $type . $categId;
		$query = "delete from `users_objectpermissions` where `objectId`=? and `objectType`=?";
		$result = $this->query($query, array(md5($object),$type));

		$query = "delete from `tiki_category_objects` where `categId`=?";
		$result = $this->query($query, array((int) $categId));
		$query = "select `categId` from `tiki_categories` where `parentId`=?";
		$result = $this->query($query, array((int) $categId));

		while ($res = $result->fetchRow()) {
			// Recursively remove the subcategory
			$this->remove_category($res["categId"]);
		}

		$cachelib->empty_type_cache('allcategs');
		$cachelib->empty_type_cache('fgals_perms');

		$values= array("categoryId"=>$categId, "categoryName"=>$categoryName, "categoryPath"=>$categoryPath,
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category removed");
		$this->notify($values);

		$this->remove_category_from_watchlists($categId);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action(
			'Removed',
			$categId,
			'category',
			array(
				'name' => $categoryName,
			)
		);

		TikiLib::events()->trigger('tiki.category.delete', [
			'type' => 'category',
			'object' => $categId,
			'user' => $GLOBALS['user'],
		]);

		return true;
	}

	// Throws an Exception if the category name conflicts
	function update_category($categId, $name, $description, $parentId)
	{
		$cachelib = TikiLib::lib('cache');

		$oldCategory=$this->get_category($categId);
		$oldCategoryName=$oldCategory['name'];
		$oldCategoryPath=$this->get_category_path_string_with_root($categId);
		$oldDescription=$oldCategory['description'];
		$oldParentId=$oldCategory['parentId'];
		$oldParentName=$this->get_category_name($oldParentId);

		if (($oldCategoryName != $name || $oldParentId != $parentId) && $this->exist_child_category($parentId, $name)) {
			throw new Exception(tr('A category named %0 already exists in %1.', $name, $this->get_category_name($parentId)));
		}

		// Make sure the description fits the column width
		if (strlen($description) > 250) {
			$description = substr($description, 0, 250);
		}

		$categs = TikiDb::get()->table('tiki_categories');
		$categs->update(
			array(
				'name' => $name,
				'description' => $description,
				'parentId' => (int) $parentId,
				'rootId' => (int) $this->find_root($parentId),
			), array(
				'categId' => $categId,
			)
		);

		$cachelib->empty_type_cache('allcategs');
		$cachelib->empty_type_cache('fgals_perms');

		$values= array("categoryId"=>$categId, "categoryName"=>$name, "categoryPath"=>$this->get_category_path_string_with_root($categId),
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category updated","oldCategoryName"=>$oldCategoryName, "oldCategoryPath"=>$oldCategoryPath,
			"oldDescription"=>$oldDescription, "oldParentId" => $parentId, "oldParentName" => $oldParentName);
		$this->notify($values);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action(
			'Updated',
			$categId,
			'category',
			array(
				'name' => $name,
			)
		);

		TikiLib::events()->trigger('tiki.category.update', [
			'type' => 'category',
			'object' => $categId,
			'user' => $GLOBALS['user'],
		]);
	}

	// Throws an Exception if the category name conflicts
	function add_category($parentId, $name, $description)
	{
		if ($this->exist_child_category($parentId, $name)) {
			throw new Exception(tr('A category named %0 already exists in %1.', $name, $this->get_category_name($parentId)));
		}
		$cachelib = TikiLib::lib('cache');

		// Make sure the description fits the column width
		// TODO: remove length constraint then remove this. See "Quiet truncation of data in database" thread on the development list
		if (strlen($description) > 250) {
			$description = substr($description, 0, 250);
		}

		$categs = TikiDb::get()->table('tiki_categories');

		$id = $categs->insert(
			array(
				'name' => $name,
				'description' => $description,
				'parentId' => (int) $parentId,
				'rootId' => (int) $this->find_root($parentId),
				'hits' => 0,
			)
		);

		$cachelib->empty_type_cache('allcategs');
		$cachelib->empty_type_cache('fgals_perms');
		$values= array("categoryId"=>$id, "categoryName"=>$name, "categoryPath"=> $this->get_category_path_string_with_root($id),
			"description"=>$description, "parentId" => $parentId, "parentName" => $this->get_category_name($parentId),
			"action"=>"category created");
		$this->notify($values);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action(
			'Created',
			$id,
			'category',
			array(
				'name' => $name,
			)
		);

		TikiLib::events()->trigger('tiki.category.create', [
			'type' => 'category',
			'object' => $id,
			'user' => $GLOBALS['user'],
		]);

		return $id;
	}

	private function find_root($parentId)
	{
		$root = 0;

		if ($parentId) {
			$categs = TikiDb::get()->table('tiki_categories');
			$root = $categs->fetchOne(
				'rootId', array(
					'categId' => $parentId,
				)
			);

			if (! $root) {
				$root = $parentId;
			}
		}

		return $root;
	}

	function is_categorized($type, $itemId)
	{
		if ( empty($itemId) ) return 0;

		$query = "select o.`objectId` from `tiki_categorized_objects` c, `tiki_objects` o, `tiki_category_objects` tco where c.`catObjectId`=o.`objectId` and o.`type`=? and o.`itemId`=? and tco.`catObjectId`=c.`catObjectId`";
		$bindvars = array($type,$itemId);
		settype($bindvars["1"], "string");
		return $this->getOne($query, $bindvars);
	}

	// $type The object's type, which has to be one of those handled by ObjectLib's add_object().
	// $checkHandled A boolean indicating whether only handled object types should be accepted when the object has no object record and no object information is given (legacy).
	// Returns the object's OID, or FALSE if the object type is not handled and $checkHandled is FALSE.
	function add_categorized_object($type, $itemId, $description = NULL, $name = NULL, $href = NULL, $checkHandled = FALSE)
	{
		global $prefs;
		$id = $this->add_object($type, $itemId, $checkHandled, $description, $name, $href);
		if ($id === FALSE) {
			return FALSE;
		}
		$query = "select `catObjectId` from `tiki_categorized_objects` where `catObjectId`=?";
		if (!$this->getOne($query, array($id))) {
			$query = "insert into `tiki_categorized_objects` (`catObjectId`) values (?)";
			$this->query($query, array($id));

			$cachelib = TikiLib::lib('cache');
			if ($prefs['categories_cache_refresh_on_object_cat'] != "n") {
				$cachelib->empty_type_cache("allcategs");
			}
			$cachelib->empty_type_cache('fgals_perms');
		}
		return $id;
	}

	/**
	 * categorizePage will do the required steps to categorize a wiki page
	 *
	 * @param mixed $pageName Page to categorize
	 * @param mixed $categId CategoryId
	 * @return nothing
	 *
	 */	
	function categorizePage($pageName, $categId, $user = '')
	{
		$objectlib = TikiLib::lib('object');

		// Categorize the new page
		$objectId = $objectlib->add_object('wiki page', $pageName);

		$description = NULL;
		$name = NULL;
		$href = NULL;
		$checkHandled = true;
		$this->add_categorized_object('wiki page', $pageName, $description, $name, $href, $checkHandled);

		$this->categorize($objectId, $categId, $user);
	}

	function categorize($catObjectId, $categId, $user = '')
	{
		global $prefs;
		if (empty($categId)) {
			return;
		}
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query, array((int) $catObjectId,(int) $categId), -1, -1, false);

		$query = "insert into `tiki_category_objects`(`catObjectId`,`categId`) values(?,?)";
		$result = $this->query($query, array((int) $catObjectId,(int) $categId));

		$cachelib = TikiLib::lib('cache');
		if ($prefs['categories_cache_refresh_on_object_cat'] != "n") {
			$cachelib->empty_type_cache("allcategs");
		}
		$info = TikiLib::lib('object')->get_object_via_objectid($catObjectId);
		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action('Categorized', $info['itemId'], $info['type'], "categId=$categId", $user);
		}
		TikiLib::events()->trigger('tiki.object.categorized', array(
			'object' => $info['itemId'],
			'type' => $info['type'],
			'added' => array($categId),
			'removed' => array(),
		));
		require_once 'lib/search/refresh-functions.php';
		refresh_index($info['type'], $info['itemId']);
	}

	function uncategorize($catObjectId, $categId)
	{
		global $prefs;
		$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId`=?";
		$result = $this->query($query, array((int) $catObjectId,(int) $categId), -1, -1, false);

		$cachelib = TikiLib::lib('cache');
		if ($prefs['categories_cache_refresh_on_object_cat'] != "n") {
			$cachelib->empty_type_cache("allcategs");
		}
		$info = TikiLib::lib('object')->get_object_via_objectid($catObjectId);
		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action('Uncategorized', $info['itemId'], $info['type'], "categId=$categId");
		}
		TikiLib::events()->trigger('tiki.object.categorized', array(
			'object' => $info['itemId'],
			'type' => $info['type'],
			'added' => array(),
			'removed' => array($categId),
		));
		require_once 'lib/search/refresh-functions.php';
		refresh_index($info['type'], $info['itemId']);
	}

	// WARNING: This may not do what you would think from the name.
	// Returns an array of the OIDs of a set of categories.
	// $categId is an integer.
	// If $categId is 0, that set is the set of all categories.
	// If $categId is the OID of a category, that set is the set of that category and its descendants.
	function get_category_descendants($categId)
	{
		if ($categId) {
			$category = $this->get_category($categId);
			if ($category == false) return false;
			return array_merge(array($categId), $category['descendants']);
		} else {
			return array_keys($this->getCategories(NULL, false, false));
		}
	}

	function list_category_objects($categId, $offset, $maxRecords, $sort_mode='pageName_asc', $type='', $find='', $deep=false, $and=false, $filter=null)
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		if ($prefs['feature_sefurl'] == 'y') {
			include_once('tiki-sefurl.php');
		}
		if ($prefs['feature_trackers'] == 'y') {
			$trklib = TikiLib::lib('trk');
		}

	    // Build the condition to restrict which categories objects must be in to be returned.
		$join = '';
		if (is_array($categId) && $and) {
			$categId = $this->get_jailed($categId);
			$i = count($categId)+1;
			$bindWhere = array();
			foreach ($categId as $c) {
				if (--$i) {
					$join .= " INNER JOIN tiki_category_objects tco$i on tco$i.`catObjectId`=o.`objectId` and tco$i.`categId`=? ";
					$bindWhere[] = $c;
				}
			}
		} elseif (is_array($categId)) {
			$bindWhere = $categId;
			if ($deep) {
				foreach ($categId as $c) {
					$bindWhere = array_merge($bindWhere, $this->get_category_descendants($c));
				}
			}

			$bindWhere = $this->get_jailed($bindWhere);
			$bindWhere[] = -1;

			$where = " AND c.`categId` IN (".str_repeat("?,", count($bindWhere)-1)."?)";
		} else {
			if ($deep) {
				$bindWhere = $this->get_category_descendants($categId);
				$bindWhere[] = $categId;
				$bindWhere = $this->get_jailed($bindWhere);
				$bindWhere[] = -1;
				$where = " AND c.`categId` IN (".str_repeat("?,", count($bindWhere)-1)."?)";
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
			if (is_array($type)) {
				$where .= ' AND `type` in ('.implode(',', array_fill(0, count($type), '?')).')';
				$bindWhere = array_merge($bindWhere, $type);
			} else {
				$where .= ' AND `type` =? ';
				$bindWhere[] = $type;
			}
		}
		if (!empty($filter['language']) && !empty($type) && ($type == 'wiki' || $type == 'wiki page' || in_array('wiki', (array)$type) || in_array('wiki page', (array)$type))) {
			$join .= 'LEFT JOIN `tiki_pages` tp ON (o.`itemId` = tp.`pageName`)';
			if (!empty($filter['language_unspecified'])) {
				$where .= ' AND (tp.`lang` IS NULL OR tp.`lang` = ? OR tp.`lang`=?)';
				$bindWhere[] = '';
			} else {
				$where .= ' AND  tp.`lang`=?';
			}
			$bindWhere[] = $filter['language'];
		}

		$bindVars = $bindWhere;

		$orderBy = '';
		if ($sort_mode) {
			if ($sort_mode != 'shuffle') {
				$orderBy = " ORDER BY ".$this->convertSortMode($sort_mode);
			}
		}

		// Fetch all results as was done before, but only do it once
		$query_cant = "SELECT DISTINCT c.*, o.* FROM `tiki_category_objects` c, `tiki_categorized_objects` co, `tiki_objects` o $join WHERE c.`catObjectId`=o.`objectId` AND o.`objectId`=co.`catObjectId` $where";
		$query = $query_cant . $orderBy;
		$result = $this->fetchAll($query, $bindVars);
		$cant = count($result);

		if ($sort_mode == 'shuffle') {
			shuffle($ret);
		}

		return $this->filter_object_list($result, $cant, $offset, $maxRecords);
	}

	/**
	 * @param array $result		object list
	 * @param int $cant			size of list
	 * @param int $offset		start of list
	 * @param int $maxRecords	size of page - NB: -1 will check perms etc on every object and can be very slow
	 * @return array
	 */
	private function filter_object_list($result, $cant, $offset, $maxRecords)
	{
		global $user, $prefs;
		$permMap = TikiLib::lib('object')->map_object_type_to_permission();
		$groupList = $this->get_user_groups($user);

		// Filter based on permissions
		$contextMap = array( 'type' => 'type', 'object' => 'itemId' );
		$contextMapMap = array_fill_keys(array_keys($permMap), $contextMap);

		if ( $maxRecords == -1 ) {
			$requiredResult = $result;
		} else {
			$requiredResult = array_slice($result, $offset, $maxRecords);
		}
		$requiredResult = Perms::mixedFilter(array(), 'type', 'object', $requiredResult, $contextMapMap, $permMap);

		if ($maxRecords != -1) {	// if filtered result is less than what's there look for more
			while (count($requiredResult) < $maxRecords && count($requiredResult) < $cant) {
				$nextResults = array_slice($result, $maxRecords, $maxRecords - count($requiredResult));
				$nextResults = Perms::mixedFilter(array(), 'type', 'object', $nextResults, $contextMapMap, $permMap);
				if (empty($nextResults)) {
					break;
				}
				$requiredResult = array_merge($requiredResult, $nextResults);
			}
		} else {
			$cant = count($requiredResult);
		}
		$result = $requiredResult;

		$ret = array();
		$objs = array();

		foreach ( $result as $res ) {
			if (!in_array($res['catObjectId'].'-'.$res['categId'], $objs)) { // same object and same category
				if (preg_match('/trackeritem/', $res['type'])&&$res['description']=='') {
					$trklib = TikiLib::lib('trk');
					$trackerId=preg_replace('/^.*trackerId=([0-9]+).*$/', '$1', $res['href']);
					$res['name']=$trklib->get_isMain_value($trackerId, $res['itemId']);
					$filed=$trklib->get_field_id($trackerId, "description");
					$res['description']=$trklib->get_item_value($trackerId, $res['itemId'], $filed);
					if (empty($res['description'])) {
						$res['description']=$this->getOne("select `name` from `tiki_trackers` where `trackerId`=?", array((int) $trackerId));
					}
				}
				if ($prefs['feature_sefurl'] == 'y') {
					$type = $res['type'] == 'wiki page'? 'wiki': $res['type'];
					$res['sefurl'] = filter_out_sefurl($res['href'], $type);
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

	function list_orphan_objects($offset, $maxRecords, $sort_mode)
	{
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

	// get specific object types that are not categorised	
	function get_catorphan_object_type($offset, $maxRecords, $object_type, $object_table, $object_ref, $sort_mode=null)
	{
	// $orderClause = $this->convertSortMode($sort_mode); // sort_mode not being used yet and may never be used?
	
	// 1st query 'common' element to get objects that are definitely not categorised if they are not in tiki_objects - needs to be modified for wiki pages using the new method
		if ( $object_type == "wiki page" ) {
		$common1 = "
			FROM tiki_".$object_table." 
			WHERE pageName NOT IN 
			(SELECT itemId FROM tiki_objects WHERE type='".$object_type."')
			";
		} else {
		$common1 = "
			FROM tiki_".$object_table." 
			WHERE ".$object_ref." NOT IN 
			(SELECT itemId FROM tiki_objects WHERE type='".$object_type."')
			";
		}

	// 2nd query 'common' element to get objects that have been categorised before so are in tiki_objects but are no longer categorised plus an additional check that the object is still in the main object table and hasn't been deleted without deleting the entries in the categorisation tables	
		if ( $object_type == "wiki page" ) {
			$common2 = "
			FROM
				tiki_objects
				LEFT JOIN tiki_category_objects ON objectId = catObjectId
			WHERE
				(catObjectId IS NULL and type='wiki page' and itemId IN (SELECT pageName as itemId FROM tiki_pages))
			";
		} else {
			$common2 = "
			FROM
				tiki_objects
				LEFT JOIN tiki_category_objects ON objectId = catObjectId
			WHERE
				(catObjectId IS NULL and type='".$object_type."' and itemId IN (SELECT ".$object_ref." as itemId FROM tiki_".$object_table."))
			";
		}
		

	//create the full queries for the results and to get the counts - modify how the query is formed dependent upon the DB field names for the different object types
		if ( $object_type == "article" ) {
			$query1 = "SELECT tiki_".$object_table.".title as name,".$object_ref." as dataId,tiki_".$object_table.".subtitle $common1";
		} elseif ( $object_type == "blog" ) {
			$query1 = "SELECT tiki_".$object_table.".title as name,".$object_ref." as dataId,tiki_".$object_table.".description $common1";
		} elseif ( $object_type == "wiki page" ) {
			$query1 = "SELECT tiki_".$object_table.".pageName,".$object_ref." as dataId,tiki_".$object_table.".description $common1";		
		} else {
			$query1 = "SELECT tiki_".$object_table.".name,".$object_ref." as dataId,tiki_".$object_table.".description $common1";
		}
	//	
		if ( $object_type == "wiki page" ) {
			$query2 = "SELECT name as pageName,itemId as dataId,description $common2";
		} else {
			$query2 = "SELECT name,itemId as dataId,description $common2";
		}
		
		$queryCount1 = "SELECT COUNT(*) $common1";
		$queryCount2 = "SELECT COUNT(*) $common2";		
		
	// get results for 1st query
		$result1 = $this->fetchAll($query1, array());
		$count1 = $this->getOne($queryCount1);
		
	// get results for 2nd query
		$result2 = $this->fetchAll($query2, array());
		$count2 = $this->getOne($queryCount2);	
		
	//merge the results for the two queries	
		$result = array_merge($result1, $result2);	
		$count = $count1 + $count2;
		$countall = $count;
		
	// do a simple sort on the data
		sort($result); 

	// apply the maxRecord and offset if not displaying all the results
		if ( $maxRecords == -1 ) {
			$requiredResult = $result;
		} else {
			$requiredResult = array_slice($result, $offset, $maxRecords);
		}

		if ($maxRecords != -1) {	// if filtered result is less than what's there look for more
			while (count($requiredResult) < $maxRecords && count($requiredResult) < $count) {
				$nextResults = array_slice($result, $maxRecords, $maxRecords - count($requiredResult));
				if (empty($nextResults)) {
					break;
				}
				$requiredResult = array_merge($requiredResult, $nextResults);
			}
		} else {
			$count = count($requiredResult);
		}
		$result = $requiredResult;
		
	// return the maxRecord data result and data count plus the actual total count as a single array
		return array(
			"data" => $result,
			"cant" => $count,
			"countall" => $countall,
		);
	}
	
	// get the parent categories of an object
	function get_object_categories($type, $itemId, $parentId=-1, $jailed = true)
	{
		$ret = array();
		if (!$itemId)
			return $ret;
		if ($parentId == -1) {
			$query = "select `categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto, `tiki_objects` o
				where tco.`catObjectId`=tto.`catObjectId` and o.`objectId`=tto.`catObjectId` and o.`type`=? and `itemId`=?";
			//settype($itemId,"string"); //itemId is defined as varchar
			$bindvars = array("$type",$itemId);
		} else {
			$query = "select tc.`categId` from `tiki_category_objects` tco, `tiki_categorized_objects` tto, `tiki_objects` o,`tiki_categories` tc
    		where tco.`catObjectId`=tto.`catObjectId` and o.`objectId`=tto.`catObjectId` and o.`type`=? and `itemId`=? and tc.`parentId` = ? and tc.`categId`=tco.`categId`";
			$bindvars = array("$type",$itemId,(int)$parentId);
		}
		$result = $this->query($query, $bindvars);
		while ($res = $result->fetchRow()) {
			$ret[] = (int) $res["categId"];
		}

		if ( $jailed ) {
			return $this->get_jailed($ret);
		} else {
			return $ret;
		}
	}

	// WARNING: This method is very different from get_categoryobjects()
	// Get all the objects in a category
	// filter = array('table'=>, 'join'=>, 'filter'=>, 'bindvars'=>)
	function get_category_objects($categId, $type=null, $filter = null)
	{
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
	function remove_object_from_category($catObjectId, $categId)
	{
		$this->remove_object_from_categories($catObjectId, array($categId));
	}

	// Removes the object with the given identifer from the categories specified in the $categIds array. The array contains category identifiers.
	function remove_object_from_categories($catObjectId, $categIds)
	{
		global $prefs;
		if (!empty($categIds)) {
			$cachelib = TikiLib::lib('cache');
			$query = "delete from `tiki_category_objects` where `catObjectId`=? and `categId` in (".implode(',', array_fill(0, count($categIds), '?')).")";
			$result = $this->query($query, array_merge(array($catObjectId), $categIds));
			$query = "select count(*) from `tiki_category_objects` where `catObjectId`=?";
			$cant = $this->getOne($query, array((int) $catObjectId));
			if (!$cant) {
				$query = "delete from `tiki_categorized_objects` where `catObjectId`=?";
				$result = $this->query($query, array((int) $catObjectId));
			}
			if ($prefs['categories_cache_refresh_on_object_cat'] != "n") {
				$cachelib->empty_type_cache("allcategs");
			}
			$cachelib->empty_type_cache('fgals_perms');
		}
	}

	// Categorize the object of the given type and with the given unique identifier in the categories specified in the second parameter.
	// $categIds can be a category OID or an array of category OIDs.
	// $type The object's type, which has to be one of those handled by ObjectLib's add_object().
	// Returns the object OID, or FALSE if the given type is not handled.
	function categorize_any( $type, $identifier, $categIds )
	{
		$catObjectId = $this->add_categorized_object($type, $identifier, NULL, NULL, NULL, TRUE);
		if ($catObjectId === FALSE) {
			return FALSE;
		}
		if (!is_array($categIds)) {
			$categIds = array($categIds);
		}
		foreach ($categIds as $categId) {
			$this->categorize($catObjectId, $categId);
		}

		return $catObjectId;
	}

	// Return an array enumerating a subtree with the given root node in preorder
	private function getSortedSubTreeNodes($root, &$categories)
	{
		global $prefs;
		$subTreeNodes = array($root);
		$childrenSubTreeNodes = array();
		foreach ($categories[$root]['children'] as $child) {
			$childrenSubTreeNodes[$categories[$child]['name']] = $this->getSortedSubTreeNodes($child, $categories);
		}
		if ($prefs['category_sort_ascii'] == 'y') {
			uksort($childrenSubTreeNodes, array("CategLib", "cmpcatname"));
		} else {
			ksort($childrenSubTreeNodes, SORT_LOCALE_STRING);
		}
		foreach ($childrenSubTreeNodes as $childSubTreeNodes) {
			$subTreeNodes = array_merge($subTreeNodes, $childSubTreeNodes);
		}
		return $subTreeNodes;
	}

	/* Returns an array of categories.
	Each category is similar to a tiki_categories record, but with the following additional fields:
		"children" is an array of identifiers of the categories the category has as children.
		"descendants" is an array of identifiers of the categories the category has as descendants.
		"objects" is the number of objects directly in the category.
		"tepath" is an array representing the path to the category in the category tree, ordered from the ancestor to the category. Each element is the name of the represented category. Indices are category OIDs.
		"categpath" is a string representing the path to the category in the category tree, ordered from the ancestor to the category. Each category is separated by "::". For example, "Tiki" could have categpath "Software::Free software::Tiki".
		"relativePathString" defaults to categpath.
		When and only when filtering with a filter of type "children" or "descendants", it becomes the part of "categpath" which starts from after the filtered category rather than from a root category.
		For example, if filtering descendants of category "Software", the "relativePathString" of a grandchild may be "Free Software::Tiki".

	By default, we start from all categories. This happens if the filter is NULL or if its type is set to "all".
	If $filter is an array with an "identifier" element or a "type" element set to "roots", starting categories are restrained.
	If the "type" element is set to "roots", start from the root categories.
	If the "type" element is unset or set to "self", start from only the designated category.
	If the "type" element is set to "children", start from the designated category's children.
	If the "type" element is set to "descendants", start from the designated category's descendants.
	In the last 3 cases, an "identifier" element must be present.

	If considerCategoryFilter is true, only categories that match the category filter are returned.
	If considerPermissions is true, only categories that the user has the permission to view are returned.
	If localized is enabled, category names are translated to the user's language.
	*/
	function getCategories($filter = array('type'=>'all'), $considerCategoryFilter = true, $considerPermissions = true, $localized = true)
	{
		global $prefs;
		$cachelib = TikiLib::lib('cache');
		$cacheKey = 'all' . ($localized ? '_' . $prefs['language'] : '');
		if ( ! $ret = $cachelib->getSerialized($cacheKey, 'allcategs') ) {
			// This generates different caches for each language. The empty key is used when no localization was requested.
			// This could be optimized, but for now each cache is generated from scratch.

			$categories = array();
			$roots = array();
			$query = "select * from `tiki_categories`";
			$result = $this->query($query, array());
			while ($res = $result->fetchRow()) {
				$id = $res["categId"];
				$query = "select count(*) from `tiki_category_objects` where `categId`=?";
				$res["objects"] = $this->getOne($query, array($id));
				$res['children'] = array();
				$res['descendants'] = array();
				if ($localized) {
					$res['name'] = tr($res['name']);
				}

				$categories[$id] = $res;
			}

			foreach ($categories as &$category) {
				if ($category['parentId']) {
					// Link this category from its parent.
					$categories[$category['parentId']]['children'][] = $category['categId'];
				} else {
					// Mark as a root category.
					$roots[$category['name']] = $category['categId'];
				}

				$path = array($category['categId'] => $category['name']);

				$parent = $category['parentId'];
				while (!empty($parent)) {
					if (isset($categories[$parent]['name'])){
						$path[$parent] = $categories[$parent]['name'];
					}else {
						$path[$parent] = "";
					}

					$categories[$parent]['descendants'][] = $category['categId']; // Link this category from its ascendants for optimization.
					if (isset($categories[$parent]['parentId'])) {
						$parent = $categories[$parent]['parentId'];
					} else {
						$parent = 0;
					}
				}
				$path = array_reverse($path, true);

				$category["tepath"] = $path;
				$category["categpath"] = implode("::", $path);
				$category["relativePathString"] = $category["categpath"];
			}

			// Sort in preorder. Siblings are sorted by name.
			if ($prefs['category_sort_ascii'] == 'y') {
				uksort($roots, array("CategLib", "cmpcatname"));
			} else {
				ksort($roots, SORT_LOCALE_STRING);
			}
			$sortedCategoryIdentifiers = array();
			foreach ($roots as $root) {
				$sortedCategoryIdentifiers = array_merge($sortedCategoryIdentifiers, $this->getSortedSubTreeNodes($root, $categories));
			}
			$ret = array();
			foreach ($sortedCategoryIdentifiers as $categoryIdentifier) {
				$ret[$categories[$categoryIdentifier]['categId']] = $categories[$categoryIdentifier];
			}
			unset($categories);

			$cachelib->cacheItem($cacheKey, serialize($ret), 'allcategs');
			$cachelib->cacheItem('roots', serialize($roots), 'allcategs'); // Used in get_category_descendants()
		}

		$type = is_null($filter) ? 'all' : (isset($filter['type']) ? $filter['type'] : 'self');
		if ($type != 'all') {
			$kept = array();
			if ($type != 'roots') {
				if (!isset($filter['identifier'])) {
					throw new Exception("Missing base category");
				}
				if (!empty($ret) && isset($ret[$filter['identifier']])) {
					$filterBaseCategory = $ret[$filter['identifier']];
				} else {
					$filterBaseCategory = null;
				}

			}
			switch ($type) {
				case 'children':
					$kept = $filterBaseCategory['children'];
    				break;
				case 'descendants':
					$kept = $filterBaseCategory['descendants'];
    				break;
				case 'roots':
					$kept = $cachelib->getSerialized('roots', 'allcategs');
	    			break;
				default:
					$ret = array($filter['identifier'] => $filterBaseCategory); // Avoid array functions for optimization
			}
			if ($type != 'self') {
				$ret = array_intersect_key($ret, array_flip($kept));

				if ($type != 'roots') {
					// Set relativePathString by stripping the length of the common ancestor plus 2 characters for the pathname separator ("::").
					$strippedLength = strlen($filterBaseCategory['categpath']) + 2;
					foreach ($ret as &$category) {
						$category['relativePathString'] = substr($category['categpath'], $strippedLength);
					}
				}
			}
		}

		if ($considerCategoryFilter) {
			if ( $jail = $this->get_jail() ) {
				$area = array();
				if ($prefs['feature_areas'] === 'y') {
					$areaslib = TikiLib::lib('areas');
					$area = $areaslib->getAreaByPerspId($_SESSION['current_perspective']);
				}
				$roots = array_filter((array) $prefs['category_jail_root']); // Skip 0 and other forms of empty

				$ret = array_filter(
					$ret,
					function ($category) use ($jail, $roots, $area)
					{
						if (in_array($category['categId'], $jail)) {
							return true;
						}
						if ($area && !$area['share_common']) {
							return false;
						}

						if ($category['rootId'] && ! in_array($category['rootId'], $roots)) {
							return true;
						} elseif (! $category['rootId'] && ! in_array($category['categId'], $roots)) {
							return true;
						}

						return false;
					}
				);

			}
		}

		if ($considerPermissions) {
			$categoryIdentifiers = array_keys($ret);
			if (is_null($categoryIdentifiers)) {
				$categoryIdentifiers = array();
			}
			Perms::bulk(array( 'type' => 'category' ), 'object', $categoryIdentifiers);
			foreach ($categoryIdentifiers as $categoryIdentifier) {
				$permissions = Perms::get(array( 'type' => 'category', 'object' => $categoryIdentifier));
				if (!$permissions->view_category) {
					unset($ret[$categoryIdentifier]);
				}
			}
		}

		return $ret;
	}

	// get categories related to a link. For Whats related module.
	function get_link_categories($link)
	{
		$ret=array();
		$parsed=parse_url($link);
		$urlPath = preg_split("#\/#", $parsed["path"]);
		$parsed["path"]=end($urlPath);
		if (!isset($parsed["query"])) return($ret);
		/* not yet used. will be used to get the "base href" of a page
		$params=array();
		$a = explode('&', $parsed["query"]);
		for ($i=0; $i < count($a);$i++) {
			$b = preg_split('/=/', $a[$i]);
			$params[htmlspecialchars(urldecode($b[0]))]=htmlspecialchars(urldecode($b[1]));
		}
		*/
		$query="select distinct co.`categId` from `tiki_objects` o, `tiki_categorized_objects` cdo, `tiki_category_objects` co  where o.`href`=? and cdo.`catObjectId`=co.`catObjectId` and o.`objectId` = cdo.`catObjectId`";
		$result=$this->query($query, array($parsed["path"]."?".$parsed["query"]));
		while ($res = $result->fetchRow()) {
		  $ret[]=$res["categId"];
		}
		return($ret);
	}

	// input is a array of category id's and return is a array of
	// maxRows related links with description
	function get_related($categories,$maxRows=10)
	{
		global $tiki_p_admin, $user;
		if (count($categories)==0) return (array());
		$quarr=implode(",", array_fill(0, count($categories), '?'));
		$query="select distinct o.`type`, o.`description`, o.`itemId`,o.`href` from `tiki_objects` o, `tiki_categorized_objects` cdo, `tiki_category_objects` co  where co.`categId` in (".$quarr.") and co.`catObjectId`=cdo.`catObjectId` and o.`objectId`=cdo.`catObjectId`";
		$result=$this->query($query, $categories);
		$ret=array();
		if ($tiki_p_admin != 'y')
			$permMap = TikiLib::lib('object')->map_object_type_to_permission();
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
			$rand_keys = array_rand($ret, $maxRows);
			foreach ($rand_keys as $value) {
				$ret2[$value]=$ret[$value];
			}
			return($ret2);
		}
		return($ret);
	}

	// combines the two functions above
	function get_link_related($link,$maxRows=10)
	{
		return ($this->get_related($this->get_link_categories($link), $maxRows));
	}

	// Moved from tikilib.php
	function uncategorize_object($type, $id)
	{
		global $prefs;
		$query = "select `catObjectId` from `tiki_categorized_objects` c, `tiki_objects` o where o.`objectId`=c.`catObjectId` and o.`type`=? and o.`itemId`=?";
		$catObjectId = $this->getOne($query, array((string) $type,(string) $id));

		if ($catObjectId) {
			$info = TikiLib::lib('object')->get_object_via_objectid($catObjectId);

		    $query = "select `categId` from `tiki_category_objects` where `catObjectId`=?";
			$result = $this->fetchAll($query, array((int) $catObjectId));
			$removed = array();
			foreach ( $result as $row ) {
				$removed[] = $row['categId'];
			}
			$removed = array_unique($removed);

			$query = "delete from `tiki_category_objects` where `catObjectId`=?";
		    $this->query($query, array((int) $catObjectId));
			// must keep tiki_categorized object because poll or ... can use it

		    // Refresh categories
		    $cachelib = TikiLib::lib('cache');
			if ($prefs['categories_cache_refresh_on_object_cat'] != "n") {
				$cachelib->empty_type_cache("allcategs");
			}
			$cachelib->empty_type_cache('fgals_perms');

			TikiLib::events()->trigger('tiki.object.categorized', array(
				'object' => $info['itemId'],
				'type' => $info['type'],
				'added' => array(),
				'removed' => $removed,
			));
		}
	}

   	// Get a string of HTML code representing an object's category paths.
   	// $cats: The OIDs of the categories of the object.
	function get_categorypath($cats)
   	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		if (!isset($prefs['categorypath_excluded'])) {
			return false;
		}

		$excluded = array();
		if (is_array($prefs['categorypath_excluded'])) {
			$excluded = $prefs['categorypath_excluded'];
		} else {
			$excluded = preg_split('/,/', $prefs['categorypath_excluded']);
		}
		$cats = array_diff($cats, $excluded);

		$catpath = '';
		foreach ($cats as $categId) {
			$catp = array();
			$info = $this->get_category($categId);
			if (!in_array($info['categId'], $excluded)) {
				$catp[$info['categId']] = $info['name'];
			}
			while ($info["parentId"] != 0) {
				$info = $this->get_category($info["parentId"]);
				if (!in_array($info['categId'], $excluded)) {
					$catp[$info['categId']] = $info['name'];
				}
			}

			// Check if user has permission to view the page
			$perms = Perms::get(array( 'type' => 'category', 'object' => $categId ));
			$canView = $perms->view_category;

			if ($canView || in_array($prefs['categorypath_format'], array('link_or_text', 'always_text'))) {
				$smarty->assign('catpathShowLink', $canView && in_array($prefs['categorypath_format'], array('link_when_visible', 'link_or_text')));
				$smarty->assign('catp', array_reverse($catp, true));
				$catpath .= $smarty->fetch('categpath.tpl');
			}

		}
		return $catpath;
	}

	// WARNING: This method is very different from get_category_objects()
	// Format a list of objects in the given categories, returning HTML code.
	function get_categoryobjects($catids,$types="*",$sort='created_desc',$split=true,$sub=false,$and=false, $maxRecords = 500, $filter=null, $displayParameters = array())
	{
		global $prefs, $user;
		$smarty = TikiLib::lib('smarty');

		$typetokens = array(
			"article" => "article",
			"blog" => "blog",
			"blog post" => "blog post",
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
			"img" => "image",
			"template" => "template",
		);	//get_strings tra("article");tra("blog");tra("directory");tra("faq");tra("FAQ");tra("file gallery");tra("forum");tra("image gallery");tra("newsletter");
			//get_strings tra("poll");tra("quiz");tra("survey");tra("tracker");tra("wiki page");tra("image");tra("calendar");tra("template");

		$typetitles = array(
			"article" => "Articles",
			"blog" => "Blogs",
			"blog post" => "Blog Post",
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
			"image" => "Image",
			"template" => "Content Templates",
		);

		$out = "";
		$listcat = $allcats = array();
		$title = '';
		$find = "";
		$offset = 0;
		$firstpassed = false;
		$typesallowed = array();
		if (!isset($displayParameters['showTitle'])) {
			$displayParameters['showTitle'] = 'y';
		}
		if (!isset($displayParameters['categoryshowlink'])) {
			$displayParameters['categoryshowlink'] = 'y';
		}
		if (!isset($displayParameters['showtype'])) {
			$displayParameters['showtype'] = 'y';
		}
		if (!isset($displayParameters['one'])) {
			$displayParameters['one'] = 'n';
		}
		if (!isset($displayParameters['showlinks'])) {
			$displayParameters['showlinks'] = 'y';
		}
		if (!isset($displayParameters['showname'])) {
			$displayParameters['showname'] = 'y';
		}
		if (!isset($displayParameters['showdescription'])) {
			$displayParameters['showdescription'] = 'n';
		}
		$smarty->assign('params', $displayParameters);
		if ($and) {
			$split = false;
		}
		if ($types == '*') {
			$typesallowed = array_keys($typetitles);
		} elseif (strpos($types, '+')) {
			$alltypes = preg_split('/\+/', $types);
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
		$out=$smarty->fetch("categobjects_title.tpl");
		foreach ($catids as $id) {
			if (!$this->user_has_perm_on_object($user, $id, 'category', 'tiki_p_view_category')) {
				continue;
			} 
			$titles["$id"] = $this->get_category_name($id);
			$objectcat = array();
			$objectcat = $this->list_category_objects($id, $offset, $and? -1: $maxRecords, $sort, $types == '*'? '': $typesallowed, $find, $sub, false, $filter);

			$acats = $andcat = array();
			foreach ($objectcat["data"] as $obj) {
				$type = $obj["type"];
				if (substr($type, 0, 7) == 'tracker') $type = 'tracker';
				if (($types == '*') || in_array($type, $typesallowed)) {
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
	function last_category_objects($categId, $maxRecords, $type="")
	{
		$mid = "and `categId`=?";
		$bindvars = array((int)$categId);
		if ($type) {
		    $mid.= " and `type`=?";
		    $bindvars[] = $type;
		}
		$sort_mode = "created_desc";
		$query = "select co.`catObjectId`, `categId`, `type`, `name`, `href` from `tiki_category_objects` co, `tiki_categorized_objects` cdo, `tiki_objects` o where co.`catObjectId`=cdo.`catObjectId` and o.`objectId`=cdo.`catObjectId` $mid order by o.".$this->convertSortMode($sort_mode);
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, 0);

		return array('data'=> $ret);
	}

	// Gets a list of categories that will block objects to be seen by user, recursive
	function list_forbidden_categories($parentId=0, $parentAllowed='', $perm='tiki_p_view_categorized')
	{
		global $user;
		$userlib = TikiLib::lib('user');
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

	/* build the portion of list join if filter by category
	 * categId can be a simple value, a list of values=>or between categ, array('AND'=>list values) for an AND
	 */
	function getSqlJoin($categId, $objType, $sqlObj, &$fromSql, &$whereSql, &$bindVars, $type = '?')
	{
		static $callno = 0;
		$callno++;
		$fromSql .= " inner join `tiki_objects` co$callno";
		$whereSql .= " AND co$callno.`type`=$type AND co$callno.`itemId`= $sqlObj ";
		if ( $type == '?' ) {
			$bind = array($objType);
		} else {
			$bind = array();
		}
		if (isset( $categId['AND'] ) && is_array($categId['AND'])) {
			$categId['AND'] = $this->get_jailed($categId['AND']);
			$i = 0;
			foreach ($categId['AND'] as $c) {
				$fromSql .= " inner join `tiki_category_objects` t{$callno}co$i ";
				$whereSql .= " AND t{$callno}co$i.`categId`= ?  AND co$callno.`objectId`=t{$callno}co$i.`catObjectId` ";
				++$i;
			}
			$bind = array_merge($bind, $categId['AND']);
		} elseif (is_array($categId)) {
			$categId = $this->get_jailed($categId);
			$fromSql .= " inner join `tiki_category_objects` tco$callno ";
			$whereSql .= " AND co$callno.`objectId`=tco$callno.`catObjectId` ";
			$whereSql .= "AND tco$callno.`categId` IN (".implode(',', array_fill(0, count($categId), '?')).')';
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
	function exist_child_category($parentId, $name)
	{
		$query = 'select `categId` from `tiki_categories` where `parentId`=? and `name`=?';
		return ($this->getOne($query, array((int)$parentId, $name)));
	}

	/**
	 * Sets watch entries for the given user and category.
	 */
	function watch_category($user, $categId, $categName)
	{
		$tikilib = TikiLib::lib('tiki');
		if ($categId != 0) {
			$name = $this->get_category_path_string_with_root($categId);
			$tikilib->add_user_watch(
				$user,
				'category_changed',
				$categId,
				'Category',
				$name,
				"tiki-browse_categories.php?parentId=".$categId."&deep=off"
			);
		}
	}


	/**
	 * Sets watch entries for the given user and category. Also includes
	 * all descendant categories for which the user has view permissions.
	 */
	function watch_category_and_descendants($user, $categId, $categName)
	{
		$tikilib = TikiLib::lib('tiki');

		if ($categId != 0) {
			$tikilib->add_user_watch(
				$user,
				'category_changed',
				$categId,
				'Category',
				$categName,
				"tiki-browse_categories.php?parentId=".$categId."&deep=off"
			);
		}

		$descendants = $this->get_category_descendants($categId);
		foreach ($descendants as $descendant) {
			if ($descendant != 0 && $this->has_view_permission($user, $descendant)) {
				$name = $this->get_category_path_string_with_root($descendant);
				$tikilib->add_user_watch(
					$user,
					'category_changed',
					$descendant,
					'Category',
					$name,
					"tiki-browse_categories.php?parentId=".$descendant."&deep=off"
				);
			}
		}
	}

	function group_watch_category_and_descendants($group, $categId, $categName = NULL, $top = true)
	{
		$tikilib = TikiLib::lib('tiki');

		if ($categId != 0 && $top == true) {
			$tikilib->add_group_watch(
				$group,
				'category_changed',
				$categId,
				'Category',
				$categName,
				"tiki-browse_categories.php?parentId=".$categId."&deep=off"
			);
		}
		$descendants = $this->get_category_descendants($categId);
		if ($top == false) {
			$length = count($descendants);
			$descendants = array_slice($descendants, 1, $length, true);
		}
		foreach ($descendants as $descendant) {
			if ($descendant != 0) {
				$name = $this->get_category_path_string_with_root($descendant);
				$tikilib->add_group_watch(
					$group,
					'category_changed',
					$descendant,
					'Category',
					$name,
					"tiki-browse_categories.php?parentId=".$descendant."&deep=off"
				);
			}
		}
	}


	/**
	 * Removes the watch entry for the given user and category.
	 */
	function unwatch_category($user, $categId)
	{
		$tikilib = TikiLib::lib('tiki');

		$tikilib->remove_user_watch($user, 'category_changed', $categId, 'Category');
	}


	/**
	 * Removes the watch entry for the given user and category. Also
	 * removes all entries for the descendants of the category.
	 */
	function unwatch_category_and_descendants($user, $categId)
	{
		$tikilib = TikiLib::lib('tiki');

		$tikilib->remove_user_watch($user, 'category_changed', $categId, 'Category');
		$descendants = $this->get_category_descendants($categId);
		foreach ($descendants as $descendant) {
			$tikilib->remove_user_watch($user, 'category_changed', $descendant, 'Category');
		}
	}

	function group_unwatch_category_and_descendants($group, $categId, $top = true)
	{
		$tikilib = TikiLib::lib('tiki');

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
	function remove_category_from_watchlists($categId)
	{
	 	$query = 'delete from `tiki_user_watches` where `object`=? and `type`=?';
	 	$this->query($query, array((int) $categId, 'Category'));
	 	$query = 'delete from `tiki_group_watches` where `object`=? and `type`=?';
	 	$this->query($query, array((int) $categId, 'Category'));
	}



	/**
	 * Returns the description of the category.
	 */
	function get_category_description($categId)
	{
		$query = "select `description` from `tiki_categories` where `categId`=?";
		return $this->getOne($query, array((int) $categId));
	}

	/**
	 * Returns the parentId of the category.
	 */
	function get_category_parent($categId)
	{
		$query = "select `parentId` from `tiki_categories` where `categId`=?";
		return $this->getOne($query, array((int) $categId));
	}

	/**
	 * Returns true if the given user has view permission for the category.
	 */
	function has_view_permission($user, $categoryId)
	{
		return Perms::get(array( 'type' => 'category', 'object' => $categoryId ))->view_category;
	}

	/**
	 * Returns true if the given user has edit permission for the category.
	 */
	function has_edit_permission($user, $categoryId)
	{
		$userlib = TikiLib::lib('user');
		return ($userlib->user_has_permission($user, 'tiki_p_admin')
				|| ($userlib->user_has_permission($user, 'tiki_p_edit') && !$userlib->object_has_one_permission($categoryId, "category"))
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
	function notify ($values)
	{
		global $prefs;

        if ($prefs['feature_user_watches'] == 'y') {
			include_once('lib/notifications/notificationemaillib.php');
          	$foo = parse_url($_SERVER["REQUEST_URI"]);
          	$machine = $this->httpPrefix(true). dirname($foo["path"]);
          	$values['event']="category_changed";
          	sendCategoryEmailNotification($values);
        }
	}

	/**
	 * Returns a categorized object.
	 */
	function get_categorized_object($cat_type, $cat_objid)
	{
	    $objectlib = TikiLib::lib('object');
		return $objectlib->get_object($cat_type, $cat_objid);
	}

	/**
	 * Returns a categorized object, identified via the $cat_objid.
	 */
	function get_categorized_object_via_category_object_id($cat_objid)
	{
	    $objectlib = TikiLib::lib('object');
		return $objectlib->get_object_via_objectid($cat_objid);
	}

	/**
	 * Returns the categories that contain the object and are in the user's watchlist.
	 */
	function get_watching_categories($objId, $objType, $user)
	{
		$tikilib = TikiLib::lib('tiki');

		$categories=$this->get_object_categories($objType, $objId);
		$watchedCategories=$tikilib->get_user_watches($user, "category_changed");
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

	// Change an object's categories
	// $objId: A unique identifier of an object of the given type, for example "Foo" for Wiki page Foo.
	function update_object_categories($categories, $objId, $objType, $desc=NULL, $name=NULL, $href=NULL, $managedCategories = null, $override_perms = false)
	{
		global $prefs, $user;
		$userlib = TikiLib::lib('user');

		if (empty($categories)) {
			$forcedcat = $userlib->get_user_group_default_category($user);
			if ( !empty($forcedcat) ) {
				$categories[] = $forcedcat;
			}
		}

		$manip = new Category_Manipulator($objType, $objId);
		if ($override_perms) {
			$manip->overrideChecks();
		}
		$manip->setNewCategories($categories ? $categories : array());

		if ( is_array($managedCategories) && !$override_perms ) {
			$manip->setManagedCategories($managedCategories);
		}

		if ($prefs['category_defaults']) {
			foreach ($prefs['category_defaults'] as $constraint ) {
				$manip->addRequiredSet($this->extentCategories($constraint['categories']), $constraint['default'], $constraint['filter'], $constraint['type']);
			}
		}

		$this->applyManipulator($manip, $objType, $objId, $desc, $name, $href);

		if ( $prefs['category_i18n_sync'] != 'n' && $prefs['feature_multilingual'] == 'y' ) {
			$multilinguallib = TikiLib::lib('multilingual');
			$targetCategories = $this->get_object_categories($objType, $objId, -1, false);

			if ( $objType == 'wiki page' ) {
				$translations = $multilinguallib->getTranslations($objType, $this->get_page_id_from_name($objId), $objId);
				$objectIdKey = 'objName';
			} else if (in_array($objType, array('article'))) {	// only try on supported types
				$translations = $multilinguallib->getTranslations($objType, $objId);
				$objectIdKey = 'objId';
			} else {
				$translations = array();
				$objectIdKey = 'objId';
			}

			$subset = $prefs['category_i18n_synced'];
			if ( is_string($subset) ) {
				$subset = unserialize($subset);
			}

			foreach ( $translations as $tr ) {
				if (!empty($tr[$objectIdKey]) && $tr[$objectIdKey] != $objId) {
					$manip = new Category_Manipulator($objType, $tr[$objectIdKey]);
					$manip->setNewCategories($targetCategories);
					$manip->overrideChecks();

					if ( $prefs['category_i18n_sync'] == 'whitelist' ) {
						$manip->setManagedCategories($subset);
					} elseif ( $prefs['category_i18n_sync'] == 'blacklist' ) {
						$manip->setUnmanagedCategories($subset);
					}

					$this->applyManipulator($manip, $objType, $tr[$objectIdKey]);
				}
			}
		}

		$added = $manip->getAddedCategories();
		$removed = $manip->getRemovedCategories();

		TikiLib::events()->trigger('tiki.object.categorized', array(
			'object' => $objId,
			'type' => $objType,
			'added' => $added,
			'removed' => $removed,
		));

		$this->notify_add($added, $name, $objType, $href);
		$this->notify_remove($removed, $name, $objType, $href);
	}

	function notify_add($new_categories, $name, $objType, $href)
	{
		global $prefs;
		if ($prefs['feature_user_watches'] == 'y' && !empty($new_categories)) {
			foreach ($new_categories as $categId) {
		   		$category = $this->get_category($categId);
				$values = array('categoryId'=>$categId, 'categoryName'=>$category['name'], 'categoryPath'=>$this->get_category_path_string_with_root($categId),
					'description'=>$category['description'], 'parentId'=>$category['parentId'], 'parentName'=>$this->get_category_name($category['parentId']),
					'action'=>'object entered category', 'objectName'=>$name, 'objectType'=>$objType, 'objectUrl'=>$href);
				$this->notify($values);
			}
		}
	}

	function notify_remove($removed_categories, $name, $objType, $href)
	{
		global $prefs;
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

	private function applyManipulator( $manip, $objType, $objId, $desc=NULL, $name=NULL, $href=NULL )
	{
		$old_categories = $this->get_object_categories($objType, $objId, -1, false);
		$manip->setCurrentCategories($old_categories);

		$new_categories = $manip->getAddedCategories();
		$removed_categories = $manip->getRemovedCategories();

		if (empty($new_categories) and empty($removed_categories)) { //nothing changed
			return;
		}

		if (! $catObjectId = $this->is_categorized($objType, $objId) ) {
			$catObjectId = $this->add_categorized_object($objType, $objId, $desc, $name, $href);
		}

		global $prefs;
		if ($prefs["category_autogeocode_within"]) {
			$geocats = $this->getCategories(array('identifier'=>$prefs["category_autogeocode_within"], 'type'=>'descendants'), true, false);
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
						$geolib = TikiLib::lib('geo');
						$geocode = $geolib->geocode($geoloc);
						if ($geocode) {
							$attributelib = TikiLib::lib('attribute');
							if ($prefs["category_autogeocode_replace"] != 'y') {
								$attributes = $attributelib->get_attributes($objType, $objId);
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

		$this->remove_object_from_categories($catObjectId, $removed_categories);
	}

	// Returns an array of OIDs of categories.
	// These categories are those from the specified categories whose parents are not in the set of specified categories.
	// $categories: An array of categories
	function findRoots( $categories )
	{
		$candidates = array();

		foreach ( $categories as $cat ) {
			$id = $cat['parentId'];
			$candidates[$id] = true;
		}

		foreach ( $categories as $cat ) {
			unset( $candidates[ $cat['categId'] ] );
		}

		return array_keys($candidates);
	}

	function get_jailed( $categories )
	{
		if ( $jail = $this->get_jail() ) {
			$existing = $this->getCategories(null, false, false, false);

			return array_values(array_intersect($categories, array_keys($existing)));
		} else {
			return $categories;
		}
	}

	// Returns the categories a new object should be in by default, that is none in general, or the perspective categories if the user is in a perspective.
	function get_default_categories()
	{
		global $prefs;
		if ( $this->get_jail() ) {
			// Default categories are not the entire jail including the sub-categories but only the "root" categories
			return is_array($prefs['category_jail'])? $prefs['category_jail']: array($prefs['category_jail']);
		} else {
			return array();
		}
	}

	// Returns an array containing the ids of the passed $objects present in any of the passed $categories.
	function filter_objects_categories($objects, $categories)
	{
		$query="SELECT `catObjectId` from `tiki_category_objects` where `catObjectId` in (".implode(',', array_fill(0, count($objects), '?')).")";
		if ($categories) {
			$query .= " and `categId` in (".implode(',', array_fill(0, count($categories), '?')).")";
		}
		$result = $this->query($query, array_merge($objects, $categories));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[]=$res["catObjectId"];
		}
		return $ret;
	}
	// unassign all objects from a category
	function unassign_all_objects($categId)
	{
		$query = 'delete from  `tiki_category_objects` where `categId`=?';
		$this->query($query, array((int)$categId));
	}
	//move all objects from a categ to anotehr one
	function move_all_objects($from, $to)
	{
		$query = 'update ignore `tiki_category_objects` set `categId`=? where `categId`=?';
		$this->query($query, array((int)$to, (int)$from));
	}
	//assign all objects of a categ to another one
	function assign_all_objects($from, $to)
	{
		$query = 'insert ignore `tiki_category_objects` (`catObjectId`, `categId`) select `catObjectId`, ? from `tiki_category_objects` where `categId`=?';
		$this->query($query, array((int)$to, (int)$from));
	}
	// generate category tree for use in various places (like categorize_list.php)
	function generate_cat_tree($categories, $canchangeall = false, $forceincat = null)
	{
		$smarty = TikiLib::lib('smarty');
		include_once ('lib/tree/BrowseTreeMaker.php');
		$tree_nodes = array();
		$roots = $this->findRoots($categories);
		foreach ($categories as $c) {
			if (isset($c['name']) || $c['parentId'] != 0) {
				// if used for purposes such as find, should be able to "change" all cats
				if ($canchangeall) {
					$c['canchange'] = true;
				}

				// if used in find, should force incat to check those that have been selected
				if (is_array($forceincat)) {
					$c['incat'] = in_array($c['categId'], $forceincat) ? 'y' : 'n';
				}

				$smarty->assign('category_data', $c);
				$tree_nodes[] = array(
					'id' => $c['categId'],
					'parent' => $c['parentId'],
					'data' => $smarty->fetch('category_tree_entry.tpl'),
				);
			}
		}
		$tm = new BrowseTreeMaker("categorize");
		$res = '';
		foreach ( $roots as $root ) {
			$res .= $tm->make_tree($root, $tree_nodes);
		}
		return $res;
	}

	static function cmpcatname($a, $b)
	{
		$a = TikiLib::strtoupper(TikiLib::take_away_accent($a));
		$b = TikiLib::strtoupper(TikiLib::take_away_accent($b));
		return strcmp($a, $b);
	}

	/* replace each *i in the categories array with the categories of the sudtree i + i */
	function extentCategories($categories)
	{
		$ret = array();
		foreach ($categories as $cat) {
			if (is_numeric($cat)) {
				$ret[] = $cat;
			} else {
				$cats = $this->get_category_descendants(substr($cat, 1));
				$ret[] = substr($cat, 1);
				$ret = array_merge($ret, $cats);
			}
		}
		$ret = array_unique($ret);
		return $ret;
	}
	
	function getCustomFacets()
	{
		$list = array_filter(array_map('intval', $this->get_preference('category_custom_facets', array(), true)));

		return $list;
	}

	/**
	 * Provides the list of all parents for a given set of categories.
	 */
	function get_with_parents($categories)
	{
		$full = array();

		foreach ($categories as $category) {
			$full = array_merge($full, $this->get_parents($category));
		}

		return array_unique($full);
	}

	function get_parents($categId)
	{
		if (! isset($this->parentCategories[$categId])) {
			$category = $this->get_category($categId);
			$this->parentCategories[$categId] = array_keys($category['tepath']);
		}

		return $this->parentCategories[$categId];
	}
}

