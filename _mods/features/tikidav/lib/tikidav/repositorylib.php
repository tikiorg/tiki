<?php


/** 
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 12/02/2004
* @copyright (C) 2005 the Tiki community
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
* 
* Basic repository functions
* 
*  */

require_once "lib/tikidav/blog_subrep.php";
include_once ('lib/tikidav/docbooklib.php');
//require_once("lib/tikilib.php");

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
}

class RepositoryLib extends TikiLib {
	var $subrepositories = array ("blog", "news");

	function RepositoryLib() {
		global $dbTiki;
		$this->db = $dbTiki;
	}

	function check_auth($user, $pass) {
		if (isset ($_SESSION["webdavSession"])) {
			return true;
		} else {
			global $userlib;
			if ($userlib->validate_user($user, $pass)) {
				$_SESSION["webdavSession"] = $user;
				return true;
			} else {
				return false;
			}
		}
		return true;
	}

	/*******************************************************************
	 * 
	 *     LOCKING  METHODS
	 * 
	 *******************************************************************/

	function lockUpdate($expiration) {

		$query = "UPDATE tikidav_locks SET expires=? where token=? and path=?";
		$result = $this->query($query, array ($expiration, $locktoken, $path));
		return true;
	}

	function lock($locktoken, $path, $owner, $timeout, $scope) {
		$scope = "exclusive" ? "1" : "0";
		$query = "INSERT INTO tikidav_locks SET token=?, path=?, owner=?, expires=?, exclusivelock=?";
		$result = $this->query($query, array ($locktoken, $path, $owner, $timeout, $scope));
		return true;
		//return mysql_affected_rows() > 0;

	}

	function checkLock($path) {
		$check = false;

		$query = "SELECT owner, token, expires, exclusivelock FROM tikidav_locks WHERE path = ?";
		$result = $this->query($query, array ($path));

		if (isset ($result)) {
			$res = $result->fetchRow();
			if ($res) {
				$check = array ("type" => "write", "scope" => $res["exclusivelock"] ? "exclusive" : "shared", "depth" => 0, "owner" => $res['owner'], "token" => $res['token'], "expires" => $res['expires']);
			}
		}

		return $check;
	}

	function unlock($path, $token) {
		$query = "DELETE FROM tikidav_locks WHERE path = ? AND token = ?";
		$result = $this->query($query, array ($path, $token));
		return true;

	}
	/* 
	 * 
	 * Properties methods
	 *
	 **/
	function proppatch($path, $properties) {

		foreach ($properties as $key => $prop) {
			if ($prop["ns"] == "DAV:") {

			}
			elseif ($prop["ns"] == "TIKI:") {

			} else {
				if (isset ($prop["val"])) {
					$query = "REPLACE INTO tikidav_properties SET path = ?, name = ?, ns= ?, value = ?";
					$result = $this->query($query, array ($path, $prop["name"], $prop["ns"], $prop["value"]));
				} else {
					$query = "DELETE FROM tikidav_properties WHERE path = ? AND name = ? AND ns = ?";
					$result = $this->query($query, array ($path, $prop["name"], $prop["ns"]));
				}

			}
		}

		return "";
	}

	function getProperties($path) {
		$query = "SELECT ns, name, value FROM tikidav_properties WHERE path = ?";
		$result = $this->query($query, array ($path));
		$properties = array ();
		while ($row = $result->fetchRow()) {
			$properties[]["ns"] = $row["ns"];
			$properties[]["name"] = $row["name"];
			$properties[]["value"] = $row["value"];
		}
		return $properties;
	}

	function deleteProperties($path) {
		$query = "DELETE FROM tikidav_properties WHERE path = ?";
		$result = $this->query($query, array ($path));
		return;
	}

