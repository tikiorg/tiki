<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/permissions/permissionlib.php,v 1.1 2004-10-26 18:36:32 lfagundes Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
Makes a cache of permissions for each object, considering individual
permissions and category permissions, and handles these permissions
*/

class PermissionLib extends TikiLib {

  var $typeMap = array('wiki page' =>     array('tiki_pages','pageName'),
		       'image gallery' => array('tiki_galleries','galleryId'),
		       'image' =>         array('tiki_images','imageId'),
		       'forum' =>         array('tiki_forums','forumId'),
		       'file gallery' =>  array('tiki_file_galleries','galleryId'),
		       'blog' =>          array('tiki_blogs','blogId'),
		       'tracker' =>       array('tiki_trackers','trackerId'),
		       'quiz' =>          array('tiki_quizzes','quizId'),
		       'poll' =>          array('tiki_polls','pollId'),
		       'survey' =>        array('tiki_surveys','surveyId'),
		       'directory' =>     array('tiki_directory_categories','categId'),
		       'faq' =>           array('tiki_faqs','faqId'),
		       'sheet' =>         array('tiki_sheets','sheetId'),
		       'article' =>       array('tiki_articles','articleId')
		       );
	
  

  function PermissionLib($db) {
	$this->db = $db;

  }

  function isUpdated() {
      global $cachelib;

      if ($cachelib->isCached('permission_updated')) {
	  return $cachelib->getCached('permission_updated') == 'y';
      } 
      
      foreach ($this->typeMap as $type => $params) {
	  if (!$this->isTypeUpdated($type)) {
	      $cachelib->cacheItem('permission_updated','n');
	      return false;
	  }
      }
	  
      $cachelib->cacheItem('permission_updated','y');
      return true;
  }

  function isTypeUpdated($type) {
      list($table, $field) = $this->typeMap[$type];

      $sql = "SELECT COUNT(*) FROM `$table` t LEFT JOIN `tiki_permission_indexed_objects` i ON MD5(".$this->db->concat("?","LOWER(`$field`)").")=i.`objectMD5` WHERE i.`objectMD5` IS NULL";
      return $this->getOne($sql, array($type)) == 0;
  }

  function update() {
      $sql = "SELECT u.* FROM `users_object_permissions` u LEFT JOIN `tiki_permission_indexed_objects` i ON u.`objectId`=i.`objectMD5` WHERE i.`objectId` IS NULL ORDER BY u.`objectId`";
      $result = $this->query($sql);
      
      $lastObj = '';
      while ($row = $result->fetchRow()) {
	  $sql = "INSERT INTO `tiki_permission_index` (`objectType`,`objectId`,`objectMD5`,`groupName`,`permName`) VALUES (?,?,?,?,?)";
	  $bindvars = array($row['objectType'],
			    $this->_getIdByMD5($row['objectType'], $row['objectId']),
			    $row['objectId'],
			    $row['groupName'],
			    $row['permName']);
	  $this->query($sql, $bindvars);

	  if ($lastObj != $row['objectId']) {
	      $this->_markUpdatedObject($row['objectId']);
	      $lastObj = $row['objectId'];
	  }
      }


      if (!$cachelib->isCached("categories_permission_names")) {
	  $perms = $userlib->get_permissions(0, -1, 'permName_desc', 'categories');
	  $cachelib->cacheItem("categories_permission_names",serialize($perms));
      } else {
	  $perms = unserialize($cachelib->getCached("categories_permission_names"));
      }

      $sql = "SELECT c.* FROM `tiki_categorized_objects` c LEFT JOIN `tiki_permission_indexed_objects` i ON MD5(".$this->db->concat("c.`type`","LOWER(c.`objId`)")."=i.`objectmd5` WHERE i.`objectMD5` IS NULL";
      $result = $this->query($sql);

      while ($row = $result->fetchRow()) {

	  $parents = $categlib->get_object_categories($row['type'],$row['objId']);

	  $groups = $userlib->list_all_groups();

	  foreach ($groups as $groupName) {

	      $allowGroup = true;

	      foreach ($parents as $categId) {
		  $categpath = $this->get_category_path($categId);
		  $arraysize = count($categpath);
		  
		  for ($i=$arraysize-1; $i>=0; $i--) {

		      if ($userlib->object_has_one_permission($categpath[$i]['categId'], 'category')) {

			  $objectId = md5('category' . $categpath[$i]['categId']);

			  $sql = "SELECT COUNT(*) FROM `users_objectpermissions` WHERE `groupName`=? AND `objectId`=? AND `objectType`=? AND `permName`=?";
			  $bindvars = array($groupName, $objectId, 'category', 'tiki_p_view_categories');

			  if ($this->getOne($sql, $bindvars)) {
			      break 1;
			  } else {
			      $allowGroup = false;
			      break 2;
			  }
		      }
		  }

	      }
	      
	      if ($allowGroup) {
		  $sql = "INSERT INTO `tiki_permission_index` (`objectType`,`objectId`,`objectMD5`,`groupName`,`permName`) VALUES (?,?,?,?,NULL)";
		  $bindVars = array($row['type'], $row['objId'], md5($row['type'].strtolower($row['objId'])), $groupName);
		  $this->query($sql, $bindVars);
	      }

	  }
	  
	  $objectMD5 = md5($row['type'] . strtolower($row['objId']));

	  $this->_markUpdatedObject($objectMD5);
      }

      foreach ($this->typeMap as $type => $params) {
	  $this->_updateCommonObjects($type);      
      }
      
  }

  function _markUpdatedObject($objectMD5) {
      $sql = "DELETE FROM `tiki_permission_indexed_objects` WHERE `objectMD5`=?";
      $this->query($sql, array($objectMD5));
      
      $sql = "INSERT INTO `tiki_permission_indexed_objects` (`objectMD5`) VALUES (?)";
      $this->query($sql, array($objectMD5));
  }
  

  function _updateCommonObjects($type) {
      list($table, $field) = $this->typeMap[$type];

      $sql = "SELECT `$field` f FROM `$table` LEFT JOIN `tiki_permission_indexed_objects` i ON MD5(".$this->db->concat("?","LOWER(f)").")=i.`objectMD5` WHERE i.`objectMD5` IS NULL";
      $result = $sql->query($sql, array($type));

      $sql = "INSERT INTO `tiki_permission_indexed_objects` (`objectMD5`) VALUES (?)";
      while ($row = $result->fetchRow()) {
	  $this->query($sql, $row['f']);
      }
  }
	
}

global $dbTiki;
$permissionlib = new PermissionLib($dbTiki);
?>