	/**
	 * Create a new collection, for the moment implemented as a Tiki category
	 * @param string path to the new collection
	 * @return array properties of the new collection
	 */
	function mkcol($path, $user) {
		if ((trim($path) == "") || ($path == "/") || ($path == "\\")) { //cant create root collection
			return FALSE;
		}

		if (substr($options["path"], -1) == "/") {
			$path = substr($path, 0, -1);
		}
		$nombre = basename($path);
		$directorio = str_replace("\\", "/", dirname($path));

		$parentDir = $this->getObjectMetaInfo($directorio, $user);

		$this->checkTikiPermissions($user, $parentDir["id"]);
		global $tiki_p_admin_categories;
		global $dbTiki;
		include_once ('./lib/categories/categlib.php');

		if ($tiki_p_admin_categories != 'y') {
			return FALSE;
		}

		if (isset ($parentDir)) {
			$categlib->add_category($parentDir["id"], $nombre, $nombre);
			return $this->getObjectMetaInfo($path, $user);
		} else {
			return FALSE;
		}
	}

	/**
	 * Copy a resource to a new path (add a category to the object)  
	 * 
	 * @access public
	 * @param string path Initial path of the resource
	 * @param string dest_path Destination path of the resource
	 * @return array Properties of the moved resource
	 */
	function copy($path, $dest_path, $user) {
		return $this->move($path, $dest_path, $user, FALSE);
	}
	/**
	 * Move a resource to a new path
	 * 
	 * @access public
	 * @param string path Initial path of the resource
	 * @param string dest_path Destination path of the resource
	 * @return array Properties of the moved resource
	 */
	function move($path, $dest_path, $user, $uncat = TRUE) {
		if ((trim($path) == "") || ($path == "/") || ($path == "\\")) {
			return FALSE;
		}
		if ((trim($dest_path) == "") || ($dest_path == "/") || ($dest_path == "\\")) {
			return FALSE;
		}

		global $dbTiki;
		$objMI = $this->getObjectMetaInfo(urldecode($path), $user);
		$destMI = $this->getObjectMetaInfo(urldecode(dirname($dest_path)), $user);
		$origenMI = $this->getObjectMetaInfo(urldecode(dirname($path)), $user);

		if (!isset ($objMI) || !isset ($destMI) || !isset ($origenMI)) {
			return FALSE;
		}

		$this->checkTikiPermissions($user, $destMI["id"]);
		global $tiki_p_admin_categories;
		if ($tiki_p_admin_categories != 'y') {
			return FALSE;
		}

		include_once ('./lib/categories/categlib.php');

		if ($objMI["tikiType"] == "category") {
			$categlib->update_category($objMI["id"], urldecode(basename($dest_path)), $objMI["description"], $destMI["id"]);
		} else {
			$idCatObj = $categlib->is_categorized($objMI["tikiType"], $objMI["id"]);
			if ($idCatObj != 0) {
				$categlib->categorize($idCatObj, $destMI["id"]);
				if ($uncat)
					$categlib->remove_object_from_category($idCatObj, $origenMI["id"]);
			}
		}
		return TRUE;
	}
	/**
	 * Delete a resource
	 * @access public
	 * @param string path Resource path to delete
	 * @return boolean True if delete ok
	 */
	function delete($path, $user) {
		if ((trim($path) == "") || ($path == "/") || ($path == "\\")) {
			return FALSE;
		}

		global $dbTiki;

		//require_once ('tiki-setup.php');
		//global $tikilib;
		//$dbTiki = $tikilib->db;

		$objMI = $this->getObjectMetaInfo(urldecode($path), $user);

		//Get parent category
		$dirPath = dirname($path);
		$collection = $this->getObjectMetaInfo($dirPath, $user);

		if (isset ($objMI) && $objMI != "") {
			$funcname = "delete_".str_replace(" ", "", $objMI["tikiType"]);
			$deleted = $this-> $funcname ($objMI, $collection, $user);
			if ($deleted)
				$this->deleteProperties($path);
			return $deleted;
		} else {
			return FALSE;
		}

	}
	/**
	 * Tiki category delete
	 * @access private
	 * @param string tikiId Tiki resource id
	 * @return boolean True if delete ok
	 */
	function delete_category($objMI, $parentMI, $user) {
		$this->checkTikiPermissions($user, $objMI["id"]);
		global $tiki_p_admin_categories;
		global $dbTiki;
		include_once ('./lib/categories/categlib.php');
		if ($tiki_p_admin_categories == 'y') {
			$categlib->remove_category($objMI["id"]);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * Tiki wikipage delete
	 * @access private
	 * @param string tikiId Tiki resource id
	 * @return boolean True if delete ok
	 */
	function delete_wikipage($objMI, $parentMI, $user) {
		$this->checkTikiPermissions($user, $objMI["id"]);
		global $tiki_p_edit;

		global $dbTiki;
		include_once ('./lib/categories/categlib.php');

		if ($tiki_p_edit == 'y') {
			$categlib->remove_object_from_category($objMI["uid"], $parentMI["id"]);
		} else {
			return FALSE;
		}

		global $tiki_p_remove;
		if ($tiki_p_remove == 'y') {
			$categorias = $categlib->get_object_categories($objMI["tikiType"], $objMI["id"]);
			if (!isset ($categorias) || (count($categorias) == 0)) { //no more categories for the object
				$this->remove_all_versions($objMI["id"]);
			}
		}
		return TRUE;
	}

	function propfind(& $path, $user = "") {
		$pos = strrpos($path, '.');
		$newpath = substr($path, 0, $pos);

		if ($pos && ($newpath.'.docbook' == $path)) {
			$path = $newpath;
			$rendition = "docbook";
		}
		elseif ($pos && ($newpath.'.sxw' == $path)) {
			$path = $newpath;
			$rendition = "sxw";
		}

		return $this->getObjectMetaInfo($path, $user);
	}

	/**
	 * GET a Tiki resource metainfo and data by path,version and rendition type
	 * 
	 * @access public
	 * @param string path Tiki resource path
	 * @param string version Tiki resource version
	 * @param string rendition Rendition type
	 * @return array Resource properties and data
	 */
	function getObjectData($path, $rendition = "default", $user = "") {
		$pos = strrpos($path, '.');
		$newpath = substr($path, 0, $pos);

		if ($pos && ($newpath.'.docbook' == $path)) {
			$path = $newpath;
			$rendition = "docbook";
		}
		elseif ($pos && ($newpath.'.sxw' == $path)) {
			$path = $newpath;
			$rendition = "sxw";
		}
		$objMeta = $this->getObjectMetaInfo($path, $user);

		if (!isset ($objMeta) || $objMeta == "")
			return;

		if ($objMeta["data"] != null || $objMeta["tikiType"] == "category") {
			return $objMeta;
		}

		$id = $objMeta["id"];
		$type = $objMeta["tikiType"];
		$funcname = "get_".str_replace(" ", "", $type);

		$data = $this-> $funcname ($id, $rendition, $user);
		if ($data == FALSE) {
			return FALSE;
		} else {
			$objMeta["data"] = $data;
			return $objMeta;
		}
	}

	function checkTikiPermissions($user, $page) {
		//machacar con los permisos particulares del objeto, los permisos generales del usuario
		//cargados por tiki-setup
		global $userlib;
		global $tiki_p_admin;
		$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
		$allperms = $allperms["data"];

		foreach ($allperms as $vperm) {
			$perm = $vperm["permName"];
			global $$perm;
			if ($user != 'admin' && (!$user || !$userlib->user_has_permission($user, 'tiki_p_admin'))) {
				$$perm = 'n';
			} else {
				$$perm = 'y';
			}
		}

		$perms = $userlib->get_user_permissions($user);
		foreach ($perms as $perm) {
			global $$perm;
			$$perm = 'y';
		}

		if ($user != 'admin' && $tiki_p_admin != 'y' && isset ($page) && $userlib->object_has_one_permission($page, 'wiki page')) {
			$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'wiki');

			if ($userlib->object_has_permission($user, $page, 'wiki page', 'tiki_p_admin_wiki')) {
				foreach ($perms["data"] as $perm) {
					$perm = $perm["permName"];
					global $$perm;
					$$perm = 'y';
				}
			} else {
				foreach ($perms["data"] as $perm) {
					$perm = $perm["permName"];
					if ($userlib->object_has_permission($user, $page, 'wiki page', $perm)) {
						global $$perm;
						$$perm = 'y';
					} else {
						global $$perm;
						$$perm = 'n';
					}
				}
			}
		}
	}

	//No podemos llamar a desbloquear al guardar desde OOo porque no sabemos si ha cerrado el documento
	function tikiUnlock($id, $user) {
		$query = "delete from `tiki_semaphores` where `semName`=? and `user`=?";
		$result = $this->query($query, array ($id, $user));
	}

	function tikiLock($id, $userTD) {
		global $user;
		$user = $userTD;
		global $warn_on_edit_time;
		
		if ($this->semaphore_is_set($id, $warn_on_edit_time * 60)) {
			$semUser = $this->get_semaphore_user($id);
			return $semUser;
		} else {
			$idlock = $this->semaphore_set($id);
			return $userTD;
		}
	}

	/**
	 * GET wikipage
	 */
	function get_wikipage($id, $rendition, $user) {
		$this->checkTikiPermissions($user, $id);
		global $tiki_p_view;

		if ($tiki_p_view == 'y') {
			$this->tikiLock($id, $user);
			$info = "";

			//if ($version == "current") {
			$info = $this->get_page_info($id);
			/*} else {
				include_once ('lib/wiki/histlib.php');
				$info = $histlib->get_version($id, $version);
			}*/
			if ($rendition == "default") {
				return $info['data'];
			}
			elseif ($rendition == "docbook") {
				global $dbTiki;
				$docbook = new DocBookLib($dbTiki);
				$docData = $docbook->parse_docbook_data($info["data"], true);
				return $docData;
			}
			elseif ($rendition == "sxw") {
				global $dbTiki;
				$docbook = new DocBookLib($dbTiki);
				$OOoData = $docbook->parse_openOffice_data($id, $info["data"], true);
				return $OOoData;
			}
			elseif ($rendition == "html") {
				return $this->parse_data($info["data"]);
			}
			return $info['data'];
		} else {
			return FALSE;
		}
	}
	/**
	 * GET article
	 */
	function get_article($id, $rendition, $user) {

		global $tikilib;
		//include_once ('lib/articles/artlib.php');
		$article_data = $tikilib->get_article($id);

		if ($rendition == "default") {
			return $article_data["body"];
		}
		elseif ($rendition == "html") {
			return $this->parse_data($article_data["body"]);
		}
	}
	/**
	 * GET usercalendar
	 */
	function get_usercalendar($id, $rendition, $user) {
		return $this->exportCalendar($user);
	}

	/**
	 * PUT create a new resource or update an existing
	 * 
	 * @access public
	 * @param string path Tiki resource path
	 * @param stream stream Data stream
	 * @param string Tiki userId
	 * @return Tiki resource properties
	 */

	function put($path, $stream, $user) {
		if ((trim($path) == "") || ($path == "/") || ($path == "\\")) {
			return;
		}

		$pos = strrpos($path, '.');
		$newpath = substr($path, 0, $pos);
		$rendition = "";
		if ($pos && ($newpath.'.docbook' == $path)) {
			$path = $newpath;
			$rendition = "docbook";
		}
		elseif ($pos && ($newpath.'.sxw' == $path)) {
			$path = $newpath;
			$rendition = "sxw";
		}

		$fileName = basename($path);
		$dirPath = dirname($path);

		$objMeta = $this->getObjectMetaInfo($path, $user);

		$collection = $this->getObjectMetaInfo($dirPath, $user);
		if (!isset ($collection)) //no parent collection
			return;

		//Read content stream
		$data = "";
		while (!feof($stream)) {
			$data = $data.fread($stream, 4096);
		}

		if (substr($data, 0, 3) == "﻿") //BOM Byte Order Mark problem, start of txt windows file
			$data = substr($data, 3);

		$putfiles = array ();
		if ($rendition == "docbook") {
			global $dbTiki;
			$docbook = new DocBookLib($dbTiki);
			$data = $docbook->docbook_to_wiki($data);
		}
		elseif ($rendition == "sxw") {
			global $dbTiki;
			$docbook = new DocBookLib($dbTiki);
			$putfiles = $docbook->openoffice_to_wiki($data);
			$data = $putfiles["wikidata"];
		}
		//fwrite($gestor, "en put DOCBOOK2 ".$data);

		//Delegate to collection subrepository
		if (in_array($collection["subrepType"], $this->subrepositories) || in_array($collection["tikiType"], $this->subrepositories)) {
			//include "lib/tikidav/".$collection["tikiType"]."_subrep.php";
			$className = $collection["tikiType"]."Subrep";
			$subrep = new $className ();
			return $subrep->put($path, $collection, $objMeta, $data, $user, $rendition, $putfiles);
		}
		elseif (isset ($objMeta) && $objMeta != "") { //update existing object
			$type = $objMeta["tikiType"];
			$funcname = "put_".str_replace(" ", "", $type);
			return $this-> $funcname ($path, $data, $objMeta, $user, $rendition, $putfiles);
		} else { //New wiki object
			return $this->put_wikipage($path, $data, $objMeta, $user, $rendition, $putfiles);
		}
		return FALSE;
	}
	/**
	 * PUT a wiki page
	 * 
	 * @access private
	 */
	function put_wikipage($path, $data, $objMeta, $user, $rendition, $putfiles) {
		//require_once ('tiki-setup.php');

		$this->checkTikiPermissions($user, $objMeta["id"]);
		global $tiki_p_edit;

		if ($tiki_p_edit == 'y') {
			$semUser = $this->get_semaphore_user($objMeta["id"]);
			if (isset($semUser) && $semUser!="" && $semUser!=$user) //the page has a semaphore of other user
				return FALSE;
			$pagename = "";
			if (isset ($objMeta) && $objMeta != "" && $this->page_exists($objMeta["id"])) {
				$this->update_page($objMeta["id"], $data, 'webdav update', $user, $_REQUEST['REMOTE_ADDR'], $objMeta["description"]);
				$pagename = $objMeta["id"];
			} else {

				$nombre = basename($path);
				$directorio = dirname($path);

				$categoria = $this->getObjectMetaInfo($directorio, $user);
				$this->create_page($nombre, 0, $data, getlastmod(), 'created from webdav', $user, $_REQUEST['REMOTE_ADDR'], $nombre);
				$pagename = $nombre;

				//Categorizar
				global $dbTiki;
				include_once ('./lib/categories/categlib.php');
				$categlib3 = new CategLib($dbTiki);
				$idCatObj = $categlib3->add_categorized_object("wiki page", $nombre, $nombre, $nombre, "./tiki-index.php?page=".$nombre);
				$categlib3->categorize($idCatObj, $categoria["id"]);
				$objMeta = $this->getObjectMetaInfo($path, $user);
				$objMeta["new"] = TRUE;
			}
			if ($rendition == "sxw") {
				global $dbTiki;
				$docbook = new DocBookLib($dbTiki);
				$docbook->attachFiles($pagename, $putfiles["pictures"], $user);
			}
			return $objMeta;
		} else {
			return FALSE;
		}
	}
	/**
	 * PUT article
	 * 
	 * @access private
	 */
	function put_article($path, $data, $objMeta, $user, $rendition, $putfiles) {
		//require_once ('tiki-setup.php');
		//global $tikilib;

		$articleId = 0;
		$publishDate = getlastmod();
		$expireDate = getlastmod();
		$title = "Tittle webdav";
		$heading = "heading";
		$authorName = $user;
		$topicId = "";
		$useImage = "n";
		$isfloat = "n";
		$imgname = "";
		$imgtype = "";
		$imgsize = "";
		$imgdata = "";
		$image_x = "";
		$image_y = "";
		$reads = 0;
		$type = "Article";
		$author = $user;
		$creator_edit = "y";
		$rating = 0;

		if (isset ($objMeta)) {
			global $tikilib;
			$article_data = $tikilib->get_article($objMeta["id"]);
			$articleId = $objMeta["id"];
			$publishDate = $article_data["publishDate"];
			$expireDate = $article_data["expireDate"];
			$title = $article_data["title"];
			$heading = $article_data["heading"];
			$authorName = $article_data["authorName"];
			$topicId = $article_data["topicId"];
			$useImage = $article_data["useImage"];
			$isfloat = $article_data["isfloat"];
			$imgname = $article_data["image_name"];
			$imgtype = $article_data["image_type"];
			$imgsize = $article_data["image_size"];
			$imgdata = $article_data["image_data"];
			$image_x = $article_data["image_x"];
			$image_y = $article_data["image_y"];
			$reads = $article_data["reads"];
			$type = $article_data["type"];
			$author = $article_data["author"];
			$creator_edit = $article_data["creator_edit"];
			$rating = $article_data["rating"];

		}
		$artid = $this->replace_article($title, $authorName, $topicId, $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $data, $publishDate, $expireDate, $user, $articleId, $image_x, $image_y, $type, $rating, $isfloat);
		return $objMeta;
	}
	function delLastSlash($path) {
		if (substr($path, -1) == "/") {
			$path = substr($path, 0, -1);
		}
		return $path;
	}

	function getObjectMetaInfo($path, $user) {
		$objMetaInfo = $this->getObjectByPath(0, $path, $user);
		if (isset ($objMetaInfo) && $objMetaInfo != "") {
			$objMetaInfo["path"] = $path;
			$objMetaInfo["properties"] = $this->getProperties($path);
		}
		return $objMetaInfo;
	}

	function getObjectByPath($parentId, $path, $user) {
		$path = substr($path, 1);
		$subpath = $path;
		$nextPath = "";
		if ($nextPath = stristr($path, "/")) { //has more child collections
			$subpath = substr($path, 0, strlen($path) - strlen($nextPath));
		}

		$obj = $this->getRepositoryObject($parentId, $subpath, $user);
		if ($subpath == $path) {
			return $obj;
		}
		elseif (isset ($obj) && $obj != "" && in_array($obj["tikiType"], $this->subrepositories)) {
			//require_once "lib/tikidav/".$obj["tikiType"]."_subrep.php";
			$className = $obj["tikiType"]."Subrep";
			$subrep = new $className ();
			return $subrep->getObjectMetaInfo($obj, $nextPath, $user);
		}
		elseif (isset ($obj) && $obj != "" && $subpath != $path) { // continue with next child path
			return $this->getObjectByPath($obj["id"], $nextPath, $user);
		} else { // Bad path
			return FALSE;
		}
	}

	function getCollectionChilds($path, $user) {
		$collection = $this->getObjectMetaInfo($path, $user);
		if (isset ($collection) && in_array($collection["tikiType"], $this->subrepositories)) {
			//include "lib/tikidav/".$collection["tikiType"]."_subrep.php";
			$className = $collection["tikiType"]."Subrep";
			$subrep = new $className ();
			return $subrep->getCollectionChilds($collection, $path, $user);
		}
		if (isset ($collection)) {

			$childs = $this->getChildObjects($collection["id"], $user);

			foreach ($childs as $key => $objMetaInfo) {
				$childs[$key]["path"] = $this->delLastSlash($path)."/".$objMetaInfo["name"];
				if ($objMetaInfo["tikiType"] == "wiki page") {
					$childs[$key.".docbook"] = $childs[$key];
					$childs[$key.".docbook"]["path"] = $this->delLastSlash($path)."/".$objMetaInfo["name"].".docbook";
					$childs[$key.".sxw"] = $childs[$key];
					$childs[$key.".sxw"]["path"] = $this->delLastSlash($path)."/".$objMetaInfo["name"].".sxw";
				}

			}
			if ($path == "/") {
				$childs[] = $this->getRepositoryObject(0, $user, $user);
			}
			elseif ($path == "/$user") {
				$calObj = $this->getRepositoryObject("9999", "calendar.csv", $user);
				$calObj["path"] = "/$user"."/".$calObj["name"];
				$childs[] = $calObj;
			}

			return $childs;
		} else {
			return;
		}
	}

	function getChildObjects($parentId, $user) {
		//get child objects
		$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`categId`=? and tbl1.`catObjectId`=tbl2.`catObjectId`";
		//and tbl2.`type`='wiki page'
		$result = $this->query($query, array ($parentId));
		$childs = array ();
		while ($res = $result->fetchRow()) {
			$info = $this->createRepositoryObject($res, $user);
			$childs[] = $info;
		}
		//get child categories
		$query = "select * from tiki_categories where parentId=?";
		$result = $this->query($query, array ($parentId));
		while ($res = $result->fetchRow()) {
			$info = $this->createRepositoryCollection($res);
			$childs[] = $info;
		}
		return $childs;
	}

	function getRepositoryObject($parentId, $objName, $user) {
		if (($parentId == 0) && ($objName == "")) { //get root collection
			$res = array ("categId" => "0", "name" => "Root", "description" => "Root collection", "parentId" => "");
			return $this->createRepositoryCollection($res);
		} else
			if (($parentId == 0) && ($objName == $user)) {
				$res = array ("categId" => "9999", "name" => $user, "description" => "User resources", "parentId" => "0");
				return $this->createRepositoryCollection($res);
			}
		elseif (($parentId == "9999") && ($objName == "calendar.csv")) {
			$calendarobj = array ("objId" => "99991", "categId" => "9999", "name" => "calendar.csv", "description" => "User calendar", "type" => "user calendar", "created" => "1090426775");
			return $this->createRepositoryObject($calendarobj);
		}

		$objId = substr($objName, 0, -strlen(stristr($objName, "~")));

		$query = "select * from tiki_categories where parentId=? and name=?";
		$result = $this->query($query, array ($parentId, $objName));
		$res = $result->fetchRow();
		$info = array ();

		if (isset ($res["categId"])) { //Is a category
			$info = $this->createRepositoryCollection($res);
		} else { //not a category, search object
			$objId = substr($objName, 0, -strlen(stristr($objName, "~")));
			$query = "";
			$result = "";
			if (!isset ($objId) || $objId == "") {
				$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`categId`=? and tbl2.`name`=? and tbl1.`catObjectId`=tbl2.`catObjectId`";
				$result = $this->query($query, array ($parentId, $objName));
			} else {
				$query = "select * from `tiki_categorized_objects` tbl2 where tbl2.`catObjectId`=?";
				$result = $this->query($query, array ($objId));
			}
			//$query = "select * from `tiki_category_objects` tbl1,`tiki_categorized_objects` tbl2 where tbl1.`categId`=? and tbl2.`name`=? and tbl1.`catObjectId`=tbl2.`catObjectId`";
			//and tbl2.`type`='wiki page'
			//$result = $this->query($query, array ($parentId, $objName));
			$res = $result->fetchRow();

			if (isset ($res) && $res != "") { //object found
				$info = $this->createRepositoryObject($res);
			} else { //Object not found
				return FALSE;
			}
		}
		return $info;
	}

	function createRepositoryCollection($obj) {
		$info["id"] = $obj["categId"];
		$info["uid"] = 'c'.$obj["categId"];
		$info["parentId"] = $obj["parentId"];
		$info["path"] = "/".$obj["name"];
		$info["name"] = $obj["name"];
		$info['description'] = $obj['description'];
		$info["tikiType"] = "category";
		$info["subrepType"] = "";
		$info["displayname"] = "/".$obj['name'];
		$info["creationdate"] = "1090426775";
		$info["getlastmodified"] = "1090426775";

		$info["resourcetype"] = "collection";
		$info["getcontenttype"] = "httpd/unix-directory";
		$info["getcontentlength"] = 0;
		$info["new"] = FALSE;
		$info["data"] = "";
		$info["mimetype"] = "httpd/unix-directory";
		return $info;
	}

	function createRepositoryObject($obj) {
		$info = array ();
		$info["id"] = $obj["objId"];
		$info["uid"] = $obj["catObjectId"];
		$info["parentId"] = $obj["categId"];
		$info["path"] = "/".$obj["name"];
		$info["name"] = $obj["name"];
		if ($obj["type"] != "wiki page")
			$info["name"] = $obj["catObjectId"]."~".$obj["name"];

		$info['description'] = $obj["description"];
		$info["tikiType"] = $obj["type"];
		$info["subrepType"] = "";
		$info["displayname"] = "/".$obj['name'];
		$info["creationdate"] = $obj["created"];
		$info["getlastmodified"] = $obj["created"];

		if (in_array($obj["type"], $this->subrepositories)) {
			$info["resourcetype"] = "collection";
			$info["getcontenttype"] = "httpd/unix-directory";
			$info["mimetype"] = "httpd/unix-directory";
		} else {
			$info["resourcetype"] = "";
			$info["getcontenttype"] = "text/plain";
			$info["mimetype"] = "text/plain; charset=\"utf-8\"";
		}
		$info["getcontentlength"] = 100;
		$info["new"] = FALSE;
		$info["data"] = "";
		$info["mimetype"] = "text/plain; charset=\"utf-8\"";
		return $info;
	}

	function exportCalendar($user) {

		function _csv($item) {
			$item = str_replace('"', '""', $item);
			$item = '"'.$item.'"';
			return $item;
		}
		global $dbTiki;
		include_once ('lib/minical/minicallib.php');

		$events = $minicallib->minical_list_events($user, 0, -1, 'start_desc', '');

		$calendar = '"Subject","Start Date","Start Time","End Date","End Time","All day event","Reminder on/off","Reminder Date","Reminder Time","Meeting Organizer","Required Attendees","Optional Attendees","Meeting Resources","Billing Information","Categories","Description","Location","Mileage","Priority","Private","Sensitivity","Show time as"';

		$calendar .= "\r\n";

		foreach ($events['data'] as $event) {
			$line = array ();

			$line[] = _csv($event['title']);
			$line[] = _csv(date("n/j/Y", $event['start']));
			$line[] = _csv(date("g:i:s A", $event['start']));
			$line[] = _csv(date("n/j/Y", $event['end']));
			$line[] = _csv(date("g:i:s A", $event['end']));
			$line[] = _csv('False');

			if ($minical_reminders) {
				$line[] = _csv('True');

				$line[] = _csv(date("n/j/Y", $event['start'] - $minical_reminders));
				$line[] = _csv(date("g:i:s A", $event['start'] - $minical_reminders));
			} else {
				$line[] = _csv('False');

				$line[] = _csv('');
				$line[] = _csv('');
			}

			$line[] = '';
			$line[] = '';
			$line[] = '';
			$line[] = '';
			$line[] = '';
			$line[] = '';
			$line[] = '';
			$line[] = _csv($event['description']);
			$line[] = '';
			$line[] = _csv('Normal');
			$line[] = _csv('False');
			$line[] = _csv('Normal');
			$line[] = _csv('2');
			$theline = join(',', $line);
			$calendar .= $theline;
			$calendar .= "\r\n";
		}
		return $calendar;
	}
}
?>